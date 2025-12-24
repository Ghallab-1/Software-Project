<?php

require_once(__DIR__ . "/database.php");

class SessionDetails
{
    // This database connection will be received as an argument
    public function getSessions($dbo)
    {
        $rv = [];

        $c = "SELECT * FROM session_details";
        $s = $dbo->conn->prepare($c);

        try {
            $s->execute();
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
        }

        return $rv;
    }
}
?>
