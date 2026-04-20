<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Festival Essen</title>
    <link rel="stylesheet" href="style.css">
</head>

<php require "php_functions.php"; ?>
<body>

    <div class="container">
        <h1 onclick="goToPage('menu.php')">Essen & Getränke</h1>

        <table>
            <tr>
                <td><button onclick="goToPage('order_drinks.php')">Getränke bestellen</button></td>
            </tr>
            <tr>
                <td><button onclick="goToPage('order_pizza.php')">Pizza bestellen</button></td>
            </tr>
            <tr>
                <td>
                    <button onclick="goToPage('order_asia.php')">Asia bestellen</button>
                </td>
            </tr>
            <tr>
                <td>
                     <button onclick="goToPage('menu.php')">Zurück</button> 
                </td>
            </tr>
        </table>
    </div>

    <script src="script.js"></script>
</body>

</html>