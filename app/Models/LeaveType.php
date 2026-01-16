<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveType extends Model
{
    protected $table            = 'leave_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'name', 'max_per_year', 'carry_forward', 'max_carry'];

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
        'id'            => 'permit_empty|integer',
        'name'          => 'required|min_length[3]|max_length[100]|is_unique[leave_types.name,id,{id}]',
        'max_per_year'  => 'required|decimal|greater_than_equal_to[0]',
        'carry_forward' => 'permit_empty|in_list[0,1]',
        'max_carry'     => 'permit_empty|decimal|greater_than_equal_to[0]'
    ];
    protected $validationMessages   = [
        'name' => [
            'is_unique' => 'This leave type name already exists.'
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
