<?php
$config_database = getenv('DATABASE_NAME');
include '../db_config.php';

// Check if table name is provided in the GET request
if (isset($_GET['table'])) {
    $table_name = $_GET['table'];

    // Get table structure
    $structure_query = "DESCRIBE $table_name";
    $structure_result = $conn->query($structure_query);

    // Get table data
    $data_query = "SELECT * FROM $table_name";
    $data_result = $conn->query($data_query);

    // Display table if both structure and data are available
    if ($structure_result && $data_result) {
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $table_name; ?></title>
            <!-- Bootstrap CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>

        <body>
            <div class="container mt-5">
                <nav class="navbar navbar-light bg-light mb-3">
                    <div class="container-fluid">
                        <h2 class="navbar-brand mb-0">Table: <?php echo $table_name; ?></h2>
                        <a href='insert-edit.php?db=<?php echo $selected_database; ?>&table=<?php echo $table_name; ?>'
                            class='btn btn-success'>Insert New</a>
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
                            echo "<td><a href='insert-edit.php?table=$table_name&id={$row['id']}' class='btn btn-primary btn-sm me-2'>Edit</a>
                <a href='delete.php?table=$table_name&id={$row['id']}' class='btn btn-danger btn-sm'>Delete</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Bootstrap JavaScript (optional, if you need components that require JS) -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        </body>

        </html>
        <?php
    } else {
        // Error handling if structure or data is not available
        echo "Error: Table structure or data not found.";
    }
} else {
    // Error handling if table name is not provided in the GET request
    echo "Error: Table name not provided.";
}
?>