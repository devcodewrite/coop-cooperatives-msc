<?php

namespace App\Controllers;

use App\Models\OfficeModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;
use Codewrite\CoopAuth\ApiResponse;
use Config\Database;

class OfficeController extends ResourceController
{
    protected $modelName = OfficeModel::class;
    protected $format    = 'json';
    protected $allowedColumns = [
        'id',
        'name',
        'off_code',
        'region_id',
        'district_id',
        'creator',
        'owner',
        "orgid",
        'updated_at',
        'created_at'
    ];

    public function index()
    {
        $params = $this->request->getVar(['columns', 'filters', 'sort', 'page', 'pageSize']);
        $response = new ApiResponse($this->model, $params, $this->allowedColumns);

        return $response->getCollectionResponse(true, ['owner', 'orgid']);
    }

    public function create()
    {
        $rules = config('Validation')->create['offices'];
        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $data = $this->validator->getValidated();
        $data['off_code'] = $data['off_code'] ?? $this->model->generateCode($data['orgid']);
        $data['creator'] = auth()->user_id();

        $response = auth()->can('create', 'offices', ['owner', 'orgid'], [$data]);
        if ($response->denied())
            return $response->responsed();

        if ($this->model->save($data)) {
            return $this->respondCreated([
                'status' => true,
                'data' => $this->model->select($this->allowedColumns)->find($this->model->getInsertID()),
                'message' => 'Office created successfully.'
            ]);
        } else {
            return $this->respond([
                'status' => false,
                'data' => $data,
                'message' => 'Failed to create office.'
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    public function update($id = null)
    {
        $office = $this->model->find($id);
        if (!$office) {
            return $this->respond([
                'status' => false,
                'message' => 'Office not found'
            ], Response::HTTP_NOT_FOUND);
        }
        $response = auth()->can('update', 'offices', ['owner', 'orgid'], [$office]);
        if ($response->denied())
            return $response->responsed();

        $rules = config('Validation')->update['offices'];
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
            'message' => 'Office updated successfully.'
        ]);
    }

    public function show($id = null)
    {
        $params = $this->request->getVar(['columns']);
        $this->model->where('id', $id);
        $response = new ApiResponse($this->model, $params, $this->allowedColumns);
        return $response->getSingleResponse(true, ['owner', 'orgid']);
    }

    public function delete($id = null)
    {
        $office = $this->model->find($id);
        if (!$office) {
            return $this->respond([
                'status' => false,
                'message' => 'Office not found'
            ], Response::HTTP_NOT_FOUND);
        }
        $response = auth()->can('delete', 'offices', ['owner', 'orgid'], [$office]);
        if ($response->denied())
            return $response->responsed();

        if ($this->model->delete($id)) {
            return $this->respondDeleted([
                'status'  => true,
                'message' => 'Office delete successfully',
                'data'    => null
            ]);
        } else {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed to delete office',
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
                'name',
                'off_code',
                'region_id',
                'district_id',
                'creator',
                'owner',
                "orgid",
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
        try {
            $db->transException(true)->transStart();
            foreach ($updates as $update) {
                if (!$update['server_id']) {
                    unset($update['id']);
                    $update['off_code'] = $this->model->generateCode($update['orgid']);
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
            else
                return $this->respond([
                    'status' => false,
                    'message' => 'Sync failed'
                ], Response::HTTP_OK);
        } catch (DatabaseException $e) {
            return $this->respond([
                'timestamp' =>  date('Y-m-d H:i:s', strtotime('now')), // Current server time for synchronization
                'status' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_OK);
        }
    }
}
