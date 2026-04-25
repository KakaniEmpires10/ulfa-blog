<?php
namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseAdminController;
use App\Models\UserProfileModel;
use App\Models\WebSettingsModel;

class SettingsController extends BaseAdminController
{
    protected $settingsModel, $userProfileModel;

    public function __construct()
    {
        $this->settingsModel = new WebSettingsModel();
        $this->userProfileModel = new UserProfileModel();
    }

    public function index(): string
    {
        return $this->renderAdmin('pages/admin/settings/index', [
            'title'            => 'Pengaturan',
            'pageTitle'        => 'Pengaturan',
            'pageDescription'  => 'Halaman pengaturan sudah siap. Langkah selanjutnya bisa fokus ke tema, identitas situs, dan preferensi tampilan publik.',
            'breadcrumbs'      => [
                ['title' => 'Dashboard', 'url' => site_url('/admin')],
                ['title' => 'Pengaturan', 'url' => null],
            ],
            'site_name'              => $this->settingsModel->getValue('site_name'),
            'contact_email'          => $this->settingsModel->getValue('contact_email'),
            'site_tagline'           => $this->settingsModel->getValue('site_tagline'),
            'site_description'       => $this->settingsModel->getValue('site_description'),
            'primary_color'          => $this->settingsModel->getValue('primary_color'),
            'secondary_color'        => $this->settingsModel->getValue('secondary_color'),
            'border_radius'          => $this->settingsModel->getValue('border_radius'),
            'enable_comments'        => $this->settingsModel->getValue('enable_comments'),
            'homepage_slider_limit'  => $this->settingsModel->getValue('homepage_slider_limit'),
            'homepage_slider_source' => $this->settingsModel->getValue('homepage_slider_source'),
            'theme'                  => $this->settingsModel->getValue('theme')
        ]);
    }

    public function updateGeneral()
    {
        $fields = [
            'site_name',
            'contact_email',
            'site_tagline',
            'site_description',
            'primary_color',
            'secondary_color',
            'border_radius',
            'enable_comments',
            'homepage_slider_limit',
            'homepage_slider_source',
            'theme'
        ];

        foreach ($fields as $field) {
            $value = $this->request->getPost($field);

            // Handle checkbox for enable_comments
            if ($field === 'enable_comments') {
                $value = $value === 'on' ? '1' : '0';
            }

            $this->settingsModel->setValue($field, $value);
        }

        return redirect()->route('settings')->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function imageProfile(): string
    {
        return $this->renderAdmin('pages/admin/settings/image_profile', [
            'title'            => 'Pengaturan Logo dan Gambar Profil',
            'pageTitle'        => 'Pengaturan Logo & Gambar Profil',
            'pageDescription'  => 'Halaman pengaturan sudah siap. Langkah selanjutnya bisa fokus ke tema, identitas situs, dan preferensi tampilan publik.',
            'breadcrumbs'      => [
                ['title' => 'Dashboard', 'url' => site_url('/admin')],
                ['title' => 'Pengaturan', 'url' => site_url('/admin/settings')],
                ['title' => 'Logo & Gambar Profil', 'url' => null],
            ]
        ]);
    }

    public function profile(): string
    {
        $profile = $this->userProfileModel->select('id, user_id, display_name, bio, about_heading, about_content, quote_text, social_links')
            ->where('user_id', auth()->id())
            ->first();

        return $this->renderAdmin('pages/admin/settings/profile', [
            'title'            => 'Pengaturan Profil',
            'pageTitle'        => 'Pengaturan Profil',
            'pageDescription'  => 'Halaman pengaturan sudah siap. Langkah selanjutnya bisa fokus ke tema, identitas situs, dan preferensi tampilan publik.',
            'breadcrumbs'      => [
                ['title' => 'Dashboard', 'url' => site_url('/admin')],
                ['title' => 'Pengaturan', 'url' => site_url('/admin/settings')],
                ['title' => 'Profil', 'url' => null],
            ],
            'profile'          => $profile
        ]);
    }

    public function updateProfile()
    {
        $data = [
            'display_name'  => $this->request->getPost('display_name'),
            'bio'           => $this->request->getPost('bio'),
            'about_heading' => $this->request->getPost('about_heading'),
            'about_content' => $this->request->getPost('about_content'),
            'quote_text'    => $this->request->getPost('quote_text'),
            'social_links'  => json_encode([
                    'twitter'   => $this->request->getPost('social')['twitter'] ?? '',
                    'facebook'  => $this->request->getPost('social')['facebook'] ?? '',
                    'linkedin'  => $this->request->getPost('social')['linkedin'] ?? '',
                    'instagram' => $this->request->getPost('social')['instagram'] ?? '',
            ])
        ];

        $existing = $this->userProfileModel->where('user_id', auth()->id())->first();

        if (!$existing) {
            return redirect()->route('settings_profile')->with('error', 'Profil tidak ditemukan.');
        } else {
            $this->userProfileModel->update($existing['id'], $data);
        }

        return redirect()->route('settings_profile')->with('success', 'Profil berhasil diperbarui.');
    }
}