<?php
if (empty(getenv('DATABASE_NAME'))) {
    echo 'Database not configured.';
} else {
    $config_database = getenv('DATABASE_NAME');
    include '../db_config.php';

}
// Check if table name is provided
if (isset($_POST['table'])) {
    $table_name = $_POST['table'];

    // Check if record ID is provided (for editing existing record)
    $is_editing = isset($_POST['id']);

    // Get table structure
    $structure_query = "DESCRIBE $table_name";
    $structure_result = $conn->query($structure_query);

    if ($structure_result) {
        // Display the form for inserting new record or editing existing record
        ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_editing ? 'Edit' : 'Add New'; ?> Record</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2><?php echo $is_editing ? 'Edit' : 'Add New'; ?> Record</h2>
        <form id="recordForm" action="update.php" method="post">
            <?php
                    while ($row = $structure_result->fetch_assoc()) {
                if ($row['Field'] !== 'id') {
                    $field_name = $row['Field'];
                    $field_value = '';
                    // If editing, fetch existing data for the fields
                    if ($is_editing) {
                        $record_id = $_POST['id'];
                        $data_query = "SELECT $field_name FROM $table_name WHERE id = $record_id";
                        $data_result = $conn->query($data_query);
                        if ($data_result->num_rows > 0) {
                            $field_data = $data_result->fetch_assoc();
                            $field_value = $field_data[$field_name];
                        }
                        }
                        ?>
            <div class="mb-3">
                <label for="<?php echo $field_name; ?>" class="form-label"><?php echo $field_name; ?>:</label>
                <input type="text" class="form-control" id="<?php echo $field_name; ?>"
                    name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>">
            </div>
            <?php
                }
            }
                    ?>
            <input type="hidden" name="table" value="<?php echo $table_name; ?>">
            <?php if ($is_editing) { ?>
            <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">
            <?php } ?>
        </form>
    </div>
</body>

</html>
<?php
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Table name not provided.";
}

// Close connection
$conn->close();
?>