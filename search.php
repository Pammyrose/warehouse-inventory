<?php
// Database connection
$connection = pg_connect("host=localhost port=5432 dbname=warehouse user=postgres password=12345678");

if (!$connection) {
    die("Database connection failed.");
}

$data = [];
if (isset($_GET['ajax']) && isset($_GET['query'])) {
    $query = strtoupper(trim($_GET['query']));
    $params = []; // Initialize parameters

    // Base SQL query without search condition
    $sql = "SELECT 
                classification.id AS classification_id,
                classification.class,
                supplier.name AS supplier_name
            FROM classification
            JOIN supplier ON classification.supplier_id = supplier.id";

    // Add WHERE clause if query is not empty
    if (!empty($query)) {
        $sql .= " WHERE UPPER(supplier.name) LIKE $1";
        $params[] = "%$query%";
    }

    // Execute the query
    $result = pg_query_params($connection, $sql, $params);

    // Fetch all rows
    while ($row = pg_fetch_assoc($result)) {
        $data[] = $row;
    }

    // Respond with the data as JSON for AJAX requests
    echo json_encode($data);
    exit; // Stop further processing
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Functionality</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <style>
        .results {
            -webkit-text-stroke: 0.7px black;

        }


        body {

            min-height: 100vh;
            margin: 0;
            padding: 0;
            backdrop-filter: blur(1.5px);
        }

        /* Restore hover effect with gray background change */
        .hover-item:hover {
            background-color: #6b7280;
            /* Darker gray background on hover (hover:bg-gray-500) */
            transform: scale(1.04);
            /* Slight zoom effect */
            transition: all 0.3s ease-in-out;
            /* Smooth transition for scaling */
        }



        .content-wrapper {
            position: relative;
            z-index: 1;
            /* Place above the overlay */
        }

        .bg {
            background-color: #3366CC;
        }

        .text-bg {
            color: #3366CC;
        }
    </style>
</head>

<body>
    <?php include("navbar.php"); ?>
    <div class="bg-white content-wrapper flex flex-col p-10 py-6 min-h-screen">
        <!-- Search Form -->
        <div class="bg items-center justify-between w-full lg:w-1/2 flex rounded-full shadow-lg p-2 mt-20 mx-auto sticky opacity-80"
            style="top: 5px">
            <input id="searchInput"
                class="font-bold uppercase rounded-full w-full py-4 pl-4 text-black bg-gray-100 leading-tight focus:outline-none focus:shadow-outline lg:text-lg text-sm"
                type="text" placeholder="Search Supplier">
        </div>

        <!-- Results Section -->
        <div id="resultsSection" class="flex flex-col gap-4 lg:p-4 p-2 rounded-lg m-2 hidden">
            <div class="text-bg results lg:text-4xl md:text-xl text-lg lg:p-3 p-1 font-black text-border ">Products
            </div>
            <div id="resultsGrid" class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-2 gap-4 p-4"></div>
        </div>

    </div>

    <script>
        // JavaScript to handle live search with AJAX
        const searchInput = document.getElementById("searchInput");
        const resultsSection = document.getElementById("resultsSection");
        const resultsGrid = document.getElementById("resultsGrid");

        searchInput.addEventListener("input", function () {
            const query = searchInput.value.trim();

            if (query.length === 0) {
                resultsSection.classList.add("hidden");
                resultsGrid.innerHTML = '';
                return;
            }

            // Fetch data using AJAX
            fetch(`?ajax=1&query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    // Show results section
                    resultsSection.classList.remove("hidden");

                    // Clear previous results
                    resultsGrid.innerHTML = '';

                    // Populate the results
                    if (data.length > 0) {
                        data.forEach(item => {
                            const resultItem = document.createElement("div");
                            resultItem.classList.add("p-4", "rounded-lg", "shadow-lg", "bg", "cursor-pointer", "focus:outline-none", "lg:rounded-full", "md:rounded-full", "hover-item", "hover:bg-gray-400", "border-2");
                            resultItem.innerHTML = `
                        
                                <div class="font-bold text-lg text-white hover:text-black"> ${item.class}</div>
                            `;
                            resultsGrid.appendChild(resultItem);
                        });
                    } else {
                        resultsGrid.innerHTML = `<div class="text-gray-500 text-center">No results found</div>`;
                    }
                })
                .catch(error => {
                    console.error("Error fetching data: ", error);
                });
        });
    </script>
</body>

</html>