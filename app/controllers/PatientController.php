<?php

class PatientController
{
    private $patientModel;

    public function __construct($db)
    {
        $this->patientModel = new Patient($db);
    }

    // Get all patients
    public function index()
    {
        AuthMiddleware::handle();

        $patients = $this->patientModel->getAll();

        echo json_encode([
            "status" => true,
            "data" => $patients
        ]);
    }

    // Create patient
    public function store()
    {
        AuthMiddleware::handle();

        $data = $_REQUEST['body'];

        if (
            empty($data['name']) ||
            empty($data['age']) ||
            empty($data['gender']) ||
            empty($data['phone']) ||
            empty($data['address'])
        ) {

            http_response_code(400);

            echo json_encode([
                "status" => false,
                "message" => "All fields are required"
            ]);

            return;
        }

        $created = $this->patientModel->create($data);

        if ($created) {

            echo json_encode([
                "status" => true,
                "message" => "Patient created successfully"
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" => "Failed to create patient"
            ]);
        }
    }

    // Update patient
    public function update($id)
    {
        AuthMiddleware::handle();

        $data = $_REQUEST['body'];

        $patient = $this->patientModel->findById($id);

        if (!$patient) {

            http_response_code(404);

            echo json_encode([
                "status" => false,
                "message" => "Patient not found"
            ]);

            return;
        }

        $updated = $this->patientModel->update($id, $data);

        if ($updated) {

            echo json_encode([
                "status" => true,
                "message" => "Patient updated successfully"
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" => "Failed to update patient"
            ]);
        }
    }

    // Delete patient
    public function delete($id)
    {
        AuthMiddleware::handle();

        $patient = $this->patientModel->findById($id);

        if (!$patient) {

            http_response_code(404);

            echo json_encode([
                "status" => false,
                "message" => "Patient not found"
            ]);

            return;
        }

        $deleted = $this->patientModel->delete($id);

        if ($deleted) {

            echo json_encode([
                "status" => true,
                "message" => "Patient deleted successfully"
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" => "Failed to delete patient"
            ]);
        }
    }
}