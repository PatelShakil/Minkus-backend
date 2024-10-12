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
            $user_email = $_POST['user_email'];
            $lat = doubleval($_POST['lat']);
            $long = doubleval($_POST['long']);
            $p_id = $_POST['p_id'];

            // Construct the response data
            $response['status'] = true;
            $response['message'] = 'Form submitted successfully';
            $response['data'] = [
                'name' => $name,
                'obj' => $obj,
                'run'=>$run,
                'user_email' => $user_email,
                'imagePath' => "https://app-minkus.com/api/$targetFilePath"
            ];

            // SQL statement with placeholders
            $sql = "INSERT INTO form_g (name, signature, state,rundgang,  user_email,lat,lon,p_id) VALUES (?,?,?,?, ?,?,  ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                // Bind the parameters to the SQL query
                $stmt->bind_param(
                    "sssssiii", // Type of each parameter (string, string, string, string, int, int, string)
                    $name,
                    $response['data']['imagePath'],
                    $obj,
                    $run,
                    $user_email,
                    $lat,
                    $long,
                    $p_id
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
