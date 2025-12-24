<?php
require_once(__DIR__ . "/database.php");

class faculty_details
{
public function verifyUser($dbo, $un, $pw)
{
    try {
        return [
            "status" => "DEBUG_OK",
            "username" => $un,
            "password_length" => strlen($pw),
            "db" => $dbo->conn->query("SELECT DATABASE()")->fetchColumn()
        ];
    } catch (Throwable $e) {
        return [
            "status" => "DEBUG_ERROR",
            "message" => $e->getMessage()
        ];
    }
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
