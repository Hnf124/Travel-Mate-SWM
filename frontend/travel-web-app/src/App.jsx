import { useEffect, useState } from "react";
import api from "./api";

function readStoredUser() {
  try {
    return JSON.parse(localStorage.getItem("user") || "null");
  } catch {
    return null;
  }
}

function readError(error, fallback) {
  const errors = error.response?.data?.errors;

  if (errors) {
    const firstGroup = Object.values(errors)[0];

    if (Array.isArray(firstGroup) && firstGroup.length > 0) {
      return firstGroup[0];
    }
  }

  return error.response?.data?.message || fallback;
}

export default function App() {
  const [token, setToken] = useState(
    localStorage.getItem("token") || ""
  );

  const [user, setUser] = useState(readStoredUser);

  const [authMode, setAuthMode] = useState("login");

  const [authForm, setAuthForm] = useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  });

  const [city, setCity] = useState("");
  const [places, setPlaces] = useState([]);
  const [selectedPlace, setSelectedPlace] = useState(null);
  const [weather, setWeather] = useState(null);
  const [favorites, setFavorites] = useState([]);
  const [history, setHistory] = useState([]);

  const [message, setMessage] = useState("");
  const [messageType, setMessageType] = useState("success");

  const [searched, setSearched] = useState(false);
  const [loading, setLoading] = useState("");

  const [drawerOpen, setDrawerOpen] = useState(false);
  const [drawerError, setDrawerError] = useState("");
  const [coverFailed, setCoverFailed] = useState(false);

  const selectedPlaceIsFavorite = selectedPlace
    ? favorites.some(
        (favorite) =>
          favorite.tourism_place_id === selectedPlace.id
      )
    : false;

  useEffect(() => {
    if (token) {
      loadFavorites();
      loadHistory();
    } else {
      setFavorites([]);
      setHistory([]);
    }
  }, [token]);

  useEffect(() => {
    if (!drawerOpen) {
      return undefined;
    }

    const oldOverflow = document.body.style.overflow;

    function handleEscape(event) {
      if (event.key === "Escape") {
        closeDrawer();
      }
    }

    document.body.style.overflow = "hidden";
    window.addEventListener("keydown", handleEscape);

    return () => {
      document.body.style.overflow = oldOverflow;
      window.removeEventListener("keydown", handleEscape);
    };
  }, [drawerOpen]);

  function notify(text, type = "success") {
    setMessage(text);
    setMessageType(type);
  }

  function changeAuthField(event) {
    const { name, value } = event.target;

    setAuthForm((current) => ({
      ...current,
      [name]: value,
    }));
  }

  function changeAuthMode(mode) {
    setAuthMode(mode);
    setMessage("");

    setAuthForm({
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
    });
  }

  function closeDrawer() {
    setDrawerOpen(false);
    setDrawerError("");
  }

  async function submitAuth(event) {
    event.preventDefault();

    setLoading("auth");
    setMessage("");

    const endpoint =
      authMode === "login" ? "/login" : "/register";

    const payload =
      authMode === "login"
        ? {
            email: authForm.email,
            password: authForm.password,
          }
        : authForm;

    try {
      const response = await api.post(endpoint, payload);
      const session = response.data.data;

      localStorage.setItem("token", session.token);
      localStorage.setItem(
        "user",
        JSON.stringify(session.user)
      );

      setToken(session.token);
      setUser(session.user);

      setAuthForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
      });

      notify(
        authMode === "login"
          ? "Login berhasil."
          : "Registrasi berhasil dan Anda sudah login."
      );
    } catch (error) {
      notify(
        readError(
          error,
          authMode === "login"
            ? "Login gagal."
            : "Registrasi gagal."
        ),
        "error"
      );
    } finally {
      setLoading("");
    }
  }

  async function loadFavorites() {
    try {
      const response = await api.get("/favorites");
      setFavorites(response.data.data);
    } catch {
      setFavorites([]);
    }
  }

  async function loadHistory() {
    try {
      const response = await api.get("/search-history");
      setHistory(response.data.data);
    } catch {
      setHistory([]);
    }
  }

  async function searchPlaces() {
    if (!city.trim()) {
      notify(
        "Masukkan nama kota terlebih dahulu.",
        "error"
      );

      return;
    }

    setLoading("search");
    setMessage("");
    setSearched(true);

    setDrawerOpen(false);
    setSelectedPlace(null);
    setWeather(null);

    try {
      const response = await api.get(
        "/tourism-places",
        {
          params: {
            city: city.trim(),
          },
        }
      );

      setPlaces(response.data.data);

      await api.post("/search-history", {
        keyword: city.trim(),
        type: "city",
      });

      await loadHistory();
    } catch (error) {
      setPlaces([]);

      notify(
        readError(
          error,
          "Gagal mencari tempat wisata."
        ),
        "error"
      );
    } finally {
      setLoading("");
    }
  }

  async function openDetail(placeId) {
    setDrawerOpen(true);
    setLoading("detail");

    setSelectedPlace(null);
    setWeather(null);
    setDrawerError("");
    setCoverFailed(false);

    try {
      const response = await api.get(
        `/tourism-places/${placeId}`
      );

      const detail = response.data.data;

      setSelectedPlace(detail);

      try {
        const weatherResponse = await api.get(
          "/weather",
          {
            params: {
              city: detail.city,
            },
          }
        );

        setWeather(weatherResponse.data.data);
      } catch (error) {
        setDrawerError(
          readError(
            error,
            "Detail wisata berhasil dimuat, tetapi data cuaca gagal diambil."
          )
        );
      }
    } catch (error) {
      setDrawerError(
        readError(
          error,
          "Gagal membuka detail tempat wisata."
        )
      );
    } finally {
      setLoading("");
    }
  }

  async function addFavorite() {
    if (!selectedPlace) {
      return;
    }

    setLoading("favorite");
    setDrawerError("");

    try {
      await api.post("/favorites", {
        tourism_place_id: selectedPlace.id,
      });

      await loadFavorites();

      notify(
        "Tempat wisata berhasil disimpan ke favorit."
      );
    } catch (error) {
      setDrawerError(
        readError(
          error,
          "Gagal menyimpan tempat wisata ke favorit."
        )
      );
    } finally {
      setLoading("");
    }
  }

  async function removeFavorite(
    tourismPlaceId
  ) {
    setLoading("favorite");
    setMessage("");

    try {
      await api.delete(
        `/favorites/${tourismPlaceId}`
      );

      await loadFavorites();

      notify(
        "Tempat wisata berhasil dihapus dari favorit."
      );
    } catch (error) {
      notify(
        readError(
          error,
          "Gagal menghapus tempat wisata dari favorit."
        ),
        "error"
      );
    } finally {
      setLoading("");
    }
  }

  async function logout() {
    try {
      await api.post("/logout");
    } catch {
      setMessage("");
    }

    localStorage.removeItem("token");
    localStorage.removeItem("user");

    setToken("");
    setUser(null);
    setPlaces([]);
    setSelectedPlace(null);
    setWeather(null);
    setFavorites([]);
    setHistory([]);
    setSearched(false);
    setDrawerOpen(false);

    notify("Logout berhasil.");
  }

  return (
    <>
      <style>
        {`
          * {
            box-sizing: border-box;
          }

          body {
            margin: 0;
            min-width: 320px;
            background: #f3f6fb;
            color: #172033;
            font-family: Inter, Arial, sans-serif;
          }

          button,
          input {
            font: inherit;
          }

          button {
            transition:
              transform 0.18s ease,
              box-shadow 0.18s ease,
              opacity 0.18s ease;
          }

          button:not(:disabled):hover {
            transform: translateY(-1px);
          }

          button:disabled {
            cursor: not-allowed;
          }

          .page {
            min-height: 100vh;
            background: #f3f6fb;
          }

          .container {
            width: min(
              1050px,
              calc(100% - 32px)
            );
            margin: 0 auto;
            padding: 32px 0 48px;
          }

          .header {
            margin-bottom: 20px;
            padding: 28px;
            border-radius: 18px;
            background:
              linear-gradient(
                135deg,
                #176b87,
                #0d9488
              );
            color: white;
            box-shadow:
              0 18px 45px
              rgba(23, 107, 135, 0.18);
          }

          .header h1 {
            margin: 0;
            color: white;
            font-size: 44px;
          }

          .header p {
            margin: 8px 0 0;
            color:
              rgba(255, 255, 255, 0.9);
          }

          .card {
            margin-bottom: 18px;
            padding: 22px;
            border:
              1px solid #dbe4ef;
            border-radius: 16px;
            background: white;
            box-shadow:
              0 8px 24px
              rgba(15, 23, 42, 0.06);
          }

          .card h2 {
            margin-top: 0;
          }

          .form-group {
            margin-bottom: 14px;
          }

          .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 700;
          }

          .input {
            width: 100%;
            padding: 12px 14px;
            border:
              1px solid #cbd5e1;
            border-radius: 10px;
            background: white;
            color: #172033;
            font-size: 16px;
            outline: none;
          }

          .input:focus {
            border-color: #0f766e;
            box-shadow:
              0 0 0 3px
              rgba(15, 118, 110, 0.12);
          }

          .row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
          }

          .primary-button {
            border: 0;
            border-radius: 10px;
            padding: 11px 17px;
            background: #0f766e;
            color: white;
            font-weight: 700;
            cursor: pointer;
          }

          .primary-button:hover:not(:disabled) {
            box-shadow:
              0 8px 18px
              rgba(15, 118, 110, 0.22);
          }

          .secondary-button {
            border:
              1px solid #94a3b8;
            border-radius: 10px;
            padding: 10px 15px;
            background: white;
            color: #334155;
            font-weight: 700;
            cursor: pointer;
          }

          .danger-button {
            border:
              1px solid #fecaca;
            border-radius: 9px;
            padding: 8px 12px;
            background: #fff1f2;
            color: #be123c;
            font-weight: 700;
            cursor: pointer;
          }

          .message-success {
            margin-bottom: 18px;
            padding: 13px;
            border:
              1px solid #a7f3d0;
            border-radius: 10px;
            background: #ecfdf5;
            color: #047857;
          }

          .message-error {
            margin-bottom: 18px;
            padding: 13px;
            border:
              1px solid #fecaca;
            border-radius: 10px;
            background: #fff1f2;
            color: #be123c;
          }

          .user-card {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content:
              space-between;
            gap: 12px;
          }

          .muted {
            color: #64748b;
          }

          .search-input {
            flex: 1 1 280px;
          }

          .grid {
            display: grid;
            grid-template-columns:
              repeat(
                auto-fit,
                minmax(230px, 1fr)
              );
            gap: 14px;
          }

          .place-card {
            display: flex;
            flex-direction: column;
            padding: 16px;
            border:
              1px solid #dbe4ef;
            border-radius: 12px;
            background: #f8fafc;
            min-height: 210px;
          }

          .place-card h3 {
            margin: 0 0 6px;
          }

          .place-card p {
            line-height: 1.6;
          }

          .place-card button {
            margin-top: auto;
          }

          .badge {
            display: inline-block;
            width: fit-content;
            margin-bottom: 8px;
            padding: 4px 9px;
            border-radius: 999px;
            background: #ccfbf1;
            color: #0f766e;
            font-size: 12px;
            font-weight: 800;
          }

          .list {
            display: grid;
            gap: 10px;
          }

          .list-item {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content:
              space-between;
            gap: 12px;
            padding: 13px;
            border:
              1px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
          }

          .drawer-overlay {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: flex;
            justify-content: flex-end;
            background:
              rgba(15, 23, 42, 0.5);
            animation:
              overlayFadeIn
              0.25s ease forwards;
          }

          .drawer {
            width:
              min(520px, 100%);
            height: 100vh;
            overflow-y: auto;
            background: white;
            box-shadow:
              -20px 0 50px
              rgba(15, 23, 42, 0.24);
            animation:
              drawerSlideIn
              0.32s
              cubic-bezier(
                0.22,
                1,
                0.36,
                1
              )
              forwards;
          }

          .drawer-header {
            position: sticky;
            top: 0;
            z-index: 4;
            display: flex;
            align-items: center;
            justify-content:
              space-between;
            gap: 16px;
            padding: 18px 20px;
            border-bottom:
              1px solid #e2e8f0;
            background:
              rgba(
                255,
                255,
                255,
                0.96
              );
            backdrop-filter:
              blur(10px);
          }

          .drawer-header h2 {
            margin: 0;
            font-size: 20px;
          }

          .close-button {
            width: 42px;
            height: 42px;
            border:
              1px solid #cbd5e1;
            border-radius: 11px;
            background: white;
            color: #334155;
            font-size: 26px;
            line-height: 1;
            cursor: pointer;
          }

          .drawer-content {
            padding: 20px;
          }

          .drawer-cover {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border:
              1px solid #dbe4ef;
            border-radius: 14px;
            background: #e2e8f0;
          }

          .cover-placeholder {
            display: grid;
            place-items: center;
            width: 100%;
            height: 230px;
            padding: 20px;
            border:
              1px dashed #94a3b8;
            border-radius: 14px;
            background:
              linear-gradient(
                135deg,
                #e0f2fe,
                #ccfbf1
              );
            color: #475569;
            font-weight: 700;
            text-align: center;
          }

          .drawer-title {
            margin: 14px 0 6px;
            font-size: 28px;
          }

          .info-grid {
            display: grid;
            grid-template-columns:
              repeat(
                2,
                minmax(0, 1fr)
              );
            gap: 10px;
          }

          .info-box {
            padding: 13px;
            border:
              1px solid #e2e8f0;
            border-radius: 11px;
            background: #f8fafc;
          }

          .info-box strong {
            display: block;
            margin-bottom: 5px;
          }

          .drawer-section {
            margin-top: 20px;
          }

          .drawer-section p {
            line-height: 1.7;
          }

          .favorite-button {
            width: 100%;
            margin-top: 20px;
          }

          .divider {
            margin: 24px 0;
            border: 0;
            border-top:
              1px solid #e2e8f0;
          }

          .drawer-error {
            margin-bottom: 16px;
            padding: 12px;
            border:
              1px solid #fecaca;
            border-radius: 10px;
            background: #fff1f2;
            color: #be123c;
          }

          @keyframes overlayFadeIn {
            from {
              opacity: 0;
            }

            to {
              opacity: 1;
            }
          }

          @keyframes drawerSlideIn {
            from {
              transform:
                translateX(100%);
            }

            to {
              transform:
                translateX(0);
            }
          }

          @media (
            max-width: 600px
          ) {
            .container {
              width:
                min(
                  100% - 20px,
                  1050px
                );
              padding-top: 10px;
            }

            .header {
              padding: 22px;
              border-radius: 14px;
            }

            .header h1 {
              font-size: 34px;
            }

            .card {
              padding: 18px;
            }

            .drawer {
              width: 100%;
            }

            .info-grid {
              grid-template-columns:
                1fr;
            }
          }
        `}
      </style>

      <div className="page">
        <main className="container">
          <header className="header">
            <h1>TravelMate</h1>

            <p>
              Sistem rekomendasi tempat wisata dan
              cuaca lokal
            </p>
          </header>

          {message && (
            <div
              className={
                messageType === "error"
                  ? "message-error"
                  : "message-success"
              }
            >
              {message}
            </div>
          )}

          {!token ? (
            <section className="card">
              <h2>
                {authMode === "login"
                  ? "Login ke TravelMate"
                  : "Buat akun"}
              </h2>

              <div
                className="row"
                style={{
                  marginBottom: 18,
                }}
              >
                <button
                  type="button"
                  className={
                    authMode === "login"
                      ? "primary-button"
                      : "secondary-button"
                  }
                  onClick={() =>
                    changeAuthMode("login")
                  }
                >
                  Login
                </button>

                <button
                  type="button"
                  className={
                    authMode === "register"
                      ? "primary-button"
                      : "secondary-button"
                  }
                  onClick={() =>
                    changeAuthMode("register")
                  }
                >
                  Register
                </button>
              </div>

              <form onSubmit={submitAuth}>
                {authMode === "register" && (
                  <div className="form-group">
                    <label htmlFor="name">
                      Nama
                    </label>

                    <input
                      id="name"
                      name="name"
                      type="text"
                      className="input"
                      value={authForm.name}
                      onChange={changeAuthField}
                      required
                    />
                  </div>
                )}

                <div className="form-group">
                  <label htmlFor="email">
                    Email
                  </label>

                  <input
                    id="email"
                    name="email"
                    type="email"
                    className="input"
                    value={authForm.email}
                    onChange={changeAuthField}
                    required
                  />
                </div>

                <div className="form-group">
                  <label htmlFor="password">
                    Password
                  </label>

                  <input
                    id="password"
                    name="password"
                    type="password"
                    className="input"
                    value={authForm.password}
                    onChange={changeAuthField}
                    minLength={6}
                    required
                  />
                </div>

                {authMode === "register" && (
                  <div className="form-group">
                    <label
                      htmlFor="password_confirmation"
                    >
                      Konfirmasi password
                    </label>

                    <input
                      id="password_confirmation"
                      name="password_confirmation"
                      type="password"
                      className="input"
                      value={
                        authForm.password_confirmation
                      }
                      onChange={changeAuthField}
                      minLength={6}
                      required
                    />
                  </div>
                )}

                <button
                  type="submit"
                  className="primary-button"
                  disabled={loading === "auth"}
                  style={{
                    opacity:
                      loading === "auth"
                        ? 0.6
                        : 1,
                  }}
                >
                  {loading === "auth"
                    ? "Memproses..."
                    : authMode === "login"
                      ? "Login"
                      : "Register"}
                </button>
              </form>
            </section>
          ) : (
            <>
              <section className="card user-card">
                <div>
                  <strong>
                    Login sebagai{" "}
                    {user?.name || "Pengguna"}
                  </strong>

                  <div className="muted">
                    {user?.email}
                  </div>
                </div>

                <button
                  type="button"
                  className="secondary-button"
                  onClick={logout}
                >
                  Logout
                </button>
              </section>

              <section className="card">
                <h2>
                  Pencarian Tempat Wisata
                </h2>

                <div className="row">
                  <div className="search-input">
                    <input
                      type="text"
                      className="input"
                      value={city}
                      placeholder="Contoh: Banda Aceh"
                      onChange={(event) =>
                        setCity(
                          event.target.value
                        )
                      }
                      onKeyDown={(event) => {
                        if (
                          event.key === "Enter"
                        ) {
                          searchPlaces();
                        }
                      }}
                    />
                  </div>

                  <button
                    type="button"
                    className="primary-button"
                    onClick={searchPlaces}
                    disabled={
                      loading === "search"
                    }
                    style={{
                      opacity:
                        loading === "search"
                          ? 0.6
                          : 1,
                    }}
                  >
                    {loading === "search"
                      ? "Mencari..."
                      : "Cari"}
                  </button>
                </div>
              </section>

              {searched && (
                <section className="card">
                  <h2>Hasil Pencarian</h2>

                  {places.length === 0 ? (
                    <p className="muted">
                      Tidak ada tempat wisata
                      yang ditemukan.
                    </p>
                  ) : (
                    <div className="grid">
                      {places.map((place) => (
                        <article
                          key={place.id}
                          className="place-card"
                        >
                          <span className="badge">
                            {place.category}
                          </span>

                          <h3>{place.name}</h3>

                          <div className="muted">
                            {place.city}
                          </div>

                          <p>
                            {
                              place.short_description
                            }
                          </p>

                          <button
                            type="button"
                            className="secondary-button"
                            onClick={() =>
                              openDetail(
                                place.id
                              )
                            }
                          >
                            Lihat Detail
                          </button>
                        </article>
                      ))}
                    </div>
                  )}
                </section>
              )}

              <section className="card">
                <h2>Favorit</h2>

                {favorites.length === 0 ? (
                  <p className="muted">
                    Belum ada tempat wisata
                    favorit.
                  </p>
                ) : (
                  <div className="list">
                    {favorites.map(
                      (favorite) => (
                        <div
                          key={favorite.id}
                          className="list-item"
                        >
                          <div>
                            <strong>
                              {favorite
                                .tourism_place
                                ?.name ||
                                "Tempat wisata tidak ditemukan"}
                            </strong>

                            <div className="muted">
                              {
                                favorite
                                  .tourism_place
                                  ?.city
                              }
                            </div>
                          </div>

                          <button
                            type="button"
                            className="danger-button"
                            onClick={() =>
                              removeFavorite(
                                favorite.tourism_place_id
                              )
                            }
                            disabled={
                              loading ===
                              "favorite"
                            }
                          >
                            Hapus
                          </button>
                        </div>
                      )
                    )}
                  </div>
                )}
              </section>

              <section className="card">
                <h2>
                  Riwayat Pencarian
                </h2>

                {history.length === 0 ? (
                  <p className="muted">
                    Belum ada riwayat
                    pencarian.
                  </p>
                ) : (
                  <div className="list">
                    {history.map((item) => (
                      <div
                        key={item.id}
                        className="list-item"
                      >
                        <div>
                          <strong>
                            {item.keyword}
                          </strong>

                          <div className="muted">
                            {item.type}
                          </div>
                        </div>

                        <div className="muted">
                          {new Date(
                            item.updated_at ||
                              item.created_at
                          ).toLocaleString(
                            "id-ID"
                          )}
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </section>
            </>
          )}
        </main>

        {drawerOpen && (
          <div
            className="drawer-overlay"
            onMouseDown={closeDrawer}
          >
            <aside
              className="drawer"
              role="dialog"
              aria-modal="true"
              aria-label="Detail tempat wisata"
              onMouseDown={(event) =>
                event.stopPropagation()
              }
            >
              <div className="drawer-header">
                <h2>
                  Detail Tempat Wisata
                </h2>

                <button
                  type="button"
                  className="close-button"
                  aria-label="Tutup detail"
                  onClick={closeDrawer}
                >
                  ×
                </button>
              </div>

              <div className="drawer-content">
                {drawerError && (
                  <div className="drawer-error">
                    {drawerError}
                  </div>
                )}

                {loading === "detail" &&
                !selectedPlace ? (
                  <div className="cover-placeholder">
                    Memuat detail tempat
                    wisata...
                  </div>
                ) : selectedPlace ? (
                  <>
                    {selectedPlace.image_url &&
                    !coverFailed ? (
                      <img
                        className="drawer-cover"
                        src={
                          selectedPlace.image_url
                        }
                        alt={
                          selectedPlace.name
                        }
                        onError={() =>
                          setCoverFailed(true)
                        }
                      />
                    ) : (
                      <div className="cover-placeholder">
                        Gambar belum tersedia
                      </div>
                    )}

                    <div
                      style={{
                        marginTop: 18,
                      }}
                    >
                      <span className="badge">
                        {
                          selectedPlace.category
                        }
                      </span>

                      <h2 className="drawer-title">
                        {selectedPlace.name}
                      </h2>

                      <div className="muted">
                        {selectedPlace.city}
                      </div>
                    </div>

                    <div
                      className="info-grid"
                      style={{
                        marginTop: 18,
                      }}
                    >
                      <div className="info-box">
                        <strong>
                          Kategori
                        </strong>

                        <div className="muted">
                          {
                            selectedPlace.category
                          }
                        </div>
                      </div>

                      <div className="info-box">
                        <strong>Kota</strong>

                        <div className="muted">
                          {selectedPlace.city}
                        </div>
                      </div>
                    </div>

                    <div className="drawer-section">
                      <strong>Alamat</strong>

                      <p>
                        {
                          selectedPlace.address
                        }
                      </p>
                    </div>

                    <div className="drawer-section">
                      <strong>Deskripsi</strong>

                      <p>
                        {
                          selectedPlace.description
                        }
                      </p>
                    </div>

                    <button
                      type="button"
                      className="primary-button favorite-button"
                      onClick={addFavorite}
                      disabled={
                        loading ===
                          "favorite" ||
                        selectedPlaceIsFavorite
                      }
                      style={{
                        opacity:
                          loading ===
                            "favorite" ||
                          selectedPlaceIsFavorite
                            ? 0.6
                            : 1,
                      }}
                    >
                      {loading === "favorite"
                        ? "Memproses..."
                        : selectedPlaceIsFavorite
                          ? "Sudah Ada di Favorit"
                          : "Simpan ke Favorit"}
                    </button>

                    <hr className="divider" />

                    <h3>Cuaca Terkini</h3>

                    {weather ? (
                      <div className="info-grid">
                        <div className="info-box">
                          <strong>
                            Suhu
                          </strong>

                          <div className="muted">
                            {
                              weather.temperature
                            }{" "}
                            °C
                          </div>
                        </div>

                        <div className="info-box">
                          <strong>
                            Kondisi
                          </strong>

                          <div className="muted">
                            {
                              weather.condition
                            }
                          </div>
                        </div>

                        <div className="info-box">
                          <strong>
                            Kelembapan
                          </strong>

                          <div className="muted">
                            {
                              weather.humidity
                            }
                            %
                          </div>
                        </div>

                        <div className="info-box">
                          <strong>
                            Kecepatan Angin
                          </strong>

                          <div className="muted">
                            {
                              weather.wind_speed
                            }{" "}
                            m/s
                          </div>
                        </div>
                      </div>
                    ) : loading ===
                      "detail" ? (
                      <p className="muted">
                        Memuat data cuaca...
                      </p>
                    ) : (
                      <p className="muted">
                        Data cuaca tidak
                        tersedia.
                      </p>
                    )}
                  </>
                ) : (
                  <p className="muted">
                    Detail tempat wisata tidak
                    tersedia.
                  </p>
                )}
              </div>
            </aside>
          </div>
        )}
      </div>
    </>
  );
}