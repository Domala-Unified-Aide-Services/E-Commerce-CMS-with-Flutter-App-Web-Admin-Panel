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

    public function index()
    {
        $data['uploads'] = $this->uploadModel->orderBy('uploaded_at', 'DESC')->findAll();
        echo view('admin/header');
        echo view('admin/uploads_list', $data);
        echo view('admin/footer');
    }

    // store: supports normal form POST (redirect) and AJAX upload (JSON response)
    public function store()
{
    $file = $this->request->getFile('file');

    if (!$file) {
        log_message('error', 'Upload store called but no file present in request.');
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'No file uploaded.'])->setStatusCode(400);
        }
        return redirect()->back()->with('error', 'No file uploaded.');
    }

    if (!$file->isValid()) {
        $err = $file->getErrorString() . ' (' . $file->getError() . ')';
        log_message('error', "Upload invalid file: {$err}");
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid file: ' . $err])->setStatusCode(400);
        }
        return redirect()->back()->with('error', 'Invalid file: ' . $err);
    }

    // ensure public/uploads exists and is writable
    $publicUploads = FCPATH . 'uploads/';
    if (!is_dir($publicUploads)) {
        if (!mkdir($publicUploads, 0755, true)) {
            log_message('error', "Failed to create uploads dir: {$publicUploads}");
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Server error: cannot create upload folder'])->setStatusCode(500);
            }
            return redirect()->back()->with('error', 'Server error: cannot create upload folder');
        }
    }

    $newName = $file->getRandomName();

    try {
        $file->move($publicUploads, $newName);
    } catch (\Exception $e) {
        log_message('error', "File move failed: " . $e->getMessage());
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Server error saving file: ' . $e->getMessage()])->setStatusCode(500);
        }
        return redirect()->back()->with('error', 'Server error saving file.');
    }

    $publicPath = 'uploads/' . $newName;
    $fullUrl = base_url($publicPath);

    // Save record to DB
    try {
        $this->uploadModel->insert([
            'filename' => $file->getClientName(),
            'filepath' => $publicPath,
            'uploaded_at' => date('Y-m-d H:i:s'),
        ]);
    } catch (\Exception $e) {
        log_message('error', "DB insert failed for upload: " . $e->getMessage());
        // attempt to delete file to avoid orphan
        @unlink($publicUploads . $newName);
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Database error saving upload'])->setStatusCode(500);
        }
        return redirect()->back()->with('error','Database error saving upload');
    }

    if ($this->request->isAJAX()) {
        return $this->response->setJSON([
            'success' => true,
            'url' => $fullUrl,
            'path' => $publicPath,
            'filename' => $file->getClientName()
        ]);
    }

    return redirect()->to(base_url('admin/uploads'))->with('success', 'File uploaded.');
}

    public function delete($id = null)
    {
        if (!$id) return redirect()->back()->with('error','Invalid id');

        $row = $this->uploadModel->find($id);
        if (!$row) return redirect()->back()->with('error','Not found.');

        // filepath stored as 'uploads/filename.ext'
        $fullPath = FCPATH . $row['filepath'];
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        $this->uploadModel->delete($id);
        return redirect()->back()->with('success','Deleted');
    }
}
