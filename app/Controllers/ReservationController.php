<?php

namespace App\Controllers;

use App\Models\Token;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Reservation;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;

class ReservationController extends BaseController
{
    use ResponseTrait;

    private $reservationModel;

    public function __construct()
    {
        $this->reservationModel = new Reservation();
    }

    public function index()
    {
        $limit = $this->request->getVar('limit') ?? 10;
        $offset = $this->request->getVar('offset') ?? 0;
        $reservations = $this->reservationModel->getAllReservations($limit, $offset);
        $response = [
            'status' => true,
            'message' => 'reservations fetched successfully',
            'data' => $reservations,
            'total' => $this->reservationModel->getReservationsCount(),
            'limit' => $limit,
            'offset' => $offset,
            'url' => base_url() . 'api/reservations'

        ];
        return $this->respond($response);
    }

    public function show($reservationId)
    {
        $reservation = $this->reservationModel->getReservation($reservationId);
        if (!$reservation) return $this->failNotFound('reservation not found');
        return $this->respond($reservation);
    }

    public function store()
    {
        $rules = $this->reservationModel->validationRules;
        $data = $this->getData();

        if ($this->validate($rules, $data)) {
            $this->reservationModel->save($data);
            return $this->respondCreated(['message' => 'reservation created']);
        }
        return $this->fail($this->validator->getErrors());
    }
    public function update($reservationId)
    {
        $rules = $this->reservationModel->validationRules;
        $data = $this->getData();
        if ($this->validate($rules, $data)) {
            $updated = $this->reservationModel->update($reservationId, $data);
            if ($updated) {
                return $this->respond(['message' => 'reservation updated successfully']);
            } else {
                return $this->failServerError('Failed to update reservation');
            }
        }
        return $this->fail($this->validator->getErrors());
    }
    public function delete($reservationId)
    {
        $reservation = $this->reservationModel->find($reservationId); // Assuming you have a method to find an reservation by ID
        if (!$reservation) return $this->failNotFound('reservation not found');
        if ($this->reservationModel->delete($reservationId)) return $this->respond(['message' => 'reservation deleted successfully']);
        return $this->failServerError('Failed to delete reservation');
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
        // $email = $decoded->email;
    }

    private function getData()
    {
        return [
            'office_id' => $this->request->getVar('office_id'),
            'price' => $this->request->getVar('price'),
            'status' => $this->request->getVar('status'),
            'start_date' => $this->request->getVar('start_date'),
            'end_date' => $this->request->getVar('end_date'),
            'user_id' => $this->getAuth(),
        ];
    }
}
