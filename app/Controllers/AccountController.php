<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\OrganizationModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;
use Codewrite\CoopAuth\ApiResponse;

class AccountController extends ResourceController
{
    protected $modelName = 'App\Models\AccountModel';
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
        $data = (array)$this->request->getVar();

        $rules = config('Validation')->create['accounts'];
        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $orgModel = new OrganizationModel();

        if (!$orgModel->find($data['orgid'])) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   =>    ["orgid" => "The Organization doesn't exist."]
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($this->model->save($data)) {
            return $this->respondCreated([
                'status' => true,
                'data' => $this->model->find($this->model->getInsertID()),
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
        $data = $this->request->getRawInput();

        $rules = config('Validation')->update['accounts'];
        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }
        if (!$this->model->find($id)) {
            return $this->respond([
                'status' => false,
                'message' => 'Account not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->model->update($id, $data);
        return $this->respond([
            'status' => true,
            'data' => $this->model->find($id),
            'message' => 'Account updated successfully.'
        ]);
    }

    public function show($id = null)
    {
        $params = $this->request->getVar(['columns', 'sort', 'page', 'pageSize']);
        $allowedColumns = [];
        $this->model->find($id);
        $response = new ApiResponse($this->model, $params, $allowedColumns);

        return $response->getSingleResponse();
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
