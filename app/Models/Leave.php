<?php

namespace App\Models;

use CodeIgniter\Model;

class Leave extends Model
{
    protected $table            = 'leaves';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id','leave_type_id','type','start_date','end_date','reason','status'];

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

    public function addLeaveRequest(array $data){
        try{
            return $this->insert($data);
        }catch(\Throwable $e){
            throw $e;
        }
    }
    public function checkDuplicate(array $data){
        try{
            $userId = $data['user_id'];
            $startDate = $data['start_date'];
            $endDate = $data['end_date'];

            // half day leave ->end date is null
            if (empty($endDate)) {
                $sql = "SELECT id FROM leaves 
                        WHERE user_id = ? 
                        AND status IN ('pending','approved') 
                        AND (
                            start_date = ? 
                            OR (end_date IS NOT NULL AND ? BETWEEN start_date AND end_date)
                        )
                        LIMIT 1";
                return $this->db->query($sql, [$userId, $startDate, $startDate])->getResultArray();
            }

            // Full-day leave ->end date is not null
            $sql = "SELECT id FROM leaves 
                    WHERE user_id = ? 
                    AND status IN ('pending','approved') 
                    AND (
                        (end_date IS NULL AND start_date BETWEEN ? AND ?) 
                        OR (end_date IS NOT NULL AND start_date <= ? AND end_date >= ?)
                    ) 
                    LIMIT 1";
            return $this->db->query($sql, [$userId, $startDate, $endDate, $endDate, $startDate])->getResultArray();

        }catch(\Throwable $e){
            throw $e;
        }
    }

    public function fetchUserLeaves(int $userId, array $filters): array
    {
        try {
            $builder = $this->builder();
            
            $builder->select('leaves.id, leaves.type, leaves.start_date, leaves.end_date, leaves.reason, leaves.status, leaves.created_at, lt.name as leave_type_name');
            $builder->join('leave_types lt', 'lt.id = leaves.leave_type_id', 'left');
            
            // Filter by user
            $builder->where('leaves.user_id', $userId);
            
            // Status filter
            if (!empty($filters['status'])) {
                $builder->where('leaves.status', $filters['status']);
            }
            
            // Search filter (by reason or leave type name or type)
            if (!empty($filters['search'])) {
                $builder->groupStart();
                $builder->like('leaves.reason', $filters['search']);
                $builder->orLike('leaves.type', $filters['search']);
                $builder->orLike('lt.name', $filters['search']);
                $builder->groupEnd();
            }
            
            $countBuilder = clone $builder;
            $total = $countBuilder->countAllResults(false);
            
            // Sorting
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $builder->orderBy('leaves.created_at', $sortOrder);
            
            // Pagination
            $page = $filters['page'] ?? 1;
            $perPage = $filters['per_page'] ?? 10;
            $offset = ($page - 1) * $perPage;
            $builder->limit($perPage, $offset);
            
            $leaves = $builder->get()->getResultArray();
            
            $from = $total > 0 ? $offset + 1 : 0;
            $to = $total > 0 ? min($offset + $perPage, $total) : 0;
            
            return [
                'leaves' => $leaves,
                'total' => $total,
                'from' => $from,
                'to' => $to,
                'current_page' => (int)$page,
                'per_page' => (int)$perPage
            ];
            
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function fetchAllLeaves(array $filters): array
    {
        try {
            $builder = $this->builder();
            
            $builder->select('leaves.id, leaves.type, leaves.start_date, leaves.end_date, leaves.reason, leaves.status, leaves.created_at, lt.name as leave_type_name, u.name as user_name, u.email as user_email');
            $builder->join('leave_types lt', 'lt.id = leaves.leave_type_id', 'left');
            $builder->join('users u', 'u.id = leaves.user_id', 'left');
            
            // Status filter
            if (!empty($filters['status'])) {
                $builder->where('leaves.status', $filters['status']);
            }
            
            // Applied on date range filter
            if (!empty($filters['applied_from'])) {
                $builder->where('DATE(leaves.created_at) >=', $filters['applied_from']);
            }
            if (!empty($filters['applied_to'])) {
                $builder->where('DATE(leaves.created_at) <=', $filters['applied_to']);
            }
            
            // Leave date range filter (Overlap check)
            if (!empty($filters['leave_from'])) {
                $builder->groupStart();
                $builder->where('leaves.end_date >=', $filters['leave_from']);
                $builder->orGroupStart();
                $builder->where('leaves.end_date IS NULL');
                $builder->where('leaves.start_date >=', $filters['leave_from']);
                $builder->groupEnd();
                $builder->groupEnd();
            }
            if (!empty($filters['leave_to'])) {
                $builder->where('leaves.start_date <=', $filters['leave_to']);
            }
            
            // Search filter
            if (!empty($filters['search'])) {
                $builder->groupStart();
                $builder->like('u.name', $filters['search']);
                $builder->orLike('u.email', $filters['search']);
                $builder->orLike('leaves.reason', $filters['search']);
                $builder->orLike('leaves.type', $filters['search']);
                $builder->orLike('lt.name', $filters['search']);
                $builder->groupEnd();
            }
            
            $countBuilder = clone $builder;
            $total = $countBuilder->countAllResults(false);
            
            // Sorting
            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortOrder = $filters['sort_order'] ?? 'desc';
            
            $sortColumn = match($sortBy) {
                'user' => 'u.name',
                'type' => 'lt.name',
                'start_date' => 'leaves.start_date',
                'status' => 'leaves.status',
                default => 'leaves.created_at'
            };
            $builder->orderBy($sortColumn, $sortOrder);
            
            // Pagination
            $page = $filters['page'] ?? 1;
            $perPage = $filters['per_page'] ?? 10;
            $offset = ($page - 1) * $perPage;
            $builder->limit($perPage, $offset);
            
            $leaves = $builder->get()->getResultArray();
            
            $from = $total > 0 ? $offset + 1 : 0;
            $to = $total > 0 ? min($offset + $perPage, $total) : 0;
            
            return [
                'leaves' => $leaves,
                'total' => $total,
                'from' => $from,
                'to' => $to,
                'current_page' => (int)$page,
                'per_page' => (int)$perPage
            ];
            
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function updateStatus(int $id, string $status): bool
    {
        try {
            return $this->update($id, ['status' => $status]);
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
