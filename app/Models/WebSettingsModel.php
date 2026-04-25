<?php

namespace App\Models;

use CodeIgniter\Model;

class WebSettingsModel extends Model
{
    protected $table = 'web_settings';
    protected $primaryKey = 'key';
    protected $allowedFields = ['key', 'value'];

    public $useAutoIncrement = false;

    protected $returnType = 'array';

    public function getValue(string $key, $default = null)
    {
        $setting = $this->find($key);

        return $setting['value'] ?? $default;
    }

    public function setValue(string $key, ?string $value): bool
    {
        $existing = $this->find($key);

        if ($existing === null) {
            return $this->insert(['key' => $key, 'value' => $value], false) !== false;
        }

        return $this->update($key, ['value' => $value]);
    }
}
