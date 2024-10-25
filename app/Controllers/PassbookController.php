<?php

namespace App\Controllers;

use App\Models\PassbookModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;
use Codewrite\CoopAuth\ApiResponse;
use Config\Database;

class PassbookController extends ResourceController
{
    protected $modelName = PassbookModel::class;
    protected $format    = 'json';
    protected $allowedColumns = [
        'id',
        'pbnum',
        'acnum',
        'association_id',
        'account_id',
        'assoc_code',
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

        return $response->getCollectionResponse(true, ['owner', 'orgid', 'association_id']);
    }

    public function create()
    {
        $rules = config('Validation')->create['passbooks'];
        // Validate input
        if (!$this->validate($rules)) {
            return $this->response->setJSON(
                [
                    'status'  => false,
                    'message' => 'Failed validating data',
                    'error'   => $this->validator->getErrors()
                ]
            )->setStatusCode(Response::HTTP_BAD_REQUEST);
        }
        $data = $this->validator->getValidated();
        $data['pbnum'] = $data['pbnum'] ?? $this->model->generateCode($data['orgid']);
        $data['creator'] = auth()->user_id();

        $response = auth()->can('create', 'passbooks', ['owner', 'orgid', 'association_id'], [$data]);
        if ($response->denied())
            return $response->responsed();

        if ($this->model->save($data)) {
            return $this->respondCreated([
                'status' => true,
                'data' => $this->model->find($this->model->getInsertID()),
                'message' => 'Passbook created successfully.'
            ]);
        } else {
            return $this->respond([
                'status' => false,
                'data' => $data,
                'message' => 'Failed to create passbook.'
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    public function update($id = null)
    {
        $passbook = $this->model->find($id);
        if (!$passbook) {
            return $this->respond([
                'status' => false,
                'message' => 'Passbook not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $response = auth()->can('update', 'passbooks', ['owner', 'orgid', 'association_id'], [$passbook]);
        if ($response->denied())
            return $response->responsed();

        $rules = config('Validation')->update['passbooks'];
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
            'message' => 'Passbook updated successfully.'
        ]);
    }

    public function show($id = null)
    {
        $params = $this->request->getVar(['columns']);
        $this->model->where('id', $id);
        $response = new ApiResponse($this->model, $params, $this->allowedColumns);

        return $response->getSingleResponse(true, ['owner', 'orgid', 'association_id']);
    }

    public function delete($id = null)
    {
        $passbook = $this->model->find($id);
        if (!$passbook) {
            return $this->respond([
                'status' => false,
                'message' => 'Passbook not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $response = auth()->can('delete', 'passbooks', ['owner', 'orgid', 'association_id'], [$passbook]);
        if ($response->denied())
            return $response->responsed();

        if ($this->model->delete($id)) {
            return $this->respondDeleted([
                'status'  => true,
                'message' => 'Passbook delete successfully',
                'data'    => null
            ]);
        } else {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed to delete passbook',
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    // Pull changes from the server
    public function pull()
    {
        $lastSyncTime = $this->request->getGet('lastSyncTime');

        // Fetch updated after the last pulled timestamp
        $records = $this->model
            ->select([
                'id as server_id',
                'pbnum',
                'acnum',
                'association_id',
                'account_id',
                'assoc_code',
                'orgid',
                'creator',
                'owner',
                'updated_at',
                'created_at'
            ])
            ->where('updated_at >', date('Y-m-d H:i:s', strtotime($lastSyncTime)))
            ->findAll();
        $deletedRecords = $this->model->select(['id as server_id', 'deleted_at'])
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
        $updates = $this->request->getJsonVar('updated', true);
        $deleted = $this->request->getJsonVar('deleted', true);

        $db = Database::connect();
        $db->transStart();
        foreach ($updates as $update) {
            unset($update['id']);
            if (empty($update['server_id'])) {
                $update['pbnum'] = $this->model->generateCode($update['orgid']);
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
                'timestamp' =>  date('Y-m-d H:i:s', strtotime('now')), // Current server time for synchronization
                'status' => true,
                'message' => 'Sync completed successfully'
            ], Response::HTTP_OK);
        else return $this->respond([
            'status' => false,
            'message' => 'Sync failed'
        ], Response::HTTP_OK);
    }
}
