<?php
// Database Connection
$connection = pg_connect("host=localhost port=5432 dbname=warehouse user=postgres password=12345678");

if (!$connection) {
    echo "An error occurred while connecting to the database.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get POST data and sanitize inputs
    $name = $_POST['name'];
    $subclass = $_POST['subclass'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $uom = $_POST['uom'];
    $stock = $_POST['stock'];
    $classification_id = isset($_POST['classification_id']) && !empty($_POST['classification_id']) ? $_POST['classification_id'] : 'NULL';

    // Insert into supplier table
    $query_supplier = "INSERT INTO supplier (name) VALUES ('$name')";
    $result_supplier = pg_query($connection, $query_supplier);

    if ($result_supplier) {
        echo "<script>Swal.fire('Success!', 'Supplier added successfully.', 'success');</script>";
    } else {
        echo "<script>Swal.fire('Error!', 'Failed to add supplier.', 'error');</script>";
    }

    // Insert into products table
    $query_product = "INSERT INTO products (subclass, description, price, uom, stock, classification_id) 
                      VALUES ('$subclass', '$description', '$price', '$uom', '$stock', $classification_id)";
    $result_product = pg_query($connection, $query_product);

    if ($result_product) {
        echo "<script>Swal.fire('Success!', 'Product added successfully.', 'success');</script>";
    } else {
        echo "<script>Swal.fire('Error!', 'Failed to add product.', 'error');</script>";
    }
}

// Retrieve and display the data from both tables
$query_display_supplier = "SELECT * FROM supplier";
$result_display_supplier = pg_query($connection, $query_display_supplier);

$query_display_product = "SELECT * FROM products";
$result_display_product = pg_query($connection, $query_display_product);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <?php include("navbar.php"); ?>
    <div class="bg-white content-wrapper flex items-start justify-center min-h-screen p-4">
        <div class="mt-20 container min-w-full p-3 mx-auto rounded-md sm:p-4 dark:text-gray-800 dark:bg-gray-50 opacity-90">

            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Add Supplier and Product</h2>

            <!-- Form for Adding Data -->
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input type="text" class="w-full p-2 border border-gray-300 rounded" id="name" name="name" placeholder="Supplier Name" required>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded" id="subclass" name="subclass" placeholder="Subclass" required>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded" id="description" name="description" placeholder="Description" required>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded" id="price" name="price" placeholder="Price" required>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded" id="uom" name="uom" placeholder="UOM" required>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded" id="stock" name="stock" placeholder="Stock" required>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded" id="classification_id" name="classification_id" placeholder="Classification ID">
                </div>
                <button type="submit" class="w-full mt-4 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg px-4 py-2">Submit</button>
            </form>

            <!-- Display Supplier Table -->
            <h2 class="text-2xl font-semibold text-gray-800 mt-8">Suppliers</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm bg-white border rounded-lg mt-4">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th class="p-3 text-center">Supplier ID</th>
                            <th class="p-3 text-center">Supplier Name</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800">
                        <?php
                        if ($result_display_supplier) {
                            while ($row = pg_fetch_assoc($result_display_supplier)) {
                                echo "<tr class='border-b'>
                                    <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['id']) . "</td>
                                    <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['name']) . "</td>
                                  </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2' class='text-center text-red-500'>No suppliers found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Display Product Table -->
            <h2 class="text-2xl font-semibold text-gray-800 mt-8">Products</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm bg-white border rounded-lg mt-4">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th class="p-3 text-center">Subclass</th>
                            <th class="p-3 text-center">Description</th>
                            <th class="p-3 text-center">Price</th>
                            <th class="p-3 text-center">UOM</th>
                            <th class="p-3 text-center">Stock Onhand</th>
                            <th class="p-3 text-center">Classification ID</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800">
                        <?php
                        if ($result_display_product) {
                            while ($row = pg_fetch_assoc($result_display_product)) {
                                echo "<tr class='border-b'>
                                    <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['subclass']) . "</td>
                                    <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['description']) . "</td>
                                    <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['price']) . "</td>
                                    <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['uom']) . "</td>
                                    <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['stock']) . "</td>
                                    <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['classification_id']) . "</td>
                                  </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center text-red-500'>No products found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>