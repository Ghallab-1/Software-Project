   <?php
   $dbo = new Database();

   echo json_encode([
    "db" => $dbo->conn->query("SELECT DATABASE()")->fetchColumn()
]);
die;
