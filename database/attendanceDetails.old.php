<?php
require_once(__DIR__ . "/database.php");

class attendanceDetails
{
    public function saveAttendance($dbo, $session, $course, $fac, $student, $ondate, $status)
    {
        $rv = [-1];

        $c = "INSERT INTO attendance_details
              (session_id, course_id, faculty_id, student_id, on_date, status)
              VALUES
              (:session_id, :course_id, :faculty_id, :student_id, :on_date, :status)";

        $s = $dbo->conn->prepare($c);

        try {
            $s->execute([
                ":session_id" => $session,
                ":course_id" => $course,
                ":faculty_id" => $fac,
                ":student_id" => $student,
                ":on_date" => $ondate,
                ":status" => $status
            ]);
            $rv = [1];
        } catch (Exception $e) {
            // If record exists, update instead
            $c = "UPDATE attendance_details SET status = :status
                  WHERE session_id = :session_id
                  AND course_id = :course_id
                  AND faculty_id = :faculty_id
                  AND student_id = :student_id
                  AND on_date = :on_date";

            $s = $dbo->conn->prepare($c);

            try {
                $s->execute([
                    ":session_id" => $session,
                    ":course_id" => $course,
                    ":faculty_id" => $fac,
                    ":student_id" => $student,
                    ":on_date" => $ondate,
                    ":status" => $status
                ]);
                $rv = [1];
            } catch (Exception $ee) {
                error_log($ee->getMessage());
            }
        }

        return $rv;
    }

    public function getPresentListOfAClassByAFacOnADate($dbo, $session, $course, $fac, $ondate)
    {
        $rv = [];
        $c = "SELECT student_id FROM attendance_details
              WHERE session_id = :session_id
              AND course_id = :course_id
              AND faculty_id = :faculty_id
              AND on_date = :on_date
              AND status = 'YES'";

        $s = $dbo->conn->prepare($c);

        try {
            $s->execute([
                ":session_id" => $session,
                ":course_id" => $course,
                ":faculty_id" => $fac,
                ":on_date" => $ondate
            ]);
            $rv = $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        return $rv;
    }

    public function getAttenDanceReport($dbo, $session, $course, $fac)
    {
        $report = [];

        // Session name
        $s = $dbo->conn->prepare("SELECT year, term FROM session_details WHERE id = :id");
        $s->execute([":id" => $session]);
        $sd = $s->fetch(PDO::FETCH_ASSOC);
        $sessionName = $sd ? $sd['year'] . " " . $sd['term'] : "";

        // Faculty name
        $s = $dbo->conn->prepare("SELECT name FROM faculty_details WHERE id = :id");
        $s->execute([":id" => $fac]);
        $fd = $s->fetch(PDO::FETCH_ASSOC);
        $facname = $fd ? $fd['name'] : "";

        // Course name
        $s = $dbo->conn->prepare("SELECT code, title FROM course_details WHERE id = :id");
        $s->execute([":id" => $course]);
        $cd = $s->fetch(PDO::FETCH_ASSOC);
        $courseName = $cd ? $cd['code'] . "-" . $cd['title'] : "";

        $report[] = ["Session:", $sessionName];
        $report[] = ["Course:", $courseName];
        $report[] = ["Faculty:", $facname];

        // Total classes
        $s = $dbo->conn->prepare(
            "SELECT DISTINCT on_date FROM attendance_details
             WHERE session_id = :session_id
             AND course_id = :course_id
             AND faculty_id = :faculty_id
             ORDER BY on_date"
        );

        $s->execute([
            ":session_id" => $session,
            ":course_id" => $course,
            ":faculty_id" => $fac
        ]);

        $dates = $s->fetchAll(PDO::FETCH_ASSOC);
        $total = count($dates);
        $start = $total > 0 ? $dates[0]['on_date'] : '';
        $end = $total > 0 ? $dates[$total - 1]['on_date'] : '';

        $report[] = ["total", $total];
        $report[] = ["start", $start];
        $report[] = ["end", $end];

        // Attendance per student
        $c = "SELECT rsd.id, rsd.roll_no, rsd.name,
                     COUNT(ad.on_date) AS attended
              FROM (
                  SELECT sd.id, sd.roll_no, sd.name, crd.session_id, crd.course_id
                  FROM student_details sd
                  JOIN course_registration crd
                  ON sd.id = crd.student_id
                  WHERE crd.session_id = :session_id
                  AND crd.course_id = :course_id
              ) rsd
              LEFT JOIN attendance_details ad
              ON rsd.id = ad.student_id
              AND rsd.session_id = ad.session_id
              AND rsd.course_id = ad.course_id
              AND ad.status = 'YES'
              AND ad.faculty_id = :faculty_id
              GROUP BY rsd.id";

        $s = $dbo->conn->prepare($c);
        $s->execute([
            ":session_id" => $session,
            ":course_id" => $course,
            ":faculty_id" => $fac
        ]);

        $rv = $s->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rv as &$row) {
            $row['percent'] = $total > 0
                ? round(($row['attended'] / $total) * 100, 2)
                : 0.00;
        }

        $report[] = ["slno", "rollno", "name", "attended", "percent"];
        $report = array_merge($report, $rv);

        return $report;
    }
}
?>
