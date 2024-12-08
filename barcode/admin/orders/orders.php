<?php


// Connect to the database
require 'db.php'; // Adjust with your database connection file

// Fetch orders from the database
$query = "SELECT * FROM orders ";
$result = $db->query($query);

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<main class="lg:ml-64 pt-16 px-4">
        <h1 class="text-2xl font-bold mb-4">Orders Management</h1>
        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="table-auto w-full text-left">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6">Order ID</th>
                        <th class="py-3 px-6">Seller Name</th>
                        <th class="py-3 px-6">Order Date</th>
                        <th class="py-3 px-6">Total Price</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6"><?php echo htmlspecialchars($row['order_id']); ?></td>
                        <td class="py-3 px-6"><?php echo htmlspecialchars($row['seller_name']); ?></td>
                        <td class="py-3 px-6"><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td class="py-3 px-6">$<?php echo htmlspecialchars($row['total_price']); ?></td>
                        <td class="py-3 px-6">
                            <span class="bg-<?php echo $row['status'] == 'Completed' ? 'green' : 'yellow'; ?>-200 text-<?php echo $row['status'] == 'Completed' ? 'green' : 'yellow'; ?>-800 text-xs font-semibold px-2 py-1 rounded">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <a href="invoice.php?order_id=<?php echo $row['order_id']; ?>" class="text-blue-500 hover:text-blue-700 mx-2">
                                <i class="fa fa-eye"></i> View
                            </a>
                            <button onclick="markComplete(<?php echo $row['order_id']; ?>)" class="text-yellow-500 hover:text-yellow-700 mx-2">
                                <i class="fa fa-check"></i> Complete
                            </button>
                            <button onclick="cancelOrder(<?php echo $row['order_id']; ?>)" class="text-red-500 hover:text-red-700 mx-2">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function markComplete(orderId) {
            alert(`Marking order ${orderId} as complete.`);
            // Add AJAX request here to update the status
        }

        function cancelOrder(orderId) {
            alert(`Cancelling order ${orderId}.`);
            // Add AJAX request here to update the status
        }
    </script>

<script>
    // URL to check for new orders (you'll create this endpoint)
    const checkOrdersUrl = "check_new_orders.php";
    const audio = new Audio("assets/audio/notification.mp3"); // Make sure this path is correct
    let lastOrderId = <?php echo json_encode($result->num_rows > 0 ? $row['order_id'] : 0); ?>; // Last fetched order ID

    function checkNewOrders() {
        fetch(checkOrdersUrl)
            .then(response => response.json())
            .then(data => {
                // Check if a new order ID is found
                if (data.newOrderId > lastOrderId) {
                    lastOrderId = data.newOrderId;
                    audio.play(); // Play the notification sound
                    alert("New order received!"); // Optional: Visual alert
                    // Reload the page to show the new order
                    window.location.href = window.location.href; // This ensures a fresh reload
                }
            })
            .catch(error => console.error("Error checking new orders:", error));
    }

    // Check for new orders every 3 seconds
    setInterval(checkNewOrders, 3000);
</script>



</body>

