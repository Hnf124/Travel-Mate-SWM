import React, { useState, useEffect } from "react";
import api from "./api";

export default function App() {
  const [token, setToken] = useState(localStorage.getItem("token") || "");
  const [user, setUser] = useState(JSON.parse(localStorage.getItem("user") || "null"));
  const [city, setCity] = useState("");
  const [places, setPlaces] = useState([]);
  const [selectedPlace, setSelectedPlace] = useState(null);
  const [weather, setWeather] = useState(null);
  const [favorites, setFavorites] = useState([]);
  const [history, setHistory] = useState([]);
  const [message, setMessage] = useState("");

  useEffect(() => {
    if(token) {
      loadFavorites();
      loadHistory();
    }
  }, [token]);

  const searchPlaces = async () => {
    try {
      const res = await api.get(`/tourism-places?city=${encodeURIComponent(city)}`);
      setPlaces(res.data.data);
      setSelectedPlace(null);
      setWeather(null);
      if(token && city.trim()!=="") {
        await api.post("/search-history", { keyword: city, type: "city" });
        await loadHistory();
      }
    } catch(err) {
      setMessage(err.response?.data?.message || "Gagal mencari tempat wisata");
    }
  };

  const openDetail = async (place) => {
    try {
      const res = await api.get(`/tourism-places/${place.id}`);
      setSelectedPlace(res.data.data);
      if(token){
        const weatherRes = await api.get(`/weather?city=${encodeURIComponent(res.data.data.city)}`);
        setWeather(weatherRes.data.data);
      }
    } catch(err){
      setMessage(err.response?.data?.message || "Gagal membuka detail");
    }
  };

  const loadFavorites = async () => {
    try {
      const res = await api.get("/favorites");
      setFavorites(res.data.data);
    } catch {
      setFavorites([]);
    }
  };

  const loadHistory = async () => {
    try {
      const res = await api.get("/search-history");
      setHistory(res.data.data);
    } catch {
      setHistory([]);
    }
  };

  const addFavorite = async () => {
    if(!selectedPlace) return;
    try {
      await api.post("/favorites",{ tourism_place_id: selectedPlace.id });
      await loadFavorites();
      setMessage("Berhasil disimpan ke favorit");
    } catch(err){
      setMessage(err.response?.data?.message || "Gagal menyimpan favorit");
    }
  };

  const removeFavorite = async (favId) => {
    try {
      await api.delete(`/favorites/${favId}`);
      await loadFavorites();
      setMessage("Favorit dihapus");
    } catch(err){
      setMessage(err.response?.data?.message || "Gagal menghapus favorit");
    }
  };

  const logout = async () => {
    try { await api.post("/logout"); } catch {}
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    setToken(""); setUser(null); setFavorites([]); setHistory([]); setSelectedPlace(null); setWeather(null);
    setMessage("Logout berhasil");
  };

  return (
    <div style={{ padding: 24, fontFamily:"Arial, sans-serif", maxWidth:1200, margin:"0 auto" }}>
      <h1>TravelMate Web App</h1>
      <p>Sistem rekomendasi tempat wisata dan cuaca lokal</p>

      {message && <div style={{ background:"#f2f2f2", padding:12, marginBottom:16 }}>{message}</div>}

      {!token ? (
        <div>
          <h2>Login/Register</h2>
          <p>Frontend login form belum dipaste di sini, nanti bisa ditambah sesuai rencana.</p>
        </div>
      ) : (
        <div style={{ marginBottom:24 }}>
          <p>Login sebagai: {user?.name}</p>
          <button onClick={logout}>Logout</button>
        </div>
      )}

      <hr style={{margin:"24px 0"}} />

      <div>
        <h2>Pencarian Tempat Wisata</h2>
        <input placeholder="Masukkan kota" value={city} onChange={(e)=>setCity(e.target.value)} />
        <button onClick={searchPlaces}>Cari</button>

        {places.map(p=>(
          <div key={p.id} style={{border:"1px solid #ddd", padding:12, cursor:"pointer"}} onClick={()=>openDetail(p)}>
            <strong>{p.name}</strong> <div>{p.city}</div> <div>{p.category}</div>
            <p>{p.short_description}</p>
          </div>
        ))}
      </div>

      {selectedPlace && (
        <div>
          <hr/>
          <h2>Detail Tempat Wisata</h2>
          <h3>{selectedPlace.name}</h3>
          <p>Kota: {selectedPlace.city}</p>
          <p>Kategori: {selectedPlace.category}</p>
          <p>Alamat: {selectedPlace.address}</p>
          <p>{selectedPlace.description}</p>
          {selectedPlace.image_url && <img src={selectedPlace.image_url} style={{width:300}} alt={selectedPlace.name}/>}
          {token && <button onClick={addFavorite}>Simpan ke Favorit</button>}
        </div>
      )}

      {weather && (
        <div>
          <hr/>
          <h2>Cuaca Terkini</h2>
          <p>Kota: {weather.city}</p>
          <p>Suhu: {weather.temperature}°C</p>
          <p>Kondisi: {weather.condition}</p>
          <p>Kelembapan: {weather.humidity}%</p>
          <p>Angin: {weather.wind_speed} m/s</p>
        </div>
      )}

      {token && (
        <div>
          <hr/>
          <h2>Favorit</h2>
          {favorites.map(f=><div key={f.id}><strong>{f.tourism_place?.name}</strong> <button onClick={()=>removeFavorite(f.id)}>Hapus</button></div>)}

          <hr/>
          <h2>Riwayat Pencarian</h2>
          {history.map(h=><div key={h.id}><strong>{h.keyword}</strong> - {h.type} - {h.created_at}</div>)}
        </div>
      )}
    </div>
  );
}