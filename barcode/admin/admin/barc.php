<?php
// Include the PHP QR Code library (download it from https://github.com/datalog/phpqrcode)
include 'phpqrcode/qrlib.php';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
include '../db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the values from the form
    $locationType = $_POST['location_type'];
    $locationNumber = $_POST['location_number'];

    // Prepare the data for the QR code
    $qrData = "Location: $locationType $locationNumber";

    // Prepare the file path to save the QR code
    $fileName = $locationType . $locationNumber . '.png';
    $filePath = 'qrcodes/' . $fileName;

    // Generate and save the QR code
    QRcode::png($qrData, $filePath, QR_ECLEVEL_L, 10);

    // Insert data into the database
    $stmt = $pdo->prepare("INSERT INTO locations (location_type, location_number, qr_code_path) VALUES (:location_type, :location_number, :qr_code_path)");
    $stmt->bindParam(':location_type', $locationType);
    $stmt->bindParam(':location_number', $locationNumber);
    $stmt->bindParam(':qr_code_path', $filePath);

    if ($stmt->execute()) {
        echo "Location added successfully!";
    } else {
        echo "Failed to add location.";
    }

    // Prepare the menu URL (for redirection)
    $menuURL = "menu.php?$locationType=$locationNumber";
}
?>

<main class="lg:ml-64 pt-20 px-4 space-y-8">
  <h2 class="text-2xl font-bold text-gray-800">Generate QR Code for Table or Room</h2>
  <form method="POST" action="" class="bg-white shadow-md rounded-lg p-6 space-y-4">
    <div class="space-y-2">
      <label for="location_type" class="block text-sm font-medium text-gray-700">Select Type</label>
      <select name="location_type" id="location_type" required class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
        <option value="Table">Table</option>
        <option value="Room">Room</option>
      </select>
    </div>
    <div class="space-y-2">
      <label for="location_number" class="block text-sm font-medium text-gray-700">Enter Number</label>
      <input type="text" name="location_number" id="location_number" required class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
    </div>
    <button type="submit" class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
      Generate QR Code
    </button>
  </form>

  <?php
  // Retrieve all QR codes from the database
  $stmt = $pdo->query("SELECT * FROM locations");
  $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Check if there are any locations to display
  if ($locations) {
      echo '<div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">'; // Grid layout
      foreach ($locations as $location) {
          echo '<div class="bg-white p-4 rounded-lg shadow-md">';
          echo "<h3 class='text-lg font-semibold text-gray-800'>{$location['location_type']} Information</h3>";
          echo "<p class='text-gray-600'>{$location['location_type']} Number: {$location['location_number']}</p>";
          echo "<h4 class='text-gray-700 font-medium mt-4'>Scan the QR Code to go to Menu</h4>";
          echo '<img src="' . $location['qr_code_path'] . '" alt="QR Code" class="my-4 max-w-xs mx-auto">';
          echo '<a href="' . $location['qr_code_path'] . '" download="QRCode.png" class="inline-block bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded-md mt-4 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Download QR Code</a>';
          echo '</div>';
      }
      echo '</div>'; // Close grid container
  } else {
      echo '<p>No locations available.</p>';
  }
  ?>

</main>
