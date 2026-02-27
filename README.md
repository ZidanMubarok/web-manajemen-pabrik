# Framework CodeIgniter 4

## Apa itu CodeIgniter?

CodeIgniter adalah *framework* web *full-stack* PHP yang ringan, cepat, fleksibel, dan aman.  
Informasi lebih lanjut dapat ditemukan di [situs resmi CodeIgniter](https://codeigniter.com).

Repositori ini berisi versi distribusi dari *framework* tersebut. Versi ini dibangun langsung dari [repositori pengembangan (development)](https://github.com/codeigniter4/CodeIgniter4).

Informasi lebih lanjut mengenai rencana untuk versi 4 dapat ditemukan di bagian [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) pada forum resmi kami.

Anda juga dapat membaca [panduan pengguna (*user guide*)](https://codeigniter.com/user_guide/) yang sesuai dengan versi terbaru *framework* ini.

---

## Perubahan Penting pada `index.php`

File `index.php` sekarang **tidak lagi berada di *root* (direktori utama) proyek!** File ini telah dipindahkan ke dalam folder `public` demi keamanan yang lebih baik dan pemisahan komponen.

Hal ini berarti Anda harus mengonfigurasi *web server* Anda agar "mengarah" ke folder `public` dari proyek Anda, dan **bukan** ke *root* proyek. 
* **Praktik Terbaik:** Konfigurasikan *virtual host* untuk mengarah langsung ke folder `public`.
* **Praktik Buruk:** Mengarahkan *web server* ke *root* proyek dan mengakses aplikasi via *URL* `namaprojek/public/...`. Hal ini sangat tidak dianjurkan karena akan mengekspos sisa logika aplikasi dan *framework* Anda ke publik.

**Mohon** baca panduan pengguna untuk penjelasan yang lebih komprehensif tentang cara kerja CI4!

---

## Manajemen Repositori

Kami menggunakan GitHub Issues di repositori utama kami untuk melacak **BUGS (masalah/kutu)** dan paket pekerjaan **DEVELOPMENT (pengembangan)** yang disetujui.  
Sedangkan untuk memberikan **DUKUNGAN** dan mendiskusikan **PERMINTAAN FITUR**, kami menggunakan [forum komunitas](http://forum.codeigniter.com).

Repositori ini khusus untuk "distribusi", yang di-*build* oleh skrip persiapan rilis kami. Jika Anda menemukan masalah pada versi ini, Anda dapat menyampaikannya di forum kami, atau membuka *issue* di repositori utama.

---

## Berkontribusi

Kami sangat menyambut kontribusi dari komunitas!

Silakan baca bagian [*Contributing to CodeIgniter* (Berkontribusi pada CodeIgniter)](https://github.com/codeigniter4/CodeIgniter4/blob/develop/CONTRIBUTING.md) di repositori pengembangan sebelum Anda mulai berkontribusi.

---

## Persyaratan Server

Dibutuhkan **PHP versi 8.1** atau yang lebih tinggi, dengan beberapa ekstensi berikut yang harus terinstal:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - Masa dukungan (*End of Life*) untuk PHP 7.4 telah berakhir pada 28 November 2022.
> - Masa dukungan untuk PHP 8.0 telah berakhir pada 26 November 2023.
> - Jika Anda masih menggunakan PHP 7.4 atau 8.0, Anda harus **segera melakukan *upgrade***.
> - Masa dukungan untuk PHP 8.1 akan berakhir pada 31 Desember 2025.

Selain itu, pastikan ekstensi berikut juga telah diaktifkan pada konfigurasi PHP Anda:

- `json` (aktif secara *default* - jangan dimatikan)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) (wajib jika Anda berencana menggunakan *database* MySQL)
- [libcurl](http://php.net/manual/en/curl.requirements.php) (wajib jika Anda berencana menggunakan *library* `HTTP\CURLRequest`)
