<!DOCTYPE html>
<html>
<head>
    <title>Simple Web App</title>
</head>
<body>
    <h1>Product Management</h1>

    <?php
    // Database credentials (replace with your actual RDS details)
    $servername = "ecomm-db.caajzpylwb8x.us-east-1.rds.amazonaws.com"; // Replace with your RDS Endpoint
    $username = "admin"; // Your RDS master username
    $password = "password123456"; // Your RDS master password
    $dbname = "webappdb"; // Your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle form submission (insert new product)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $productName = $_POST["productName"];
        $productDescription = $_POST["productDescription"];

        $sql = "INSERT INTO products (name, description) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $productName, $productDescription);

        if ($stmt->execute()) {
            echo "<p>New product added successfully!</p>";
        } else {
            echo "<p>Error adding product: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }

    // Delete product (handle GET request with delete ID)
    if (isset($_GET["delete_id"])) {
        $deleteId = $_GET["delete_id"];
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $deleteId);

        if ($stmt->execute()) {
            echo "<p>Product deleted successfully!</p>";
        } else {
            echo "<p>Error deleting product: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }

    // Fetch and display products
    $sql = "SELECT id, name, description FROM products";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Product List</h2>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Actions</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["description"] . "</td>";
            echo "<td><a href='webapp.php?delete_id=" . $row["id"] . "'>Delete</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No products found.</p>";
    }
    $conn->close();
    ?>

    <h2>Add New Product</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Product Name: <input type="text" name="productName"><br><br>
        Description:  <textarea name="productDescription"></textarea><br><br>
        <input type="submit" value="Add Product">
    </form>

    <style>
        table {
            border-collapse: collapse;
            width: 80%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</body>
</html>