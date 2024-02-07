<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once "../config.php";
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username'])) {
    $msg = "Username is required";
    $status = false;
} else {
    $username = mysqli_real_escape_string($conn, $data['username']);
    $result = mysqli_query($conn, "SELECT * FROM user_links WHERE username = '{$username}'");

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $output = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $msg = "Success";
            $status = true;
        } else {
            $msg = "This username does not exists";
            $status = false;
        }
    } else {
        $msg = "Query failed" . mysqli_error($conn);
        $status = false;
    }
}

if (isset($output)) {
    echo json_encode(array('status' => $status, 'message' => $msg, 'url_data' => $output));
} else {
    echo json_encode(array('status' => $status, 'message' => $msg));
}

mysqli_close($conn);
