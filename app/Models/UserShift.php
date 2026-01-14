<?php

namespace App\Models;

use CodeIgniter\Model;

class UserShift extends Model
{
    protected $table            = 'user_shifts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'shift_id', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function assignShiftToUser($userId, $shiftId)
    {
        try {
            $existing = $this->where('user_id', $userId)->first();

            if ($existing) {
                return $this->update($existing['id'], ['shift_id' => $shiftId]);
            } else {
               return $this->insert([
                    'user_id' => $userId,
                    'shift_id' => $shiftId
                ]);
                
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
