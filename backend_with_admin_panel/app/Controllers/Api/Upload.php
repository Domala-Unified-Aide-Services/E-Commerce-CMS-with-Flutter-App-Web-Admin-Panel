<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UploadModel;

class Uploads extends BaseController
{
    protected $uploadModel;
    protected $helpers = ['form', 'url', 'filesystem'];

    public function __construct()
    {
        $this->uploadModel = new UploadModel();
    }

    // Show upload form & list
    public function index()
    {
        $data['uploads'] = $this->uploadModel->orderBy('uploaded_at', 'DESC')->findAll();
        return view('admin/uploads_list', $data);
    }

    // Handle POST upload
    public function store()
    {
        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'No file uploaded or invalid file.');
        }

        // ensure writable/uploads exists
        $targetFolder = WRITEPATH . 'uploads/';
        if (!is_dir($targetFolder)) {
            mkdir($targetFolder, 0755, true);
        }

        $newName = $file->getRandomName();
        $file->move($targetFolder, $newName);

        $filepath = 'writable/uploads/' . $newName; // store web-relative path or full path as per your app design

        $this->uploadModel->insert([
            'filename' => $file->getClientName(),
            'filepath' => $filepath,
            'uploaded_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('admin/uploads'))->with('success', 'File uploaded.');
    }

    // Optionally delete
    public function delete($id)
    {
        $row = $this->uploadModel->find($id);
        if (!$row) return redirect()->back()->with('error', 'Not found.');

        // remove file from disk
        $full = FCPATH . $row['filepath']; // if you stored relative to public, adjust
        if (file_exists($full)) {
            @unlink($full);
        }

        $this->uploadModel->delete($id);
        return redirect()->back()->with('success', 'Deleted');
    }
}
