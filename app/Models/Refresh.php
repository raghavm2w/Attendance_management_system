<?php

namespace App\Models;

use CodeIgniter\Model;

class Refresh extends Model
{
    protected $table            = 'refresh_tokens';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = [
        'user_id',
        'token',
        'expires_at',
        'created_at'
        
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
    protected $deletedField  = null;

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function addRefreshToken($id, $token)
    {
        try {
            return $this->db->table($this->table)->insert([
                'user_id'    => $id,
                'token'      => $token,
                'expires_at' => date('Y-m-d H:i:s', time() + (int) getenv('JWT_REFRESH_TTL')),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function validateRefreshToken($userId, $token)
    {
        return $this->where('user_id', $userId)
                    ->where('token', $token)
                    ->where('expires_at >', date('Y-m-d H:i:s'))
                    ->first();
    }

    public function deleteRefreshToken($token)
    {
        return $this->where('token', $token)->delete();
    }
}
