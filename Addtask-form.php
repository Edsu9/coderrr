<?php
session_start();

include 'includes/database.php';
$currentUser = $_SESSION['user'];
$userId = $_SESSION['id'];

if (!isset($_SESSION['user']) || !isset($_SESSION['id'])) {
  header("Location: Login.php");
  exit();
}

if (isset($_POST['add'])) {

  $code = $userId;
  $description = $_POST['description'];
  $date = $_POST['date'];
  $status = $_POST['status'];
  $query = "INSERT INTO task 
            (descrpt, DATE, STATUS, ID)
            VALUES
            ('{$description}', '{$date}', '{$status}', '{$code}' )";

  $result = query($query);
  $_SESSION['action'] = 'Task successfully added!';
  header('location: Dashboard.php');
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
  $Taskcode = $_GET['id'];

  $query = "SELECT * FROM task WHERE taskid='{$Taskcode}'";

  $result = query($query);

  $row = $result->fetch_assoc();

  if (isset($_POST['edit'])) {
    $description = htmlspecialchars($_POST['description']);
    $date = $_POST['date'];
    $status = $_POST['status'];
    $code = $Taskcode;

    $query = "UPDATE task 
              SET descrpt='{$description}',
                  DATE='{$date}',
                  STATUS='{$status}'
              WHERE taskid='{$code}'";

    $result = query($query);
    
    if ($result) {
        $_SESSION['action'] = 'Task successfully updated!';
    } else {
        $_SESSION['action'] = 'Failed to update task.';
    }
    
    header('location: Dashboard.php');
    exit();
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Task Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/addtask.css" rel="stylesheet">
    
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center mb-0">
                            <?= isset($_GET['id']) ? '<i class="fas fa-edit"></i> Edit Task' : '<i class="fas fa-plus-circle"></i> New Task' ?>
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="current-status mb-4 <?= isset($_GET['id']) ? '' : 'd-none' ?>">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="status-label mr-2">Current Status:</div>
                                <div class="status-indicator 
                                    <?php 
                                        if (isset($row['status'])) {
                                            if ($row['status'] == 'priority') echo 'status-priority';
                                            elseif ($row['status'] == 'new') echo 'status-new';
                                            else echo 'status-done';
                                        }
                                    ?>">
                                    <?php 
                                        if (isset($row['status'])) {
                                            if ($row['status'] == 'priority') echo 'Priority';
                                            elseif ($row['status'] == 'new') echo 'New';
                                            else echo 'Done';
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="description">
                                    <i class="fas fa-tasks"></i> Description
                                </label>
                                <input type="text" 
                                       name="description" 
                                       class="form-control" 
                                       id="description" 
                                       placeholder="What needs to be done?" 
                                       value="<?= isset($row['descrpt']) ? $row['descrpt'] : '' ?>" 
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="date">
                                    <i class="fas fa-calendar-alt"></i> Due Date
                                </label>
                                <input type="date" 
                                       name="date" 
                                       class="form-control" 
                                       id="date" 
                                       value="<?= isset($row['date']) ? $row['date'] : '' ?>" 
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="status">
                                    <i class="fas fa-flag"></i> Status
                                </label>
                                <select class="form-control custom-select" id="status" name="status" required>
                                    <option value="priority" <?= isset($row['status']) && $row['status'] == 'priority' ? 'selected' : '' ?>>
                                        Priority
                                    </option>
                                    <option value="new" <?= isset($row['status']) && $row['status'] == 'new' ? 'selected' : '' ?>>
                                        New
                                    </option>
                                    <option value="pending" <?= isset($row['status']) && $row['status'] == 'pending' ? 'selected' : '' ?>>
                                        Pending
                                    </option>
                                    <option value="done" <?= isset($row['status']) && $row['status'] == 'done' ? 'selected' : '' ?>>
                                        Done
                                    </option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-custom btn-save" name="<?= isset($_GET['id']) ? 'edit' : 'add' ?>">
                                    <i class="fas fa-save mr-2"></i> <?= isset($_GET['id']) ? 'Update Task' : 'Save Task' ?>
                                </button>
                                <a href="Dashboard.php" class="btn btn-custom btn-back">
                                    <i class="fas fa-arrow-left mr-2"></i> Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        const statusIndicator = document.querySelector('.status-indicator');

        statusSelect.addEventListener('change', function() {
            if (statusIndicator) {
                // Remove all existing status classes
                statusIndicator.classList.remove('status-priority', 'status-new', 'status-done');
                // Add the new status class
                statusIndicator.classList.add('status-' + this.value);
                // Update the text
                statusIndicator.textContent = this.options[this.selectedIndex].text;
            }
        });
    });
    </script>
</body>
</html>