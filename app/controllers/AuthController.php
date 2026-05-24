<?php

class AuthController
{
    private $userModel;

    public function __construct($db)
    {
        $this->userModel = new User($db);
    }

    // Register User
    public function register()
    {
        $data = $_REQUEST['body'];

        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Validation
        if (!$name || !$email || !$password) {

            http_response_code(400);

            echo json_encode([
                "status" => false,
                "message" => "All fields are required"
            ]);

            return;
        }

        // Check existing email
        $existingUser = $this->userModel->findByEmail($email);

        if ($existingUser) {

            http_response_code(400);

            echo json_encode([
                "status" => false,
                "message" => "Email already exists"
            ]);

            return;
        }

        // Hash password
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT   // Uses secure hashing algorithm automatically.
        );

        // Create user
        $created = $this->userModel->create(
            $name,
            $email,
            $hashedPassword
        );

        if ($created) {

            echo json_encode([
                "status" => true,
                "message" => "User registered successfully"
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" => "Registration failed"
            ]);
        }
    }

    // Login User
    public function login()
    {
        $data = $_REQUEST['body'];

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Check user
        $user = $this->userModel->findByEmail($email);

        if (
            !$user ||
            !password_verify(
                $password,
                $user['password']
            )
        ) {

            http_response_code(401);

            echo json_encode([
                "status" => false,
                "message" => "Invalid credentials"
            ]);

            return;
        }

        // Generate JWT
        $token = JWT::generate($user);

        echo json_encode([
            "status" => true,
            "message" => "Login successful",
            "token" => $token,
            "expires_in" => $_ENV['JWT_EXPIRY']
        ]);
    }
}