<?php

namespace App\Controllers;

use App\Models\AssociationModel;
use App\Models\OrganizationModel;
use App\Models\PassbookModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;
use Codewrite\CoopAuth\ApiResponse;

class PassbookController extends ResourceController
{
    protected $modelName = 'App\Models\PassbookModel';
    protected $format    = 'json';

    public function index()
    {

        $params = $this->request->getVar(['columns', 'sort', 'page', 'pageSize']);
        $allowedColumns = [];

        $response = new ApiResponse($this->model, $params, $allowedColumns);

        return $response->getCollectionResponse();
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
            'data' => $this->model->find($id),
            'message' => 'Passbook updated successfully.'
        ]);
    }

    public function show($id = null)
    {
        $params = $this->request->getVar(['columns', 'sort', 'page', 'pageSize']);
        $allowedColumns = [];
        $this->model->where('id', $id);
        $response = new ApiResponse($this->model, $params, $allowedColumns);

        return $response->getSingleResponse();
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
