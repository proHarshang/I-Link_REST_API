<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once "../config.php";
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data)) {
    $msg = "Username, link and link_id is required";
    $status = false;
} else {
    if (!isset($data['username'])) {
        $msg = "Username is required";
        $status = false;
    } else if (!isset($data['link_id'])) {
        $msg = "Provide link_id to preform action";
        $status = false;
    } else {
        $username = mysqli_real_escape_string($conn, $data['username']);
        $link_title = '';
        if (!isset($data['link_title'])) {
            $link_title = '';
        } else {
            $link_title = mysqli_real_escape_string($conn, $data['link_title']);
        }
        $link_id = mysqli_real_escape_string($conn, $data['link_id']);
        if ($link_id == 0) {
            if (!isset($data['link'])) {
                $msg = "Link is required";
                $status = false;
            } else {
                $link = mysqli_real_escape_string($conn, $data['link']);
                $sql = "INSERT INTO user_links (username, link_title, link) VALUES ('$username', '$link_title', '$link')";
                if (mysqli_query($conn, $sql)) {
                    $msg = "Link added successfully";
                    $status = true;
                } else {
                    $msg = "Query failed : " . mysqli_error($conn);
                    $status = false;
                }
            }
        } else if ($link_id != 0) {
            $set_clause = '';
            unset($data['username']);
            unset($data['link_id']);
            foreach ($data as $column => $value) {
                $escaped_value = mysqli_real_escape_string($conn, $value);
                $set_clause .= is_numeric($escaped_value) ? "$column = $escaped_value, " : "$column = '$escaped_value', ";
            }
            $set_clause = rtrim($set_clause, ', ');

            $sql = "UPDATE user_links SET $set_clause WHERE username='{$username}' AND social_link_id = {$link_id}";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $msg = "Link updated successfully";
                $status = true;
            } else {
                $msg = "Query failed : " . mysqli_error($conn);
                $status = false;
            }
        } else {
            $msg = "link_id is not valid";
            $status = false;
        }
    }
}

echo json_encode(array('status' => $status, 'message' => $msg));
mysqli_close($conn);
