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

    public function categoriesTest()
    {
        try {
            $db = \Config\Database::connect();
            
            // Check if table exists
            if (!$db->tableExists('vital_categories')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Table vital_categories does not exist'
                ])->setStatusCode(500);
            }
            
            // Check table structure
            $fields = $db->getFieldData('vital_categories');
            $fieldNames = array_map(fn($field) => $field->name, $fields);
            
            // Get all categories
            $categories = $db->table('vital_categories')->get()->getResultArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Categories test successful',
                'table_exists' => true,
                'columns' => $fieldNames,
                'total_categories' => count($categories),
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ])->setStatusCode(500);
        }
    }
}
