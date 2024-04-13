<?php
if (!empty(getenv('DATABASE_NAME'))) {
    $selected_database = getenv('DATABASE_NAME');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // LEVEL 1 UPDATE SERVER SETTINGS 
    if (isset($_POST['mysql_server'])) {
        // Form validation and processing
        $mysql_user = isset($_POST['mysql_user']) ? $_POST['mysql_user'] : '';
        $mysql_passwd = isset($_POST['mysql_passwd']) ? $_POST['mysql_passwd'] : '';
        $mysql_server = isset($_POST['mysql_server']) ? $_POST['mysql_server'] : '';

        // Generate the content for the .htaccess file
        $htaccess_content = "SetEnv MYSQL_USER $mysql_user\n";
        $htaccess_content .= "SetEnv MYSQL_PASSWD $mysql_passwd\n";
        $htaccess_content .= "SetEnv MYSQL_SERVER $mysql_server\n";
        $htaccess_content .= "SetEnv SERVER_STATUS True\n";

        // Path to the .htaccess file
        $htaccess_file = '.htaccess';

        // Check if the .htaccess file exists
        if (file_exists($htaccess_file)) {
            // .htaccess file exists, update its content
            file_put_contents($htaccess_file, $htaccess_content);
            echo "Updated .htaccess file.";
        } else {
            // .htaccess file does not exist, create it
            if (file_put_contents($htaccess_file, $htaccess_content) !== false) {
                echo "Created .htaccess file.";
            } else {
                echo "Failed to create .htaccess file.";
            }
        }
        header("Location: /?success=SERVER SET.");
    }
    if (isset($_POST['db']) && !empty($_POST['db'])) {
        // LEVEL 2 UPDATE DATABASE NAME
        $selected_database = $_POST['db'];
        echo "<script>var selected_database = '" . $selected_database . "';</script>";
        // set db config to get tables
        include 'db_config.php';
        // Sanitize the input to prevent directory traversal
        $new_database_name = basename($_POST['db']);
        echo $new_database_name;
        // Get the existing database name from the environment variable
        $existing_database_name = getenv('DATABASE_NAME');
        $existing_mysql_user = getenv('MYSQL_USER');
        $existing_mysql_passwd = getenv('MYSQL_PASSWD');
        $existing_mysql_server = getenv('MYSQL_SERVER');
        // Generate .htaccess content
        $htaccess_content = "SetEnv MYSQL_USER $existing_mysql_user\n";
        $htaccess_content .= "SetEnv MYSQL_PASSWD $existing_mysql_passwd\n";
        $htaccess_content .= "SetEnv MYSQL_SERVER $existing_mysql_server\n";

        if ($existing_database_name !== false) {
            // Check if the new database name is different from the existing one
            if ($new_database_name !== $existing_database_name) {
                // Generate the content for the .htaccess file
                $htaccess_content .= "SetEnv DATABASE_NAME $new_database_name\n";

                // Write the content to .htaccess file
                $htaccess_file = fopen('.htaccess', 'w');
                if ($htaccess_file) {
                    fwrite($htaccess_file, $htaccess_content);
                    fclose($htaccess_file);
                    echo "Database name updated successfully.";
                    header("Location:/");
                } else {
                    echo "Error writing to .htaccess file.";
                }
            } else {
                echo "Database name is already up to date.";
            }
        } else {
            // Generate the content for the .htaccess file
            $htaccess_content .= "SetEnv DATABASE_NAME $new_database_name\n";

            // Write the content to .htaccess file
            $htaccess_file = fopen('.htaccess', 'w');
            if ($htaccess_file) {
                fwrite($htaccess_file, $htaccess_content);
                fclose($htaccess_file);
                echo "Database name updated successfully.";
                header("Location:/");

            } else {
                echo "Error writing to .htaccess file.";
            }
        }
    }
}
?>
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
        if (empty(getenv('MYSQL_SERVER')) || empty(getenv('MYSQL_PASSWD')) || empty(getenv('MYSQL_USER')) || isset($_GET['server'])) {
            ?>
        <!-- View 1 MYSQL SETTINGS -->
        <div class="card mb-2">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="card-header text-center">
                    <nav class="navbar navbar-expand-lg ">
                        <div class="container">
                            <!-- Brand -->
                            <h3 class=" mb-0">
                                <span class="text-muted"> <small> Server Configuration</small> </span>
                            </h3>
                            <!-- Button -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                            <?php ?>
                        </div>
                    </nav>
                </div>
                <div class="card-body row g-3">
                    <div class="col-md-4 col-auto">
                        <label for="mysql_user" class="form-label">MySQL User</label>
                        <input type="text" class="form-control" id="mysql_user" name="mysql_user"
                            value="<?php echo isset($_POST['mysql_user']) ? htmlspecialchars($_POST['mysql_user']) : ''; ?>"
                            required>
                        <div class="invalid-feedback">Please provide a MySQL user.</div>
                    </div>
                    <div class="col-md-4 col-auto">
                        <label for="mysql_passwd" class="form-label">MySQL Password</label>
                        <input type="password" class="form-control" id="mysql_passwd" name="mysql_passwd"
                            value="<?php echo isset($_POST['mysql_passwd']) ? htmlspecialchars($_POST['mysql_passwd']) : ''; ?>"
                            required>
                        <div class="invalid-feedback">Please provide a MySQL password.</div>
                    </div>
                    <div class="col-md-4 col-auto">
                        <label for="mysql_server" class="form-label">MySQL Server</label>
                        <input type="text" class="form-control" id="mysql_server" name="mysql_server"
                            value="<?php echo isset($_POST['mysql_server']) ? htmlspecialchars($_POST['mysql_server']) : ''; ?>"
                            required>
                        <div class="invalid-feedback">Please provide a MySQL server URL.</div>
                    </div>
                </div>
            </form>
        </div>
        <?php } elseif (empty(getenv('DATABASE_NAME')) || isset($_GET['change'])) {
            // clear selected db and get db list
            $config_database = '';
            include 'db_config.php';
            ?>
        <!-- View 2 DATABASE SELECT SCREEN -->
        <div class="card mb-2">
            <div class="card-header text-center">
                <nav class="navbar navbar-expand-lg ">
                    <div class="container">
                        <?php
                            if (isset($selected_database)) {
                                $text = '
                                <span class="text-muted"> <small> Current database </small> </span>
                                <span class="text-primary">' . $selected_database . '</span>
                                ';
                            } else {
                                $text = '<span class="text-muted"> <small>Please select database below to work with it.</small> </span>
                                ';
                            }
                            ?>
                        <!-- Brand -->
                        <h3 class=" mb-0">
                            <?= $text ?>
                        </h3>
                        <div class="d-flex justify-content-end">
                            Do you want to <a href="?server" class="mx-2">
                                Change Server</a> Details
                        </div>
                    </div>
                </nav>
            </div>
            <div class="card-body">
                <form id="database_select" method="POST" class="row g-3">
                    <input type="hidden" name="change" value="1">

                    <div class="col-sm-9 col-8">
                        <select id="database_select" name="db" class="form-select  mb-3">
                            <option selected>Select New Database</option>
                            <?php
                                // Veritabanlrını sorgula
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
                    </div>
                    <div class="col-3">
                        <button type="submit"
                            class="ms-auto btn btn-primary"><?php echo isset($selected_database) ? 'Change' : 'Select' ?></button>
                    </div>
                </form>
            </div>
        </div>
        <?php
        }

        ?>

        <?php
        if (isset($selected_database) && !isset($_GET['change'])&& !isset($_GET['server'])) {
            $config_database = $selected_database;
            include 'db_config.php';

            // Read all tables in the database
            $tables_query = "SHOW TABLES";
            $tables_result = $conn->query($tables_query);

            if ($tables_result->num_rows > 0) {
                ?>
        <div class="card mb-2">
            <div class="card-header text-center ">
                <nav class="navbar navbar-expand-lg ">
                    <div class="container">
                        <!-- Brand -->
                        <h3 class=" mb-0">
                            <span class="text-muted"> <small> Currently working on database </small> </span>
                            <span class="text-primary"><?= $selected_database ?></span>
                        </h3>
                        <!-- Button -->
                        <div class="d-flex align-content-end justify-content-end">
                            Do you want to <a href="?change" class="mx-2">
                                Change </a> it
                        </div>
                        <?php ?>
                    </div>
                </nav>
            </div>
            <div class="card-body">
                <?php
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
                            // Close connection
                            // $conn->close();
                            if ($structure_result) {
                                // Display form for table options within a card
                                ?>

                <div class="accordion" id="accordion-<?php echo $table_name; ?>">
                    <form action="save_options.php" method="post">
                        <div class="card mb-2">
                            <div class="card-header bg-light" id="heading-<?php echo $table_name; ?>"
                                data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $table_name; ?>"
                                aria-expanded="false" aria-controls="collapse-<?php echo $table_name; ?>">
                                <nav class="navbar navbar-expand-lg ">
                                    <div class="container">
                                        <!-- Brand -->
                                        <h3 class="navbar-brand mb-0"><small><span class="text-muted">Table
                                                    Name:</span></small>
                                            <?php echo $table_name; ?></h3>
                                        <!-- Button -->
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="mx-auto btn btn-outline-primary">Save</button>
                                        </div>
                                    </div>
                                </nav>
                            </div>
                        </div>
                        <div id="collapse-<?php echo $table_name; ?>" class="collapse"
                            aria-labelledby="heading-<?php echo $table_name; ?>" data-bs-parent="#accordionExample">
                            <div class="card-body">
                                <input type="hidden" name="table" value="<?php echo $table_name; ?>">
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
                                                <select class="form-select"
                                                    name="input_types[<?php echo $field_name; ?>]"
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
                                        <div class="form-floating mx-2 my-3"
                                            id="<?php echo $field_name; ?>_dimensions_wrapper" style="display: none;">
                                            <input type="text" class="form-control"
                                                id="<?php echo $field_name; ?>_dimensions"
                                                name="dimensions[<?php echo $field_name; ?>]"
                                                placeholder="width x height format">
                                            <label for="<?php echo $field_name; ?>_dimensions">
                                                Dimensions</label>
                                        </div>

                                        <script>
                                        // Add event listener to select element
                                        document.getElementById("<?php echo $field_name; ?>").addEventListener(
                                            "change",
                                            function() {
                                                console.log()
                                                var dimensionsWrapper = document.getElementById(
                                                    "<?php echo $field_name; ?>_dimensions_wrapper");
                                                // If selected option is "image", show dimension input; otherwise, hide it
                                                dimensionsWrapper.style.display = this.value === "image" ?
                                                    "block" :
                                                    "none";
                                            });
                                        </script>
                                    </div>
                                </div>
                                <?php
                                                }
                                                ?>
                                <button type="submit" class="btn btn-outline-primary">Save</button>
                            </div>
                        </div>
                    </form>

                </div>
                <?php
                            }
                        }
            } else {
                echo "<div class='alert alert-info' role='alert'>No tables found in database.</div>";
            }
            ?>
            </div>
        </div> <?php
        }
        ?>
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