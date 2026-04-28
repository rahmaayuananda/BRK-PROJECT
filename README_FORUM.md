BRK Project - Simple Forum (CI3)

Cara menjalankan (local):

- Pastikan webserver (Laragon) mengarahkan `d:/laragon/www/brk-project` sebagai root.
- Buka browser ke `http://localhost/brk-project/` atau sesuai konfigurasi Laragon.
- Folder penyimpanan flatfile: `forums_data/` akan dibuat otomatis di root project setelah akses pertama.

Fitur awal:
- Landing page: daftar topik dan pembuatan topik bebas tanpa login.
- Setiap topik berisi chat room: pesan disimpan di file JSON per topik.
- Pesan dibatasi 50 karakter (dipotong otomatis).
- Realtime memakai polling JS (1.5s untuk chat, 3s untuk list topik).

Next improvements:
- Ganti polling dengan WebSocket (Ratchet/PHP-PM) untuk realtime nyata.
- Tambah sanitasi dan rate limiting.
- Tambah pengelolaan file dan backup untuk scalability.

WebSocket Realtime (opsional)

- Persyaratan: Node.js (v14+).
- Untuk menjalankan server WebSocket lokal (development):

	```bash
	cd d:/laragon/www/brk-project
	npm install
	npm start
	```

- Server akan berjalan di `ws://localhost:8080` dan aplikasi PHP akan mengirim notifikasi ke `http://127.0.0.1:8080/notify` setiap kali topik atau pesan dibuat. Klien di browser akan menerima event realtime.

Catatan: server WebSocket bersifat optional; jika tidak dijalankan, aplikasi akan tetap bekerja menggunakan polling sebagai fallback.
