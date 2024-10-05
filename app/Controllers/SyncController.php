<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\MemberModel;

class SyncController extends ResourceController
{
    protected $modelName = 'App\Models\MemberModel';
    protected $format    = 'json';

    // Pull changes from the server
    public function pull()
    {
        $lastPulledAt = $this->request->getPost('lastPulledAt');
        
        // Fetch members updated after the last pulled timestamp
        $members = $this->model
            ->where('updated_at >', date('Y-m-d H:i:s', $lastPulledAt))
            ->orWhere('deleted_at >', date('Y-m-d H:i:s', $lastPulledAt))
            ->findAll();

        $changes = [
            'members' => [
                'updated' => [],
                'deleted' => [],
            ],
        ];

        foreach ($members as $member) {
            if ($member['deleted_at']) {
                $changes['members']['deleted'][] = [
                    'id' => $member['id'],
                    'deleted_at' => strtotime($member['deleted_at']),
                ];
            } else {
                $changes['members']['updated'][] = $member;
            }
        }

        return $this->respond([
            'changes' => $changes,
            'timestamp' => time(), // Current server time for synchronization
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
