<?php

class User
{
    private $conn;

    private $table = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Find user by email
    public function findByEmail($email)
    {
        $query = "SELECT * FROM {$this->table} WHERE email = ?";

        $stmt = mysqli_prepare($this->conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    // Create new user
    public function create($name, $email, $password)
    {
        $query = "INSERT INTO {$this->table}
                  (
                    name,
                    email,
                    password
                  )
                  VALUES
                  (?, ?, ?)";

        $stmt = mysqli_prepare($this->conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "sss",
            $name,
            $email,
            $password
        );

        return mysqli_stmt_execute($stmt);
    }
}