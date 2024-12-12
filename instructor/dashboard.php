<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
if (!isInstructor()) {
    die("Access Denied: Instructors Only");
}

// Fetch instructor's sections
$stmt = $conn->prepare("
    SELECT s.*, 
    (SELECT COUNT(*) FROM enrollments e WHERE e.section_id = s.id) as enrolled_students
    FROM sections s 
    WHERE s.instructor_id = :instructor_id
");
$stmt->bindParam(':instructor_id', $_SESSION['user_id']);
$stmt->execute();
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Instructor Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, Instructor <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        
        <div class="dashboard-actions">
            <a href="create_section.php" class="btn">Create New Section</a>
            <a href="manage_lessons.php" class="btn">Manage Lessons</a>
            <a href="../logout.php" class="btn btn-logout">Logout</a>
        </div>

        <h2>Your Sections</h2>
        <table>
            <thead>
                <tr>
                    <th>Section Name</th>
                    <th>Course Name</th>
                    <th>Section Code</th>
                    <th>Enrolled Students</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sections as $section): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($section['section_name']); ?></td>
                        <td><?php echo htmlspecialchars($section['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($section['section_code']); ?></td>
                        <td><?php echo $section['enrolled_students']; ?></td>
                        <td>
                            <a href="view_section.php?id=<?php echo $section['id']; ?>">View</a>
                            <a href="add_lesson.php?section_id=<?php echo $section['id']; ?>">Add Lesson</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>