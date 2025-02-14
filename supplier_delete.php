<?php
// Start output buffering
ob_start();

// Database connection
$connection = pg_connect("host=localhost port=5432 dbname=warehouse user=postgres password=12345678");

if (!$connection) {
    die("Database connection failed.");
}

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_record'])) {
    $id = $_POST['id'];

    // Use parameterized query to prevent SQL injection
    $sql = "DELETE FROM products WHERE id = $1";
    $result = pg_query_params($connection, $sql, array($id));

    if ($result) {
        $_SESSION['delete_message'] = "Record deleted successfully.";
        // Redirect back to supplier.php after deletion
        header("Location: supplier.php?status=success");
        exit();
    } else {
        $_SESSION['delete_message'] = "Failed to delete record.";
    }
}

// Close connection
pg_close($connection);

// End output buffering
ob_end_flush();
