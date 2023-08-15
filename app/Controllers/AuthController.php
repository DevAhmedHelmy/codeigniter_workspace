<?php

namespace App\Controllers;

use Exception;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\Token;

class AuthController extends BaseController
{
    use ResponseTrait;
    private $userModel, $tokenModel;
    const HASH_ALGORITHM = 'HS256';
    public function __construct()
    {
        $this->userModel = new User();
        $this->tokenModel = new Token();
    }
    public function login()
    {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->userModel->where('email', $email)->first();

        if (is_null($user)) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }

        $pwd_verify = password_verify($password, $user['password']);

        if (!$pwd_verify) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }

        $key = getenv('JWT_SECRET');

        $iat = time(); // current timestamp value
        $exp = $iat + 3600;

        $payload = array(
            "iss" => "Issuer of the JWT",
            "aud" => "Audience that the JWT",
            "sub" => "Subject of the JWT",
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "email" => $user['email'],
            'user_id' => $user['id']
        );

        $token = JWT::encode($payload, $key, self::HASH_ALGORITHM);
        $this->tokenModel->where('user_id', $user['id'])->delete();
        $this->tokenModel->save([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'token' => $token,
            'expiration_time' => $exp,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $response = [
            'message' => 'Login Successful',
            'user' => $user,
            'access_token' => $token
        ];

        return $this->respond($response, 200);
    }


    public function register()
    {
        $rules = [
            'name' => ['rules' => 'required|min_length[4]|max_length[255]'],
            'email' => ['rules' => 'required|min_length[4]|max_length[255]|valid_email|is_unique[users.email]'],
            'password' => ['rules' => 'required|min_length[8]|max_length[255]'],
            'confirm_password'  => ['label' => 'confirm password', 'rules' => 'matches[password]']
        ];

        if ($this->validate($rules)) {
            $data = [
                'email'    => $this->request->getVar('email'),
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
            ];
            $this->userModel->save($data);

            return $this->respond(['message' => 'Registered Successfully'], 200);
        } else {
            $response = [
                'errors' => $this->validator->getErrors(),
                'message' => 'Invalid Inputs'
            ];
            return $this->fail($response, 409);
        }
    }

    public function logout()
    {

        $key = getenv('JWT_SECRET');
        $header = $this->request->getHeaderLine('Authorization');
        $token = null;
        // extract the token from the header
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) $token = $matches[1];
        }
        try {
            $decoded =  JWT::decode($token, new Key($key, self::HASH_ALGORITHM));
            $this->tokenModel->where('email', $decoded->email)->where('token', $token)->delete();
        } catch (Exception $e) {
            // Handle any token decoding errors
            return $this->respond(['message' => 'Invalid token'], 401);
        }
        return $this->respond(['message' => 'Logged out successfully']);
    }
}
