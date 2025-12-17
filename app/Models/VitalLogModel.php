<?php

namespace App\Models;

use CodeIgniter\Model;

class VitalLogModel extends Model
{
    protected $table = 'vital_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'category_id', 'value', 'note', 'log_date'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'user_id' => 'required|integer',
        'category_id' => 'required|integer',
        'value' => 'required|max_length[50]',
        'log_date' => 'required|valid_date'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function getLogsWithCategory($userId, $limit = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('vital_logs.*, vital_categories.name as category_name, vital_categories.unit as category_unit');
        $builder->join('vital_categories', 'vital_categories.id = vital_logs.category_id');
        $builder->where('vital_logs.user_id', $userId);
        $builder->orderBy('vital_logs.log_date', 'DESC');
        $builder->orderBy('vital_logs.id', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->get()->getResultArray();
    }

    public function getLogById($id, $userId)
    {
        return $this->where('id', $id)->where('user_id', $userId)->first();
    }
}