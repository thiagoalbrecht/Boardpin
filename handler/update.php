<?php
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Origin: *");

require "functions.php";
require '../secret/credentials.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // collect value of input field
    $content = $_POST['json'];
    // Create connection
    $conn = new mysqli(servername, username, password, dbname);
    mysqli_set_charset($conn,"utf8mb4");
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    
    
    $board_id = $_POST['boardid'];
    $select_query = "SELECT trimmed_json FROM boards WHERE board='$board_id'";

    $result = $conn->query($select_query);
    
    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
        $board_content = $row["trimmed_json"];
      }
    }
    
    switch($_POST['task']){
        case "add":
            if (strlen($board_content) <= 1){
                $content = $_POST['json'];
            } else{
                $content = $_POST['json'].",".$board_content;
            }
            break;
        case "remove":
            //$content = str_replace(stripslashes($_POST['json']),"",$board_content); // Remove target string from JSON
            //$content = str_replace(",,",",",$content); // Remove delimiter from previous record
            //echo stripslashes($_POST['json']) . "<br>" . "in:" . "<br>" . $board_content . "<br> Is: <br>" . $content;
            $board_content_array = json_decode("[".$board_content."]");
            $requested_array= json_decode("[".$_POST['json']."]");
            $id_to_delete = $requested_array[0] -> Id;
            $key_of_found_id = array_search($id_to_delete, array_column($board_content_array, 'Id'));
            array_splice($board_content_array, $key_of_found_id, 1);
            $content = trim(json_encode($board_content_array),"[]");
            
            break;
    }

    
    if (strpos($content, 'Text":"/clear"') !== false) $content = "";
    if (strpos($content, 'Text":"/demo"') !== false) $content = '{"Id":7,"Text":"Vestibulum sed aliquet lorem","IsDone":false},{"Id":6,"Text":"Praesent rutrum lorem sed erat convallis","IsDone":false},{"Id":5,"Text":"Curabitur consectetur feugiat dolor sed dictum","IsDone":false},{"Id":4,"Text":"Aliquam ornare lectus quis lorem volutpat","IsDone":false},{"Id":3,"Text":"Nunc aliquet quam velit","IsDone":false},{"Id":2,"Text":"consectetur adipiscing elit","IsDone":false},{"Id":1,"Text":"Lorem ipsum dolor sit amet","IsDone":false}'; // Create a demo list
    
    $content = checkDuplicates($content);

    $stmt = $conn->prepare("UPDATE boards SET trimmed_json=? WHERE board=?");
    $stmt->bind_param("ss", $content, $board_id);
    $stmt->execute();
    $stmt->close();

    $conn->close();
} else {
    header("HTTP/1.1 400 Bad Request");
}