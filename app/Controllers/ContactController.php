<?php

namespace App\Controllers;

use App\Models\UserProfileModel;
use CodeIgniter\HTTP\RedirectResponse;

class ContactController extends BaseController
{
    public function index(): string
    {
        $profileModel = new UserProfileModel();

        return view('pages/contact', [
            'title'          => 'Kontak',
            'seoTitle'       => 'Kontak | ' . get_setting('site_name', 'Ulfa Blog'),
            'seoDescription' => 'Hubungi penulis melalui halaman kontak Ulfa Blog.',
            'profile'        => $profileModel->getPrimaryProfile() ?? [],
        ]);
    }

    public function send(): RedirectResponse
    {
        $rules = [
            'name'    => 'required|min_length[2]|max_length[100]',
            'email'   => 'required|valid_email|max_length[254]',
            'message' => 'required|min_length[10]|max_length[2000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name    = trim((string) $this->request->getPost('name'));
        $email   = trim((string) $this->request->getPost('email'));
        $message = trim((string) $this->request->getPost('message'));

        $mailer = service('email');
        $mailer->setTo(get_setting('contact_email', config('Email')->fromEmail));
        $mailer->setReplyTo($email, $name);
        $mailer->setSubject('Pesan baru dari formulir kontak: ' . $name);
        $mailer->setMessage("Nama: {$name}\nEmail: {$email}\n\nPesan:\n{$message}");

        if (! $mailer->send()) {
            return redirect()->back()->withInput()->with('error', 'Pesan belum bisa dikirim sekarang. Silakan coba lagi setelah konfigurasi email siap.');
        }

        return redirect()->to('/contact')->with('message', 'Terima kasih. Pesan kamu sudah berhasil dikirim.');
    }
}
