<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>
<style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;

        font-size: 0;
        font-family: Helvetica, Verdana, sans-serif;
    }

    nav {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        padding: 16px 20px 30px 20px;
        display: flex;
        align-items: center;
        transition: 0.3s ease-out;


        text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
        background-color: #3366cc;
        color: white;
        font-size: 16px;
        z-index: 1000;

        &.mask {
            top: 150px;
            mask-image: linear-gradient(black 70%, transparent);
            -webkit-mask-image: linear-gradient(black 70%, transparent);
        }

        &.mask-pattern {
            top: 300px;
            mask-image: url("data:image/svg+xml, %3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 12.21 10.57%27%3E%3Cpath fill=%27%23ffffff%27 d=%27M6.1 0h6.11L9.16 5.29 6.1 10.57 3.05 5.29 0 0h6.1z%27/%3E%3C/svg%3E"), linear-gradient(black calc(100% - 30px), transparent calc(100% - 30px));
            mask-size: auto 30px, 100% 100%;
            mask-repeat: repeat-x, no-repeat;
            mask-position: left bottom, top left;

            -webkit-mask-image: url("data:image/svg+xml, %3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 12.21 10.57%27%3E%3Cpath fill=%27%23ffffff%27 d=%27M6.1 0h6.11L9.16 5.29 6.1 10.57 3.05 5.29 0 0h6.1z%27/%3E%3C/svg%3E"), linear-gradient(black calc(100% - 30px), transparent calc(100% - 30px));
            -webkit-mask-size: auto 30px, 100% 100%;
            -webkit-mask-repeat: repeat-x, no-repeat;
            -webkit-mask-position: left bottom, top left;
        }

        @media (min-width: 640px) {
            padding: 16px 50px 30px 50px;
        }
    }

    nav.is-hidden {
        transform: translateY(-100%);
    }

    a {
        color: white;
        text-decoration: none;


    }

    .list {
        list-style-type: none;
        margin-left: auto;
        display: none;


        @media (min-width: 640px) {
            display: flex;
        }

        li {
            margin-left: 20px;
        }
    }

    .a:hover {
        color: #fff;
    }



    .menu {
        display: inline-block;
        padding: 0;
        font-size: 0;
        background: none;
        border: none;
        margin-left: 20px;
        filter: drop-shadow(0 0 5px rgba(0, 0, 0, .5));

        &::before {
            content: url("data:image/svg+xml, %3Csvg%20xmlns=%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox=%270%200%2024.8%2018.92%27%20width=%2724.8%27%20height=%2718.92%27%3E%3Cpath%20d=%27M23.8,9.46H1m22.8,8.46H1M23.8,1H1%27%20fill=%27none%27%20stroke=%27%23fff%27%20stroke-linecap=%27round%27%20stroke-width=%272%27%2F%3E%3C%2Fsvg%3E")
        }

        @media (min-width: 640px) {
            display: none;
        }
    }
</style>

<body>
    <nav>
        <a href="#">Mask with linear-gradient</a>
        <ul class="list">
            <li><a href="search.php">Home</a></li>
            <li><a href="supplier.php">Suppliers</a></li>
            <li><a href="list.php">In</a></li>
            <li><a href="list.php">Out</a></li>
            <li><a href="#">Logout</a></li>
        </ul>

        <button class="menu">Menu</button>
    </nav>
</body>

</html>