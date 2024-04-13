<?php


// Check if form data and table name are provided
if (isset($_POST['table'])) {
    $table_name = $_POST['table'];
    $selected_database = $_POST['db'];

    // Check if ID is provided for update operation
    if (isset($_POST['id'])) {
        $record_id = $_POST['id'];
        // Build SQL query for updating record
        $update_query = "UPDATE $table_name SET ";
        foreach ($_POST as $key => $value) {
            if ($key !== 'table' && $key !== 'id') {
                $update_query .= "$key = '$value', ";
            }
        }
        // Remove trailing comma and space
        $update_query = rtrim($update_query, ", ");
        $update_query .= " WHERE id = $record_id";

        // Execute the update query
        if ($conn->query($update_query) === TRUE) {
            // Redirect to index page with success message
            header("Location: index.php?success=Record updated successfully.");
            exit();
        } else {
            // Redirect to index page with error message
            header("Location: index.php?error=Error updating record: " . $conn->error);
            exit();
        }
    } else {
        // Redirect to index page with error message
        header("Location: index.php?error=Record ID not provided for update operation.");
        exit();
    }
} else {
    // Redirect to index page with error message
    header("Location: index.php?error=Form data or table name not provided.");
    exit();
}
?>