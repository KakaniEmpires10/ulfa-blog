<?php
namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseAdminController;
use App\Models\UserProfileModel;
use App\Models\WebSettingsModel;
use CodeIgniter\HTTP\ResponseInterface;

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
            'enable_comment'         => $this->settingsModel->getValue('enable_comment', $this->settingsModel->getValue('enable_comments', '0')),
            'disqus_shortname'       => $this->settingsModel->getValue('disqus_shortname'),
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
            'enable_comment',
            'disqus_shortname',
            'homepage_slider_limit',
            'homepage_slider_source',
            'theme'
        ];

        foreach ($fields as $field) {
            $value = $this->request->getPost($field);

            if ($field === 'enable_comment') {
                $value = $value === '1' ? '1' : '0';
            }

            if ($field === 'disqus_shortname') {
                $value = strtolower(trim((string) $value));
                $value = preg_replace('/[^a-z0-9-]/', '', $value) ?? '';
            }

            $this->settingsModel->setValue($field, $value);
        }

        return redirect()->route('settings')->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function imageProfile(): string
    {
        $data = $this->userProfileModel->select('cover_image_path, avatar_path')
            ->where('user_id', auth()->id())
            ->first();

        return $this->renderAdmin('pages/admin/settings/image_profile', [
            'title'            => 'Pengaturan Logo dan Gambar Profil',
            'pageTitle'        => 'Pengaturan Logo & Gambar Profil',
            'pageDescription'  => 'Halaman pengaturan sudah siap. Langkah selanjutnya bisa fokus ke tema, identitas situs, dan preferensi tampilan publik.',
            'breadcrumbs'      => [
                ['title' => 'Dashboard', 'url' => site_url('/admin')],
                ['title' => 'Pengaturan', 'url' => site_url('/admin/settings')],
                ['title' => 'Logo & Gambar Profil', 'url' => null],
            ],
            'data'             => $data
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

    public function updateProfileAvatar()
    {
        $file = $this->request->getFile('profile_image');

        if (!$file || !$file->isValid()) {
            return $this->respondWithRedirect('error', 'Pilih file gambar terlebih dahulu.');
        }

        $rules = [
            'profile_image' => [
                'rules' => 'uploaded[profile_image]|max_size[profile_image,4096]|is_image[profile_image]|mime_in[profile_image,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Pilih file gambar terlebih dahulu.',
                    'max_size' => 'Ukuran gambar maksimal 4 MB. Silakan kompres gambar terlebih dahulu.',
                    'is_image' => 'File yang dipilih bukan gambar. Harap unggah file gambar.',
                    'mime_in'  => 'Format gambar tidak didukung. Gunakan JPG, JPEG, atau PNG saja.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return $this->respondWithRedirect('error', implode(' ', $errors));
        }

        $newName    = 'avatar_' . auth()->id() . '_' . time() . '.' . $file->getExtension();
        $uploadPath = FCPATH . 'uploads/profiles/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if ($file->move($uploadPath, $newName)) {
            $user = $this->userProfileModel
                ->where('user_id', auth()->id())
                ->first();

            if ($user && $user['avatar_path'] && !preg_match('/^https?:\/\//', $user['avatar_path'])) {
                $oldFile = FCPATH . $user['avatar_path'];
                if (file_exists($oldFile) && is_file($oldFile)) {
                    unlink($oldFile);
                }
            }

            $this->userProfileModel
                ->where('user_id', auth()->id())
                ->set(['avatar_path' => 'uploads/profiles/' . $newName])
                ->update();

            return $this->respondWithRedirect('success', 'Gambar profil berhasil diperbarui.');
        }

        return $this->respondWithRedirect('error', 'Gagal mengupload gambar. Silakan coba lagi.');
    }

    public function updateProfileCover()
    {
        $file = $this->request->getFile('cover_image');

        if (!$file || !$file->isValid()) {
            return $this->respondWithRedirect('error', 'Pilih file gambar terlebih dahulu.');
        }

        $rules = [
            'cover_image' => [
                'rules' => 'uploaded[cover_image]|max_size[cover_image,4096]|is_image[cover_image]|mime_in[cover_image,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Pilih file gambar terlebih dahulu.',
                    'max_size' => 'Ukuran gambar maksimal 4 MB. Silakan kompres gambar terlebih dahulu.',
                    'is_image' => 'File yang dipilih bukan gambar. Harap unggah file gambar.',
                    'mime_in'  => 'Format gambar tidak didukung. Gunakan JPG, JPEG, atau PNG saja.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return $this->respondWithRedirect('error', implode(' ', $errors));
        }

        $newName    = 'cover_' . auth()->id() . '_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $file->getExtension();
        $uploadPath = FCPATH . 'uploads/covers/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if ($file->move($uploadPath, $newName)) {
            $user = $this->userProfileModel
                ->where('user_id', auth()->id())
                ->first();

            if ($user && $user['cover_image_path'] && !preg_match('/^https?:\/\//', $user['cover_image_path'])) {
                $oldFile = FCPATH . $user['cover_image_path'];
                if (file_exists($oldFile) && is_file($oldFile)) {
                    unlink($oldFile);
                }
            }

            $this->userProfileModel
                ->where('user_id', auth()->id())
                ->set(['cover_image_path' => 'uploads/covers/' . $newName])
                ->update();

            return $this->respondWithRedirect('success', 'Gambar cover berhasil diperbarui.');
        }

        return $this->respondWithRedirect('error', 'Gagal mengupload gambar. Silakan coba lagi.');
    }

    // public function updateLogo()
    // {
    //     $file = $this->request->getFile('');

    //     $fileRules = [
    //         'cover_image' => 'uploaded[cover_image]|max_size[cover_image,3072]|is_image[cover_image]|mime_in[cover_image,image/jpg,image/jpeg,image/png]'
    //     ];
    //     $fileMessages = [
    //         'cover_image' => [
    //             'uploaded' => 'Cover image wajib diunggah. Pilih gambar sampul untuk berita Anda.',
    //             'max_size' => 'Ukuran gambar maksimal 3 MB. Silakan kompres gambar terlebih dahulu.',
    //             'is_image' => 'File yang dipilih bukan gambar. Harap unggah file gambar.',
    //             'mime_in'  => 'Format gambar tidak didukung. Gunakan JPG, JPEG, atau PNG saja.'
    //         ]
    //     ];

    //     if (!$this->validate($fileRules, $fileMessages)) {
    //         return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    //     }
    // }

    /**
     * Helper: set flash session lalu kirim JSON untuk XHR
     * atau redirect biasa untuk form submission normal.
     */
    private function respondWithRedirect(string $type, string $message): ResponseInterface
    {
        $redirectUrl = site_url('admin/settings/image-profile');

        // Set flash session seperti biasa (untuk halaman yang di-load ulang)
        session()->setFlashdata($type, $message);

        // Jika request dari XHR (Alpine), kirim JSON
        if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status'      => $type,
                    'message'     => $message,
                    'redirect'    => $redirectUrl,
                ]);
        }

        return redirect()->to($redirectUrl);
    }
}
