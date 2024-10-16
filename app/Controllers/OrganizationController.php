<?php

namespace App\Controllers;

use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;
use Codewrite\CoopAuth\ApiResponse;

class OrganizationController extends ResourceController
{
    protected $modelName = 'App\Models\OrganizationModel';
    protected $format    = 'json';
    protected $allowedColumns = [];

    public function index()
    {
        $params = $this->request->getVar(['columns', 'filters', 'sort', 'page', 'pageSize']);
        $response = new ApiResponse($this->model, $params, $this->allowedColumns);
        return $response->getCollectionResponse();
    }

    public function create()
    {
        $rules = config('Validation')->create['organizations'];
        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $data = $this->validator->getValidated();

        $response = auth()->can('delete', 'organizations', ['owner'], [$data]);
        if ($response->denied())
            return $response->responsed();

        $data['orgid'] = $this->model->generateId($data['name']);

        if ($this->model->save($data)) {
            return $this->respondCreated([
                'status' => true,
                'data' => $this->model->find($this->model->getInsertID()),
                'message' => 'Organization created successfully.'
            ]);
        } else {
            return $this->respond([
                'status' => false,
                'data' => $data,
                'message' => 'Failed to create organization.'
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    public function update($id = null)
    {
        $organization = $this->model->find($id);
        if (!$organization) {
            return $this->respond([
                'status' => false,
                'message' => 'Organization not found'
            ], Response::HTTP_NOT_FOUND);
        }
        // authorization checks
        $response = auth()->can('delete', 'organizations', ['owner'], [$organization]);
        if ($response->denied())
            return $response->responsed();

        $rules = config('Validation')->update['organizations'];
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
            'message' => 'Organization updated successfully.'
        ]);
    }

    public function show($id = null)
    {
        $params = $this->request->getVar(['columns']);
        $response = new ApiResponse($this->model, $params, $this->allowedColumns);

        return $response->getSingleResponse();
    }

    public function delete($id = null)
    {
        $organization = $this->model->find($id);
        if (!$organization) {
            return $this->respond([
                'status' => false,
                'message' => 'Organization not found'
            ], Response::HTTP_NOT_FOUND);
        }
        // authorization checks
        $response = auth()->can('delete', 'organizations', ['owner'], [$organization]);
        if ($response->denied())
            return $response->responsed();

        if ($this->model->delete($id)) {
            return $this->respondDeleted([
                'status'  => true,
                'message' => 'Organization delete successfully',
                'data'    => null
            ]);
        } else {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed to delete organization',
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }
}
