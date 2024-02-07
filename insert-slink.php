<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once "../config.php";
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data)) {
    $msg = "Username, social-media name and link is required";
    $status = false;
} else {
    if (!isset($data['username'])) {
        $msg = "Username is required";
        $status = false;
    } else if (!isset($data['social_media_name'])) {
        $msg = "Provide social Media name to preform action";
        $status = false;
    } else if (!isset($data['social_link'])) {
        $msg = "Provide social Media link to preform action";
        $status = false;
    } else {
        $username = mysqli_real_escape_string($conn, $data['username']);
        $social_media_name = mysqli_real_escape_string($conn, $data['social_media_name']);
        $social_link = mysqli_real_escape_string($conn, $data['social_link']);

        $sql = "INSERT INTO social_links (username, social_media_name, social_link) VALUES ('$username', '$social_media_name', '$social_link')";
        if (mysqli_query($conn, $sql)) {
            $msg = "Social Media Link added successfully";
            $status = true;
        } else {
            $msg = "Query failed : " . mysqli_error($conn);
            $status = false;
        }
    }
}

echo json_encode(array('status' => $status, 'message' => $msg));
mysqli_close($conn);
