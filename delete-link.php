<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once "../config.php";
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['link_id'])) {
    $msg = "link_id is required";
    $status = false;
} else {
    $link_id = mysqli_real_escape_string($conn, $data['link_id']);
    $result = mysqli_query($conn, "DELETE FROM user_links WHERE social_link_id = '{$link_id}'");

    if ($result) {
        if (mysqli_affected_rows($conn) > 0) {
            $msg = "Link deleted successfully";
            $status = true;
        } else {
            $msg = "This link does not exists";
            $status = false;
        }
    } else {
        $msg = "Query failed" . mysqli_error($conn);
        $status = false;
    }
}

echo json_encode(array('status' => $status, 'message' => $msg));
mysqli_close($conn);
