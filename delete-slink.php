<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once "../config.php";
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    $msg = "Id is required to delete social-media link";
    $status = false;
} else {
    $id = mysqli_real_escape_string($conn, $data['id']);
    $result = mysqli_query($conn, "DELETE FROM social_links WHERE id = {$id}");

    if ($result) {
        if (mysqli_affected_rows($conn) > 0) {
            $msg = "Social media link deleted successfully";
            $status = true;
        } else {
            $msg = "This social media link does not exists";
            $status = false;
        }
    } else {
        $msg = "Query failed" . mysqli_error($conn);
        $status = false;
    }
}

echo json_encode(array('status' => $status, 'message' => $msg));
mysqli_close($conn);
