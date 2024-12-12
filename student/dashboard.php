<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
if (!isStudent()) {
    die("Access Denied: Students Only");
}

// Fetch student's enrolled sections
$stmt = $conn->prepare("
    SELECT s.*, e.enrolled_at 
    FROM sections s
    JOIN enrollments e ON s.id = e.section_id
    WHERE e.student_id = :student_id
");
$stmt->bindParam(':student_id', $_SESSION['user_id']);
$stmt->execute();
$enrolled_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, Student <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        
        <div class="dashboard-actions">
            <a href="enroll.php" class="btn">Enroll in Section</a>
            <a href="../logout.php" class="btn btn-logout">Logout</a>
        </div>

        <h2>Your Enrolled Sections</h2>
        <table>
            <thead>
                <tr>
                    <th>Section Name</th>
                    <th>Course Name</th>
                    <th>Enrolled At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrolled_sections as $section): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($section['section_name']); ?></td>
                        <td><?php echo htmlspecialchars($section['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($section['enrolled_at']); ?></td>
                        <td>
                            <a href="view_lessons.php?section_id=<?php echo $section['id']; ?>">View Lessons</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>