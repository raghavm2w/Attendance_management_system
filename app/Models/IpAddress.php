<?php

namespace App\Models;

use CodeIgniter\Model;

class IpAddress extends Model
{
    protected $table            = 'allowed_ips';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['ip_address', 'label', 'is_active'];
      protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getIps(array $filters): array
    {
        try {
            $builder = $this->builder();
            
            if (!empty($filters['search'])) {
                $builder->like('label', $filters['search']);
            }
            
            // Status filter
            if ($filters['status'] !== '') {
                $builder->where('is_active', $filters['status']);
            }
            
            // Total count
            $countBuilder = clone $builder;
            $total = $countBuilder->countAllResults(false);
            
            // Sorting
            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $builder->orderBy($sortBy, $sortOrder);
            
            // Pagination
            $page = (int)($filters['page'] ?? 1);
            $perPage = (int)($filters['per_page'] ?? 10);
            $offset = ($page - 1) * $perPage;
            $builder->limit($perPage, $offset);
            
            $ips = $builder->get()->getResultArray();
            
            $from = $total > 0 ? $offset + 1 : 0;
            $to = $total > 0 ? min($offset + $perPage, $total) : 0;
            
            return [
                'ips' => $ips,
                'total' => $total,
                'from' => $from,
                'to' => $to
            ];
        } catch (\Throwable $e) {
            throw $e;
        }
    }
    public function updateIp($id,$data){
        try {
            return $this->update($id,$data);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function restore($id)
    {
        try {
            return $this->update($id, ['is_active' => 1]);
        } catch (\Throwable $e) {
            throw $e;
        }
    }
     public function isAllowed(string $ip)
    {
        try{
            return $this->where('ip_address', $ip)
                           ->where('is_active', 1)
                           ->first();
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
