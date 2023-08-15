<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\Tag;

class TagController extends BaseController
{
    use ResponseTrait;
    public $tagModel;
    public function __construct()
    {
        $this->tagModel = new Tag();
    }
    public function index()
    {
        $tags = $this->tagModel->findAll();

        return $this->respond($tags, 200);
    }

    public function show($id)
    {
        $tag = $this->tagModel->find($id);
        return $this->respond($tag, 200);
    }

    public function store()
    {
        $rules = $this->tagModel->validationRules;
        $data = ['name' => $this->request->getVar('name')];
        if ($this->validate($rules, $data)) {
            $this->tagModel->save($data);
            return $this->respondCreated(['message' => 'tag created']);
        }
        return $this->fail($this->validator->getErrors());
    }

    public function update($id)
    {
        $rules = $this->tagModel->validationRules;
        $data = ['name' => $this->request->getVar('name')];
        if ($this->validate($rules, $data)) {
            $updated = $this->tagModel->update($id, $data);
            if ($updated) {
                return $this->respond(['message' => 'tag updated successfully']);
            } else {
                return $this->failServerError('Failed to update tag');
            }
        }
        return $this->fail($this->validator->getErrors());
    }

    public function delete($id)
    {
        $deleted = $this->tagModel->deleteTagsFromPivot($id);
        if ($deleted) return $this->respond(['message' => 'tag deleted successfully']);

        return $this->failServerError('Failed to delete tag');
    }
}
