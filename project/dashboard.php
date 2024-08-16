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

$user_id = $_SESSION['user_id'];
$user_sql = "SELECT username FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchField = isset($_GET['searchField']) ? $_GET['searchField'] : 'all';

$whereClause = "";
if (!empty($search)) {
    $search = $conn->real_escape_string($search); // Prevent SQL injection
    switch ($searchField) {
        case 'id':
            $whereClause = "WHERE id LIKE '%$search%'";
            break;
        case 'name':
            $whereClause = "WHERE proj_name LIKE '%$search%'";
            break;
        case 'status':
            $whereClause = "WHERE status LIKE '%$search%'";
            break;
        case 'type':
            $whereClause = "WHERE type LIKE '%$search%'";
            break;
        default:
            $whereClause = "WHERE id LIKE '%$search%' OR proj_name LIKE '%$search%' OR status LIKE '%$search%' OR type LIKE '%$search%'";
    }
}

// Delete project
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM projects WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success_message'] = "Project deleted successfully";
    } else {
        $_SESSION['error_message'] = "Error deleting project: " . $conn->error;
    }
    header("Location: dashboard.php");
    exit();
}

// Fetch projects based on search
$sql = "SELECT * FROM projects $whereClause";
$result = $conn->query($sql);

// Fetch project status counts
$statusCounts = array(
    'ongoing' => 0,
    'completed' => 0,
    'stopped' => 0
);

$sqlCount = "SELECT status, COUNT(*) as count FROM projects GROUP BY status";
$resultCount = $conn->query($sqlCount);

if ($resultCount->num_rows > 0) {
    while($rowCount = $resultCount->fetch_assoc()) {
        $statusCounts[$rowCount['status']] = $rowCount['count'];
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management System Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
<style>

div#status {
    margin-bottom: 50px;
}


    /* Enhance table appearance */
.table {
    border-collapse: separate;
    border-spacing: 0;
    margin: 20px 0;
    font-size: 16px;
    font-family: 'Arial', sans-serif;
}

.table th, .table td {
    padding: 12px 15px;
    text-align: center;
}

.table thead {
    background-color: #4b6cb7;
    color: #fff;
}

.table tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}

.table tbody tr:hover {
    background-color: #ddd;
}

/* Make table responsive */
.table-responsive-md {
    display: block;
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Interactive icon buttons */
.btn i {
    transition: transform 0.2s ease-in-out;
}

.btn:hover i {
    transform: scale(1.2);
}

</style>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Welcome! <?php echo htmlspecialchars($user['username']); ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="logout-link" href="#">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4">Project Management Dashboard</h2>
        <form action="dashboard.php" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <select name="searchField" class="form-select">
                        <option value="all" <?php echo $searchField == 'all' ? 'selected' : ''; ?>>All Fields</option>
                        <option value="id" <?php echo $searchField == 'id' ? 'selected' : ''; ?>>ID</option>
                        <option value="name" <?php echo $searchField == 'name' ? 'selected' : ''; ?>>Project Name</option>
                        <option value="status" <?php echo $searchField == 'status' ? 'selected' : ''; ?>>Status</option>
                        <option value="type" <?php echo $searchField == 'type' ? 'selected' : ''; ?>>Project Type</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>
        <a href="create.php" class="btn btn-primary mb-3">Add New Project</a>
        <table class="table table-bordered table-striped table-responsive-sm">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Project Name</th>
            <th>Budget</th>
            <th>Type</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>".htmlspecialchars($row["id"])."</td>
            <td>".htmlspecialchars($row["proj_name"])."</td>
            <td>₱".htmlspecialchars($row["budget"])."</td>
            <td>".htmlspecialchars($row["type"])."</td>
            <td>".htmlspecialchars($row["status"])."</td>
            <td>
                <a href='view.php?id=".htmlspecialchars($row["id"])."' class='btn btn-info btn-sm'><i class='bi bi-eye'></i> View</a>
                <a href='edit.php?id=".htmlspecialchars($row["id"])."' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i> Edit</a>
                <a href='delete.php' data-id='".htmlspecialchars($row["id"])."' class='btn btn-danger btn-sm delete-button'><i class='bi bi-trash'></i> Delete</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>No projects found</td></tr>";
}
?>
    </tbody>
</table>
    </div>
    
    <div class="container"id="status">
        <div class="row mt-4">
            <h3 class="mb-3">Project Status Summary</h3>
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Ongoing Projects</h5>
                        <p class="card-text display-4"><?php echo $statusCounts['ongoing']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Completed Projects</h5>
                        <p class="card-text display-4"><?php echo $statusCounts['completed']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Stopped Projects</h5>
                        <p class="card-text display-4"><?php echo $statusCounts['stopped']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        document.getElementById('logout-link').addEventListener('click', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php?logout=true';
                }
            })
        });
    </script>
 <script>
    $(document).ready(function() {
        $('.delete-button').on('click', function(e) {
            e.preventDefault();
            var projectId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'dashboard.php?delete=' + projectId;
                }
            });
        });

        // Show success message if available
        <?php if(isset($_SESSION['success_message'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "<?php echo $_SESSION['success_message']; ?>"
            });
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        // Show error message if available
        <?php if(isset($_SESSION['error_message'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "<?php echo $_SESSION['error_message']; ?>"
            });
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
    });
</script>
</body>
</html>

<?php
$conn->close();
?>