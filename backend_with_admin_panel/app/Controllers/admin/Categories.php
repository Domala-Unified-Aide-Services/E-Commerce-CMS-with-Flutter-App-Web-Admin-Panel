<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Categories extends BaseController
{
    public function index()
    {
        $model = new CategoryModel();
        $categories = $model->orderBy('id', 'DESC')->findAll();

        return view('admin/categories_list', ['categories' => $categories]);
    }

    public function create()
    {
        return view('admin/category_form', ['category' => null]);
    }

    public function edit($id)
    {
        $model = new CategoryModel();
        $category = $model->find($id);

        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Category not found.');
        }

        return view('admin/category_form', ['category' => $category]);
    }

    public function store()
    {
        $model = new CategoryModel();

        $data = $this->request->getPost();
        $name = trim($data['name'] ?? '');
        $desc = $data['description'] ?? '';

        if ($name === '') {
            return redirect()->back()->with('error', 'Name is required.');
        }

        $saveData = [
            'name'        => $name,
            'description' => $desc,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        // Upload Image
        $file = $this->request->getFile('image');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/categories';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);

            $saveData['image'] = 'uploads/categories/' . $newName;
        }

        $model->insert($saveData);

        return redirect()->to('/admin/categories')->with('success', 'Category added.');
    }

    public function update($id)
    {
        $model = new CategoryModel();
        $existing = $model->find($id);

        if (!$existing) {
            return redirect()->to('/admin/categories')->with('error', 'Category not found.');
        }

        $data = $this->request->getPost();
        $name = trim($data['name'] ?? '');
        $desc = $data['description'] ?? '';

        if ($name === '') {
            return redirect()->back()->with('error', 'Name is required.');
        }

        $saveData = [
            'name'        => $name,
            'description' => $desc,
        ];

        // Upload Image (replace old)
        $file = $this->request->getFile('image');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/categories';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);

            $saveData['image'] = 'uploads/categories/' . $newName;

            // Delete old image
            if (!empty($existing['image'])) {
                $oldPath = FCPATH . $existing['image'];
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        $model->update($id, $saveData);

        return redirect()->to('/admin/categories')->with('success', 'Category updated.');
    }

    public function delete($id)
    {
        $model = new CategoryModel();
        $existing = $model->find($id);

        if ($existing) {
            // delete image
            if (!empty($existing['image'])) {
                $oldPath = FCPATH . $existing['image'];
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $model->delete($id);
        }

        return redirect()->to('/admin/categories')->with('success', 'Category deleted.');
    }
}
