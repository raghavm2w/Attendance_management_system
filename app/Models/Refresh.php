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
    protected $protectFields    = true;
    protected $allowedFields    = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
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
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function addRefreshToken($id,$token)
    {
        try{
            return $this->insert( [
                 'user_id'    => $id,
                 'token'      => $token,
                 'expires_at' => date('Y-m-d H:i:s',time() + (int) getenv('JWT_REFRESH_TTL')),
                 'created_at' => date('Y-m-d H:i:s')
            ]);


        }catch(\Throwable $e){
            throw $e;
        }
    }
}
