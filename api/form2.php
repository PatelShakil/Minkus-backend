<?php

header('Content-Type: application/json');
require('connection.php'); // Assuming this file contains the $conn variable for database connection

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Define the folder to save the images
    $targetDir = "assets/";

    try {
        // Generate a unique name for the image file
        $fileName = date('Y_m_d_H_i_s') . ".png";
        $targetFilePath = $targetDir . $fileName;

        // Initialize response array
        $response = [
            'success' => false,
            'message' => '',
            'data' => null
        ];

        // Check if the directory exists or create it
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Check if the image file is valid and move it to the target directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            // Collect other form data
            $name = $_POST['name'];
            $run = $_POST['run'];
            $obj = $_POST['obj'];
            $isJa1 = filter_var($_POST['isJa1'], FILTER_VALIDATE_BOOLEAN);
            $isJa2 = filter_var($_POST['isJa2'], FILTER_VALIDATE_BOOLEAN);
            $isJa3 = filter_var($_POST['isJa3'], FILTER_VALIDATE_BOOLEAN);
            $isJa4 = filter_var($_POST['isJa4'], FILTER_VALIDATE_BOOLEAN);
            $sauCount = intval($_POST['sauCount']);
            $user_email = $_POST['user_email'];
            $lat = doubleval($_POST['lat']);
            $long = doubleval($_POST['long']);

            // Construct the response data
            $response['status'] = true;
            $response['message'] = 'Form submitted successfully';
            $response['data'] = [
                'name' => $name,
                'run' => $run,
                'obj' => $obj,
                'isJa1' => $isJa1,
                'isJa2' => $isJa2,
                'isJa3' => $isJa3,
                'isJa4' => $isJa4,
                'sauCount' => $sauCount,
                'user_email' => $user_email,
                'imagePath' => "https://app-minkus.com/api/$targetFilePath"
            ];

            // SQL statement with placeholders
            $sql = "INSERT INTO form_b (name, signature, state, rundgang, sauberkit, checkbox1, checkbox2,checkbox3,checkbox4, user_email,lat,lon) VALUES (?, ?, ?, ?, ?,?,?,?,?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                // Bind the parameters to the SQL query
                $stmt->bind_param(
                    "ssssiiiiisdd", // Type of each parameter (string, string, string, string, int, int, string)
                    $name,
                    $response['data']['imagePath'],
                    $obj,
                    $run,
                    $sauCount,
                    $isJa1,
                    $isJa2,
                    $isJa3,
                    $isJa4,
                    $user_email,
                    $lat,
                    $long
                );

                // Execute the statement
                if ($stmt->execute()) {
                    $response['status'] = true;
                    $response['message'] = 'Form submitted successfully and data stored in database';
                    $response['data'] = "Form Submitted Successfully";
                } else {
                    $response['status'] = false;
                    $response['message'] = 'Database error: ' . $stmt->error;
                }

                $stmt->close();
            } else {
                $response['status'] = false;
                $response['message'] = 'Failed to prepare SQL statement: ' . $conn->error;
            }
        } else {
            $response['message'] = 'Failed to move uploaded file.';
        }
    } catch (Exception $e) {
        // If an error occurred during file upload, log it
        error_log($e->getMessage());
        $response['message'] = $e->getMessage();
    }

    // Send JSON response back to the client
    echo json_encode($response);
} else {
    // If the request method is not POST, return an error
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Please use POST.'
    ]);
}
