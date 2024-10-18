<?php

namespace App\Models;

use CodeIgniter\Model;

class AssociationModel extends Model
{
    protected $table            = 'associations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'community_id',
        'orgid',
        'office_id',
        'creator',
        'owner',
        'deleted_at',
        'updated_at',
        'created_at'
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

    public function generateCode(string $orgId): string
    {
        $prefix = intval(preg_replace('/\D/', '', $orgId));
        $code = $prefix . "001";
        $last = $this->where('orgid', $orgId)->orderBy('id', 'desc')->first();
        if ($last) {
            $code = intval(preg_replace('/\D/', '', substr($last->assoc_code, 3))) + 1;
            $code = str_pad($code, 3, 0, STR_PAD_LEFT);
            $code = $prefix . $code;
        }
        return "A" . $code;
    }
}
