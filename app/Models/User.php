<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name','email','password_hash','role','is_active','created_at','updated_at'
    ];

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

     public function findByEmail(string $email): ?array
    {
        try{
        return $this->where('email', $email)
                    ->where('is_active', 1)
                    ->first();
        }catch(\Throwable $e){
            throw $e;
        }
    }
    public function addUser($data)
    {
        try{
            return $this->insert($data);
        }catch(\Throwable $e){
            throw $e;
        }
    }
    
    public function getUsers(array $filters): array
    {
        try {
            $builder = $this->builder();
            
            // Select with LEFT JOIN to get shift info
            $builder->select('users.id, users.name, users.email, users.role, users.is_active, users.created_at, s.type as shift_type');
            $builder->join('user_shifts us', 'us.user_id = users.id', 'left');
            $builder->join('shifts s', 's.id = us.shift_id', 'left');
            
            // Search filter
            if (!empty($filters['search'])) {
                $builder->groupStart();
                $builder->like('users.name', $filters['search']);
                $builder->orLike('users.email', $filters['search']);
                $builder->groupEnd();
            }
            
            // Role filter
            if (!empty($filters['role'])) {
                $builder->where('users.role', $filters['role']);
            }
            
            // Status filter
            if ($filters['status'] !== '') {
                $builder->where('users.is_active', $filters['status']);
            }
            
            if (!empty($filters['shift'])) {
                $shiftType = ucfirst(strtolower($filters['shift']));
                $builder->where('s.type', $shiftType);
            }
            
            // Get total count before pagination
            $countBuilder = clone $builder;
            $total = $countBuilder->countAllResults(false);
            
            // Sorting
            $sortColumn = $filters['sort_by'];
            if ($sortColumn === 'shift') {
                $sortColumn = 's.type';
            } elseif (in_array($sortColumn, ['name', 'email', 'role', 'is_active', 'created_at'])) {
                $sortColumn = 'users.' . $sortColumn;
            }
            $builder->orderBy($sortColumn, $filters['sort_order']);
            
            // Pagination
            $page = $filters['page'];
            $perPage = $filters['per_page'];
            $offset = ($page - 1) * $perPage;
            $builder->limit($perPage, $offset);
            
            $users = $builder->get()->getResultArray();
            
            // Calculate from/to for pagination info
            $from = $total > 0 ? $offset + 1 : 0;
            $to = $total > 0 ? min($offset + $perPage, $total) : 0;
            
            return [
                'users' => $users,
                'total' => $total,
                'from' => $from,
                'to' => $to
            ];
            
        } catch (\Throwable $e) {
            throw $e;
        }
    }
    public function restore($id){
        try{
            return $this->update($id, ['is_active' => 1]);

        }catch (\Throwable $e) {
            throw $e;
        }
    }
}
