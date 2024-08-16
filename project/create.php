<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$error = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data and sanitize inputs
    $proj_name = $conn->real_escape_string(trim($_POST['projName']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    $budget = floatval($_POST['budget']);
    $type = $conn->real_escape_string(trim($_POST['type']));
    $team = $conn->real_escape_string(trim($_POST['team']));
    $date_approved = $conn->real_escape_string(trim($_POST['dateApproved']));
    $date_started = $conn->real_escape_string(trim($_POST['dateStarted']));
    $date_ended = $conn->real_escape_string(trim($_POST['dateEnded']));
    $status = $conn->real_escape_string(trim($_POST['status']));
    $remarks = $conn->real_escape_string(trim($_POST['remarks']));

    // Validate inputs
    if (!empty($proj_name) && !empty($description) && $budget > 0 && !empty($type) && !empty($team) && !empty($date_approved) && !empty($date_started) && !empty($status)) {
        // Prepare SQL statement
        $sql = "INSERT INTO projects (proj_name, description, budget, type, team, date_approved, date_started, date_ended, status, remarks) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsssssss", $proj_name, $description, $budget, $type, $team, $date_approved, $date_started, $date_ended, $status, $remarks);

        // Execute the statement
        if ($stmt->execute()) {
            $message = "New project created successfully";
        } else {
            $error = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = "Please fill in all required fields and ensure budget is greater than 0.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Information Form</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        h2 {
            color: #333;
        }
        .form-label {
            font-weight: 600;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Project Information Form</h2>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
                <label for="projName" class="form-label">Project Name</label>
                <input type="text" class="form-control" id="projName" name="projName" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            
            <div class="mb-3">
                <label for="budget" class="form-label">Budget</label>
                <input type="number" class="form-control" id="budget" name="budget" step="0.01" required>
            </div>
            
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <input type="text" class="form-control" id="type" name="type" required>
            </div>
            
            <div class="mb-3">
                <label for="team" class="form-label">Team</label>
                <input type="text" class="form-control" id="team" name="team" required>
            </div>
            
            <div class="mb-3">
                <label for="dateApproved" class="form-label">Date Approved</label>
                <input type="text" class="form-control datepicker" id="dateApproved" name="dateApproved" required>
            </div>
            
            <div class="mb-3">
                <label for="dateStarted" class="form-label">Date Started</label>
                <input type="text" class="form-control datepicker" id="dateStarted" name="dateStarted" required>
            </div>
            
            <div class="mb-3">
                <label for="dateEnded" class="form-label">Date Ended</label>
                <input type="text" class="form-control datepicker" id="dateEnded" name="dateEnded">
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="">Select status</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="stopped">Stopped</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function(){
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            <?php if (!empty($message)) : ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '<?php echo $message; ?>'
                });
            <?php endif; ?>

            <?php if (!empty($error)) : ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '<?php echo $error; ?>'
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>
