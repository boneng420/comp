<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
if (!isInstructor()) {
    die("Access Denied: Instructors Only");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sectionName = $_POST['section_name'];
    $courseName = $_POST['course_name'];
    $sectionCode = generateSectionCode();
    $instructorId = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO sections 
        (instructor_id, section_name, course_name, section_code, created_at) 
        VALUES (:instructor_id, :section_name, :course_name, :section_code, NOW())");
    
    $stmt->bindParam(':instructor_id', $instructorId);
    $stmt->bindParam(':section_name', $sectionName);
    $stmt->bindParam(':course_name', $courseName);
    $stmt->bindParam(':section_code', $sectionCode);
    
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Section</title>
</head>
<body>
    <h2>Create New Section</h2>
    <form method="POST">
        <input type="text" name="section_name" placeholder="Section Name" required>
        <input type="text" name="course_name" placeholder="Course Name" required>
        <button type="submit">Create Section</button>
    </form>

    <h3>Your Sections</h3>
    <table>
        <thead>
            <tr>
                <th>Section Name</th>
                <th>Section Code</th>
                <th>Course Name</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $conn->prepare("SELECT * FROM sections WHERE instructor_id = :instructor_id");
            $stmt->bindParam(':instructor_id', $_SESSION['user_id']);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['section_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['section_code']) . "</td>";
                echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>