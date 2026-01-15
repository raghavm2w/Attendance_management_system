<?php

namespace App\Models;

use CodeIgniter\Model;

class Shift extends Model
{
    protected $table            = 'shifts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'type', 'start_time', 'end_time', 'grace_time', 'created_at', 'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function addShift(array $data): bool|int
    {
        try {
            return $this->insert($data);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function getShifts(array $filters): array
    {
        try {
            $builder = $this->builder();
            
            $builder->select('id, type, start_time, end_time, grace_time, created_at');
            
            // Search filter
            if (!empty($filters['search'])) {
                $builder->like('type', $filters['search']);
            }
            
            // Get total count before pagination
            $countBuilder = clone $builder;
            $total = $countBuilder->countAllResults(false);
            
            // Sorting
            $sortColumn = $filters['sort_by'];
            $allowedColumns = ['type', 'start_time', 'end_time', 'grace_time', 'created_at'];
            if (!in_array($sortColumn, $allowedColumns)) {
                $sortColumn = 'created_at';
            }
            $builder->orderBy($sortColumn, $filters['sort_order']);
            
            // Pagination
            $page = $filters['page'];
            $perPage = $filters['per_page'];
            $offset = ($page - 1) * $perPage;
            $builder->limit($perPage, $offset);
            
            $shifts = $builder->get()->getResultArray();
            
            $from = $total > 0 ? $offset + 1 : 0;
            $to = $total > 0 ? min($offset + $perPage, $total) : 0;
            
            return [
                'shifts' => $shifts,
                'total' => $total,
                'from' => $from,
                'to' => $to
            ];
            
        } catch (\Throwable $e) {
            throw $e;
        }
    }
    public function getShiftByUserId($userId)
    {
        try{
             $builder = $this->builder();
           return $builder->select('shifts.type,shifts.start_time,shifts.end_time,shifts.grace_time')
            ->join('user_shifts u', 'shifts.id = u.shift_id')
            ->where('u.user_id', $userId)
             ->get()->getResultArray();
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
