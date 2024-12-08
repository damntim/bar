<?php
require 'db.php'; // Database connection

// Fetch order and order items
$orderId = $_GET['order_id'] ?? null;

$orderQuery = "SELECT * FROM orders WHERE order_id = ?";
$orderStmt = $db->prepare($orderQuery);
$orderStmt->bind_param("i", $orderId);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();

$itemsQuery = "SELECT * FROM order_items WHERE order_id = ?";
$itemsStmt = $db->prepare($itemsQuery);
$itemsStmt->bind_param("i", $orderId);
$itemsStmt->execute();
$items = $itemsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            body {
                width: 58mm;
                font-size: 12px;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body class="bg-white text-gray-800">
    <div class="p-4">
        <h1 class="text-xl font-bold mb-4">Invoice #<?php echo $order['order_id']; ?></h1>
        <p><strong>Seller:</strong> <?php echo htmlspecialchars($order['seller_name']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
        <table class="w-full mt-4 text-sm border-t border-b">
            <thead>
                <tr class="text-left">
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                    <td>$<?php echo htmlspecialchars($item['subtotal']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <p class="mt-4 font-bold">Total: $<?php echo htmlspecialchars($order['total_price']); ?></p>
    </div>
</body>
</html>
