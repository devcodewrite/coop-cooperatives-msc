<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountModel extends Model
{
    protected $table            = 'accounts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'acnum',
        'title',
        'name',
        'given_name',
        'family_name',
        'sex',
        'dateofbirth',
        'occupation',
        'primary_phone',
        'email',
        'marital_status',
        'education',
        'nid_type',
        'nid',
        'orgid',
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
    protected $beforeInsert   = ['formatFields'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['formatFields'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function formatFields($record)
    {
        $data = $record['data'];
        $record['data']['dateofbirth'] = isset($data['dateofbirth']) ? date('Y-m-d', strtotime($data['dateofbirth'])) : null;

        return $record;
    }

    public function generateCode(string $orgId): string
    {
        $prefix = intval(preg_replace('/\D/', '', $orgId));
        $code = $prefix . "000001";
        $last = $this->where('orgid', $orgId)->orderBy('id', 'desc')->first();
        if ($last) {
            $code = intval(preg_replace('/\D/', '', substr($last->acnum, 3))) + 1;
            $code = str_pad($code, 6, 0, STR_PAD_LEFT);
            $code = $prefix . $code;
        }
        return $code;
    }
}
