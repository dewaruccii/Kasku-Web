# Kasku — Finance & Contract Management for Lawyers

Kasku adalah aplikasi untuk **mengelola arus kas** dan **mengaitkannya langsung dengan data kontrak**, dirancang khusus untuk kebutuhan pengacara dan firma hukum. Proyek ini dikembangkan oleh **Bimo Mahendra Wikara (Fullstack Developer – Telkomsel)** dengan dukungan **AI Granite** untuk mempercepat _refactor code_, _bug fixing_ logika, dan mendapatkan rekomendasi UI.

> **TL;DR**
>
> -   🎯 **Tujuan**: Mempermudah pengacara mengelola pemasukan/pengeluaran serta pemasukan berbasis kontrak.
> -   🔐 **Fokus**: Keamanan, transparansi, dan efisiensi alur kerja.
> -   🤖 **Teknologi AI**: AI Granite untuk peningkatan kualitas kode & desain.

---

## 🚀 General Overview

-   **Problem**: Pencatatan manual rawan salah, sulit ditelusuri, dan tidak terhubung dengan kontrak.
-   **Solution**: Dashboard keuangan yang mengaitkan setiap transaksi dengan **kontrak** terkait.
-   **Impact**: Efisiensi administrasi, pelacakan transparan, dan laporan yang lebih akurat.

## ✨ Key Features

-   Pencatatan **cash in/out** (arus kas)
-   **Link transaksi ↔ kontrak** (relasi langsung)
-   **Dashboard & laporan** (ringkasan, filter, ekspor)
-   **Kontrol akses berbasis peran** (Admin, Finance, Lawyer)
-   **Audit trail** (riwayat perubahan)
-   🔒 **Keamanan data** (role-based permission, backup)

> _Catatan_: Beberapa fitur bisa disesuaikan dengan kebutuhan organisasi Anda.

---

## 🧱 Tech Stack

> Sesuaikan bagian ini dengan stack yang Anda gunakan.

-   **Backend**: Laravel
-   **Frontend**: Blade
-   **Database**: MySQL
-   **Auth**: Session
-   **Infra/Deploy**: VPS , CI/CD GitHub Actions

---

## 👥 Akses & Peran (Aplikasi)

-   **Admin**: mengelola pengguna, peran, kontrak, dan seluruh transaksi.
-   **Finance**: input/validasi transaksi, generate laporan.
-   **Lawyer**: melihat kontrak terkait, meninjau arus kas terkait kontrak.
-   **Audit**: melihat audit trail & laporan (read-only).

> **Akses Login**:

-   Role: **Admin**
-   Email: **admin@gmail.com**
-   Password: **12345678**
-   Website: https://21-rpl.my.id

## 🙌 Kredit

Dikembangkan oleh **Bimo Mahendra Wikara** menggunakan bantuan **AI Granite** sebagai _development accelerator_.
