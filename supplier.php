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
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include("navbar.php"); ?>
    <div class="bg-white content-wrapper flex items-start justify-center min-h-screen p-4">
        <div class="mt-20 container min-w-full p-3 mx-auto rounded-md sm:p-4 dark:text-gray-800 dark:bg-gray-50 opacity-90">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-2xl font-semibold leading-tight">Suppliers</h2>
                <div class=" relative text-gray-600 bg-white">
                    <input id="searchInput" type="search" placeholder="Search" class="search mr-14 bg-gray-200 h-10 px-5 pr-10 rounded-full text-sm focus:outline-none">
                    <div class="rounded-lg bg-gray-300 absolute right-0 top-1 p-1 mr-16">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>


                    </div>
                    <a href="add_supplier.php" class="rounded-lg bg-gray-300 absolute right-0 top-1 p-1 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>

                    </a>
                </div>
            </div>
            <form method="POST">
                <div class=" gap-4">
                    <input type="text" class="form-control w-1/4" id="name" name="name" placeholder="Supplier Name" required>
                    <div class="grid grid-cols-4 gap-4 mt-2">
                        <input type="text" class="form-control w-full" id="subclass" name="subclass" placeholder="Subclass" required>
                        <input type="text" class="form-control w-full" id="description" name="description" placeholder="Description" required>
                        <input type="text" class="form-control w-full" id="price" name="price" placeholder="Price" required>
                        <input type="text" class="form-control w-full" id="uom" name="uom" placeholder="UOM" required>
                        <input type="text" class="form-control w-full" id="stock" name="stock" placeholder="Stock" required>
                    </div>
                    <button type="submit" class="mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Submit</button>
                </div>
            </form>



            <h2 class="text-xl font-normal leading-tight mt-4">Suppliers</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="rounded-t-lg dark:bg-gray-300">
                        <tr class="text-right">
                            <th class="p-3 text-center">Subclass</th>
                            <th class="p-3 text-center">Description</th>
                            <th class="p-3 text-center">Price</th>
                            <th class="p-3 text-center">UOM</th>
                            <th class="p-3 text-center">Stock Onhand</th>
                            <th class="p-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="results">
                        <?php
                        if ($result_display_product) {
                            while ($row = pg_fetch_assoc($result_display_product)) {
                                echo "<tr class='border-b'>
                <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['subclass']) . "</td>
                <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['description']) . "</td>
                <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['price']) . "</td>
                <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['uom']) . "</td>
                <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['stock']) . "</td>
                <td class='px-3 py-2 text-center'>
                    <form method='POST' action='supplier_delete.php' style='display:inline;'>
                        <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                        <button title='Edit' type='button'  class='text-black hover:text-blue-500 w-6 h-6 ' style='background:none; border:none; padding:0; cursor:pointer;'>
                                        <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='size-6'>
                                        <path stroke-linecap='round' stroke-linejoin='round' d='m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10' />
                                      </svg>
                                        </button>
                        <button title='Delete' type='button' class='text-black hover:text-red-500 w-6 h-6' onclick='confirmDelete(this.form)'>
                            <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='w-6 h-6'>
                                <path stroke-linecap='round' stroke-linejoin='round' d='m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0' />
                            </svg>
                        </button>
                    </form>
                </td>
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
<script>
    // document.addEventListener("DOMContentLoaded", () => {
    //     const searchInput = document.getElementById("searchInput");
    //     const resultsGrid = document.getElementById("results");

    //     searchInput.addEventListener("input", () => {
    //         const query = searchInput.value.trim();

    //         fetch( ? ajax = 1 & query = $ {
    //                 encodeURIComponent(query)
    //             })
    //             .then(response => response.json())
    //             .then(data => {
    //                 resultsGrid.innerHTML = "";

    //                 if (data.error) {
    //                     resultsGrid.innerHTML = < tr > < td colspan = "2"
    //                     class = "text-center text-red-500" > $ {
    //                         data.error
    //                     } < /td></tr > ;
    //                     return;
    //                 }

    //                 if (data.length > 0) {
    //                     data.forEach((item, index) => {
    //                         resultsGrid.innerHTML +=
    //                             <
    //                             tr class = "text-right border-b border-opacity-20 dark:border-gray-300 dark:bg-gray-100" >
    //                             <
    //                             td class = "px-3 py-2 text-center" > $ {
    //                                 index + 1
    //                             } < /td> <
    //                         td class = "px-3 py-2 text-center" > $ {
    //                             item.name
    //                         } < /td> < /
    //                         tr > ;
    //                     });
    //                 } else {
    //                     resultsGrid.innerHTML = < tr > < td colspan = "2"
    //                     class = "text-center text-gray-500" > No results found < /td></tr > ;
    //                 }
    //             })
    //             .catch(error => {
    //                 console.error("Error fetching data:", error);
    //                 resultsGrid.innerHTML = < tr > < td colspan = "2"
    //                 class = "text-center text-red-500" > An error occurred. < /td></tr > ;
    //             });
    //     });
    // });

    function confirmDelete(form) {
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
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_record';
                deleteInput.value = '1';
                form.appendChild(deleteInput);

                form.submit(); // Submit the form if confirmed
            }
        });
    }
</script>

</html>