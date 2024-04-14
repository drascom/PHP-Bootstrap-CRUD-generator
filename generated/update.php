<?php
if (empty(getenv('DATABASE_NAME'))) {
    echo 'Database not configured.';
} else {
    $config_database = getenv('DATABASE_NAME');
    include '../db_config.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the 'table' field is present in the POST data
    if (!isset($_POST['table'])) {
        // If the 'table' field is missing, return an error response
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    // If 'id' is not provided, perform an insert operation
    if (!isset($_POST['id'])) {
        // Check if all required fields are present in the POST data
        $requiredFields = ['table']; // Add more fields as needed
        foreach ($requiredFields as $fieldName) {
            if (!isset($_POST[$fieldName])) {
                // If any required field is missing, return an error response
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                exit;
            }
        }

        // Sanitize and validate input data (you can add more validation as needed)

        // Connect to your database (include db_config.php)

        // Prepare and execute the SQL insert query
        $table_name = mysqli_real_escape_string($conn, $_POST['table']);
        $insertQuery = "INSERT INTO $table_name (";
        $values = "";
        foreach ($_POST as $fieldName => $fieldValue) {
            if ($fieldName !== 'table') {
                $insertQuery .= "$fieldName, ";
                $values .= "'" . mysqli_real_escape_string($conn, $fieldValue) . "', ";
            }
        }
        $insertQuery = rtrim($insertQuery, ', ') . ") VALUES (" . rtrim($values, ', ') . ")";

        if (mysqli_query($conn, $insertQuery)) {
            // If insert successful, return success response
            echo json_encode(['success' => 'Record inserted successfully']);
        } else {
            // If insert fails, return error response
            http_response_code(500);
            echo json_encode(['error' => 'Error inserting record: ' . mysqli_error($conn)]);
        }

        // Close database connection
        mysqli_close($conn);

    } else {
        // Prepare and execute the SQL update query
        $table_name = mysqli_real_escape_string($conn, $_POST['table']);
        $record_id = $_POST['id'];
        $updateQuery = "UPDATE $table_name SET ";
        foreach ($_POST as $fieldName => $fieldValue) {
            if ($fieldName !== 'table' && $fieldName !== 'id') {
                $updateQuery .= "$fieldName = '" . mysqli_real_escape_string($conn, $fieldValue) . "', ";
            }
        }
        $updateQuery = rtrim($updateQuery, ', ') . " WHERE id = $record_id";

        if (mysqli_query($conn, $updateQuery)) {
            // If update successful, return success response
            echo json_encode(['success' => 'Record updated successfully']);
        } else {
            // If update fails, return error response
            http_response_code(500);
            echo json_encode(['error' => 'Error updating record: ' . mysqli_error($conn)]);
        }
        // Close database connection
        mysqli_close($conn);
    }
} else {
    // If request method is not POST, return error response
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>