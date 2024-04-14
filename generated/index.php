<?php
$config_database = getenv('DATABASE_NAME');
include '../db_config.php';

// Check if table name is provided in the GET request
if (isset($_GET['table']) && !empty($_GET['table'])) {
    $table_name = $_GET['table'];

    // Get table structure
    $structure_query = "DESCRIBE $table_name";
    $structure_result = $conn->query($structure_query);

    // Get table data
    $data_query = "SELECT * FROM $table_name";
    $data_result = $conn->query($data_query);
} else {
    // Error handling if table name is not provided in the GET request
    echo "Error: Table name not provided.";
}
// check is edit or insertt with id
$is_editing = isset($_GET['id']) ? true : false;

// Display table if both structure and data are available
if (isset($structure_result) && isset($data_result)) {
    $table = true;
} else {
    $table = false;
    // Error handling if structure or data is not available
    echo "Error: Table structure or data not found.";
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $table_name; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
    /* Content area */
    .content {
        padding: 1rem;
    }
    </style>
</head>

<body>
    <!-- notification -->
    <!-- Stacked toasts -->
    <div id="toastContainer" class="toast-container position-absolute top-0 start-50 translate-middle-x">
        <!-- Append toasts here with JavaScript -->
    </div>



    <!-- Content area -->
    <div class="content">
        <?php if ($table) { ?>
        <nav class="navbar navbar-light bg-light mb-3">
            <div class="container-fluid">
                <h2 class="navbar-brand mb-0">Table: <?php echo $table_name; ?></h2>
                <button class="btn btn-primary d-block edit-btn" data-table="<?= $table_name; ?>" type="button"> <i
                        class="fas fa-plus"></i> Add
                    New</button>
            </div>
        </nav>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <?php
                        // Add table headers
                        while ($row = $structure_result->fetch_assoc()) {
                            echo "<th>" . $row['Field'] . "</th>";
                        }
                        ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Add table data
                    while ($row = $data_result->fetch_assoc()) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            echo "<td>" . $value . "</td>";
                        }
                        // Add action buttons
                        echo '<td>
                            <button class="btn btn-primary btn-sm edit-btn" data-table="' . $table_name . '" data-id="' . $row['id'] . '">Edit</button>
                            </td>';
                        echo "</tr>";
                    }
                    ?>
            </tbody>
        </table>

        <!-- Offcanvas -->
        <div class="offcanvas offcanvas-end bg-light" tabindex="-1" id="sidebar" aria-labelledby="offcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasLabel">Edit Form</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div id="formFields"></div>
                <button id="submitBtn" type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
        <?php } else { ?>
        table bulunamadi
        <?php } ?>
    </div>

    <!-- Bootstrap JavaScript (optional, if you need components that require JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // GET EDIT FORM IN SIDEBAR    
    var offcanvas = new bootstrap.Offcanvas(sidebar);

    document.addEventListener('DOMContentLoaded', function() {
        // Get all edit buttons
        var editButtons = document.querySelectorAll('.edit-btn');

        // Add click event listener to each edit button
        editButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                // Extract the ID and table name from the data attributes
                var id = button.getAttribute('data-id');
                var table = button.getAttribute('data-table');

                // Send AJAX request to the server
                var xhr = new XMLHttpRequest();
                xhr.open('POST',
                    'insert-edit.php'); // Replace 'insert-edit.php' with your server endpoint
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Open the sidebar
                        offcanvas.toggle();

                        // Update the sidebar HTML
                        document.getElementById('formFields').innerHTML = xhr.responseText;
                    } else {
                        // Handle errors
                        console.error('Request failed. Status: ' + xhr.status);
                        // You can display an error message to the user here if needed
                    }
                };
                xhr.send('table=' + table + '&id=' + id);
            });
        });
    });
    //  SUBMIT FUNCTION
    document.addEventListener('DOMContentLoaded', function() {
        var submitBtn = document.getElementById('submitBtn');
        submitBtn.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default form submission
            var formData = new FormData(recordForm); // Create FormData object
            // Send AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        console.log(xhr.responseText);
                        var decodedMessage = JSON.parse(xhr.responseText);
                        var header = Object.keys(decodedMessage)[0];
                        var message = decodedMessage[header];
                        createToast(header, message, 2000);
                        // close side bar
                        offcanvas.toggle();
                    } else {
                        // Handle HTTP errors
                        console.error('HTTP Error: ', xhr.status);
                    }
                }
            };
            xhr.send(formData);
        });
    });
    // NOTITICATION FUNCTION
    function createToast(header, message, delay) {
        var toastContainer = document.getElementById('toastContainer');
        var newToast = document.createElement('div');
        newToast.classList.add('toast');
        newToast.setAttribute('role', 'alert');
        newToast.setAttribute('aria-live', 'assertive');
        newToast.setAttribute('aria-atomic', 'true');

        newToast.innerHTML = `
    <div class="toast-header bg-success text-white">
      <strong class="me-auto">${header}</strong>
      <small>just now</small>
      <button type="button" class="btn-close text-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      ${message}
    </div>
   
  `;
        toastContainer.appendChild(newToast);

        // Show the toast
        var toast = new bootstrap.Toast(newToast);
        toast.show();

        // Remove the toast after delay
        setTimeout(function() {
            newToast.remove();
        }, delay);
    }
    </script>

</body>

</html>