<?php
require_once(__DIR__ . "/database.php");

class faculty_details
{
    public function verifyUser($dbo, $un, $pw)
    {
        $rv = ["id" => -1, "status" => "ERROR"];

        $s = $dbo->conn->prepare(
            "SELECT id, password FROM faculty_details WHERE user_name = :un"
        );
        $s->execute([":un" => $un]);

        if ($s->rowCount() > 0) {
            $row = $s->fetch(PDO::FETCH_ASSOC);
            if ($row["password"] === $pw) {
                $rv = ["id" => $row["id"], "status" => "ALL OK"];
            } else {
                $rv = ["id" => $row["id"], "status" => "Wrong password"];
            }
        } else {
            $rv = ["id" => -1, "status" => "USER NAME DOES NOT EXISTS"];
        }

        return $rv;
    }

    public function getCoursesInASession($dbo, $sessionid, $facid)
    {
        $stmt = $dbo->conn->prepare(
            "SELECT cd.id, cd.code, cd.title
             FROM course_allotment ca
             JOIN course_details cd ON ca.course_id = cd.id
             WHERE ca.faculty_id = :facid
             AND ca.session_id = :sessionid"
        );

        $stmt->execute([
            ":facid" => (int)$facid,
            ":sessionid" => (int)$sessionid
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
