<?php

class Patient
{
    private $conn;

    private $table = "patients";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get all patients
    public function getAll()
    {
        $query = "SELECT * FROM {$this->table}";

        $result = mysqli_query($this->conn, $query);

        $patients = [];

        while ($row = mysqli_fetch_assoc($result)) {

            $patients[] = $row;
        }

        return $patients;
    }

    // Create patient
    public function create($data)
    {
        $query = "INSERT INTO {$this->table}
                  (
                    name,
                    age,
                    gender,
                    phone,
                    address
                  )
                  VALUES
                  (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "sisss",
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone'],
            $data['address']
        );

        return mysqli_stmt_execute($stmt);
    }

    // Update patient
    public function update($id, $data)
    {
        $query = "UPDATE {$this->table}
                  SET
                  name = ?,
                  age = ?,
                  gender = ?,
                  phone = ?,
                  address = ?
                  WHERE id = ?";

        $stmt = mysqli_prepare($this->conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "sisssi",
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone'],
            $data['address'],
            $id
        );

        return mysqli_stmt_execute($stmt);
    }

    // Delete patient
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = ?";

        $stmt = mysqli_prepare($this->conn, $query);

        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }

      // Find patient by ID
    public function findById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";

        $stmt = mysqli_prepare($this->conn, $query);

        mysqli_stmt_bind_param($stmt, "i", $id);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

  
}