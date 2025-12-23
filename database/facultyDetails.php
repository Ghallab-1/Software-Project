<?php
require_once(__DIR__ . "/database.php");

class faculty_details
{
    public function verifyUser($dbo, $un, $pw)
    {
        $rv = ["id" => -1, "status" => "ERROR"];

        $c = "SELECT id, password FROM faculty_details WHERE user_name = :un";
        $s = $dbo->conn->prepare($c);
        $s->execute([":un" => $un]);

        if ($s->rowCount() > 0) {
            $result = $s->fetch(PDO::FETCH_ASSOC);
            if ($result['password'] === $pw) {
                $rv = ["id" => $result['id'], "status" => "ALL OK"];
            } else {
                $rv = ["id" => $result['id'], "status" => "Wrong password"];
            }
        } else {
            $rv = ["id" => -1, "status" => "USER NAME DOES NOT EXISTS"];
        }

        return $rv;
    }

    public function getCoursesInASession($dbo, $sessionid, $facid)
    {
        $c = "
            SELECT cd.id, cd.code, cd.title
            FROM course_allotment ca
            JOIN course_details cd ON ca.course_id = cd.id
            WHERE ca.faculty_id = :facid
            AND ca.session_id = :sessionid
        ";

        $s = $dbo->conn->prepare($c);
        $s->execute([
            ":facid" => (int)$facid,
            ":sessionid" => (int)$sessionid
        ]);

        return $s->fetchAll(PDO::FETCH_ASSOC);
    }
}
