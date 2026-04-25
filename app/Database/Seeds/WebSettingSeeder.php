<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WebSettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            'site_name'              => 'Ulfa Blog',
            'site_tagline'           => 'Catatan perjalanan, bacaan, dan hidup yang ditulis dengan tenang.',
            'site_description'       => 'Blog pribadi tentang perjalanan, buku, film, dan hal-hal kecil yang layak diingat.',
            'site_logo'              => 'ULFA',
            'contact_email'          => 'hello@ulfablog.test',
            'primary_color'          => '#ce8460',
            'secondary_color'        => '#1c1d1f',
            'theme_mode'             => 'light',
            'border_radius'          => '24',
            'homepage_slider_source' => 'popular',
            'homepage_slider_limit'  => '3',
            'enable_comment'         => '0',
        ];

        foreach ($settings as $key => $value) {
            $existing = $this->db->table('web_settings')->where('key', $key)->get()->getRowArray();

            if ($existing) {
                $this->db->table('web_settings')
                    ->where('key', $key)
                    ->update(['value' => $value]);
            } else {
                $this->db->table('web_settings')->insert([
                    'key'   => $key,
                    'value' => $value,
                ]);
            }
        }
    }
}
