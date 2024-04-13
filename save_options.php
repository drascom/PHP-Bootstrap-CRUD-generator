<?php
$config_database = getenv('DATABASE_NAME');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // MySQL database credentials
    include 'db_config.php';
    // Get table name from the form data
    $table_name = $_POST['table'];
    // Initialize an array to store field options
    $field_options = [];
    // Loop through the input types for each field
    foreach ($_POST['input_types'] as $field_name => $input_type) {
        // Store field name and input type in the array
        $field_options[$field_name]['input_type'] = $input_type;
        // Check if the field is visible at list
        $field_options[$field_name]['visible_at_list'] = isset($_POST['visible_at_list'][$field_name]);
        // Check if the field is visible at form
        $field_options[$field_name]['visible_at_form'] = isset($_POST['visible_at_form'][$field_name]);
    }

    // Convert the array to JSON format
    $json_options = json_encode($field_options);

    // Insert or update the table options in the database
    $sql = "INSERT INTO table_options (table_name, options) VALUES (?, ?) ON DUPLICATE KEY UPDATE options = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $table_name, $json_options, $json_options);

    if ($stmt->execute()) {
        $stmt->close();
        // Close connection
        $conn->close();
        // Redirect to index.php with success message
        header("Location: /?success=saved.");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        // Redirect to index.php with error message
        header("Location: /?error=Error: " . $conn->error);
        exit();
    }

} else {
    // Redirect to geneindexrate.php with error message
    header("Location: /?error=Invalid request.");
    exit();
}
?>