<?php

namespace App\Controllers;


use App\Models\Image;
use App\Models\Token;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Office;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;

class OfficeController extends BaseController
{
    use ResponseTrait;
    public $officeModel, $imageModel;
    public function __construct()
    {
        $this->imageModel = new Image();
        $this->officeModel = new Office();
        helper('url');
    }
    public function index()
    {
        $limit = $this->request->getVar('limit') ?? 10;
        $offset = $this->request->getVar('offset') ?? 0;
        $offices = $this->officeModel->getOfficesWithTags($limit, $offset);
        $response = [
            'status' => true,
            'message' => 'Offices fetched successfully',
            'data' => $offices,
            'total' => $this->officeModel->getOfficesCount(),
            'limit' => $limit,
            'offset' => $offset,
            'url' => base_url() . 'api/offices?limit=' . $limit . '&offset=' . $offset

        ];
        return $this->respond($response);
    }

    public function show($officeId)
    {
        $office = $this->officeModel->getOfficeWithTags($officeId);
        if (!$office) return $this->failNotFound('Office not found');
        return $this->respond($office);
    }

    public function store()
    {
        $rules = $this->officeModel->validationRules;
        $data = $this->getData();
        if ($this->validate($rules, $data)) {
            $office = $this->officeModel->insert($data);
            $tagIds = $this->request->getVar('tags') ?? [];
            if (!empty($tagIds))  $this->officeModel->syncTags($tagIds, $office);
            if ($this->request->getFileMultiple('images')) $this->storeImages($office, "Office");
            return $this->respondCreated(['message' => 'Office created']);
        }
        return $this->fail($this->validator->getErrors());
    }

    public function update($officeId)
    {
        $rules = $this->officeModel->validationRules;
        $data = $this->getData();
        if ($this->validate($rules, $data)) {
            $updated = $this->officeModel->update($officeId, $data);
            if ($updated) {
                $tagIds = $this->request->getVar('tags') ?? [];
                if (!empty($tagIds)) $this->officeModel->syncTags($tagIds, $officeId);
                if ($this->request->getFileMultiple('images')) $this->storeImages($officeId, "Office");
                return $this->respond(['message' => 'Office updated successfully']);
            }
            return $this->failServerError('Failed to update office');
        }
        return $this->fail($this->validator->getErrors());
    }

    public function delete($officeId)
    {
        $office = $this->officeModel->find($officeId); // Assuming you have a method to find an office by ID

        if (!$office) return $this->failNotFound('Office not found');
        $this->officeModel->deleteTagsByOfficeId($officeId);
        $deleted = $this->officeModel->delete($officeId);
        if ($deleted) {
            $this->deleteImages($officeId, "Office");
            return $this->respondDeleted(['message' => 'Office deleted successfully']);
        }
        return $this->failServerError('Failed to delete office');
    }

    private function getAuth()
    {
        $key = getenv('JWT_SECRET');
        $header = $this->request->getHeaderLine('Authorization');
        $token = null;
        // extract the token from the header
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }
        // Decode the token using your secret key
        $key = getenv('JWT_SECRET');
        $decoded =  JWT::decode($token, new Key($key, Token::HASH_ALGORITHM));
        // Now you can access the user data from the decoded token
        return $decoded->user_id;
    }

    private function getData()
    {
        return [
            'title' => $this->request->getVar('title'),
            'description' => $this->request->getVar('description'),
            'lat' => $this->request->getVar('lat'),
            'lng' => $this->request->getVar('lng'),
            'address_line_1' => $this->request->getVar('address_line_1'),
            'address_line_2' => $this->request->getVar('address_line_2'),
            'approval_status' => $this->request->getVar('approval_status'),
            'hidden' => $this->request->getVar('hidden'),
            'price_per_day' => $this->request->getVar('price_per_day'),
            'monthly_discount' => $this->request->getVar('monthly_discount') ?? 0,
            'user_id' => $this->getAuth(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }

    function storeImages($resource_id, $resource)
    {
        $uploadPath = 'uploads/images/';
        $this->deleteImages($resource_id, $resource);
        if ($this->request->getFileMultiple('images')) {
            foreach ($this->request->getFileMultiple('images') as $file) {
                $timestamp = date('YmdHis'); // Get current timestamp
                $newFileName = $timestamp . '_' . $file->getClientName();
                $file->move(WRITEPATH . $uploadPath, $newFileName);
                $data = [
                    'resource_id' => $resource_id,
                    'resource' => $resource,
                    'file_name' =>  $file->getClientName(),
                    'path' => $uploadPath . $newFileName,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize()
                ];
                $this->imageModel->save($data);
                $msg = 'Files have been successfully uploaded';
                return $msg;
            }
        }
    }

    private function deleteImages($resource_id, $resource)
    {

        $existingImages = $this->imageModel->where('resource_id', $resource_id)->where('resource', $resource)->findAll();
        if ($existingImages) {
            foreach ($existingImages as $image) {
                // delete existing images file from path
                $file = WRITEPATH . $image['path'];
                if (file_exists($file)) unlink($file);
                $this->imageModel->delete($image['id']);
            }
            return true;
        }
        return false;
    }
}
