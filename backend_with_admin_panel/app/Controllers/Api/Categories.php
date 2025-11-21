<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CategoryModel;

class Categories extends ResourceController
{
    protected $modelName = CategoryModel::class;
    protected $format    = 'json';

    /**
     * GET /api/categories
     * Public: fetch all categories
     */
    public function index()
    {
        $data = $this->model->orderBy('id', 'DESC')->findAll();

        // Map to ensure image_url key exists (relative path)
        $mapped = array_map(function ($item) {
            if (!isset($item['image_url'])) {
                // prefer 'image' DB column if present
                $item['image_url'] = $item['image'] ?? '';
            }
            return $item;
        }, $data);

        return $this->respond($mapped);
    }

    public function show($id = null)
    {
        $item = $this->model->find($id);

        if (!$item) {
            return $this->failNotFound('Category not found.');
        }

        if (!isset($item['image_url'])) {
            $item['image_url'] = $item['image'] ?? '';
        }

        return $this->respond($item);
    }

    /**
     * POST /api/categories
     * Admin: create category
     */
    public function create()
    {
        $data = $this->request->getJSON(true) ?? [];

        if (!isset($data['name']) || trim($data['name']) === '') {
            return $this->failValidationErrors('Category name is required.');
        }

        $id = $this->model->insert([
            'name'        => $data['name'],
            'description' => $data['description'] ?? '',
        ]);

        return $this->respondCreated([
            'id'      => $id,
            'message' => 'Category created successfully.'
        ]);
    }

    /**
     * PUT /api/categories/{id}
     * Admin: update category
     */
    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Category not found.');
        }

        $data = $this->request->getJSON(true) ?? [];

        $ok = $this->model->update($id, $data);

        return $this->respond([
            'updated' => (bool) $ok,
            'message' => 'Category updated successfully.'
        ]);
    }

    /**
     * DELETE /api/categories/{id}
     * Admin: delete category
     */
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Category not found.');
        }

        $ok = $this->model->delete($id);

        return $this->respond([
            'deleted' => (bool) $ok,
            'message' => 'Category deleted successfully.'
        ]);
    }
}
