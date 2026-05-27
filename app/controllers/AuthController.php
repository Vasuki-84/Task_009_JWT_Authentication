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
    {   // receives data from postman 
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

        // Create user navigates to user.php
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

        // Generate refresh token
        $refreshToken = bin2hex(random_bytes(40));

        $expiry = date("Y-m-d H:i:s",  time() + $_ENV['REFRESH_TOKEN_EXPIRY']);

        $this->userModel->saveRefreshToken($user['id'],$refreshToken, $expiry);

        // SET HTTP ONLY COOKIE
        setcookie("refresh_token",$refreshToken,
        [
        "expires" => time() + $_ENV['REFRESH_TOKEN_EXPIRY'],
        "path" => "/",
        "httponly" => true
        ]);

        // Postman Response 
       echo json_encode([
       "status" => true,
       "message" => "Login successful",

       "access_token" => $token,

        "access_token_expires_in" =>
        $_ENV['JWT_EXPIRY'],

        "refresh_token" => $refreshToken,

        "refresh_token_expires_in" =>
        $_ENV['REFRESH_TOKEN_EXPIRY']
    ]);
    }

    public function refresh(){
    $refreshToken =
        $_COOKIE['refresh_token'] ?? null;

    if (!$refreshToken) {

        http_response_code(401);

        echo json_encode([
            "status" => false,
            "message" => "Refresh token missing"
        ]);

        return;
    }

    $user = $this->userModel
        ->findByRefreshToken($refreshToken);

    if (!$user) {

        http_response_code(401);

        echo json_encode([
            "status" => false,
            "message" => "Invalid or expired refresh token"
        ]);

        return;
    }

    // Generate new access token
    $newAccessToken = JWT::generate($user);
 
   echo json_encode([
        "status" => true,
        "message" => "Token refreshed successfully",
        "access_token" => $newAccessToken,
        "access_token_expires_in" => $_ENV['JWT_EXPIRY']
    ]);
    }

}