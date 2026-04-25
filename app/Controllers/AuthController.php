<?php

namespace App\Controllers;

use CodeIgniter\Shield\Authentication\Authentication;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    public function register()
    {
        return view('auth/register');
    }

    public function attemptRegister()
    {
        $users = new UserModel();
        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        if ($users->insert($data)) {
            return redirect()->to('/auth/login')->with('success', 'Registration Successful. Please login.');
        }

        return redirect()->back()->with('error', 'Registration failed.');
    }

    public function login()
    {
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $auth = service('authentication');

        $credentials = [
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        if ($auth->attempt($credentials)) {
            return redirect()->to('/dashboard');
        }

        return redirect()->back()->with('error', 'Invalid login credentials.');
    }

    public function logout()
    {
        service('authentication')->logout();
        return redirect()->to('/auth/login')->with('success', 'Logged out successfully.');
    }
}