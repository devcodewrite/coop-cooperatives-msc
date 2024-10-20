<?php

namespace App\Controllers;

use App\Models\DistrictModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;
use Codewrite\CoopAuth\ApiResponse;

class DistrictController extends ResourceController
{
    protected $modelName = 'App\Models\DistrictModel';
    protected $format    = 'json';
    protected  $allowedColumns = [
        'id',
        'name',
        'region_id',
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

        return $response->getCollectionResponse();
    }

    public function create()
    {
        $data = $this->request->getPost();
        $rules = config('Validation')->create['districts'];
        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($this->model->save($data)) {
            return $this->respondCreated([
                'status' => true,
                'data' => $this->model->select($this->allowedColumns)->find($this->model->getInsertID()),
                'message' => 'District created successfully.'
            ]);
        } else {
            return $this->respond([
                'status' => false,
                'data' => $data,
                'message' => 'Failed to create district.'
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->respond([
                'status' => false,
                'message' => 'District not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $rules = config('Validation')->update['districts'];
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
            'message' => 'District updated successfully.'
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
        $district = $this->model->find($id);
        if (!$district) {
            return $this->respond([
                'status' => false,
                'message' => 'District not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted([
                'status'  => true,
                'message' => 'District delete successfully',
                'data'    => null
            ]);
        } else {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed to delete district',
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    // Pull changes from the server
    public function pull()
    {
        $lastSyncTime = $this->request->getGet('lastSyncTime');

        // Fetch updated after the last pulled timestamp
        $records = $this->model
            ->where('updated_at >', date('Y-m-d H:i:s', strtotime($lastSyncTime)))
            ->findAll();
        $deletedRecords = $this->model->select(['id', 'deleted_at'])
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
        $rules = config('Validation')->sync;
        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $updates = $this->request->getVar('updated');
        $nrowsUpdated = sizeof($updates);
        $deleted = $this->request->getVar('deleted');
        $nrowsDeleted = sizeof($updates);

        if ($nrowsUpdated > 0)
            $this->model->builder()->updateBatch($updates, ['id'], sizeof($updates));
        if ($nrowsDeleted > 0)
            $this->model->builder()->updateBatch($deleted, ['id'], sizeof($deleted));

        return $this->respond([
            'status' => true,
            'message' => 'Sync completed successfully'
        ], Response::HTTP_OK);
    }
}
