<?php

namespace App\Controllers;

use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;
use Codewrite\CoopAuth\ApiResponse;
use DateTimeZone;

class RegionController extends ResourceController
{
    protected $modelName = 'App\Models\RegionModel';
    protected $format    = 'json';
    protected $allowedColumns = [
        'id',
        'name',
        'deleted_at',
        'updated_at',
        'created_at'
    ];

    public function index()
    {
        $params = $this->request->getVar(['columns', 'filters', 'sort', 'page', 'pageSize']);
        $response = new ApiResponse($this->model, $params, $this->allowedColumns);

        return $response->getCollectionResponse();
    }

    public function create()
    {
        $rules = config('Validation')->create['regions'];
        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $data = $this->validator->getValidated();

        if ($this->model->save($data)) {
            return $this->respondCreated([
                'status' => true,
                'data' => $this->model->select($this->allowedColumns)->find($this->model->getInsertID()),
                'message' => 'Region created successfully.'
            ]);
        } else {
            return $this->respond([
                'status' => false,
                'data' => $data,
                'message' => 'Failed to create region.'
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    public function update($id = null)
    {

        if (!$this->model->find($id)) {
            return $this->respond([
                'status' => false,
                'message' => 'Region not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $rules = config('Validation')->update['regions'];
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
            'message' => 'Region updated successfully.'
        ]);
    }

    public function show($id = null)
    {
        $params = $this->request->getVar(['columns']);
        $this->model->where('id', $id);
        $response = new ApiResponse($this->model, $params, $this->allowedColumns);
        return $response->getSingleResponse();
    }

    public function delete($id = null)
    {
        $region = $this->model->find($id);
        if (!$region) {
            return $this->respond([
                'status' => false,
                'message' => 'Region not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted([
                'status'  => true,
                'message' => 'Region delete successfully',
                'data'    => null
            ]);
        } else {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed to delete region',
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
                'deleted_at',
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
}
