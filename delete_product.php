<?php
// Database Connection
$connection = pg_connect("host=localhost port=5432 dbname=warehouse user=postgres password=12345678");

if (!$connection) {
    echo "An error occurred while connecting to the database.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']); // Sanitize input

    $query_delete = "DELETE FROM products WHERE id = $id";
    $result_delete = pg_query($connection, $query_delete);

    if ($result_delete) {
        echo "<script>
            Swal.fire('Deleted!', 'Product has been deleted.', 'success')
            .then(() => {
                window.location.href = 'YOUR_PAGE_NAME.php';
            });
        </script>";
    } else {
        echo "<script>Swal.fire('Error!', 'Failed to delete product.', 'error');</script>";
    }
} else {
    echo "<script>Swal.fire('Error!', 'Invalid Request.', 'error');</script>";
}
