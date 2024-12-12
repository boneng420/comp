<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
if (!isStudent()) {
    die("Access Denied: Students Only");
}

$enrollmentError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sectionCode = $_POST['section_code'];
    $studentId = $_SESSION['user_id'];

    // Verify section code exists
    $stmt = $conn->prepare("SELECT id FROM sections WHERE section_code = :section_code");
    $stmt->bindParam(':section_code', $sectionCode);
    $stmt->execute();
    $section = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($section) {
        // Check if already enrolled
        $checkEnrollment = $conn->prepare("SELECT * FROM enrollments 
            WHERE student_id = :student_id AND section_id = :section_id");
        $checkEnrollment->bindParam(':student_id', $studentId);
        $checkEnrollment->bindParam(':section_id', $section['id']);
        $checkEnrollment->execute();

        if ($checkEnrollment->rowCount() == 0) {
            // Enroll student
            $enrollStmt = $conn->prepare("INSERT INTO enrollments 
                (student_id, section_id, enrolled_at) 
                VALUES (:student_id, :section_id, NOW())");
            $enrollStmt->bindParam(':student_id', $studentId);
            $enrollStmt->bindParam(':section_id', $section['id']);
            $enrollStmt->execute();

            $enrollmentError = "Successfully enrolled in the section!";
        } else {
            $enrollmentError = "You are already enrolled in this section.";
        }
    } else {
        $enrollmentError = "Invalid section code.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Enroll in Section</title>
</head>
<body>
    <h2>Enroll in a Section</h2>
    <?php if ($enrollmentError): ?>
        <p style="color: <?php echo strpos($enrollmentError, 'Successfully') !== false ? 'green' : 'red'; ?>">
            <?php echo $enrollmentError; ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="section_code" placeholder="Enter Section Code" required>
        <button type="submit">Enroll</button>
    </form>

    <h3>Your Enrolled Sections</h3>
    <table>
        <thead>
            <tr>
                <th>Section Name</th>
                <th>Course Name</th>
                <th>Enrolled At</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $conn->prepare("
                SELECT s.section_name, s.course_name, e.enrolled_at 
                FROM sections s
                JOIN enrollments e ON s.id = e.section_id
                WHERE e.student_id = :student_id
            ");
            $stmt->bindParam(':student_id', $_SESSION['user_id']);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['section_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['enrolled_at']) . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>