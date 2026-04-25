<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProfileModel extends Model
{
    protected $table            = 'user_profiles';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'user_id',
        'display_name',
        'bio',
        'avatar_path',
        'cover_image_path',
        'about_heading',
        'about_content',
        'quote_text',
        'social_links',
    ];

    public function getPrimaryProfile(): ?array
    {
        $profile = $this->select('user_profiles.*, users.username')
            ->join('users', 'users.id = user_profiles.user_id')
            ->orderBy('user_profiles.id', 'ASC')
            ->first();

        if ($profile === null) {
            return null;
        }

        $profile['social_links'] = decode_social_links($profile['social_links'] ?? null);

        return $profile;
    }

    public function getProfileByUserId(int $userId): ?array
    {
        $profile = $this->select('user_profiles.*, users.username')
            ->join('users', 'users.id = user_profiles.user_id')
            ->where('user_profiles.user_id', $userId)
            ->first();

        if ($profile === null) {
            return null;
        }

        $profile['social_links'] = decode_social_links($profile['social_links'] ?? null);

        return $profile;
    }
}
