<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once "../config.php";
$data = json_decode(file_get_contents("php://input"), true);
$status = null;
$msg = null;

function verifyToken($token, $conn)
{
    $selectToken = mysqli_query($conn, "SELECT * FROM access_token WHERE token = '{$token}'");
    if ($selectToken) {
        $tokenData = mysqli_fetch_assoc($selectToken);
        return (!$tokenData || mysqli_num_rows($selectToken) <= 0 || $tokenData['status'] != 1) ? false :  true;
    } else {
        return false;
    }
}

if (!isset($data['username']) || !isset($data['email']) || !isset($data['password']) || !isset($data['actionCode'])) {
    $msg = "Provide Data to be inserted";
    $status = false;
} else {
    $username = mysqli_real_escape_string($conn, $data['username']);
    $actionCode = mysqli_real_escape_string($conn, $data['actionCode']);

    if ($data['actionCode'] === 0) {
        $email = mysqli_real_escape_string($conn, $data['email']);
        $password = mysqli_real_escape_string($conn, $data['password']);
        // Insert 
        // Extract the token from the Authorization header
        $headers = getallheaders();
        $authorizationHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if ($authorizationHeader) {
            $token = str_replace('Bearer ', '', $authorizationHeader);
            if (verifyToken($token, $conn)) {

                $selectQuery = mysqli_query($conn, "SELECT username FROM users WHERE email = '{$email}' OR username = '{$username}'");

                if ($selectQuery) {
                    if (mysqli_num_rows($selectQuery) >= 1) {
                        $msg = "User already exists";
                        $status = false;
                    } else {
                        $insertQuery = mysqli_query($conn, "INSERT INTO users (username, email, password) VALUES ('{$username}', '{$email}', '{$password}')");
                        if ($insertQuery) {
                            // Insert username column in analytics table
                            $insertIntoAnalytics = mysqli_query($conn, "INSERT INTO analytics (username, views, clicks, subscriber) VALUES ('{$username}', 0, 0, 0)");
                            if ($insertIntoAnalytics) {
                                $msg = "Register successfully";
                                $status = true;
                            } else {
                                $msg = "Query Failed";
                                $status = false;
                            }
                        }
                    }
                } else {
                    $msg = "Query Failed";
                    $status = false;
                }
            } else {
                $msg = "Invalid access token";
                $status = false;
            }
        }
    } else if ($data['actionCode'] === 1) {
        // update 
        if (!isset($data['bio']) || !isset($data['title'])) {
            $msg = "Provide username, title and bio to be inserted";
            $status = false;
        } else {
            $bio = mysqli_real_escape_string($conn, $data['bio']);
            $title = mysqli_real_escape_string($conn, $data['title']);
            $set_clause = '';
            unset($data['username']);
            unset($data['actionCode']);
            foreach ($data as $column => $value) {
                $escaped_value = mysqli_real_escape_string($conn, $value);
                $set_clause .= "$column = '$escaped_value', ";
            }
            $set_clause = rtrim($set_clause, ', ');

            // update bio and title username column in analytics table
            $updateQuery = mysqli_query($conn, "UPDATE users SET $set_clause WHERE username='{$username}'");
            if ($updateQuery) {
                $msg = "Updated successfully";
                $status = true;
            } else {
                $msg = "Query Failed";
                $status = false;
            }
        }
    } else {
        $msg = "Provide a valid action code";
        $status = false;
    }
}

echo json_encode(array('status' => $status, 'message' => $msg));
mysqli_close($conn);
