<?php

namespace App\Controllers;

class Test extends BaseController
{
    public function dbTest()
    {
        try {
            $db = \Config\Database::connect();
            
            // Test koneksi
            $result = $db->query("SELECT VERSION() as version")->getRow();
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Database connected!',
                'version' => $result->version ?? 'Unknown',
                'hostname' => getenv('database.default.hostname'),
                'database' => getenv('database.default.database'),
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function userTest()
    {
        try {
            $db = \Config\Database::connect();
            $users = $db->query("SELECT id, name, email FROM users LIMIT 5")->getResultArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Users fetched successfully',
                'data' => $users,
                'total' => count($users)
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function loginTest()
    {
        $email = 'admin@example.com';
        $password = 'admin123';
        
        try {
            $db = \Config\Database::connect();
            $user = $db->table('users')->where('email', $email)->get()->getRow();
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not found with email: ' . $email
                ]);
            }
            
            $passwordMatch = password_verify($password, $user->password);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Login test result',
                'user_found' => true,
                'email' => $user->email,
                'password_stored' => substr($user->password, 0, 20) . '...',
                'password_match' => $passwordMatch,
                'provided_password' => $password
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
