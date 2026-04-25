<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Entities\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = auth()->getProvider();
        $accounts = [
            [
                'username' => 'admin',
                'email'    => 'admin@blog.com',
                'password' => 'admin1234',
                'group'    => 'superadmin',
            ],
            [
                'username' => 'ulfa',
                'email'    => 'ulfa@blog.com',
                'password' => 'ulfa1234',
                'group'    => 'admin',
            ],
        ];

        foreach ($accounts as $account) {
            $user = $users->where('username', $account['username'])->first();

            if (! $user) {
                $user = new User([
                    'username' => $account['username'],
                    'email'    => $account['email'],
                    'password' => $account['password'],
                    'active'   => 1,
                ]);

                $users->save($user);
                $user = $users->findById($users->getInsertID());
            } else {
                $this->db->table('users')
                    ->where('id', $user->id)
                    ->update([
                        'username' => $account['username'],
                        'active'   => 1,
                    ]);
            }

            if ($user !== null && ! $user->inGroup($account['group'])) {
                $user->addGroup($account['group']);
            }

            if ($user !== null) {
                $identity = $this->db->table('auth_identities')
                    ->where('user_id', $user->id)
                    ->where('type', Session::ID_TYPE_EMAIL_PASSWORD)
                    ->get()
                    ->getRowArray();

                $payload = [
                    'user_id'    => $user->id,
                    'type'       => Session::ID_TYPE_EMAIL_PASSWORD,
                    'secret'     => $account['email'],
                    'secret2'    => password_hash($account['password'], PASSWORD_DEFAULT),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if ($identity) {
                    $this->db->table('auth_identities')
                        ->where('id', $identity['id'])
                        ->update($payload);
                } else {
                    $payload['created_at'] = date('Y-m-d H:i:s');
                    $this->db->table('auth_identities')->insert($payload);
                }
            }
        }
    }
}
