<?php

namespace App\Controllers;

use App\Models\CommunityModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;
use Codewrite\CoopAuth\ApiResponse;

class CommunityController extends ResourceController
{
    protected $modelName = 'App\Models\CommunityModel';
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

        $rules = config('Validation')->create['communities'];
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
                'message' => 'Community created successfully.'
            ]);
        } else {
            return $this->respond([
                'status' => false,
                'data' => $data,
                'message' => 'Failed to create community.'
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    public function update($id = null)
    {
        $data = $this->request->getRawInput();

        $rules = config('Validation')->update['communities'];
        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->model->find($id)) {
            return $this->failNotFound('Community not found');
        }

        $this->model->update($id, $data);
        return $this->respond(['status' => 'Community updated successfully.']);
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
        $community = $this->model->find($id);
        if (!$community) {
            return $this->respond([
                'status' => false,
                'message' => 'Community not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted([
                'status'  => true,
                'message' => 'Community delete successfully',
                'data'    => null
            ]);
        } else {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed to delete community',
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }
}
