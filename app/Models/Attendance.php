<?php

namespace App\Models;

use CodeIgniter\Model;

class Attendance extends Model
{
    protected $table            = 'attendance';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id','check_in','check_out','status'];

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
    public const STATUS_ABSENT      = 0;
    public const STATUS_PRESENT     = 1;
    public const STATUS_HALF_DAY    = 2;
    public const STATUS_IN_PROGRESS = 3;

    public function checkIn( array $data){
        try{
            $data['status'] = self::STATUS_IN_PROGRESS;
            return $this->insert($data);

        }catch (\Throwable $e) {
            throw $e;
        }
    }
}
