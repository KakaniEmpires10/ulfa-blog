<?php
namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseAdminController;
use App\Models\UserProfileModel;
use App\Models\WebSettingsModel;
use CodeIgniter\Shield\Authentication\Passwords;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class SettingsController extends BaseAdminController
{
    protected $settingsModel, $userProfileModel, $userModel;

    public function __construct()
    {
        $this->settingsModel = new WebSettingsModel();
        $this->userProfileModel = new UserProfileModel();
        $this->userModel = new UserModel();
    }

    public function index(): string
    {
        return $this->renderAdmin('pages/admin/settings/index', [
            'title'            => 'Pengaturan',
            'pageTitle'        => 'Pengaturan',
            'pageDescription'  => 'Halaman pengaturan sudah siap. Langkah selanjutnya bisa fokus ke tema, identitas situs, dan preferensi tampilan publik.',
            'breadcrumbs'      => [
                ['title' => 'Dashboard', 'url' => url_to('dashboard')],
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
                ['title' => 'Dashboard', 'url' => url_to('dashboard')],
                ['title' => 'Pengaturan', 'url' => url_to('settings')],
                ['title' => 'Logo & Gambar Profil', 'url' => null],
            ],
            'data'             => $data
        ]);
    }

    public function profile(): string
    {
        $user = auth()->user();
        $profile = $this->userProfileModel->select('id, user_id, display_name, bio, about_heading, about_content, quote_text, social_links')
            ->where('user_id', auth()->id())
            ->first();

        return $this->renderAdmin('pages/admin/settings/profile', [
            'title'            => 'Pengaturan Profil',
            'pageTitle'        => 'Pengaturan Profil',
            'pageDescription'  => 'Halaman pengaturan sudah siap. Langkah selanjutnya bisa fokus ke tema, identitas situs, dan preferensi tampilan publik.',
            'breadcrumbs'      => [
                ['title' => 'Dashboard', 'url' => url_to('dashboard')],
                ['title' => 'Pengaturan', 'url' => url_to('settings')],
                ['title' => 'Profil', 'url' => null],
            ],
            'profile'          => $profile,
            'accountUser'      => $user,
        ]);
    }

    public function updateProfile()
    {
        $userId = (int) auth()->id();
        $user = $this->userModel->findById($userId);

        if ($user === null) {
            return redirect()->route('settings_profile')->with('error', 'Akun tidak ditemukan.');
        }

        $username = trim((string) $this->request->getPost('username'));
        $currentUsername = (string) ($user->username ?? '');
        $newPassword = (string) $this->request->getPost('new_password');
        $newPasswordConfirmation = (string) $this->request->getPost('new_password_confirmation');
        $currentPassword = (string) $this->request->getPost('current_password');
        $isUsernameChanged = $username !== $currentUsername;
        $isPasswordChangeRequested = $newPassword !== '' || $newPasswordConfirmation !== '';
        $isAccountChangeRequested = $isUsernameChanged || $isPasswordChangeRequested;

        if ($isAccountChangeRequested) {
            if ($currentPassword === '') {
                return redirect()->route('settings_profile')->withInput()->with('warning', 'Masukkan password saat ini untuk mengubah username atau password.');
            }

            $passwordHash = $user->getPasswordHash();
            if ($passwordHash === null || ! service('passwords')->verify($currentPassword, $passwordHash)) {
                return redirect()->route('settings_profile')->withInput()->with('warning', 'Password saat ini tidak sesuai.');
            }
        }

        $accountErrors = $this->validateAccountChanges(
            $user,
            $userId,
            $username,
            $newPassword,
            $newPasswordConfirmation,
            $isAccountChangeRequested,
            $isPasswordChangeRequested
        );

        if ($accountErrors !== []) {
            return redirect()->route('settings_profile')->withInput()->with('warning', reset($accountErrors));
        }

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
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->userProfileModel->update($existing['id'], $data);

            if ($isAccountChangeRequested) {
                $user->username = $username;

                if ($isPasswordChangeRequested) {
                    $user->password = $newPassword;
                }

                $this->userModel->update($userId, $user);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }
        } catch (\Throwable $exception) {
            $db->transRollback();
            return redirect()->route('settings_profile')->withInput()->with('error', 'Gagal memperbarui profil. ' . $exception->getMessage());
        }

        $message = $isAccountChangeRequested
            ? 'Profil dan akun berhasil diperbarui.'
            : 'Profil berhasil diperbarui.';

        return redirect()->route('settings_profile')->with('success', $message);
    }

    private function validateAccountChanges($user, int $userId, string $username, string $newPassword, string $newPasswordConfirmation, bool $isAccountChangeRequested, bool $isPasswordChangeRequested): array
    {
        if (!$isAccountChangeRequested) {
            return [];
        }

        $rules = [
            'username' => [
                'rules' => "required|min_length[3]|max_length[30]|regex_match[/\A[a-zA-Z0-9\.]+\z/]|is_unique[users.username,id,{$userId}]",
                'errors' => [
                    'required'    => 'Username wajib diisi.',
                    'min_length'  => 'Username minimal 3 karakter.',
                    'max_length'  => 'Username maksimal 30 karakter.',
                    'regex_match' => 'Username hanya boleh berisi huruf, angka, dan titik.',
                    'is_unique'   => 'Username sudah digunakan.',
                ],
            ],
        ];

        if ($isPasswordChangeRequested) {
            $rules['new_password'] = [
                'rules' => 'required|' . Passwords::getMaxLengthRule() . '|matches[new_password_confirmation]',
                'errors' => [
                    'required'   => 'Password baru wajib diisi.',
                    'max_byte'   => 'Password baru terlalu panjang.',
                    'max_length' => 'Password baru terlalu panjang.',
                    'matches'    => 'Konfirmasi password baru tidak cocok.',
                ],
            ];
            $rules['new_password_confirmation'] = [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Konfirmasi password baru wajib diisi.',
                ],
            ];
        }

        if (!$this->validate($rules)) {
            return $this->validator->getErrors();
        }

        if ($isPasswordChangeRequested) {
            $passwordUser = clone $user;
            $passwordUser->username = $username;
            $passwordCheck = service('passwords')->check($newPassword, $passwordUser);

            if (!$passwordCheck->isOK()) {
                return ['new_password' => $passwordCheck->reason() ?? 'Password baru tidak memenuhi syarat.'];
            }
        }

        return [];
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
        $redirectUrl = url_to('settings_image');

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
