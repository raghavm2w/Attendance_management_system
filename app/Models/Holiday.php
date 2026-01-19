<?php

namespace App\Models;

use CodeIgniter\Model;

class Holiday extends Model
{
    protected $table            = 'holidays';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'holiday_date', 'name', 'is_optional'];

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
        'id'           => 'permit_empty|integer',
        'holiday_date' => 'required|valid_date|is_unique[holidays.holiday_date,id,{id}]',
        'name'         => 'required|min_length[3]|max_length[150]',
        'is_optional'  => 'permit_empty|in_list[0,1]'
    ];
    protected $validationMessages   = [
        'holiday_date' => [
            'is_unique' => 'A holiday already exists on this date.'
        ]
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
}
