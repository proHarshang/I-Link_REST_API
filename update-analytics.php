<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once "../config.php";
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data) || count($data) < 2) {
    $msg = "Provide required data to update record";
    $status = false;
} else {
    $username = mysqli_real_escape_string($conn, $data['username']);

    $sql = "SELECT * FROM users WHERE username = '{$username}'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $set_clause = '';
            unset($data['username']);
            foreach ($data as $column => $value) {
                $escaped_value = mysqli_real_escape_string($conn, $value);
                $set_clause .= "$column = $escaped_value, ";
            }
            $set_clause = rtrim($set_clause, ', ');

            $sql = "UPDATE analytics SET $set_clause WHERE username='{$username}'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $msg = "Analytics updated successfully";
                $status = true;
            } else {
                $msg = "Query failed : " . mysqli_error($conn);
                $status = false;
            }
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
    echo json_encode(array('status' => $status, 'message' => $msg));
} else {
    echo json_encode(array('status' => $status, 'message' => $msg));
}

mysqli_close($conn);
