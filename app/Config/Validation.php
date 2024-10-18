<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    public $create = [
        'regions' => [
            'name' => 'required|max_length[45]',
            'short_name' => 'required|max_length[10]',
        ],
        'districts' => [
            'name' => 'required|max_length[45]',
            'region_id' => 'required|integer',
        ],
        'organizations' => [
            'name' => 'required|max_length[40]',
            'owner' => 'required|min_length[3]'
        ],
        'offices' => [
            'name' => 'required|max_length[40]',
            'region_id' => 'required|integer',
            'district_id' => 'required|integer',
            'owner' => 'required|min_length[3]',
            'orgid' => 'required|max_length[10]|is_not_unique[organizations.orgid]',
        ],
        'communities' => [
            'name' => 'required|max_length[40]',
            'office_id' => 'required|integer',
            'region_id' => 'required|integer',
            'district_id' => 'required|integer',
            'owner' => 'required|min_length[3]',
        ],
        'associations' => [
            'name' => 'required|max_length[45]',
            'community_id' => 'required|integer',
            'office_id' => 'required|integer|',
            'owner' => 'required|min_length[3]',
            'orgid' => 'required|max_length[10]|is_not_unique[organizations.orgid]',
        ],
        'accounts' => [
            'title' => 'required|in_list[mr,mrs,miss,dr,prof]',
            'name' => 'required|max_length[60]',
            'given_name' => 'required|max_length[45]',
            'family_name' => 'required|max_length[45]',
            'sex' => 'required|in_list[male,female,other]',
            'dateofbirth' => 'required|valid_date',
            'occupation' => 'permit_empty|max_length[60]',
            'primary_phone' => 'required|max_length[60]',
            'email' => 'required|valid_email|max_length[60]',
            'marital_status' => 'permit_empty|in_list[single,married,divorced,widowed]',
            'education' => 'permit_empty|in_list[none,primary,secondary,tertiary,postgraduate,other]',
            'nid_type' => 'permit_empty|in_list[passport,driver_license,voter_id,national_id_card]',
            'nid' => 'permit_empty|max_length[20]',
            'owner' => 'required|min_length[3]',
            'orgid' => 'required|max_length[10]|is_not_unique[organizations.orgid]',
        ],
        'passbooks' => [
            'account_id' => 'required|integer|is_not_unique[accounts.id,id,id]',
            'association_id' => 'required|integer|is_not_unique[associations.id,id,id]',
            'owner' => 'required|min_length[3]',
            'orgid' => 'required|max_length[10]|is_not_unique[organizations.orgid]',
        ]
    ];

    public $update = [
        'regions' => [
            'name' => 'max_length[45]',
            'short_name' => 'max_length[10]',
        ],
        'districts' => [
            'name' => 'max_length[45]',
            'region_id' => 'integer',
        ],
        'organizations' => [
            'name' => 'max_length[40]'
        ],
        'offices' => [
            'name' => 'max_length[40]',
            'region_id' => 'integer',
            'district_id' => 'integer',
        ],
        'communities' => [
            'name' => 'required|max_length[40]',
            'office_id' => 'required|integer',
            'region_id' => 'required|integer',
            'district_id' => 'required|integer'
        ],
        'accounts'  => [
            'title' => 'in_list[mr,mrs,miss,dr,prof]',
            'name' => 'max_length[60]',
            'given_name' => 'max_length[45]',
            'family_name' => 'max_length[45]',
            'sex' => 'in_list[male,female,other]',
            'dateofbirth' => 'valid_date',
            'occupation' => 'permit_empty|max_length[60]',
            'primary_phone' => 'max_length[60]',
            'email' => 'valid_email|max_length[60]',
            'marital_status' => 'in_list[single,married,divorced,widowed]',
            'education' => 'in_list[none,primary,secondary,tertiary,postgraduate,other]',
            'nid_type' => 'in_list[passport,driver_license,voter_id,national_id_card]',
            'nid' => 'max_length[60]',
        ],
        'passbooks' => [
            'account_id' => 'required|integer|is_not_unique[accounts.id,id,id]',
            'association_id' => 'required|integer|is_not_unique[associations.id,id,id]',
            'pbnum' => 'max_length[10]|is_not_unique[passbooks.pbnum,pbnum,pbnum]',
        ]
        // Other validation rules similar to 'create', with only required fields removed
    ];

    public $sync = [
        'updated' => 'required',
        'deleted' => 'required',
    ];
}
