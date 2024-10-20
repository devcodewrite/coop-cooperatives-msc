<?php

namespace Config;

use App\Validation\CustomRules;
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
        CustomRules::class
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
            'region_id' => 'required|integer|is_not_unique[regions.id]',
        ],
        'organizations' => [
            'name' => 'required|max_length[40]',
            'owner' => 'required|min_length[3]'
        ],
        'offices' => [
            'name' => 'required|max_length[40]',
            'region_id' => 'required|is_not_unique[regions.id]',
            'district_id' => 'required|is_not_unique[districts.id]',
            'owner' => 'required|min_length[12]',
            'orgid' => 'required|max_length[10]|is_not_unique[organizations.orgid]',
        ],
        'communities' => [
            'name' => 'required|max_length[40]',
            'office_id' => 'required|integer|is_not_unique[offices.id]',
            'region_id' => 'required|integer|is_not_unique[regions.id]',
            'district_id' => 'required|integer|is_not_unique[districts.id]',
            'owner' => 'required|min_length[12]',
        ],
        'associations' => [
            'name' => 'required|max_length[45]',
            'community_id' => 'required|exists_for_where[communities,id]',
            'office_id' => 'required|exists_for_where[offices,id,owner,owner,orgid,orgid]',
            'owner' => 'required|min_length[12]',
            'orgid' => 'required|max_length[10]|exists_for_where[organizations,orgid,owner,owner]',
        ],
        'accounts' => [
            'title' => 'required|in_list[mr,mrs,miss,dr,prof]',
            'name' => 'required|max_length[60]',
            'given_name' => 'required|max_length[45]',
            'family_name' => 'required|max_length[45]',
            'sex' => 'required|in_list[male,female,other]',
            'dateofbirth' => 'required|valid_date',
            'occupation' => 'required|max_length[60]',
            'primary_phone' => 'required|max_length[60]',
            'email' => 'permit_empty|valid_email|max_length[60]',
            'marital_status' => 'required|in_list[single,married,divorced,widowed]',
            'education' => 'required|in_list[none,primary,secondary,tertiary,postgraduate,other]',
            'nid_type' => 'permit_empty|in_list[passport,driver_license,voter_id,national_id_card]',
            'nid' => 'permit_empty|max_length[20]',
            'owner' => 'required|min_length[12]',
            'orgid' => 'required|max_length[10]|is_not_unique[organizations.orgid]',
            'community_id' => 'required|integer|is_not_unique[communities.id]',
        ],
        'passbooks' => [
            'account_id' => 'required|integer|is_not_unique[accounts.id]',
            'association_id' => 'required|integer|is_not_unique[associations.id]',
            'owner' => 'required|min_length[12]',
            'orgid' => 'required|max_length[10]|is_not_unique[organizations.orgid]',
        ]
    ];

    public $update = [
        'regions' => [
            'name' => 'if_exist|max_length[45]',
            'short_name' => 'if_exist|max_length[10]',
        ],
        'districts' => [
            'name' => 'if_exist|max_length[45]',
            'region_id' => 'if_exist|is_not_unique[regions.id]',
        ],
        'organizations' => [
            'name' => 'if_exist|max_length[40]'
        ],
        'offices' => [
            'name' => 'if_exist|max_length[40]',
            'region_id' => 'if_exist|is_not_unique[regions.id]',
            'district_id' => 'if_exist|is_not_unique[districts.id]',
        ],
        'communities' => [
            'name' => 'if_exist|max_length[40]',
            'office_id' => 'if_exist|is_not_unique[offices.id]',
            'region_id' => 'if_exist|is_not_unique[regions.id]',
            'district_id' => 'if_exist|is_not_unique[districts.id]'
        ],
        'associations' => [
            'name' => 'if_exist|max_length[45]',
            'community_id' => 'if_exist|is_not_unique[communities.id]',
            'office_id' => 'if_exist|is_not_unique[offices.id]',
            'owner' => 'if_exist|min_length[12]',
            'orgid' => 'if_exist|max_length[10]|is_not_unique[organizations.orgid]',
        ],
        'accounts'  => [
            'title' => 'if_exist|in_list[mr,mrs,miss,dr,prof]',
            'name' => 'if_exist|max_length[60]',
            'given_name' => 'if_exist|max_length[45]',
            'family_name' => 'if_exist|max_length[45]',
            'sex' => 'if_exist|in_list[male,female,other]',
            'dateofbirth' => 'if_exist|valid_date',
            'occupation' => 'if_exist|permit_empty|max_length[60]',
            'primary_phone' => 'if_exist|max_length[60]',
            'email' => 'if_exist|valid_email|max_length[60]',
            'marital_status' => 'if_exist|in_list[single,married,divorced,widowed]',
            'education' => 'if_exist|in_list[none,primary,secondary,tertiary,postgraduate,other]',
            'nid_type' => 'if_exist|in_list[passport,driver_license,voter_id,national_id_card]',
            'nid' => 'if_exist|max_length[60]',
            'orgid' => 'if_exist|max_length[10]|is_not_unique[organizations.orgid]',
            'community_id' => 'if_exist|is_not_unique[communities.id]',
        ],
        'passbooks' => [
            'account_id' => 'if_exist|is_not_unique[accounts.id]',
            'association_id' => 'if_exist|is_not_unique[associations.id]',
            'pbnum' => 'if_exist|max_length[10]|is_not_unique[passbooks.pbnum]',
        ]
        // Other validation rules similar to 'create', with only required fields removed
    ];

    public $sync = [
        'updated' => 'required',
        'deleted' => 'required',
    ];
}
