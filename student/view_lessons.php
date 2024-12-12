<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
if (!isStudent()) {
    die("Access Denied: Students Only");
}

// Check if student is enrolled in the section
$sectionId = $_GET['section_id'] ?? null;
$studentId = $_SESSION['user_id'];

$checkEnrollment = $conn->prepare("
    SELECT * FROM enrollments 
    WHERE student_id = :student_id AND section_id = :section_id
");
$checkEnrollment->bindParam(':student_id', $studentId);
$checkEnrollment->bindParam(':section_id', $sectionId);
$checkEnrollment->execute();

if ($checkEnrollment->rowCount() == 0) {
    die("You are not enrolled in this section.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Section Lessons</title>
</head>
<body>
    <h2>Section Lessons</h2>
    <table>
        <thead>
            <tr>
                <th>Lesson Title</th>
                <th>Description</th>
                <th>Content</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $conn->prepare("
                SELECT * FROM lessons 
                WHERE section_id = :section_id
            ");
            $stmt->bindParam(':section_id', $sectionId);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td><a href='view_lesson.php?lesson_id=" . $row['id'] . "'>View Lesson</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>