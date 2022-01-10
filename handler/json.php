<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Origin: *");

require '../secret/credentials.php';


$board_id = $_GET['boardid'];

function getJSONfromDB($board_id)
{
  // Create connection
  $conn = new mysqli(servername, username, password, dbname);
  mysqli_set_charset($conn, "utf8mb4");
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }


  $select_query = "SELECT trimmed_json FROM boards WHERE board='$board_id'";

  $result = $conn->query($select_query);

  if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
      $board_content = $row["trimmed_json"];
    }
  } else {
    $conn->close();
    die("404");
  }

  $conn->close();
  return $board_content;
}




$starting_content = $current_content = getJSONfromDB($board_id);

$polling_delay_seconds = 0.5;

if ($_GET['instant'] != '1') {
  while ($starting_content == $current_content && ($x < 15 / $polling_delay_seconds)) {
    usleep($polling_delay_seconds * 1000000); // Sleep 0.5s
    $current_content = getJSONfromDB($board_id);
    $x++;
  }
}

$json = "[" . $current_content . "]";
$data = json_decode($json);

echo json_encode($data);
