<?php

namespace App\Controllers;

use App\Models\AccountModel;
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
        $data = $this->request->getPost();

        $rules = config('Validation')->create['accounts'];
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
            return $this->failNotFound('Account not found');
        }

        $this->model->update($id, $data);
        return $this->respond(['status' => 'Account updated successfully.']);
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
}
