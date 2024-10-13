<?php

namespace App\Controllers;

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
        $data = $this->request->getPost();

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
        $data = $this->request->getRawInput();

        $rules = config('Validation')->update['passbooks'];
        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->model->find($id)) {
            return $this->failNotFound('Passbook not found');
        }

        $this->model->update($id, $data);
        return $this->respond(['status' => 'Passbook updated successfully.']);
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
}
