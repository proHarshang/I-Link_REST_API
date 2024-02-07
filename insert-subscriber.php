<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once "../config.php";
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['subscriber_email'])) {
    $msg = "Username and subscriber_email is required";
    $status = false;
} else {
    $username = mysqli_real_escape_string($conn, $data['username']);
    $subscriber_email = mysqli_real_escape_string($conn, $data['subscriber_email']);

    $selectQuery = mysqli_query($conn, "SELECT username FROM users WHERE username = '{$username}'");

    if ($selectQuery) {
        if (mysqli_num_rows($selectQuery) >= 1) {
            $sql = "INSERT INTO subscribers (id, username, subscriber_email) VALUES ('NULL', '{$username}', '{$subscriber_email}')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                // Increament the subscriber number in analytics table 
                $GET_oldSubsNumbers = mysqli_query($conn, "SELECT subscriber FROM analytics WHERE username = '{$username}'");
                $oldSubsNumbers = intval(mysqli_fetch_assoc($GET_oldSubsNumbers)['subscriber']);
                $newSubsNumbers = $oldSubsNumbers = $oldSubsNumbers + 1;
                $UPDATE_oldSubsNumbers = mysqli_query($conn, "UPDATE analytics SET subscriber = '{$newSubsNumbers}' WHERE username = '{$username}'");
                if ($GET_oldSubsNumbers and $UPDATE_oldSubsNumbers) {
                    $msg = "Subscriber inserted successfully";
                    $status = true;
                } else {
                    $msg = "Query Failed";
                    $status = false;
                }
            } else {
                $msg = "Query Failed";
                $status = false;
            }
        } else {
            $msg = "User does not exist";
            $status = false;
        }
    }
}

echo json_encode(array('status' => $status, 'message' => $msg));
mysqli_close($conn);
