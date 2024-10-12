<?php
// Allow from any origin
header('Content-Type: application/json');
$allowed_origins = ['http://localhost:5173', 'https://admin.app-minkus.com'];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require 'connection.php';

// Fetch all protocol subscriptions
$sql = "SELECT * FROM protocol_subs";
$result = $conn->query($sql);

$protocols = [];
if ($result->num_rows > 0) {
    // Iterate through each protocol subscription
    while ($row = $result->fetch_assoc()) {
        $protocol_id = $row['id'];
        $user_email = $row['user_email'];

        // Fetch associated forms based on p_id
        $forms = [];

        // Fetch form_a
        $sql_a = "SELECT * FROM form_a WHERE p_id = $protocol_id";
        $result_a = $conn->query($sql_a);
        while ($form_a = $result_a->fetch_assoc()) {
            $forms['form_a'] = $form_a;
        }

        // Fetch form_b
        $sql_b = "SELECT * FROM form_b WHERE p_id = $protocol_id";
        $result_b = $conn->query($sql_b);
        while ($form_b = $result_b->fetch_assoc()) {
            $forms['form_b'] = $form_b;
        }

        // Fetch form_c
        $sql_c = "SELECT * FROM form_c WHERE p_id = $protocol_id";
        $result_c = $conn->query($sql_c);
        while ($form_c = $result_c->fetch_assoc()) {
            $forms['form_c'] = $form_c;
        }

        // Fetch form_d
        $sql_d = "SELECT * FROM form_d WHERE p_id = $protocol_id";
        $result_d = $conn->query($sql_d);
        while ($form_d = $result_d->fetch_assoc()) {
            $forms['form_d'] = $form_d;
        }

        // Fetch form_e
        $sql_e = "SELECT * FROM form_e WHERE p_id = $protocol_id";
        $result_e = $conn->query($sql_e);
        while ($form_e = $result_e->fetch_assoc()) {
            $forms['form_e'] = $form_e;
        }

        // Fetch form_f
        $sql_f = "SELECT * FROM form_f WHERE p_id = $protocol_id";
        $result_f = $conn->query($sql_f);
        while ($form_f = $result_f->fetch_assoc()) {
            $forms['form_f'] = $form_f;
        }

        // Fetch form_g
        $sql_g = "SELECT * FROM form_g WHERE p_id = $protocol_id";
        $result_g = $conn->query($sql_g);
        while ($form_g = $result_g->fetch_assoc()) {
            $forms['form_g'] = $form_g;
        }

        // Add protocol subscription data with its associated forms
        $protocols[] = [
            'protocol_subs' => $row,
            'forms' => $forms
        ];
    }

    echo json_encode([
        "status" => true,
        "message" => "Protocols and forms fetched successfully.",
        "data" => array_reverse($protocols)
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "No protocol subscriptions found.",
        "data" => []
    ]);
}

$conn->close();
