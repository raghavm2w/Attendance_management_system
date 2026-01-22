<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveBalance extends Model
{
    protected $table            = 'leave_balances';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id','leave_type_id','year','total_allocated','used','remaining'];

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

    public function getUserBalance($user_id,$leave_type_id,$year){
        try{
            return $this->where('user_id',$user_id)
            ->where('leave_type_id',$leave_type_id)
            ->where('year',$year)->first();

        }catch (\Throwable $e) {
            throw $e;
        }
    }
    public function addUserBalance($user_id,$leave_id,$year,$total_allocated,$used,$remaining){
        try{
            return $this->insert([
                'user_id'=>$user_id,
                'leave_type_id'=>$leave_id,
                'year'=>$year,
                'total_allocated'=>$total_allocated,
                'used'=>$used,
                'remaining'=>$remaining
            ]);

        }catch (\Throwable $e) {
            throw $e;
        }
    }
}
