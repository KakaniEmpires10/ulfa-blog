<?php

namespace App\Controllers\Admin;

class PageController extends BaseAdminController
{
    public function posts(): string
    {
        return $this->placeholder(
            'Postingan',
            'Halaman manajemen postingan sudah disiapkan. Berikutnya kita bisa fokus ke tabel data, filter status, dan form editor.'
        );
    }

    public function categories(): string
    {
        return $this->placeholder(
            'Kategori',
            'Halaman kategori sudah siap sebagai fondasi. Nanti kita isi dengan daftar kategori, form tambah, edit, dan validasi slug.'
        );
    }

    public function tags(): string
    {
        return $this->placeholder(
            'Tag',
            'Halaman tag sudah tersedia. Pada tahap berikutnya kita bisa tambahkan list tag, pencarian, serta aksi tambah dan ubah.'
        );
    }

    public function media(): string
    {
        return $this->placeholder(
            'Media',
            'Halaman media sudah disiapkan. Nanti kita kerjakan galeri aset, upload file, dan pemilihan gambar untuk postingan.'
        );
    }

    public function settings(): string
    {
        return $this->placeholder(
            'Pengaturan',
            'Halaman pengaturan sudah siap. Langkah selanjutnya bisa fokus ke tema, identitas situs, dan preferensi tampilan publik.'
        );
    }

    public function profile(): string
    {
        return $this->placeholder(
            'Profil',
            'Halaman profil penulis sudah disiapkan. Nanti kita isi dengan form identitas, avatar, bio, dan tautan sosial.'
        );
    }

    protected function placeholder(string $title, string $description): string
    {
        return $this->renderAdmin('pages/admin/placeholder', [
            'title'           => $title,
            'pageTitle'       => $title,
            'pageDescription' => $description,
        ]);
    }
}
