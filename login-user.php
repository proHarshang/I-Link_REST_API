<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once "../config.php";
$data = json_decode(file_get_contents("php://input"), true);

// function verifyToken($token, $conn)
// {
//     $tokenQuery = "SELECT * FROM access_token WHERE token = '{$token}'";
//     $selectToken = mysqli_query($conn, $tokenQuery);
//     if ($selectToken) {
//         $tokenData = mysqli_fetch_assoc($selectToken);
//         return (!$tokenData || mysqli_num_rows($selectToken) <= 0 || $tokenData['status'] != 1) ? false :  true;
//     } else {
//         return false;
//     }
// }

$the_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZDFkNmVkMzZjN2VhN2U3NmM1MjFjNjY1MzM0NGFkYjFlZWRkZmEzMTVmZTQ4NTM4ZjgxNGI2MDc5MDMwMzdkYjk0YWQxYWI1MDNhODk1MmEiLCJpYXQiOjE3MDI1NjA4MTcuNjE4MzM3LCJuYmYiOjE3MDI1NjA4MTcuNjE4MzQyLCJleHAiOjE3MzQxODMyMTcuNjE1NTE2LCJzdWIiOiIxIiwic2NvcGVzIjpbInVzZXItdGFzayJdfQ.E7YwBf20j06RRo2R4e130qnZRYFM4sg9KGVo5nUJ1Xj_xxMLdpGTK4BsNWdtH9ZtF71XoaoNeD8vFPfO1t67LcXEct7GlBq2zLnfY-_tkYQZavKQ6A2VUfnnoL9ghr74-QoRDGUZj6yZjekCCzeos-p162uuB_t-0qI-TX369VR7zmJ5YHcohAsN9IATFFScGVL_lw673gPteQTxBkkiWLQskivwtCINrzblH7Wt6M671U1sdYXMne0bnc_INMONoKxJQkzhPfFqkKZz-TxBbjDZDuTFt5OoocnIip4GdCQRhIAfsSKBm7vEpu73OLgumqtI0nMSOegcVkaHQr5AIxHmMA0s8BdM19xcJDg6b4lQO39SseKdDwu-lz_PeInK1ehJg4saAM5wDcOKiu91Py3-8gV6y-QMcLvu5m7QqpAYl2pk7INkK_hJMS81mP-MzFcDHXTukv_cNlIKfP7XGOBZgcSH43-yJ20R7emfIaWYxeuN2-gcV_mx3L_QuEdg3VtYLVj-AoiAiyAVS8Uca7z9rjwgmQGneWsvN5GEw6yN8NqFvKo_SHDHnU2doII4FXroTzfK0Ybv5ue2cVP6c5EkjVSTs0ol0NHmwdJPOF9hFH_KJ98KvS7AqlFbCtUnlmyTUyf5RK2ZUYvz4RdDhUlI9wk2rDWg8HXy8DFWgLM';

if (!isset($data['email']) || !isset($data['password'])) {
    $msg = "Provide Data to be inserted";
    $status = false;
} else {
    // $token = mysqli_real_escape_string($conn, $data['token']);
    // if (verifyToken($token, $conn)) {
    $email = mysqli_real_escape_string($conn, $data['email']);
    $password = mysqli_real_escape_string($conn, $data['password']);

    $selectQuery = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}' AND password = '{$password}'");

    if ($selectQuery) {
        if (mysqli_num_rows($selectQuery) <= 0) {
            $msg = "Email or Password is incorrect";
            $status = false;
        } else {
            $output = mysqli_fetch_object($selectQuery);
            $msg = "Login successful";
            $status = true;
        }
    } else {
        $msg = "Query Failed";
        $status = false;
    }
    // } else {
    //     $msg = "Invalid access token";
    //     $status = false;
    // }
}

if (isset($output)) {
    echo json_encode(array('status' => $status, 'message' => $msg, 'user_details' => $output, 'token' => $the_token));
} else {
    echo json_encode(array('status' => $status, 'message' => $msg));
}

mysqli_close($conn);
