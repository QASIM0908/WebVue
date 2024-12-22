<?php
include 'connect.php';
session_start();

if (isset($_GET['service_id'])) {
    $service_id = $_GET['service_id'];

    $sql = "SELECT * FROM services WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        die("Error getting result: " . $stmt->error);
    }

    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
        if ($service === null) {
            die("Error fetching associative array: " . $stmt->error);
        }
    } else {
        echo "Service not found.";
        exit;
    }
} else {
    echo "Invalid service ID.";
    exit;
}

// ***CRUCIAL CHANGE: Check if it's NOT a POST request and display the form***
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        </head>
    <body>
        <div class="container mt-5">
            <h1>Order Service</h1>
            <p><strong>Service:</strong> <?php echo $service['name']; ?></p>
            <p><strong>Description:</strong> <?php echo $service['description']; ?></p>
            <p><strong>Price:</strong> $<?php echo $service['price']; ?></p>

            <form action="order_service.php?service_id=<?php echo $service_id; ?>" method="POST">
                <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" required>
                </div>
                <button type="submit" class="btn btn-primary">Place Order</button>
            </form>
        </div>
        </body>
    </html>
    <?php
    exit; // Very important: Stop execution after displaying the form
} // End of the if ($_SERVER['REQUEST_METHOD'] !== 'POST') block

// Process the order ONLY if it's a POST request
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    echo "You must be logged in to place an order.";
    exit;
}

$quantity = $_POST['quantity'];

$sql = "INSERT INTO orders (user_id, service_id, quantity, order_date) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("iii", $user_id, $service_id, $quantity);

if ($stmt->execute()) {
    header("Location: order_success.php");
    exit;
} else {
    echo "Error placing order: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>