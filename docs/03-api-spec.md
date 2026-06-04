API Specification

Register Pengguna

Method: POST

URL: /api/v1/register

Deskripsi: Endpoint ini digunakan untuk mendaftarkan akun pengguna baru ke dalam sistem TravelMate.

Autentikasi Diperlukan: Tidak

Sumber: Internal System

Request Headers:
Content-Type: application/json

Request Body:
{
"name": "string",
"email": "string",
"password": "string",
"password_confirmation": "string"
}

Response Sukses:
{
"status": "success",
"message": "User registered successfully",
"data": {
"id": 1,
"name": "Hanif Hidayat",
"email": "[hanif@example.com](mailto:hanif@example.com)"
}
}

Response Gagal:
{
"status": "error",
"message": "Validation failed"
}

Login Pengguna

Method: POST

URL: /api/v1/login

Deskripsi: Endpoint ini digunakan agar pengguna dapat masuk ke aplikasi menggunakan email dan password. Jika login berhasil, sistem akan mengembalikan token autentikasi.

Autentikasi Diperlukan: Tidak

Sumber: Internal System

Request Headers:
Content-Type: application/json

Request Body:
{
"email": "string",
"password": "string"
}

Response Sukses:
{
"status": "success",
"message": "Login successful",
"data": {
"token": "bearer_token",
"user": {
"id": 1,
"name": "Hanif Hidayat",
"email": "[hanif@example.com](mailto:hanif@example.com)"
}
}
}

Response Gagal:
{
"status": "error",
"message": "Invalid email or password"
}

Logout Pengguna

Method: POST

URL: /api/v1/logout

Deskripsi: Endpoint ini digunakan untuk menghapus token autentikasi pengguna yang sedang login.

Autentikasi Diperlukan: Ya

Sumber: Internal System

Request Headers:
Authorization: Bearer <token>
Content-Type: application/json

Request Body:
{}

Response Sukses:
{
"status": "success",
"message": "Logout successful"
}

Response Gagal:
{
"status": "error",
"message": "Unauthenticated"
}

Get Tourism Places by City

Method: GET

URL: /api/v1/tourism-places?city={city_name}

Deskripsi: Endpoint ini digunakan untuk mengambil daftar tempat wisata berdasarkan nama kota yang dicari oleh pengguna.

Autentikasi Diperlukan: Tidak

Sumber: Internal System

Request Headers:
Content-Type: application/json

## Request Body:

Response Sukses:
{
"status": "success",
"data": [
{
"id": 1,
"name": "Pantai Lampuuk",
"city": "Banda Aceh",
"category": "Beach",
"short_description": "Tempat wisata pantai dengan pemandangan laut dan pasir putih."
},
{
"id": 2,
"name": "Museum Tsunami Aceh",
"city": "Banda Aceh",
"category": "Museum",
"short_description": "Museum edukatif yang menyimpan sejarah peristiwa tsunami Aceh."
}
]
}

Response Gagal:
{
"status": "error",
"message": "Tourism places not found"
}

Get Tourism Place Detail

Method: GET

URL: /api/v1/tourism-places/{id}

Deskripsi: Endpoint ini digunakan untuk mengambil detail tempat wisata berdasarkan ID tempat wisata yang dipilih oleh pengguna.

Autentikasi Diperlukan: Tidak

Sumber: Internal System

Request Headers:
Content-Type: application/json

## Request Body:

Response Sukses:
{
"status": "success",
"data": {
"id": 1,
"name": "Pantai Lampuuk",
"city": "Banda Aceh",
"category": "Beach",
"address": "Lhoknga, Aceh Besar",
"description": "Pantai Lampuuk merupakan salah satu destinasi wisata pantai yang terkenal di Aceh Besar.",
"image_url": "https://example.com/images/pantai-lampuuk.jpg"
}
}

Response Gagal:
{
"status": "error",
"message": "Tourism place not found"
}

Get Current Weather

Method: GET

URL: /api/v1/weather?city={city_name}

