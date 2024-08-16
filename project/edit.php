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

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "No project specified";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $proj_name = $_POST['projName'];
    $description = $_POST['description'];
    $budget = $_POST['budget'];
    $type = $_POST['type'];
    $team = $_POST['team'];
    $date_approved = $_POST['dateApproved'];
    $date_started = $_POST['dateStarted'];
    $date_ended = $_POST['dateEnded'] ?: null;
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    $sql = "UPDATE projects SET 
            proj_name=?, description=?, budget=?, type=?, team=?, 
            date_approved=?, date_started=?, date_ended=?, status=?, remarks=? 
            WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsssssssi", $proj_name, $description, $budget, $type, $team, 
                       $date_approved, $date_started, $date_ended, $status, $remarks, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
    $stmt->close();
    exit;
}

$sql = "SELECT * FROM projects WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $project = $result->fetch_assoc();
} else {
    echo "Project not found";
    exit;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <h2 class="mb-4 text-center">Edit Project</h2>
        <form id="editProjectForm">
            <div class="mb-3">
                <label for="projName" class="form-label">Project Name</label>
                <input type="text" class="form-control" id="projName" name="projName" value="<?php echo htmlspecialchars($project['proj_name']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($project['description']); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="budget" class="form-label">Budget</label>
                <input type="number" class="form-control" id="budget" name="budget" step="0.01" value="<?php echo htmlspecialchars($project['budget']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <input type="text" class="form-control" id="type" name="type" value="<?php echo htmlspecialchars($project['type']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="team" class="form-label">Team</label>
                <input type="text" class="form-control" id="team" name="team" value="<?php echo htmlspecialchars($project['team']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="dateApproved" class="form-label">Date Approved</label>
                <input type="text" class="form-control datepicker" id="dateApproved" name="dateApproved" value="<?php echo htmlspecialchars($project['date_approved']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="dateStarted" class="form-label">Date Started</label>
                <input type="text" class="form-control datepicker" id="dateStarted" name="dateStarted" value="<?php echo htmlspecialchars($project['date_started']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="dateEnded" class="form-label">Date Ended</label>
                <input type="text" class="form-control datepicker" id="dateEnded" name="dateEnded" value="<?php echo htmlspecialchars($project['date_ended']); ?>">
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="ongoing" <?php echo $project['status'] == 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="stopped" <?php echo $project['status'] == 'stopped' ? 'selected' : ''; ?>>Stopped</option>
                    <option value="completed" <?php echo $project['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea class="form-control" id="remarks" name="remarks" rows="3"><?php echo htmlspecialchars($project['remarks']); ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Project</button>
            <button type="button" class="btn btn-secondary" id="cancelButton">Cancel</button>
        </form>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            $('#editProjectForm').on('submit', function(event) {
                event.preventDefault();
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Project updated successfully',
                                showConfirmButton: true,
                             
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });
                        }
                    }
                });
            });

            $('#cancelButton').on('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, cancel it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'dashboard.php';
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
