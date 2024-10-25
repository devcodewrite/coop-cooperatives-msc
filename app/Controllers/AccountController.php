<?php

namespace App\Controllers;

use App\Models\AccountModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;
use Codewrite\CoopAuth\ApiResponse;
use Config\Database;

class AccountController extends ResourceController
{
    protected $modelName = AccountModel::class;
    protected $format    = 'json';
    protected $allowedColumns = [
        'id',
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
        'updated_at',
        'created_at'
    ];

    public function index()
    {
        $params = $this->request->getVar(['columns', 'filters', 'sort', 'page', 'pageSize']);
        $response = new ApiResponse($this->model, $params, $this->allowedColumns);
        return $response->getCollectionResponse(true, ['owner', 'orgid', 'community_id']);
    }

    public function create()
    {
        $rules = config('Validation')->create['accounts'];
        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $data = $this->validator->getValidated();
        $data['acnum'] = $data['acnum'] ?? $this->model->generateCode($data['orgid']);
        $data['creator'] = auth()->user_id();

        $response = auth()->can('create', 'passbooks', ['owner', 'orgid', 'community_id'], [$data]);
        if ($response->denied())
            return $response->responsed();

        if ($this->model->save($data)) {
            return $this->respondCreated([
                'status' => true,
                'data' => $this->model->select($this->allowedColumns)->find($this->model->getInsertID()),
                'message' => 'Account created successfully.'
            ]);
        } else {
            return $this->respond([
                'status' => false,
                'data' => $data,
                'message' => 'Failed to create account.'
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    public function update($id = null)
    {
        $account = $this->model->find($id);
        if (!$account) {
            return $this->respond([
                'status'  => false,
                'message' => "Account doesn't exist.",
            ], Response::HTTP_NOT_FOUND);
        }
        $response = auth()->can('update', 'accounts', ['owner', 'orgid', 'community_id'], [$account]);
        if ($response->denied())
            return $response->responsed();

        $rules = config('Validation')->update['accounts'];

        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $data = $this->validator->getValidated();

        $this->model->update($id, $data);
        return $this->respond([
            'status' => true,
            'data' => $this->model->select($this->allowedColumns)->find($id),
            'message' => 'Account updated successfully.'
        ]);
    }

    public function show($id = null)
    {
        $params = $this->request->getVar(['columns']);
        $this->model->where('id', $id);
        $response = new ApiResponse($this->model, $params, $this->allowedColumns);

        return $response->getSingleResponse(true, ['owner', 'orgid', 'community_id']);
    }

    public function delete($id = null)
    {
        $account = $this->model->find($id);
        if (!$account) {
            return $this->respond([
                'status' => false,
                'message' => 'Account not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $response = auth()->can('delete', 'accounts', ['owner', 'orgid', 'community_id'], [$account]);
        if ($response->denied())
            return $response->responsed();

        if ($this->model->delete($id)) {
            return $this->respondDeleted([
                'status'  => true,
                'message' => 'Account delete successfully',
                'data'    => null
            ]);
        } else {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed to delete account',
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    // Pull changes from the server
    public function pull()
    {
        $lastSyncTime = $this->request->getGet('lastSyncTime');
        $filter = $this->request->getGet(['filters']);
        // Fetch updated after the last pulled timestamp
        $records = $this->model
            ->select([
                'id as server_id',
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
                'updated_at',
                'created_at'
            ])
            ->where($filter)
            ->where('updated_at >', date('Y-m-d H:i:s', strtotime($lastSyncTime)))
            ->findAll();
        $deletedRecords = $this->model->select(['id as server_id', 'deleted_at'])
            ->where($filter)
            ->where('deleted_at >', date('Y-m-d H:i:s', strtotime($lastSyncTime)))
            ->onlyDeleted()->findAll();

        return $this->respond([
            'updated' => $records,
            'deleted' => $deletedRecords,
            'timestamp' =>  date('Y-m-d H:i:s', strtotime('now')) // Current server time for synchronization
        ]);
    }

    // Push changes to the server
    public function push()
    {
        $updates = $this->request->getVar('updated');
        $deleted = $this->request->getVar('deleted');

        $db = Database::connect();
        $db->transStart();
        foreach ($updates as $update) {
            unset($update['id']);
            if (empty($update['server_id'])) {
                $update['acnum'] = $this->model->generateCode($update['orgid']);
            } else {
                $update['id'] = $update['server_id'];
            }
            $this->model->save($update);
        }
        foreach ($deleted as $update) {
            $this->model->delete($update['server_id']);
        }

        if ($db->transComplete())
            return $this->respond([
                'status' => true,
                'message' => 'Sync completed successfully'
            ], Response::HTTP_OK);
        else return $this->respond([
            'status' => false,
            'message' => 'Sync failed'
        ], Response::HTTP_OK);
    }
}
