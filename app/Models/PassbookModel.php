<?php

namespace App\Models;

use CodeIgniter\Model;

class PassbookModel extends Model
{
    protected $table            = 'passbooks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pbnum',
        'acnum',
        'association_id',
        'account_id',
        'assoc_code',
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
    protected $beforeInsert   = ['addFields'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['addFields'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function addFields($record)
    {
        $data = $record['data'];
        $assocModel = new AssociationModel();
        $accountModel = new AccountModel();
        $record['data']['assoc_code'] = $assocModel->find($data['association_id'])['assoc_code'];
        $record['data']['acnum'] = $accountModel->find($data['account_id'])['acnum'];

        return $record;
    }

    public function generateCode(string $orgId): string
    {
        $code = "0001";
        $last = $this->where('orgid', $orgId)->orderBy('id', 'desc')->first();
        if ($last) {
            $code = intval(preg_replace('/\D/', '', $last->pbnum)) + 1;
            $code = str_pad($code, 4, 0, STR_PAD_LEFT);
            $code = $code;
        }
        return "PB".$code;
    }
}
