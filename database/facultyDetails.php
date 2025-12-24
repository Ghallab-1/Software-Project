<?php
require_once(__DIR__ . "/database.php");

class faculty_details
{
public function verifyUser($dbo, $un, $pw)
{
    try {
        $stmt = $dbo->conn->prepare(
            "SELECT id, name FROM faculty_details WHERE user_name = :un AND password = :pw LIMIT 1"
        );
        $stmt->execute([":un" => $un, ":pw" => $pw]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return [
                "status" => "ALL OK",
                "id" => (int)$row['id'],
                "name" => $row['name']
            ];
        }

        return ["status" => "ERROR", "message" => "Invalid credentials"];
    } catch (Throwable $e) {
        return ["status" => "ERROR", "message" => $e->getMessage()];
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
