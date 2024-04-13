<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Options</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h1 class="mb-4">Generate Page</h1>

        <?php
        // Check if success message is present in the URL
        if (isset($_GET['success'])) {
            $success_message = $_GET['success'];
            echo "<div class='alert alert-success' role='alert'>$success_message</div>";
        }

        // Check if error message is present in the URL
        if (isset($_GET['error'])) {
            $error_message = $_GET['error'];
            echo "<div class='alert alert-danger' role='alert'>$error_message</div>";
        }
        if (isset($_GET['db'])) {
            $selected_database = $_GET['db'];
            echo "<script>var selected_database = '" . $selected_database . "';</script>";
            // set db config to get tables
            include 'db_config.php';

            // Sanitize the input to prevent SQL injection
            $new_database_name = mysqli_real_escape_string($conn, $selected_database);

            // Query to get the current database name
            $get_current_database_name_query = "SELECT database_name FROM settings WHERE id = 1";
            $result = mysqli_query($conn, $get_current_database_name_query);

            if (mysqli_num_rows($result) > 0) {
                // Fetch the current database name
                $row = mysqli_fetch_assoc($result);
                $current_database_name = $row['database_name'];

                // Check if the new database name is different from the current one
                if ($new_database_name != $current_database_name) {
                    // Update the database name
                    $update_query = "UPDATE settings SET database_name = '$new_database_name' WHERE id = 1";
                    if (mysqli_query($conn, $update_query)) {
                        echo "Database name updated successfully.";
                    } else {
                        echo "Error updating record: " . mysqli_error($conn);
                    }
                } else {
                    echo "Database name is already up to date.";
                }
            } else {
                // If no record with id = 1 exists, insert a new record
                $insert_query = "INSERT INTO settings (id, database_name) VALUES (1, '$new_database_name')";
                if (mysqli_query($conn, $insert_query)) {
                    echo "New record created successfully.";
                } else {
                    echo "Error inserting record: " . mysqli_error($conn);
                }
            }
        } else {
            ?>
            <?php
            // set db config to get tables
            include 'db_config.php';
            ?>
            <form id="database_select" method="GET">
                <label for="database_select" class="form-label">Select Database:</label>
                <select id="database_select" name="db" class="form-select mb-3">
                    <?php
                    // Veritabanlarını sorgula
                    $sql = "SHOW DATABASES";
                    $result = $conn->query($sql);
                    // Veritabanlarını dropdown listesine ekle
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $database_name = $row["Database"];
                            echo "<option value='$database_name'>$database_name</option>";
                        }
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <?php
        }
        ?>

        <?php
        if (isset($selected_database)) {
            // Read all tables in the database
            $tables_query = "SHOW TABLES";
            $tables_result = $conn->query($tables_query);

            if ($tables_result->num_rows > 0) {
                // Loop through each table
                while ($row = $tables_result->fetch_row()) {
                    $table_name = $row[0];
                    // Skip the table_options table
                    if ($table_name === 'table_options') {
                        continue;
                    }
                    // Get table structure
                    $structure_query = "DESCRIBE $table_name";
                    $structure_result = $conn->query($structure_query);

                    if ($structure_result) {
                        // Display form for table options within a card
                        ?>
                        <div class="accordion" id="accordion-<?php echo $table_name; ?>">
                            <div class="card mb-2">
                                <div class="card-header" id="heading-<?php echo $table_name; ?>" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-<?php echo $table_name; ?>" aria-expanded="false"
                                    aria-controls="collapse-<?php echo $table_name; ?>">
                                    <h2 class="mb-0">
                                        <button class="btn btn-outline-primary collapsed" type="button">
                                            <small><span class="text-muted">Table Name:</span></small> <?php echo $table_name; ?>
                                            <i class="bi bi-caret-down"></i> <!-- Caret icon -->
                                        </button>
                                    </h2>
                                </div>
                            </div>
                            <div id="collapse-<?php echo $table_name; ?>" class="collapse"
                                aria-labelledby="heading-<?php echo $table_name; ?>" data-bs-parent="#accordionExample">
                                <div class="card-body">
                                    <form action="save_options.php" method="post">
                                        <input type="hidden" name="table" value="<?php echo $table_name; ?>">
                                        <input type="hidden" name="db" value="<?php echo $selected_database; ?>">
                                        <?php
                                        $saved_options_query = "SELECT options FROM table_options WHERE table_name = '$table_name'";
                                        $saved_options_result = $conn->query($saved_options_query);
                                        if ($saved_options_result->num_rows > 0) {
                                            $row = $saved_options_result->fetch_assoc();
                                            $saved_options = json_decode($row['options'], true);
                                        }
                                        // Loop through each field in the table
                                        while ($field = $structure_result->fetch_assoc()) {
                                            $field_name = $field['Field'];
                                            // Skip the 'id' field
                                            if ($field_name === 'id') {
                                                continue;
                                            }
                                            ?>
                                            <div class="card mb-1">
                                                <div class="card-header">
                                                    <label for="<?php echo $field_name; ?>" class="form-label"><span
                                                            class="text-muted">Field:
                                                        </span><b><?php echo $field_name; ?></b>
                                                    </label>
                                                </div>
                                                <div class="card-body">
                                                    <div class="">
                                                        <div class="d-inline-block mx-3">
                                                            <select class="form-select" name="input_types[<?php echo $field_name; ?>]"
                                                                id="<?php echo $field_name; ?>">
                                                                <?php
                                                                // Available input types
                                                                $input_types = [
                                                                    'text',
                                                                    'textarea',
                                                                    'number',
                                                                    'email',
                                                                    'password',
                                                                    'date',
                                                                    'time',
                                                                    'datetime-local',
                                                                    'file',
                                                                    'image',
                                                                    'select',
                                                                    'checkbox',
                                                                    'radio',
                                                                    'switch'
                                                                ];
                                                                // Get saved options from the database
                                                                if (isset($saved_options[$field_name]['input_type'])) {
                                                                    // Loop through each field in the table
                                                                    foreach ($input_types as $input_type) {
                                                                        // Check if the saved option exists for the field
                                                                        $selected = $saved_options[$field_name]['input_type'] === $input_type ? 'selected' : '';
                                                                        echo "<option value='$input_type' $selected>$input_type</option>";
                                                                    }
                                                                } else {
                                                                    // If no saved options found, display all options as default
                                                                    foreach ($input_types as $input_type) {
                                                                        echo "<option value='$input_type'>$input_type</option>";
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-check form-switch d-inline-block pl-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="visible_at_list[<?php echo $field_name; ?>]" value="true"
                                                                id="<?php echo $field_name; ?>_list" <?php
                                                                   if (isset($saved_options[$field_name]['input_type'])) {
                                                                       echo $saved_options[$field_name]['visible_at_list'] == '1' ? 'checked' : '';
                                                                   } else {
                                                                       echo 'checked';
                                                                   }
                                                                   ?>>
                                                            <label class="form-check-label" for="<?php echo $field_name; ?>_list">
                                                                Visible at List?
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-switch d-inline-block pl-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="visible_at_form[<?php echo $field_name; ?>]" value="true"
                                                                id="<?php echo $field_name; ?>_form" <?php
                                                                   if (isset($saved_options[$field_name]['input_type'])) {
                                                                       echo $saved_options[$field_name]['visible_at_form'] == '1' ? 'checked' : '';
                                                                   } else {
                                                                       echo 'checked';
                                                                   }
                                                                   ?>>
                                                            <label class="form-check-label" for="<?php echo $field_name; ?>_form">
                                                                Visible at Form?
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-floating mx-2 my-3" id="<?php echo $field_name; ?>_dimensions_wrapper"
                                                        style="display: none;">
                                                        <input type="text" class="form-control" id="<?php echo $field_name; ?>_dimensions"
                                                            name="dimensions[<?php echo $field_name; ?>]"
                                                            placeholder="width x height format">
                                                        <label for="<?php echo $field_name; ?>_dimensions">
                                                            Dimensions</label>
                                                    </div>

                                                    <script>
                                                        // Add event listener to select element
                                                        document.getElementById("<?php echo $field_name; ?>").addEventListener("change",
                                                            function () {
                                                                console.log()
                                                                var dimensionsWrapper = document.getElementById(
                                                                    "<?php echo $field_name; ?>_dimensions_wrapper");
                                                                // If selected option is "image", show dimension input; otherwise, hide it
                                                                dimensionsWrapper.style.display = this.value === "image" ? "block" :
                                                                    "none";
                                                            });
                                                    </script>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <button type="submit" class="btn btn-outline-primary">Save</button>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <?php
                    }
                }
            } else {
                echo "<div class='alert alert-info' role='alert'>No tables found in database.</div>";
            }

        }

        // Close connection
        $conn->close();
        ?>
    </div>
    </div>

    <!-- Bootstrap JavaScript (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript ile localStorage'a veriyi kaydedelim
        localStorage.setItem('selected_database', selected_database);
        console.log("Veri localStorage'a kaydedildi.");
    </script>
</body>

</html>