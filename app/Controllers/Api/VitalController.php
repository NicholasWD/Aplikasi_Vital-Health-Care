<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\VitalCategoryModel;
use App\Models\VitalLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class VitalController extends BaseController
{
    protected $categoryModel;
    protected $logModel;

    public function __construct()
    {
        $this->categoryModel = new VitalCategoryModel();
        $this->logModel = new VitalLogModel();
    }

    public function getCategories()
    {
        $categories = $this->categoryModel->findAll();

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Categories retrieved successfully',
            'data' => $categories
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }

    public function getLogs()
    {
        $userId = $this->request->userId;
        $limit = $this->request->getGet('limit');

        $logs = $this->logModel->getLogsWithCategory($userId, $limit);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Logs retrieved successfully',
            'data' => $logs
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }

    public function getLog($id)
    {
        $userId = $this->request->userId;
        $log = $this->logModel->getLogById($id, $userId);

        if (!$log) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Log not found'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Log retrieved successfully',
            'data' => $log
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }

    public function addLog()
    {
        $rules = [
            'category_id' => 'required|integer',
            'value' => 'required|max_length[50]',
            'log_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $userId = $this->request->userId;
        $json = $this->request->getJSON();

        $data = [
            'user_id' => $userId,
            'category_id' => $json->category_id,
            'value' => $json->value,
            'note' => $json->note ?? null,
            'log_date' => $json->log_date
        ];

        if ($this->logModel->insert($data)) {
            $log = $this->logModel->find($this->logModel->getInsertID());

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Vital log added successfully',
                'data' => $log
            ])->setStatusCode(ResponseInterface::HTTP_CREATED);
        }

        return $this->response->setJSON([
            'status' => false,
            'message' => 'Failed to add vital log'
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function updateLog($id)
    {
        $userId = $this->request->userId;
        $log = $this->logModel->getLogById($id, $userId);

        if (!$log) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Log not found'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $rules = [
            'category_id' => 'required|integer',
            'value' => 'required|max_length[50]',
            'log_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $json = $this->request->getJSON();

        $data = [
            'category_id' => $json->category_id,
            'value' => $json->value,
            'note' => $json->note ?? null,
            'log_date' => $json->log_date
        ];

        if ($this->logModel->update($id, $data)) {
            $updatedLog = $this->logModel->find($id);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Vital log updated successfully',
                'data' => $updatedLog
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        }

        return $this->response->setJSON([
            'status' => false,
            'message' => 'Failed to update vital log'
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function deleteLog($id)
    {
        $userId = $this->request->userId;
        $log = $this->logModel->getLogById($id, $userId);

        if (!$log) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Log not found'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        if ($this->logModel->delete($id)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Vital log deleted successfully'
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        }

        return $this->response->setJSON([
            'status' => false,
            'message' => 'Failed to delete vital log'
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function getStats()
    {
        $userId = $this->request->userId;
        
        $db = \Config\Database::connect();
        
        // Get total logs
        $totalLogs = $this->logModel->where('user_id', $userId)->countAllResults();
        
        // Get categories tracked
        $categoriesTracked = $db->table('vital_logs')
            ->select('COUNT(DISTINCT category_id) as count')
            ->where('user_id', $userId)
            ->get()
            ->getRow()
            ->count;

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Stats retrieved successfully',
            'data' => [
                'total_logs' => $totalLogs,
                'categories_tracked' => $categoriesTracked
            ]
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }
}