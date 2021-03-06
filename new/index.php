<?php
require '../secret/credentials.php';

$board_id = generateBoardID();
createNewBoard($board_id);

function generateBoardID($length = 7)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function createNewBoard($board_id)
{
    $conn = new mysqli(servername, username, password, dbname);
    mysqli_set_charset($conn, "utf8mb4");
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $select_query = "SELECT trimmed_json FROM boards WHERE board='$board_id'";

    $result = $conn->query($select_query);

    if ($result->num_rows > 0) { // In the improbable case that the generated id already exists, generate a new one and try again
        $board_id = generateBoardID();
        createNewBoard($board_id);
    } else {
        $insert_query = "INSERT INTO `boards`(`board`, `trimmed_json`) VALUES ('$board_id','')";
        $result = $conn->query($insert_query);
        header('Location: ' . URL . '/b/' . $board_id);
        exit;
    }
}
