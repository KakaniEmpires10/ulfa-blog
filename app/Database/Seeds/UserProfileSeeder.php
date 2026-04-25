<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserProfileSeeder extends Seeder
{
    public function run()
    {
        $user = $this->db->table('users')->where('username', 'ulfa')->get()->getRowArray();

        if (! $user) {
            return;
        }

        $payload = [
            'user_id'           => $user['id'],
            'display_name'      => 'Ulfa',
            'bio'               => 'Saya menulis tentang perjalanan, buku, film, dan potongan hidup yang terasa penting untuk disimpan.',
            'avatar_path'       => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=800&q=80',
            'cover_image_path'  => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1600&q=80',
            'about_heading'     => 'Halo, saya Ulfa',
            'about_content'     => 'Ulfa Blog adalah ruang personal yang merangkum perjalanan, bacaan, film, dan refleksi sederhana. Saya menyukai tulisan yang hangat, visual yang tenang, dan cerita yang terasa dekat dengan kehidupan sehari-hari.',
            'quote_text'        => 'Saya percaya tulisan yang sederhana bisa menjadi tempat pulang untuk kenangan, pelajaran, dan rasa syukur.',
            'social_links'      => json_encode([
                'instagram' => 'https://instagram.com/ulfa',
                'linkedin'  => 'https://linkedin.com/in/ulfa',
                'website'   => 'https://ulfablog.test',
            ], JSON_UNESCAPED_SLASHES),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        $existing = $this->db->table('user_profiles')->where('user_id', $user['id'])->get()->getRowArray();

        if ($existing) {
            $this->db->table('user_profiles')->where('user_id', $user['id'])->update($payload);
            return;
        }

        $payload['created_at'] = date('Y-m-d H:i:s');
        $this->db->table('user_profiles')->insert($payload);
    }
}
