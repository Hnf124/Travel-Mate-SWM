Rencana Fitur

Fitur 1  Login dan Register Pengguna

Role Penanggung Jawab: Backend Developer — M. Jimly

Sumber Data: Internal System

Deskripsi & Ekspektasi:
Fitur ini digunakan agar pengguna dapat membuat akun dan masuk ke aplikasi TravelMate. Pengguna akan mengisi data seperti nama, email, dan password pada halaman register, kemudian data tersebut dikirim ke backend Laravel untuk divalidasi dan disimpan ke database. Setelah akun berhasil dibuat, pengguna dapat login menggunakan email dan password. Ekspektasi dari fitur ini adalah sistem mampu menyediakan proses autentikasi yang aman sehingga pengguna yang sudah login dapat mengakses fitur seperti favorit dan riwayat pencarian.

Fitur 2 Pencarian Tempat Wisata Berdasarkan Kota

Role Penanggung Jawab: Frontend Developer — M. Taqwa Aulia

Sumber Data: Internal System

Deskripsi & Ekspektasi:
Fitur ini digunakan agar pengguna dapat mencari tempat wisata berdasarkan nama kota. Pengguna memasukkan nama kota pada kolom pencarian di aplikasi TravelMate Web App, kemudian frontend mengirimkan permintaan ke backend melalui REST API. Backend akan mengambil data tempat wisata yang sesuai dari database dan mengirimkan hasilnya kembali ke frontend. Ekspektasi dari fitur ini adalah pengguna dapat menemukan daftar tempat wisata secara cepat, jelas, dan sesuai dengan kota yang dicari.

Fitur 3 Detail Tempat Wisata

Role Penanggung Jawab: Backend Developer — M. Jimly

Sumber Data: Internal System

Deskripsi & Ekspektasi:
Fitur ini digunakan untuk menampilkan informasi lengkap mengenai tempat wisata yang dipilih oleh pengguna. Informasi yang ditampilkan meliputi nama tempat wisata, kota, kategori, alamat, deskripsi, dan gambar. Frontend akan mengirimkan ID tempat wisata ke backend, kemudian backend Laravel mengambil detail data dari database dan mengirimkan hasilnya kembali ke frontend. Ekspektasi dari fitur ini adalah pengguna dapat melihat informasi tempat wisata secara lengkap sebelum memutuskan untuk mengunjunginya.

Fitur 4 Informasi Cuaca Terkini

Role Penanggung Jawab: Backend Developer — M. Jimly

Sumber Data: Third-Party API — OpenWeatherMap

Deskripsi & Ekspektasi:
Fitur ini digunakan untuk menampilkan informasi cuaca terkini berdasarkan kota yang dicari oleh pengguna. Backend Laravel akan mengirimkan permintaan data ke OpenWeatherMap API, kemudian mengambil informasi seperti suhu, kondisi cuaca, kelembapan, dan kecepatan angin. Data tersebut kemudian dikirimkan ke frontend untuk ditampilkan kepada pengguna. Ekspektasi dari fitur ini adalah pengguna dapat mempertimbangkan kondisi cuaca sebelum memilih atau mengunjungi tempat wisata tertentu.

Fitur 5 Simpan Tempat Wisata Favorit

Role Penanggung Jawab: Backend Developer — M. Jimly

Sumber Data: Internal System

Deskripsi & Ekspektasi:
Fitur ini digunakan agar pengguna yang sudah login dapat menyimpan tempat wisata ke dalam daftar favorit. Ketika pengguna menekan tombol favorit pada tempat wisata tertentu, frontend akan mengirimkan data tempat wisata ke backend. Backend kemudian menyimpan data favorit berdasarkan akun pengguna yang sedang login. Ekspektasi dari fitur ini adalah pengguna dapat menyimpan tempat wisata yang disukai dan membukanya kembali tanpa perlu melakukan pencarian ulang.

Fitur 6 Riwayat Pencarian Pengguna

Role Penanggung Jawab: Backend Developer — M. Jimly

Sumber Data: Internal System

Deskripsi & Ekspektasi:
Fitur ini digunakan untuk mencatat riwayat pencarian yang pernah dilakukan oleh pengguna. Ketika pengguna mencari kota atau tempat wisata, backend akan menyimpan kata kunci pencarian tersebut ke database berdasarkan akun pengguna yang sedang login. Frontend kemudian dapat menampilkan daftar riwayat pencarian pada halaman khusus. Ekspektasi dari fitur ini adalah pengguna dapat melihat kembali pencarian sebelumnya dan mengakses hasil pencarian dengan lebih mudah.
