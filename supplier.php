<?php
$connection = pg_connect("host=localhost port=5432 dbname=warehouse user=postgres password=12345678");

if (!$connection) {
    die("An error occurred while connecting to the database.");
}

// Get Supplier ID from URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $supplier_id = $_GET['id'];
} else {
    die("Invalid supplier ID.");
}

// Handle Form Submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subclass = $_POST['subclass'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $uom = $_POST['uom'];
    $stock = $_POST['stock'];

    if (!empty($subclass) && !empty($description) && !empty($price) && !empty($uom) && !empty($stock)) {
        $insert_query = "INSERT INTO products (supplier_id, subclass, description, price, uom, stock) VALUES ($1, $2, $3, $4, $5, $6)";
        $result = pg_query_params($connection, $insert_query, array($supplier_id, $subclass, $description, $price, $uom, $stock));

        if ($result) {
            $message = "Product added successfully!";
            // Redirect to the same page to prevent form resubmission on refresh
            header("Location: ".$_SERVER['PHP_SELF']."?id=".$supplier_id);
            exit;
        } else {
            $message = "Failed to add product.";
        }
    } else {
        $message = "All fields are required.";
    }
}

// Fetch supplier details
$supplier_query = "SELECT * FROM supplier WHERE id = $1";
$supplier_result = pg_query_params($connection, $supplier_query, array($supplier_id));

if (!$supplier_result) {
    die("Error fetching supplier data.");
}

$supplier = pg_fetch_assoc($supplier_result);
if (!$supplier) {
    die("Supplier not found.");
}

// Fetch products related to the supplier
$products_query = "SELECT * FROM products WHERE supplier_id = $1";
$products_result = pg_query_params($connection, $products_query, array($supplier_id));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            backdrop-filter: blur(1.5px);
        }
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }
        .content-wrapper {
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>
    <?php include("navbar.php"); ?>

    <div class="bg-white content-wrapper flex items-start justify-center min-h-screen p-4">
        <div class="mt-20 container min-w-full p-3 mx-auto rounded-md sm:p-4 dark:text-gray-800 dark:bg-gray-50 opacity-90">
            
            <!-- Display Success/Error Message -->
            <?php if (!empty($message)) : ?>
                <div class="text-center text-white p-2 bg-green-500 rounded"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="gap-4">
                    <div class="grid grid-cols-4 gap-4 mt-2">
                    <select class="form-control w-full" id="subclass" name="subclass" required>
                    <option selected disabled>Sub-Classification</option>
                    <option value="Bakery Direct Material">Bakery Direct Material</option>
                    <option value="Beverages - Drinks">Beverages - Drinks</option>
                    <option value="Other Materials">Other Materials</option>
                    <option value="Rice">Rice</option>
                    <option value="Bakery Tools and Supplies - Overhead">Bakery Tools and Supplies - Overhead</option>
                    <option value="Take Out and Packaging">Take Out and Packaging</option>
                    <option value="Kitchen Tools and Supplies - Overhead">Kitchen Tools and Supplies - Overhead</option>
                    <option value="Office Supplies and Expenses">Office Supplies and Expenses</option>
                    <option value="Cleaning Supplies">Cleaning Supplies</option>
                    <option value="Janitorial Expense">Janitorial Expense</option>
                    <option value="Miscellaneous Expense">Miscellaneous Expense</option>
                    <option value="Beef">Beef</option>
                    <option value="Chicken">Chicken</option>
                    <option value="Egg">Egg</option>
                    <option value="Pork">Pork</option>
                    <option value="Seafood">Seafood</option>
                    <option value="Employees Meal">Employees Meal</option>
                    </select>
                        <input type="text" class="form-control w-full" name="description" placeholder="Description" required>
                        <input type="text" class="form-control w-full" name="price" placeholder="Price" required>
                        <input type="text" class="form-control w-full" name="uom" placeholder="UOM" required>
                        <input type="text" class="form-control w-full" name="stock" placeholder="Stock" required>
                    </div>
                    <button type="submit" class="mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Submit</button>
                </div>
            </form>

            <!-- Product List -->
            <h3 class="mt-4 text-xl font-semibold">Products from <?php echo htmlspecialchars($supplier['name']); ?></h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs table table-striped">
                    <thead class="rounded-t-lg dark:bg-gray-300">
                        <tr class="text-right">
                            <th class="p-3 text-center">Subclass</th>
                            <th class="p-3 text-center">Description</th>
                            <th class="p-3 text-center">Price</th>
                            <th class="p-3 text-center">UOM</th>
                            <th class="p-3 text-center">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = pg_fetch_assoc($products_result)) { ?>
                            <tr class="text-right border-b border-opacity-20 dark:border-gray-300 dark:bg-gray-100">
                                <td class="px-3 py-2 text-center"><?php echo htmlspecialchars($product['subclass']); ?></td>
                                <td class="px-3 py-2 text-center"><?php echo htmlspecialchars($product['description']); ?></td>
                                <td class="px-3 py-2 text-center"><?php echo htmlspecialchars($product['price']); ?></td>
                                <td class="px-3 py-2 text-center"><?php echo htmlspecialchars($product['uom']); ?></td>
                                <td class="px-3 py-2 text-center"><?php echo htmlspecialchars($product['stock']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</body>

</html>
