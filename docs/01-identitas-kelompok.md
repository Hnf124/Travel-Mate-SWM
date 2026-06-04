Identitas Kelompok

Nama Kelompok: Kelompok 3
Nama Proyek / Aplikasi: TravelMate
Jumlah Anggota: 3
Repositori: https://github.com/Hnf124/Travel-Mate-SWM
Anggota & Role

Anggota 1
•	Nama Lengkap	: Hanif Hidayat
•	NIM			: 230705005
•	Role			: DevOps Engineer
•	Teknologi		: GitHub, Railway/VPS, Docker opsional, environment configuration, deployment
Anggota 2
•	Nama Lengkap	: M. Taqwa Aulia
•	NIM			: 230705013
•	Role			: Frontend Developer
•	Teknologi		: React.js, Tailwind CSS, Axios
Anggota 3
•	Nama Lengkap	: M. Jimly
•	NIM			: 230705021
•	Role			: Backend Developer
•	Teknologi		: Laravel, MySQL, Laravel Sanctum, REST API, OpenWeatherMap API
Stack Teknologi
Frontend			: React.js
Backend			: Laravel
Database			: MySQL
DevOps / Infrastruktur	: GitHub, Railway/VPS, Docker opsional

Arsitektur Aplikasi
	Proyek TravelMate dibangun dengan arsitektur aplikasi berbasis layanan yang terdiri dari aplikasi frontend dan backend. Aplikasi frontend digunakan sebagai antarmuka pengguna untuk mencari rekomendasi tempat wisata, melihat detail wisata, melihat informasi cuaca, menyimpan tempat favorit, dan melihat riwayat pencarian. Aplikasi backend berbasis Laravel berfungsi sebagai penyedia layanan API, pengelola data pengguna, data tempat wisata, favorit, riwayat pencarian, serta integrasi dengan layanan pihak ketiga seperti OpenWeatherMap API. Frontend berkomunikasi dengan backend melalui REST API, sedangkan backend berkomunikasi dengan database dan third-party API.
Aplikasi 1 Frontend
•	Nama Aplikasi: TravelMate Web App
•	Deskripsi Singkat: Aplikasi web yang digunakan oleh pengguna untuk mencari rekomendasi tempat wisata, melihat detail tempat wisata, melihat informasi cuaca, menyimpan tempat favorit, dan mengakses riwayat pencarian.
•	Berkomunikasi dengan: TravelMate API Service melalui REST API
Aplikasi 2 Backend (Laravel)
•	Nama Aplikasi / Service: TravelMate API Service
•	Deskripsi Singkat: Layanan backend berbasis Laravel yang mengelola autentikasi pengguna, data tempat wisata, data favorit, riwayat pencarian, serta integrasi dengan OpenWeatherMap API untuk mengambil informasi cuaca terkini.
•	Menyediakan layanan untuk: TravelMate Web App
