<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: index.php');
}

include 'db.php'; // Assuming db.php contains the database connection code

$userId = $_SESSION['user'];

// Use a prepared statement to prevent SQL injection
$sql = "SELECT projet.id_projet, projet.Nom_projet, projet.Date_dp, projet.Nbr_taches
        FROM projet 
        JOIN user ON user.id_u = projet.id_u
        WHERE user.id_u = ?";

// Prepare the statement
$stmt = $conn->prepare($sql);

// Bind the parameter
$stmt->bind_param("i", $userId);

// Execute the statement
$stmt->execute();

// Get the result set
$result = $stmt->get_result();

// Check for errors
if ($result === false) {
    die("Error in SQL query: " . $conn->error);
}

?>
<!-- Rest of your HTML code remains unchanged -->

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
                    <a class="nav-link page-scroll popup-with-move-anim" id="createNewProjectBtn" href="#details-lightbox-2">Add New Project</a>
                </li>
                </ul>
                <span class="nav-item">
                    <a class="btn-outline-sm " href="logout.php">Log Out</a>
                </span>
            </div>
        </div> <!-- end of container -->
    </nav> <!-- end of navbar -->
    <!-- end of navigation -->

    <header id="header" class="ex-2-header"  style="background-color: #ffff;"  >
        <h1 style="color: #5f4dee; font-size: 4rem; margin-top: 5%; " >My Projects</h1>
        <div class="container" style="margin-top: 5%;" >
            <div class="row">
                <div class="col-lg-12">
                <!-- Table -->
<?php  if ($result->num_rows > 0) { ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Project Name</th>
                <th>Project Date</th>
                <th>Number of Tasks</th>
                <th> </th>
            </tr>
        </thead>
        <tbody>
        <?php
// Output data of each row
while ($row = $result->fetch_assoc()) {
    echo '<tr>
            <td>' . $row['id_projet'] . '</td>
            <td>' . $row['Nom_projet'] . '</td>
            <td>' . $row['Date_dp'] . '</td>
            <td>' . $row['Nbr_taches'] . '</td>
            <td>
                <a class="btn-solid-lg dlt" href="deleteProject.php?id=' . $row['id_projet'] . '">Delete</a>
                <a class="btn-solid-lg edt" href="projectDetails.php?id=' . $row['id_projet'] . '">Edit</a>
                <!-- Add some space between buttons -->
                &nbsp;&nbsp;&nbsp;
                <a class="btn-solid-lg edt" href="PERT.php?id='. $row['id_projet'] . '">View</a>
                </td>
          </tr>';
}
?>

        </tbody>
    </table>
<?php } else {
    echo "0 results";
}
?>
<!-- End of Table -->

<!-- New Project Form -->
<div id="details-lightbox-2" class="lightbox-basic zoom-anim-dialog mfp-hide">
    <div class="container">
        <div class="row">
            <button title="Close (Esc)" type="button" class="mfp-close x-button">×</button>
            <div class="col-lg-12">
                <form action="addProject.php" method="post" id="addProjectForm">
                    <h3>New Project</h3>
                    <hr>
                    <div id="features" class="tabs">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <!-- Tabs Links -->
                                    <ul class="nav nav-tabs" id="argoTabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="nav-tab-1" data-toggle="tab" href="#tab-1" role="tab" aria-controls="tab-1" aria-selected="true"><i class="fas fa-list"></i>Project infos</a>
                                        </li>
                                    </ul>
                                    <!-- Tabs Content -->
                                    <div class="tab-content" id="argoTabsContent">
                                        <!-- Tab -->
                                        <div class="tab-pane fade show active" id="tab-1" role="tabpanel" aria-labelledby="tab-1">
                                            <div class="row">
                                                <div class="col-lg-11" id="addT">
                                                    <div class="form-container">
                                                        <div id="signUpForm" data-toggle="validator" data-focus="false">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control-input" id="pname" name="pname" required>
                                                                <label class="label-control" for="pname">Project Name</label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="date" class="form-control-input" placeholder="start date" id="pstart" name="pstart" required>
                                                                <label class="label-control" for="pends">Start Date</label>
                                                                <label class="label-control" for="pstart"></label>
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                            <!-- <div class="form-group">
                                                                <input type="number" class="form-control-input" id="pnbr" name="pnbr" required>
                                                                <label class="label-control" for="pnbr">number of tasks</label>
                                                                <div class="help-block with-errors"></div>
                                                            </div> -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!-- end of row -->
                                        </div> <!-- end of tab-pane -->
                                    </div> <!-- end of tab content -->
                                </div> <!-- end of col -->
                            </div> <!-- end of row -->
                        </div> <!-- end of container -->
                    </div>
                    <button type="submit" class="btn-outline-reg as-button">Save</button>
                </form>
            </div>
        </div> <!-- end of row -->
    </div> <!-- end of container -->
</div>

                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </header> <!-- end of ex-header -->
    <!-- end of header -->


    	
    <!-- Scripts -->
    <script src="js/jquery.min.js"></script> <!-- jQuery for Bootstrap's JavaScript plugins -->
    <script src="js/popper.min.js"></script> <!-- Popper tooltip library for Bootstrap -->
    <script src="js/bootstrap.min.js"></script> <!-- Bootstrap framework -->
    <script src="js/jquery.easing.min.js"></script> <!-- jQuery Easing for smooth scrolling between anchors -->
    <script src="js/swiper.min.js"></script> <!-- Swiper for image and text sliders -->
    <script src="js/jquery.magnific-popup.js"></script> <!-- Magnific Popup for lightboxes -->
    <script src="js/validator.min.js"></script> <!-- Validator.js - Bootstrap plugin that validates forms -->
    <script src="js/scripts.js"></script> <!-- Custom scripts -->
    <script>
$(document).ready(function () {
    // Initialize Magnific Popup
    $('.popup-with-move').magnificPopup({
        type: 'inline',
        removalDelay: 300,
        mainClass: 'mfp-fade'
    });
});
</script>
</body>
</html>
