<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table            = 'blog_tags';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateSlug'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['generateSlug'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function generateSlug(array $data)
    {
        // Pastikan ada data 'name' dan 'slug' belum diisi (atau biarkan otomatis)
        if (isset($data['data']['name'])) {
            helper('url');

            // Buat slug dasar
            $baseSlug = url_title($data['data']['name'], '-', true);
            $slug = $baseSlug;
            $i = 1;

            while (true) {
                $builder = $this->builder(); // Gunakan builder agar lebih clean
                $builder->where('slug', $slug);

                // Jika ini proses UPDATE, abaikan ID yang sedang diedit agar tidak konflik dengan dirinya sendiri
                // Di CI4, ID update biasanya ada di $data['id'] (bisa berupa array atau single value)
                if (isset($data['id'])) {
                    $id = is_array($data['id']) ? $data['id'][0] : $data['id'];
                    $builder->where('id !=', $id);
                }

                if ($builder->countAllResults() === 0) {
                    break;
                }

                // Jika slug sudah ada, tambahkan suffix -1, -2, dst.
                $slug = $baseSlug . '-' . $i;
                $i++;
            }

            $data['data']['slug'] = $slug;
        }

        // SANGAT PENTING: Kembalikan data agar proses insert/update berlanjut
        return $data;
    }
}
