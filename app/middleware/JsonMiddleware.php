<?php

class JsonMiddleware
{
    public static function handle()
    {
        header("Content-Type: application/json");

        $method = $_SERVER['REQUEST_METHOD'];

        // Allow only JSON for POST, PUT, PATCH
        if (
            in_array(
                $method,
                ['POST', 'PUT', 'PATCH']
            )
        ) {

            if (
                !isset($_SERVER['CONTENT_TYPE']) ||
                strpos(
                    $_SERVER['CONTENT_TYPE'],
                    'application/json'
                ) === false
            ) {

                http_response_code(400);

                echo json_encode([
                    "status" => false,
                    "message" => "Content-Type must be application/json"
                ]);

                exit;
            }

            $input = file_get_contents(
                "php://input"
            );

            // Empty body
            if (empty($input)) {

                http_response_code(400);

                echo json_encode([
                    "status" => false,
                    "message" => "Empty JSON body"
                ]);

                exit;
            }

            $data = json_decode($input, true);

            // Invalid JSON
            if (
                json_last_error() !== JSON_ERROR_NONE
            ) {

                http_response_code(400);

                echo json_encode([
                    "status" => false,
                    "message" => "Invalid JSON"
                ]);

                exit;
            }

            $_REQUEST['body'] = $data;
        }
    }
}