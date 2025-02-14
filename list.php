<?php
$connection = pg_connect("host=localhost port=5432 dbname=warehouse user=postgres password=12345678");

if (!$connection) {
    die("An error occurred while connecting to the database.");
}

// Handle AJAX requests
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $searchQuery = isset($_GET['query']) ? $_GET['query'] : '';
    $searchQuery = pg_escape_string($connection, $searchQuery); // Sanitize input

    $query = "SELECT * FROM supplier WHERE name ILIKE '%$searchQuery%'"; // Case-insensitive search
    $result = pg_query($connection, $query);

    if (!$result) {
        echo json_encode(["error" => "An error occurred while fetching data."]);
        exit;
    }

    $suppliers = [];
    while ($row = pg_fetch_assoc($result)) {
        $suppliers[] = $row;
    }

    echo json_encode($suppliers);
    exit;
}

// Default search query for initial page load
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM supplier WHERE name ILIKE '%$searchQuery%'";
$result = pg_query($connection, $query);

if (!$result) {
    die("An error occurred while fetching data.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Search Suppliers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
            /* Black overlay with 50% opacity */
            z-index: 0;
            /* Place below all content */
        }

        .content-wrapper {
            position: relative;
            z-index: 1;
            /* Place above the overlay */
        }

        .search {
            width: 100%;
            max-width: 300px;
            height: 70px;
        }
    </style>
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
                    <a href="supplier.php" class="rounded-lg bg-gray-300 absolute right-0 top-1 p-1 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
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
                            <th class="p-3 text-center">Name</th>
                            <th class="p-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="results">
                        <?php
                        $counter = 1;
                        while ($row = pg_fetch_assoc($result)) {
                            echo "<tr class='text-right border-b border-opacity-20 dark:border-gray-300 dark:bg-gray-100'>
                                    <td class='px-3 py-2 text-center'>$counter</td>
                                    <td class='px-3 py-2 text-center'>" . htmlspecialchars($row['name']) . "</td>
                                    <td class='px-3 py-2 text-center'> <form method='POST' action='delete_product.php' style='display:inline;'>
                                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                                    <button title='Delete' type='button'  class='text-black hover:text-blue-500 w-6 h-6 ' onclick='confirmDelete(this.form)' style='background:none; border:none; padding:0; cursor:pointer;'>
                                    <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='size-6'>
                                    <path stroke-linecap='round' stroke-linejoin='round' d='m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10' />
                                  </svg>
                                    </button>
                                    <button title='Delete' type='button' onclick='confirmDelete(this.form)' style='background:none; border:none; padding:0; cursor:pointer;'>
                                        <svg class='text-black hover:text-blue-500 w-6 h-6 ' aria-hidden='true' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'>
                                            <path stroke-linecap='round' stroke-linejoin='round' d='m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0' />
                                        </svg>
                                    </button>
                                </form> </td>
                                  </tr>";
                            $counter++;
                        }
                        ?>
                    </tbody>
                </table>
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