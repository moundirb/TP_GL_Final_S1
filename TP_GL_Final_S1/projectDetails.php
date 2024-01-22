<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit();
}

include 'db.php';

// Get the project ID from the URL parameter
if (isset($_GET['id'])) {
    $projectId = $_GET['id'];

    // Query to retrieve project details and tasks
    $sql = "SELECT id_tache, Nom_tache, DTO,FTO, Predecesseur, Durée
            FROM tache
            WHERE id_projet = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameter
    $stmt->bind_param("i", $projectId);

    // Execute the statement
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    // Check for errors
    if ($result === false) {
        die("Error in SQL query: " . $conn->error);
    }

    // Fetch the project details
// Fetch all rows from the result set
$projectDetails = [];
while ($row = $result->fetch_assoc()) {
    $projectDetails[] = $row;
}


    // Close the statement
    $stmt->close();
// Check if any rows were fetched
if (empty($projectDetails)) {
    // Handle the case where no rows were found
}


} else {
    // Handle the case where 'id' is not set
    echo "Invalid request";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Tivo is a HTML landing page template built with Bootstrap to help you crate engaging presentations for SaaS apps and convert visitors into users.">
    <meta name="author" content="Inovatik">

    <!-- OG Meta Tags to improve the way the post looks when you share the page on LinkedIn, Facebook, Google+ -->
	<meta property="og:site_name" content="" /> <!-- website name -->
	<meta property="og:site" content="" /> <!-- website link -->
	<meta property="og:title" content=""/> <!-- title shown in the actual shared post -->
	<meta property="og:description" content="" /> <!-- description shown in the actual shared post -->
	<meta property="og:image" content="" /> <!-- image link, make sure it's jpg -->
	<meta property="og:url" content="" /> <!-- where do you want your post to link to -->
	<meta property="og:type" content="article" />

    <!-- Website Title -->
    <title>TaskMaster</title>
    
    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700&display=swap&subset=latin-ext" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/fontawesome-all.css" rel="stylesheet">
    <link href="css/swiper.css" rel="stylesheet">
	<link href="css/magnific-popup.css" rel="stylesheet">
	<link href="css/styles.css" rel="stylesheet">
	
	<!-- Favicon  -->
    <link rel="icon" href="images/Chat_Maker_logo (1).png">
</head>
<body data-spy="scroll" data-target=".fixed-top">
    
    <!-- Preloader -->
	<div class="spinner-wrapper">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
    <!-- end of preloader -->
    

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top" style="background-color: #5f4dee;" >
        <div class="container">

            <!-- Text Logo - Use this if you don't have a graphic logo -->
            <!-- <a class="navbar-brand logo-text page-scroll" href="index.html">Tivo</a> -->

            <!-- Image Logo -->
            <a class="navbar-brand logo-image" href="index.php"><img src="images/Chat_Maker_logo (1).png" alt="alternative"></a> 
            
            <!-- Mobile Menu Toggle Button -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-awesome fas fa-bars"></span>
                <span class="navbar-toggler-awesome fas fa-times"></span>
            </button>
            <!-- end of mobile menu toggle button -->

            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link page-scroll" href="mesProjects.php">My Projects</a>
                    </li>
                </ul>
                <span class="nav-item">
                    <a class="btn-outline-sm" href="logout.php">Log Out</a>
                </span>
            </div>
        </div> <!-- end of container -->
    </nav> <!-- end of navbar -->
    
    <header id="header" class="ex-2-header"  style="background-color: #ffff;"  >
        <h1 style="color: #5f4dee; font-size: 4rem; margin-top: 5%; " >Tasks</h1>
        <div class="container" style="margin-top: 5%;" >
            <div class="row">
                <div class="col-lg-12">
                      <!-- Table -->
                      <a class="btn-solid-reg popup-with-move-anim adTask" href="#details-lightbox-2">Add New Task<i class="fa-solid fa-plus"></i></a>

                      <?php if (!empty($projectDetails)) {
                        echo '<table class="table">
                                <thead>
                                    <tr>
                                        <th>Task name</th>
                                        <th>Start date</th>
                                        <th>End date</th>
                                        <th>Predecessor</th>
                                        <th>Duration</th>
                                        <th> </th>
                                    </tr>
                                </thead>
                                <tbody>';

                        // Output data of each row
                        foreach ($projectDetails as $row) {
                            echo '<tr>
                                <td>' . $row['Nom_tache'] . '</td>
                                <td>' . $row['DTO'] . '</td>
                                <td>' . $row['FTO'] . '</td>
                                <td>' . $row['Predecesseur'] . '</td>
                                <td>' . $row['Durée'] . '</td>
                                <td>
                                    <a class="btn-solid-lg dlt" href="deleteTask.php?id=' . $row['id_tache'] . '">Delete</a>
                                </td>
                            </tr>';
                        }

                    echo '</tbody></table>';
                    } else {
                        echo "No tasks found.";
                    }

                    $conn->close();
                    ?>
                    <!-- End of Table -->

                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </header> <!-- end of ex-header -->
    <!-- end of header -->
    <div id="details-lightbox-2" class="lightbox-basic zoom-anim-dialog mfp-hide">
        <div class="container">
            <div class="row">
                 <button title="Close (Esc)" type="button" class="mfp-close x-button">×</button>
                 <div class="col-lg-12">
                 <form action="add_task.php?id=<?php echo $_GET['id']; ?>" method="post" id="addTaskForm">
                        <h3>New Task</h3>
                        <hr>
                        <div id="features" class="tabs">
                             <div class="container">
                                <div class="row">
                                <div class="col-lg-12">

                                    <!-- Tabs Links -->
                                    <ul class="nav nav-tabs" id="argoTabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="nav-tab-1" data-toggle="tab" href="#tab-1" role="tab"
                                                aria-controls="tab-1" aria-selected="true"><i class="fas fa-list"></i>Task Infos</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="nav-tab-2" data-toggle="tab" href="#tab-2" role="tab"
                                                aria-controls="tab-2" aria-selected="false"><i class="fas fa-envelope-open-text"></i>Dependencies</a>
                                        </li>
                                    </ul>
                                    <!-- end of tabs links -->

                                    <!-- Tabs Content -->
                                    <div class="tab-content" id="argoTabsContent">

                                        <!-- Tab -->
                                        <div class="tab-pane fade show active" id="tab-1" role="tabpanel" aria-labelledby="tab-1">
                                            <div class="row">
                                                <div class="col-lg-11" id="addT">
                                                    <div class="form-container">
                                                        <div id="signUpForm" data-toggle="validator" data-focus="false">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control-input" id="tname" name="tname" required>
                                                                <label class="label-control" for="tname">Task Name</label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="date" class="form-control-input" id="tstart" name="tstart" required>
                                                                <label class="label-control" for="tstart">Start Date</label>
                                                                <div class="help-block with-errors"></div>
                                                            </div> 
                                                            <div class="form-group">
                                                                <input type="number" class="form-control-input" id="tdurat" name="tdurat" required>
                                                                <label class="label-control" for="tdurat">Duration</label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="date" class="form-control-input" id="tfta" name="tfta" required>
                                                                <label class="label-control" for="tfta">Late Finish Date</label>
                                                                <div class="help-block with-errors"></div>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!-- end of row -->
                                        </div> <!-- end of tab-pane -->
<!-- New section for task dependencies -->
<div class="tab-pane fade" id="tab-2" role="tabpanel" aria-labelledby="tab-2">
    <div class="row">
        <div class="col-lg-11" id="addD">
            <?php foreach ($projectDetails as $row) : ?>
                <div class="form-check">
                    <label class="form-check-label" for="dependency_<?php echo $row['id_tache']; ?>"><?php echo $row['Nom_tache']; ?></label>
                    <input type="checkbox" class="form-check-input" id="dependency_<?php echo $row['id_tache']; ?>" name="dependencies[]" value="<?php echo $row['Nom_tache']; ?>">
                </div>
            <?php endforeach; ?>
        </div>
    </div> <!-- end of row -->
</div>

                                </div> <!-- end of row -->
                            </div><!-- end of tab-pane -->
                        </div> <!-- end of tab content -->
                    </div> <!-- end of col -->
                </div> <!-- end of row -->
            </div> <!-- end of container -->
        </div>
        <input type="hidden" name="projectId" value="<?php echo $_GET['id']; ?>">

        <button type="submit" class="btn-outline-reg as-button">SAVE</button>
    </form>
</div>
</div> <!-- end of row -->

<style>
    /* Add your custom styles here */
 

    .form-check-label {
       
        margin-right: 30px; /* Adjust the margin between checkbox and task name */
/* Adjust the margin between checkbox and task name */
    }
</style>

<!-- end of col -->
    	
    <!-- Scripts -->
    <script src="js/jquery.min.js"></script> <!-- jQuery for Bootstrap's JavaScript plugins -->
    <script src="https://kit.fontawesome.com/6778be6c00.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js"></script> <!-- Popper tooltip library for Bootstrap -->
    <script src="js/bootstrap.min.js"></script> <!-- Bootstrap framework -->
    <script src="js/jquery.easing.min.js"></script> <!-- jQuery Easing for smooth scrolling between anchors -->
    <script src="js/swiper.min.js"></script> <!-- Swiper for image and text sliders -->
    <script src="js/jquery.magnific-popup.js"></script> <!-- Magnific Popup for lightboxes -->
    <script src="js/validator.min.js"></script> <!-- Validator.js - Bootstrap plugin that validates forms -->
    <script src="js/scripts.js"></script> <!-- Custom scripts -->
</body>
</html>
