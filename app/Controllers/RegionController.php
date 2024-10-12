<?php

namespace App\Controllers;

use App\Models\RegionModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;
use Codewrite\CoopAuth\ApiResponse;
use DateTimeZone;

class RegionController extends ResourceController
{
    protected $modelName = 'App\Models\RegionModel';
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
        $data = $this->request->getVar();

        $rules = config('Validation')->create['regions'];
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
                'data' => $data,
                'message' => 'Region created successfully.'
            ]);
        } else {
            return $this->respond([
                'status' => true,
                'data' => $data,
                'message' => 'Failed to create region.'
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    public function update($id = null)
    {
        $data = $this->request->getRawInput();

        $rules = config('Validation')->update['regions'];
        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->model->find($id)) {
            return $this->failNotFound('Region not found');
        }

        $this->model->update($id, $data);
        return $this->respond(['status' => 'Region updated successfully.']);
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
        $lastPulledAt = $this->request->getGet('lastPulledAt');

        // Fetch updated after the last pulled timestamp
        $records = $this->model
            ->where('updated_at >', date('Y-m-d H:i:s', strtotime($lastPulledAt)))
            ->findAll();
        $deletedRecords = $this->model->select(['id', 'deleted_at'])
            ->where('deleted_at >', date('Y-m-d H:i:s', strtotime($lastPulledAt)))
            ->onlyDeleted()->findAll();
        $changes = [
            'members' => [
                'updated' => $records,
                'deleted' => $deletedRecords,
            ],
        ];

        return $this->respond([
            'changes' => $changes,
            'timestamp' =>  date('Y-m-d H:i:s', strtotime('now')) // Current server time for synchronization
        ]);
    }

    // Push changes to the server
    public function push()
    {
        $changes = $this->request->getPost('changes');

        if (isset($changes['members']['updated'])) {
            foreach ($changes['members']['updated'] as $member) {
                $this->model->save([
                    'id'      => $member['id'],
                    'name'    => $member['name'],
                    'email'   => $member['email'],
                    'version' => $member['version'],
                    'updated_at' => date('Y-m-d H:i:s', $member['updated_at']),
                ]);
            }
        }

        if (isset($changes['members']['deleted'])) {
            foreach ($changes['members']['deleted'] as $deletedMember) {
                $this->model->update($deletedMember['id'], ['deleted_at' => date('Y-m-d H:i:s', $deletedMember['deleted_at'])]);
            }
        }

        return $this->respond(['status' => 'success']);
    }
}
