<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once "../config.php";
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data)) {
    $msg = "Username is required to get records";
    $status = false;
} else {
    $username = mysqli_real_escape_string($conn, $data['username']);

    $sql = "SELECT * FROM social_links WHERE username = '{$username}'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $output = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $msg = "Success";
            $status = true;
        } else {
            $msg = "This user does not exists";
            $status = false;
        }
    } else {
        $msg = "Query failed";
        $status = false;
    }
}

if (isset($output)) {
    echo json_encode(array('status' => $status, 'message' => $msg, 'social_links' => $output));
} else {
    echo json_encode(array('status' => $status, 'message' => $msg));
}

mysqli_close($conn);
