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
    <title>View Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Roboto', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 30px;
        }
        .card-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .card-text {
            margin-bottom: 15px;
            font-size: 1rem;
        }
        .btn {
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }
        .icon {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-5">Project Details</h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-project-diagram icon"></i><?php echo htmlspecialchars($project['proj_name']); ?></h5>
                <p class="card-text"><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                <p class="card-text"><strong>Budget:</strong> $<?php echo htmlspecialchars($project['budget']); ?></p>
                <p class="card-text"><strong>Type:</strong> <?php echo htmlspecialchars($project['type']); ?></p>
                <p class="card-text"><strong>Team:</strong> <?php echo htmlspecialchars($project['team']); ?></p>
                <p class="card-text"><strong>Date Approved:</strong> <?php echo htmlspecialchars($project['date_approved']); ?></p>
                <p class="card-text"><strong>Date Started:</strong> <?php echo htmlspecialchars($project['date_started']); ?></p>
                <p class="card-text"><strong>Date Ended:</strong> <?php echo htmlspecialchars($project['date_ended']); ?></p>
                <p class="card-text"><strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?></p>
                <p class="card-text"><strong>Remarks:</strong> <?php echo nl2br(htmlspecialchars($project['remarks'])); ?></p>
            </div>
        </div>
       <div class="mt-4 text-center">
    <a href="edit.php?id=<?php echo $project['id']; ?>" id="edit-button" class="btn btn-primary"><i class="fas fa-edit icon"></i>Edit</a>
    <a href="dashboard.php" id="back-button" class="btn btn-secondary"><i class="fas fa-arrow-left icon"></i>Back to List</a>
</div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        $('#edit-button').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to edit this project.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, edit it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'edit.php?id=<?php echo $project['id']; ?>';
                }
            });
        });

        $('#back-button').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Go back to the list?',
                text: "You are about to return to the project list.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, go back!'
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
