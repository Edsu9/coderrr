<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user']) || !isset($_SESSION['id'])) {
    header("Location: Login.php");
    exit();
}

include 'includes/database.php';

$currentUser = $_SESSION['user'];
$userId = $_SESSION['id'];

function handleDelete($id) {
    $query = "DELETE FROM task WHERE taskid='{$id}'";
    return query($query);
}

function handleMarkAsDone($taskid) {
    $query = "SELECT status FROM task WHERE taskid = '{$taskid}'";
    $result = query($query);
    $task = $result->fetch_assoc();

    if ($task['status'] == 'pending') {
        // If task is pending, mark it as done
        $query = "UPDATE task SET status = 'done' WHERE taskid = '{$taskid}'";
        return query($query);
    } else {
        // If task is not pending, mark it as pending
        $query = "UPDATE task SET status = 'pending' WHERE taskid = '{$taskid}'";
        return query($query);
    }
}

function getTaskCounts($userId) {
    $query = "SELECT
                SUM(CASE WHEN status = 'priority' THEN 1 ELSE 0 END) AS priority_count,
                SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) AS new_count,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
                SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) AS done_count
              FROM task
              WHERE ID = '{$userId}'";
    return query($query)->fetch_assoc();
}

function getTasks($userId, $search = '') {
    if ($search) {
        $query = "SELECT * FROM task WHERE (descrpt LIKE '%$search%' OR status LIKE '%$search%') AND ID = '$userId' ORDER BY 
                  CASE status 
                      WHEN 'priority' THEN 1 
                      WHEN 'new' THEN 2 
                      WHEN 'pending' THEN 3 
                      WHEN 'done' THEN 4 
                  END";
    } else {
        $query = "SELECT * FROM task WHERE ID='$userId' ORDER BY 
                  CASE status 
                      WHEN 'priority' THEN 1 
                      WHEN 'new' THEN 2 
                      WHEN 'pending' THEN 3 
                      WHEN 'done' THEN 4 
                  END";
    }
    return query($query);
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $_SESSION['action'] = handleDelete($id) ? 'Deleted successfully.' : 'Failed to delete data.';
    header('Location: Dashboard.php');
    exit();
}

if (isset($_GET['taskid'])) {
    $taskid = $_GET['taskid'];
    $_SESSION['action'] = handleMarkAsDone($taskid) ? 'Task marked as done.' : 'Failed to mark task as done.';
    header('Location: Dashboard.php');
    exit();
}

$search = $_POST['search'] ?? '';
$tasks = getTasks($userId, $search);
$taskCounts = getTaskCounts($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="css/dashboard.css" rel="stylesheet">
    
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img.png" alt="Logo" class="img-fluid" style="height: 80px;">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <h1 class="navbar-text text-white mx-auto">Welcome, <?php echo ucfirst($currentUser); ?>!</h1>
                <form method="post" class="form-inline my-2 my-lg-0 ml-auto">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search tasks...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-light"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <button type="submit" name="refresh" class="btn btn-outline-light ml-2"><i class="fas fa-sync"></i></button>
                </form>
                <ul class="navbar-nav ml-2">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a href="javascript:void(0)" onclick="confirmLogout()" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row text-center mt-4">
            <div class="col-md-3">
                <div class="card card-custom">
                    <div class="card-header card-header-first">Priority</div>
                    <div class="card-body"><?php echo $taskCounts['priority_count']; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom">
                    <div class="card-header card-header-new">New</div>
                    <div class="card-body"><?php echo $taskCounts['new_count']; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom">
                    <div class="card-header card-header-pending">Pending</div>
                    <div class="card-body"><?php echo $taskCounts['pending_count']; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom">
                    <div class="card-header card-header-done">Done</div>
                    <div class="card-body"><?php echo $taskCounts['done_count']; ?></div>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['action'])): ?>
            <div class="alert alert-success mt-4"><?php echo $_SESSION['action']; unset($_SESSION['action']); ?></div>
            <?php endif; ?>

        <div class="container mt-4">
    <!-- Add Task Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="Addtask-form.php" class="btn btn-success btn-lg rounded-circle shadow-lg" style="position: fixed; bottom: 20px; right: 20px;">
            <i class="fas fa-plus"></i></a>
        </div>

    <!-- Task Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $tasks->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="circle 
                                <?php 
                                    if ($row['status'] == 'priority') echo 'bg-priority';
                                    elseif ($row['status'] == 'new') echo 'bg-new';
                                    elseif ($row['status'] == 'pending') echo 'bg-pending';
                                    else echo 'bg-done';
                                ?>">
                            </div>
                        </td>
                        <td><?php echo $row['descrpt']; ?></td>
                        <td><?php echo date('d F Y', strtotime($row['date'])); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view-task.php?id=<?php echo $row['taskid']; ?>" 
                                   class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($row['status'] == 'done'): ?>
                                    <a href="Addtask-form.php?id=<?php echo $row['taskid']; ?>" 
                                       class="btn btn-primary" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="Addtask-form.php?id=<?php echo $row['taskid']; ?>" 
                                       class="btn btn-primary" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="Dashboard.php?taskid=<?php echo $row['taskid']; ?>" 
                                       class="btn btn-success" 
                                       onclick="return confirm('Mark this task as done?')" title="Mark as Done">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $row['taskid']; ?>)" 
                                   class="btn btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script> $(document).ready(function(){$('[data-toggle="tooltip"]').tooltip();});</script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownItems = document.querySelectorAll('.dropdown-item');
        const statusInput = document.getElementById('statusInput');
        const statusDropdown = document.getElementById('statusDropdown');

        dropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const value = this.getAttribute('data-value');
                const text = this.textContent;

                // Update the hidden input value
                statusInput.value = value;

                // Update the button text and class
                statusDropdown.textContent = text;
                statusDropdown.className = `btn btn-secondary dropdown-toggle form-control text-left btn-status-${value}`;
            });
        });

        // Set initial state if editing a task
        const initialStatus = "<?= isset($row['status']) ? $row['status'] : '' ?>";
        if (initialStatus) {
            const initialText = document.querySelector(`.dropdown-item[data-value="${initialStatus}"]`).textContent;
            statusDropdown.textContent = initialText;
            statusDropdown.classList.add(`btn-status-${initialStatus}`);
        }
    });
</script>

<script>
function showLoading() {
    document.getElementById('loading-overlay').classList.add('active');
}

function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
    }
}

function confirmDelete(taskId) {
    if (confirm('Are you sure you want to delete this task?')) {
        window.location.href = 'Dashboard.php?delete=' + taskId;
    }
}

// Add loading for search form only
document.querySelector('form').addEventListener('submit', function() {
    showLoading();
});

// Remove the general button click handlers and update the table buttons HTML
</script>
</body>
</html>
