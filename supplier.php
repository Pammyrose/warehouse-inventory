<?php
$connection = pg_connect("host=localhost port=5432 dbname=warehouse user=postgres password=12345678");

if (!$connection) {
    die("An error occurred while connecting to the database.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_name = trim($_POST['name']);
    $class = trim($_POST['class']); // Get selected classification

    if (!empty($supplier_name) && !empty($class)) {
        // Insert into supplier table
        $query = "INSERT INTO supplier (name) VALUES ($1) RETURNING id";
        $result = pg_query_params($connection, $query, [$supplier_name]);

        if ($result) {
            $supplier_id = pg_fetch_result($result, 0, 'id');

            // Insert into classification table
            $class_query = "INSERT INTO classification (supplier_id, class) VALUES ($1, $2)";
            $class_result = pg_query_params($connection, $class_query, [$supplier_id, $class]);

            if ($class_result) {
                echo "<script>
                    alert('Supplier and Classification added successfully!');
                    window.location.href = 'supplier.php'; 
                </script>";
            } else {
                echo "<script>alert('Error inserting into classification table.');</script>";
            }
        } else {
            echo "<script>alert('Error inserting into supplier table.');</script>";
        }
    } else {
        echo "<script>alert('Supplier name and classification cannot be empty.');</script>";
    }
}

$query = "SELECT supplier.id, supplier.name, classification.class 
          FROM supplier 
          LEFT JOIN classification ON supplier.id = classification.supplier_id
          ORDER BY supplier.id ASC";

$result = pg_query($connection, $query);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Search Suppliers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        body {

            min-height: 100vh;
            margin: 0;
            padding: 0;
            backdrop-filter: blur(1.5px);
            overflow: hidden;
            overflow-y: auto;
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
            overflow: hidden;
            /* Allow content within to scroll if necessary */
            max-height: 100vh;
            box-sizing: border-box;
        }

        .search {
            width: 100%;
            max-width: 300px;
            height: 70px;
        }
    </style>
</head>

<body>
    <?php include("sidebar.php"); ?>

    <div class="ml-20">
        <div class=" bg-white content-wrapper flex items-start justify-center min-h-screen p-4">
            <div
                class="mt-20 container min-w-full p-3 mx-auto rounded-md sm:p-4 dark:text-gray-800 dark:bg-gray-50 opacity-90">
                <form method="POST">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" class="form-control w-full" id="name" name="name" placeholder="Supplier Name"
                            required>

                        <select class="form-control w-full" id="class" name="class" required>
                            <option selected disabled>Classification</option>
                            <option value="Direct Materials - Beverage">Direct Materials - Beverage</option>
                            <option value="Direct Materials - Kitchen">Direct Materials - Kitchen</option>
                            <option value="Direct Materials - Bakery">Direct Materials - Bakery</option>
                            <option value="Supplies & Packaging - Kitchen">Supplies & Packaging - Kitchen</option>
                            <option value="Supplies & Packaging - Beverage">Supplies & Packaging - Beverage</option>
                            <option value="Supplies & Packaging - Bakery">Supplies & Packaging - Bakery</option>
                            <option value="Janitorials">Janitorials</option>
                            <option value="Office Supplies">Office Supplies</option>
                        </select>
                    </div>
                    <button type="submit"
                        class="mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Submit</button>
                </form>
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-2xl font-semibold leading-tight">Suppliers</h2>
                    <div class=" relative text-gray-600 bg-white">
                        <input id="searchInput" type="search" placeholder="Search"
                            class="search mr-14 bg-gray-200 h-10 px-5 pr-10 rounded-full text-sm focus:outline-none">
                        <div class="rounded-lg bg-gray-300 absolute right-0 top-1 p-1 mr-16">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <a href="supplier.php" class="rounded-lg bg-gray-300 absolute right-0 top-1 p-1 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>

                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="rounded-t-lg dark:bg-gray-300">
                            <tr class="text-right">
                                <th class="p-3 text-center">#</th>
                                <th class="p-3 text-center">Supplier</th>
                                <th class="p-3 text-center">Classification</th>
                                <th class="p-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="results">
                            <tr>

                            </tr>
                            <?php
                            $counter = 1;
                            while ($row = pg_fetch_assoc($result)) {
                                echo "<tr class='text-right border-b border-opacity-20 dark:border-gray-300 dark:bg-gray-100'>
            <td class='px-3 py-2 text-center'>$counter</td>
            <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['name']) . "</td>
<td class='px-3 py-2 text-center'>" . htmlspecialchars($row['class'] ?? 'N/A') . "</td>

            <!-- Delete Button -->
            <td class='px-3 py-2 text-center'>
                <form method='POST' action='delete_product.php' style='display:inline;'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>

                    <!-- Edit Icon -->
                    <button style='background:none; border:none; padding:0; cursor:pointer;'>
                     <a href='products.php?id=" . $row['id'] . "' class='text-black hover:text-blue-700'>
                        <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='size-6'>
                          <path stroke-linecap='round' stroke-linejoin='round' d='M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z' />
                            <path stroke-linecap='round' stroke-linejoin='round' d='M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z' />
                        </svg>


                        </a>
                    </button>

                    <!-- Delete Icon -->
                    <button title='Delete' type='button' onclick='confirmDelete(this.form)' style='background:none; border:none; padding:0; cursor:pointer;'>
                        <svg class='text-black hover:text-blue-500 w-6 h-6 ' aria-hidden='true' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'>
                            <path stroke-linecap='round' stroke-linejoin='round' d='m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0'/>
                        </svg>
                    </button>
                </form>
            </td>
        </tr>";
                                $counter++;
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.getElementById("searchInput");
            const resultsGrid = document.getElementById("results");

            searchInput.addEventListener("input", () => {
                const query = searchInput.value.trim();

                fetch(`?ajax=1&query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        resultsGrid.innerHTML = "";

                        if (data.error) {
                            resultsGrid.innerHTML = `<tr><td colspan="2" class="text-center text-red-500">${data.error}</td></tr>`;
                            return;
                        }

                        if (data.length > 0) {
                            data.forEach((item, index) => {
                                resultsGrid.innerHTML += `
                                    <tr class="text-right border-b border-opacity-20 dark:border-gray-300 dark:bg-gray-100">
                                        <td class="px-3 py-2 text-center">${index + 1}</td>
                                        <td class="px-3 py-2 text-center">${item.name}</td>
                                       
                                    </tr>`;
                            });
                        } else {
                            resultsGrid.innerHTML = `<tr><td colspan="2" class="text-center text-gray-500">No results found</td></tr>`;
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching data:", error);
                        resultsGrid.innerHTML = `<tr><td colspan="2" class="text-center text-red-500">An error occurred.</td></tr>`;
                    });
            });
        });
    </script>
</body>

</html>