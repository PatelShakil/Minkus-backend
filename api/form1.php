<?php

header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Define the folder to save the images
    $targetDir = "assets/";

    // Generate a unique name for the image file
    $fileName = basename($_FILES['image']['name']);
    $targetFilePath = $targetDir . $fileName;

    // Initialize response array
    $response = [
        'success' => false,
        'message' => '',
        'data' => $_FILES
    ];

    // Check if the directory exists or create it
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Check if the image file is valid and move it to the target directory
    if (move_uploaded_file($_FILES['image'], $targetFilePath)) {
        // Collect other form data
        $name = $_POST['name'];
        $run = $_POST['run'];
        $obj = $_POST['obj'];
        $isJa1 = filter_var($_POST['isJa1'], FILTER_VALIDATE_BOOLEAN);
        $isJa2 = filter_var($_POST['isJa2'], FILTER_VALIDATE_BOOLEAN);
        $sauCount = intval($_POST['sauCount']);

        // Construct the response data
        $response['success'] = true;
        $response['message'] = 'Form submitted successfully';
        $response['data'] = [
            'name' => $name,
            'run' => $run,
            'obj' => $obj,
            'isJa1' => $isJa1,
            'isJa2' => $isJa2,
            'sauCount' => $sauCount,
            'imagePath' => $targetFilePath
        ];
    } else {
        $response['message'] = 'Failed to move uploaded file.';
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
