<?php
// Allow from any origin
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");  // Specify your React dev server's URL
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require 'connection.php';


function getForm($tbl, $conn)
{
    $sql = "SELECT * FROM $tbl";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();  // Get the result set from the prepared statement

        if ($result->num_rows > 0) {
            $data = $result->fetch_all(MYSQLI_ASSOC);  // Fetch all users as an associative array

            $response['status'] = true;
            $response['data'] = $data;
            $response['message'] = 'Users found';
        } else {
            $response['status'] = false;
            $response['message'] = 'No users found';
        }

        $stmt->close();
    } else {
        $response['status'] = false;
        $response['message'] = 'Failed to prepare the statement';
    }
    return $response;
}

// Initialize response array
$response = array();
$response['data'] = null;
$response['message'] = "";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {

    $id = $_GET["id"];

    switch ($id) {
        case 1: {
                $response = getForm("form_a", $conn);
                break;
            }
        case 2: {
                $response = getForm("form_b", $conn);
                break;
            }
        case 3: {
                $response = getForm("form_c", $conn);
                break;
            }
        case 4: {
                $response = getForm("form_d", $conn);
                break;
            }
        case 5: {
                $response = getForm("form_e", $conn);
                break;
            }
        case 6: {
                $response = getForm("form_f", $conn);
                break;
            }
        case 7: {
                $response = getForm("form_f", $conn);
                break;
            }
            default:{
                $response['status'] = false;
                $response['message'] = 'Invalid ID requested';
            }
    }
} else {
    $response['status'] = false;
    $response['message'] = 'Invalid request method';
}

// Close connection
$conn->close();

// Output the response in JSON format
echo json_encode($response);
