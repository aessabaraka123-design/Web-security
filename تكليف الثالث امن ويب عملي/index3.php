<?php
include "datab.php";
$result = "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>تكليف الثالث امن ويب عملي</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #ffffff;
            padding: 30px;
            width: 350px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #2c7be5;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
        }
        button:hover {
            background-color: #1a5fd0;
        }
        .result {
            margin-top: 15px;
            font-weight: bold;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>فحص ثغارات</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Enter username" required>
        <button type="submit" name="search">Search</button>
    </form>
    <?php
    if (isset($_POST['search'])) {
        $username = $_POST['username'];
        $stmt = $conn->prepare("SELECT * FROM Assignment WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch()) {
                echo "<div class='result success'>User Found: "
                    . htmlspecialchars($row['username']) .
                    "</div>";
            }
        } else {
            echo "<div class='result error'>No results found</div>";
        }
    }
    ?>
</div>
</body>
</html>
