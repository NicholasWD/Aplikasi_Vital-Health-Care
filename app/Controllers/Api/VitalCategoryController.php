<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\VitalCategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

class VitalCategoryController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new VitalCategoryModel();
    }

    /**
     * GET /api/vital-categories
     * Get all vital categories
     */
    public function index()
    {
        try {
            $categories = $this->categoryModel->findAll();

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/vital-categories/:id
     * Get single category
     */
    public function show($id)
    {
        try {
            $category = $this->categoryModel->find($id);

            if (!$category) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Category not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Category retrieved successfully',
                'data' => $category
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to retrieve category',
                'error' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * POST /api/vital-categories
     * Create new category
     */
    public function create()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'unit' => 'required|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $data = [
                'name' => $this->request->getJSON()->name,
                'unit' => $this->request->getJSON()->unit,
            ];

            if ($this->categoryModel->insert($data)) {
                $category = $this->categoryModel->find($this->categoryModel->getInsertID());

                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Category created successfully',
                    'data' => $category
                ])->setStatusCode(ResponseInterface::HTTP_CREATED);
            }

            throw new \Exception('Failed to create category');
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * PUT /api/vital-categories/:id
     * Update category
     */
    public function update($id)
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'unit' => 'required|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $category = $this->categoryModel->find($id);
            if (!$category) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Category not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $data = [
                'name' => $this->request->getJSON()->name,
                'unit' => $this->request->getJSON()->unit,
            ];

            if ($this->categoryModel->update($id, $data)) {
                $category = $this->categoryModel->find($id);

                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Category updated successfully',
                    'data' => $category
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            }

            throw new \Exception('Failed to update category');
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * DELETE /api/vital-categories/:id
     * Delete category
     */
    public function delete($id)
    {
        try {
            $category = $this->categoryModel->find($id);
            if (!$category) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Category not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            if ($this->categoryModel->delete($id)) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Category deleted successfully'
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            }

            throw new \Exception('Failed to delete category');
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
