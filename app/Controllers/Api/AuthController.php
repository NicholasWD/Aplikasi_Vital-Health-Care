<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function register()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $data = [
            'name' => $this->request->getJSON()->name,
            'email' => $this->request->getJSON()->email,
            'password' => $this->request->getJSON()->password,
        ];

        if ($this->userModel->insert($data)) {
            $user = $this->userModel->find($this->userModel->getInsertID());
            unset($user['password']);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'User registered successfully',
                'data' => $user
            ])->setStatusCode(ResponseInterface::HTTP_CREATED);
        }

        return $this->response->setJSON([
            'status' => false,
            'message' => 'Failed to register user'
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function login()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $email = $this->request->getJSON()->email;
        $password = $this->request->getJSON()->password;

        $user = $this->userModel->getUserByEmail($email);

        if (!$user) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid email or password'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        if (!password_verify($password, $user['password'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid email or password'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        // Generate JWT Token
        $key = getenv('JWT_SECRET') ?: 'your_secret_key_here';
        $payload = [
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24 * 7), // 7 days
            'user_id' => $user['id'],
            'email' => $user['email']
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        unset($user['password']);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'user' => $user
            ]
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }

    public function logout()
    {
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Logout successful'
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }

    public function profile()
    {
        $userId = $this->request->userId;
        $user = $this->userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'User not found'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        unset($user['password']);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Profile retrieved successfully',
            'data' => $user
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }
}