Deskripsi: Endpoint ini digunakan untuk mengambil informasi cuaca terkini berdasarkan nama kota. Backend Laravel akan mengambil data dari OpenWeatherMap API, kemudian mengirimkan hasilnya ke frontend.

Autentikasi Diperlukan: Ya

Sumber: Third-Party API — OpenWeatherMap

Request Headers:
Authorization: Bearer <token>
Content-Type: application/json

## Request Body:

Response Sukses:
{
"status": "success",
"data": {
"city": "Banda Aceh",
"temperature": 31,
"condition": "Sunny",
"humidity": 78,
"wind_speed": 12
}
}

Response Gagal:
{
"status": "error",
"message": "City not found or weather service unavailable"
}

Add Favorite Tourism Place

Method: POST

URL: /api/v1/favorites

Deskripsi: Endpoint ini digunakan untuk menyimpan tempat wisata ke daftar favorit pengguna yang sedang login.

Autentikasi Diperlukan: Ya

Sumber: Internal System

Request Headers:
Authorization: Bearer <token>
Content-Type: application/json

Request Body:
{
"tourism_place_id": "integer"
}

Response Sukses:
{
"status": "success",
"message": "Tourism place added to favorites",
"data": {
"id": 1,
"user_id": 1,
"tourism_place_id": 1
}
}

Response Gagal:
{
"status": "error",
"message": "Tourism place already added to favorites"
}

Get Favorite Tourism Places

Method: GET

URL: /api/v1/favorites

Deskripsi: Endpoint ini digunakan untuk mengambil daftar tempat wisata favorit milik pengguna yang sedang login.

Autentikasi Diperlukan: Ya

Sumber: Internal System

Request Headers:
Authorization: Bearer <token>
Content-Type: application/json

## Request Body:

Response Sukses:
{
"status": "success",
"data": [
{
"id": 1,
"tourism_place": {
"id": 1,
"name": "Pantai Lampuuk",
"city": "Banda Aceh",
"category": "Beach"
}
}
]
}

Response Gagal:
{
"status": "error",
"message": "Unauthenticated"
}

Delete Favorite Tourism Place

Method: DELETE

URL: /api/v1/favorites/{id}

Deskripsi: Endpoint ini digunakan untuk menghapus tempat wisata dari daftar favorit pengguna.

Autentikasi Diperlukan: Ya

Sumber: Internal System

Request Headers:
Authorization: Bearer <token>
Content-Type: application/json

## Request Body:

Response Sukses:
{
"status": "success",
"message": "Favorite tourism place removed successfully"
}

Response Gagal:
{
"status": "error",
"message": "Favorite data not found"
}

Save Search History

Method: POST

URL: /api/v1/search-history

Deskripsi: Endpoint ini digunakan untuk menyimpan riwayat pencarian kota atau tempat wisata yang dilakukan oleh pengguna.

Autentikasi Diperlukan: Ya

Sumber: Internal System

Request Headers:
Authorization: Bearer <token>
Content-Type: application/json

Request Body:
{
"keyword": "string",
"type": "city"
}

Response Sukses:
{
"status": "success",
"message": "Search history saved successfully",
"data": {
"id": 1,
"keyword": "Banda Aceh",
"type": "city"
}
}

Response Gagal:
{
"status": "error",
"message": "Failed to save search history"
}

Get Search History

Method: GET

URL: /api/v1/search-history

Deskripsi: Endpoint ini digunakan untuk mengambil daftar riwayat pencarian milik pengguna yang sedang login.

Autentikasi Diperlukan: Ya

Sumber: Internal System

Request Headers:
Authorization: Bearer <token>
Content-Type: application/json

## Request Body:

Response Sukses:
{
"status": "success",
"data": [
{
"id": 1,
"keyword": "Banda Aceh",
"type": "city",
"created_at": "2026-06-03 20:30:00"
}
]
}

Response Gagal:
{
"status": "error",
"message": "Unauthenticated"
}
