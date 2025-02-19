<?php
session_start();
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user']) || !isset($_SESSION['id'])) {
    header("Location: Login.php");
    exit();
}

include 'includes/database.php';

$userId = $_SESSION['id'];
$taskId = $_GET['id'] ?? null;

if (!$taskId) {
    header("Location: Dashboard.php");
    exit();
}

$query = "SELECT * FROM task WHERE taskid = '$taskId' AND ID = '$userId'";
$result = query($query);

if (!$result || $result->num_rows === 0) {
    $_SESSION['action'] = "Task not found.";
    header("Location: Dashboard.php");
    exit();
}

$task = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Task</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/viewtask.css" rel="stylesheet">
    
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="task-card">
                    <div class="task-header text-center">
                        <i class="fas fa-tasks me-2"></i> Task Details
                    </div>
                    <div class="task-body">
                        <!-- Status Section -->
                        <div class="task-info">
                            <div class="task-label">Status</div>
                            <div class="status-badge status-<?php echo $task['status']; ?>">
                                <?php echo ucfirst($task['status']); ?>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="task-info">
                            <div class="task-label">Description</div>
                            <div class="task-value">
                                <?php echo htmlspecialchars($task['descrpt']); ?>
                            </div>
                        </div>

                        <!-- Due Date Section -->
                        <div class="task-info">
                            <div class="task-label">Due Date</div>
                            <div class="task-value">
                                <?php echo date('F d, Y', strtotime($task['date'])); ?>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="actions">
                            <?php if ($task['status'] != 'done'): ?>
                                <a href="Dashboard.php?taskid=<?php echo $task['taskid']; ?>" 
                                   class="btn btn-success btn-custom"
                                   onclick="return confirm('Mark this task as done?')">
                                    <i class="fas fa-check"></i> Mark as Done
                                </a>
                            <?php endif; ?>
                            <a href="Dashboard.php" class="btn btn-secondary btn-custom btn-back">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function confirmDelete(taskId) {
        if (confirm('Are you sure you want to delete this task?')) {
            window.location.href = 'Dashboard.php?delete=' + taskId;
        }
    }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 