<?php
require_once(__DIR__ . "/database.php");

class CourseRegistrationDetails
{
    public function getRegisteredStudents($dbo, $sessionid, $courseid)
    {
        $rv = [];

        $c = "SELECT sd.id, sd.roll_no, sd.name
              FROM student_details sd
              JOIN course_registration crg
              ON crg.student_id = sd.id
              WHERE crg.session_id = :sessionid
              AND crg.course_id = :courseid";

        $s = $dbo->conn->prepare($c);

        try {
            $s->execute([
                ":sessionid" => $sessionid,
                ":courseid" => $courseid
            ]);
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
        }

        return $rv;
    }
}
?>
