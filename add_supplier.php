<?php
$connection = pg_connect("host=localhost port=5432 dbname=warehouse user=postgres password=12345678");

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $preferences = isset($_POST['preferences']) ? $_POST['preferences'] : [];

    // Insert the supplier into the database
    $query = "INSERT INTO supplier (name) VALUES ('$name') RETURNING id";
    $result = pg_query($connection, $query);
    if ($result) {
        $supplier_id = pg_fetch_result($result, 0, 'id');

        // Insert preferences into the classification table
        foreach ($preferences as $preference) {
            $query = "INSERT INTO classification (class, supplier_id) VALUES ('$preference', $supplier_id)";
            pg_query($connection, $query);
        }

        // Redirect after successful submission to prevent resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<script>Swal.fire('Error!', 'Failed to add supplier.', 'error');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<style>
    body {
        background-image: url('2.jpeg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        color: black;
        backdrop-filter: blur(1.5px);
    }

    .card {
        padding: 30px 40px;
        border: none !important;
        box-shadow: 0 6px 12px 0 rgba(0, 0, 0, 0.2);
        background-color: rgba(255, 255, 255, 0.8);
        color: black;
        border-radius: 8px;
        width: 100%;
        max-width: 400px;
        min-height: 500px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .checkbox-group {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .checkbox-group h6 {
        margin: 0;
    }

    body::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        /* Black overlay with 50% opacity */
        z-index: 0;
        /* Place below all content */
    }

    .content-wrapper {
        position: relative;
        z-index: 1;
        /* Place above the overlay */
    }
</style>

<body>
    <?php include("navbar.php"); ?>
    <div class="content-wrapper">
        <div class="card">
            <h5 class="text-center ">Add Suppliers</h5>
            <form action="" method="POST">
                <!-- Name Input -->
                <div class="form-group">
                    <label for="name">Supplier Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Supplier Name" required>
                </div>

                <!-- Preferences -->
                <div class="form-group">

                    <div style="display: grid; grid-template-columns: 1fr; row-gap: 1rem;">
                        <!-- Preference 1 -->
                        <div class="checkbox-group">
                            <input type="checkbox" id="checkbox1" name="preferences[]" value="Direct Materials - Beverage">
                            <h6>Direct Materials - Beverage</h6>
                        </div>

                        <!-- Preference 2 -->
                        <div class="checkbox-group">
                            <input type="checkbox" id="checkbox2" name="preferences[]" value="Direct Materials - Kitchen">
                            <h6>Direct Materials - Kitchen</h6>
                        </div>

                        <!-- Preference 3 -->
                        <div class="checkbox-group">
                            <input type="checkbox" id="checkbox3" name="preferences[]" value="Direct Materials - Bakery">
                            <h6>Direct Materials - Bakery</h6>
                        </div>

                        <!-- Add more preferences as needed -->
                        <div class="checkbox-group">
                            <input type="checkbox" id="checkbox4" name="preferences[]" value="Supplies & Packaging - Kitchen">
                            <h6>Supplies & Packaging - Kitchen</h6>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="checkbox5" name="preferences[]" value="Supplies & Packaging - Beverage">
                            <h6>Supplies & Packaging - Beverage</h6>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="checkbox6" name="preferences[]" value="Supplies & Packaging - Bakery">
                            <h6>Supplies & Packaging - Bakery</h6>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="checkbox7" name="preferences[]" value="Janitorials">
                            <h6>Janitorials</h6>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="checkbox7" name="preferences[]" value="Office Supplies">
                            <h6>Office Supplies</h6>
                        </div>
                    </div>

                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>