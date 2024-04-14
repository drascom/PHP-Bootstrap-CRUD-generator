<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Toggle</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Content area */
        .content {
            padding: 1rem;
        }

        /* Sidebar */
        .sidebar {
            background-color: #f5f5f5;
            transition: margin-right 0.5s;
            height: 100vh;
        }

        @media (min-width: 576px) {
            .sidebar {
                margin-right: -20rem;
                /* Initial hidden state */
            }

            .sidebar.active {
                margin-right: 0;
                /* Visible state */
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar position-fixed top-0 end-0 bg-light" id="sidebar">
        <h2>Sidebar</h2>
        <p>This is the sidebar content.</p>
    </div>


    <!-- Content area -->
    <div class="content">
        <h1>Main Content</h1>
        <p>This is the main content area. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        <!-- Toggle button -->
        <button class="btn btn-primary d-block " id="toggleBtn">Toggle Sidebar</button>
    </div>

    <!-- Bootstrap JavaScript (optional, if you need components that require JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Get sidebar element
        const sidebar = document.getElementById('sidebar');
        // Get toggle button element
        const toggleBtn = document.getElementById('toggleBtn');

        // Add click event listener to toggle button
        toggleBtn.addEventListener('click', function () {
            // Toggle the active class to show/hide the sidebar
            sidebar.classList.toggle('active');
        });
    </script>
</body>

</html>