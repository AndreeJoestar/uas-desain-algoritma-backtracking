# Aplikasi Penjadwalan Karyawan Menggunakan Backtracking

## Deskripsi Aplikasi
Aplikasi ini merupakan aplikasi penjadwalan karyawan yang dibuat untuk memenuhi Project Ujian Akhir Semester (UAS) mata kuliah Analisis Desain Algoritma.  
Aplikasi bertujuan untuk menyusun jadwal kerja karyawan berdasarkan hari dan shift kerja dengan memperhatikan berbagai aturan (constraint) agar tidak terjadi konflik jadwal.

## Algoritma yang Digunakan
Algoritma yang digunakan dalam aplikasi ini adalah **Backtracking**.

Backtracking digunakan untuk:
- Mencoba semua kemungkinan penjadwalan karyawan
- Mengecek apakah penjadwalan melanggar aturan (constraint)
- Mengembalikan (backtrack) ke kondisi sebelumnya jika terjadi konflik
- Menemukan solusi jadwal yang valid

## Aturan (Constraint) Penjadwalan
Beberapa aturan yang diterapkan dalam penjadwalan:
1. Satu karyawan tidak boleh bekerja pada lebih dari satu shift di hari yang sama.
2. Setiap shift hanya dapat diisi oleh jumlah karyawan tertentu.
3. Jadwal yang melanggar aturan akan dibatalkan dan dicari solusi lain.

## Cara Menjalankan Aplikasi
1. Pastikan sudah menginstal XAMPP atau web server PHP lainnya.
2. Letakkan folder project di dalam direktori `htdocs`.
3. Jalankan Apache pada XAMPP.
4. Buka browser dan akses: http://localhost/jadwal_shift/index.php
5. Aplikasi akan menampilkan hasil penjadwalan karyawan.

## Struktur File
jadwal_shift/
│
├── index.php -> File utama berisi logika algoritma backtracking
└── README.md -> Dokumentasi aplikasi

## Keterangan
Seluruh logika algoritma backtracking, pengecekan constraint, dan proses penjadwalan diimplementasikan langsung di dalam file `index.php`.

---
