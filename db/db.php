<?php
$servername = "localhost:3306";
$username = "root";
$password = "Shortstory@2025";
$dbname = "shortstory";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
// echo "Connected successfully";

$sql = "CREATE TABLE Books (
        id INT(6) AUTO_INCREMENT PRIMARY KEY,
        Title VARCHAR(30) NOT NULL,
        Authors VARCHAR(30) NOT NULL
    )";

    if ($conn->query($sql) === TRUE) {
          echo "Table has been created successfully";
    } else {
          echo "Error creating table: " . $conn->error;
    }

$i = 1;
while ($i < 10) {
  $sql = "INSERT INTO BOOKS(Title, Authors) VALUES ('Demo Title "." $i', 'Demo Author "." $i')";
if ($conn->query($sql) === TRUE) {
echo " <br />Row has been inserted successfully";
}
else {
         echo "Error inserting row: " . $conn->error;
     }
  $i++;
}


$sql = "SELECT id, Title, Authors FROM books";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            echo "Book Id: " . $row["id"]. ": Title: " 
                . $row["Title"]. ", By: " . $row["Authors"]. "<br /><br />";
          }
    } 
    else {
          echo "No records has been found";
    }
     
    $conn->close();
?>