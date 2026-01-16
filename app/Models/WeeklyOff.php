<?php

namespace App\Models;

use CodeIgniter\Model;

class WeeklyOff extends Model
{
    protected $table            = 'weekly_offs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'day_of_week'];

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
    protected $validationRules      = [
        'id'          => 'permit_empty|integer',
        'day_of_week' => 'required|integer|in_list[0,1,2,3,4,5,6]|is_unique[weekly_offs.day_of_week,id,{id}]'
    ];
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

    public function updateOffDay($days) {
        try {
            $this->db->transStart();
            // Remove existing weekly offs
            $this->truncate();

            // Insert new weekly offs
            if (!empty($days)) {
                $insertData = [];

                foreach ($days as $day) {
                    $insertData[] = [
                        'day_of_week' => (int) $day,
                    ];
                }

             $this->insertBatch($insertData);
            }

            $this->db->transComplete();

            return $this->db->transStatus();

        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
