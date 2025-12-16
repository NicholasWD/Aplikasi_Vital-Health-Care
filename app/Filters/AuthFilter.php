<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('JWT_SECRET') ?: 'your_secret_key_here';
        $header = $request->getHeader('Authorization');
        
        if (!$header) {
            return service('response')->setJSON([
                'status' => false,
                'message' => 'Token not provided'
            ])->setStatusCode(401);
        }
        
        try {
            $token = str_replace('Bearer ', '', $header->getValue());
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            
            // Store user data in request
            $request->userId = $decoded->user_id;
            $request->userEmail = $decoded->email;
            
        } catch (\Exception $e) {
            return service('response')->setJSON([
                'status' => false,
                'message' => 'Invalid or expired token'
            ])->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}