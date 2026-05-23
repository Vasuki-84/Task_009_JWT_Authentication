<?php

class Database
{
    public $conn;

    public function connect()
    {
        // Load .env file
        $envPath = __DIR__ . '/../../.env';

        if (!file_exists($envPath)) {
            die(".env file not found");
        }

        $env = parse_ini_file($envPath);

        if (!$env) {
            die("Failed to read .env file");
        }

        // Read values from .env
        $host = $env['DB_HOST'] ?? 'localhost';
        $user = $env['DB_USER'] ?? 'root';
        $pass = $env['DB_PASS'] ?? '';
        $db   = $env['DB_NAME'] ?? '';

        // Default MySQL port (WAMP/XAMPP)
        $port = 3308;

        // Create connection
        $this->conn = mysqli_connect($host, $user, $pass, $db, $port);

        // Check connection
        if (!$this->conn) {
            die("DB Connection Failed: " . mysqli_connect_error());
        }

        return $this->conn;
    }
}