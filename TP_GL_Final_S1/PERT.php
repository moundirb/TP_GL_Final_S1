
<?php

// Database credentials
$host = 'localhost';
$dbname = 'tp_gl_final';
$username = 'root';
$password = '';
$projectId=$_GET['id'];


try {
    // Create a new PDO instance
   
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Set the PDO error mode to exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Prepare the SQL query
$stmt_tasks = $pdo->prepare("SELECT Id_tache, Nom_tache, Durée, DTO,FTO, Predecesseur,DTA, FTA FROM tache WHERE id_projet='$projectId'");
$stmt_tasks->execute();

// Initialize arrays to store node and link data
$nodeDataArray = [];
$linkDataArray = [];

while ($row = $stmt_tasks->fetch(PDO::FETCH_ASSOC)) {
    // Convert Date_dt to a timestamp (assuming it's in the format 'd-m-Y')
    $imad3 = $row['Nom_tache'];
    $dita = $row['Id_tache'];
    $predecesseurs = explode(',', $row['Predecesseur']); // Split Predecesseur by comma

    // Prepare the SQL query to check for predecessor tasks
    $stmt_tasks2 = $pdo->prepare("SELECT Id_tache FROM tache WHERE id_projet='$projectId' AND FIND_IN_SET(:imad3, Predecesseur) > 0");
    $stmt_tasks2->bindParam(':imad3', $imad3, PDO::PARAM_STR);
    $stmt_tasks2->execute();

    // Check if any of the predecessor tasks exist
    $row2 = $stmt_tasks2->fetch(PDO::FETCH_ASSOC);
    if (empty($row2['Id_tache'])) {
        // Add a link if none of the predecessor tasks are found
        $linkDataArray[] = [
            'from' => $dita,
            'to' => '9',
            // Add other properties as needed
        ];
    }


        $startDate = strtotime($row['DTO']);
        $latefinish=strtotime($row['FTA']);
        $latestart=strtotime($row['DTA']);
        // Add Durée to the start date and convert to a timestamp
        $endDate = strtotime($row['FTO']);
        
        $slack= $latefinish -$endDate;
        $slack2=$slack/86400;

        // Create node data slack
        $nodeDataArray[] = [
            'key' => $row['Id_tache'],
            'text' => $row['Nom_tache'],
            'length' => $row['Durée'],
            'lateFinish' => $row['FTA'],
            'earlyStart' => $row['DTO'],
            'earlyFinish' => $row['FTO'],
            'lateStart' => $row['DTA'],
            'slack'=> $slack2,
            'critical' => 'false'
        ];

        // Check if Predecesseur is not empty
        if (!empty($row['Predecesseur'])) {
          // Split Predecesseur string into an array of task IDs
          $predecessors = explode(',', $row['Predecesseur']);
  
          // Loop through each predecessor and create links
          foreach ($predecessors as $predecessor) {
              $stmt_predecessor = $pdo->prepare("SELECT Id_tache FROM tache WHERE id_projet = '$projectId' AND Nom_tache = :predecessor");
              $stmt_predecessor->bindParam(':predecessor', $predecessor, PDO::PARAM_STR);
              $stmt_predecessor->execute();
  
              while ($row_predecessor = $stmt_predecessor->fetch(PDO::FETCH_ASSOC)) {
                  $linkDataArray[] = [
                      'from' => $row_predecessor['Id_tache'],
                      'to' => $row['Id_tache'],
                      // Add other properties as needed
                  ];
              }
          }
      } else {
          // If Predecesseur is empty, link to a default task (e.g., task with Id_tache = 1)
          $linkDataArray[] = [
              'from' => '1',
              'to' => $row['Id_tache'],
              // Add other properties as needed
          ];
      }
  }

    // Convert the arrays to JSON
    $jsonNodeData = json_encode($nodeDataArray);
    $jsonLinkData = json_encode($linkDataArray);
} catch (PDOException $e) {
    // Handle database connection errors
    echo "Connection failed: " . $e->getMessage();
}

// Close the database connection

?>



<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="">
    <meta name="author" content="">

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
  <html lang="en">
    
<body style="background-color: #F8F8F8;">
<button class="btn btn-primary" onclick="goBack()" style="color: white; background-color: #5f4dee; border-color: #5f4dee;">Go Back</button>

    <script src="https://unpkg.com/gojs@2.3.12/release/go.js"></script>
  <div style="display: flex;">
  <div style="width: 50%;">
  <div id="allSampleContent" class="p-4 w-full">
  <?php $stmt_min_date_dt = $pdo->prepare("SELECT MIN(DTO) as min_date_dt FROM tache WHERE id_projet='$projectId'");
$stmt_min_date_dt->execute();
$min_date_dt_result = $stmt_min_date_dt->fetch(PDO::FETCH_ASSOC);
$min_date_dt = $min_date_dt_result['min_date_dt'];
$mondatedt= strtotime($min_date_dt);
$stmt_max_date_ft = $pdo->prepare("SELECT MAX(FTO) as max_date_ft FROM tache WHERE id_projet='$projectId'");
$stmt_max_date_ft->execute();
$max_date_ft_result = $stmt_max_date_ft->fetch(PDO::FETCH_ASSOC);
$max_date_ft = $max_date_ft_result['max_date_ft'];
$maxdateft=strtotime($max_date_ft);
$duree=$maxdateft-$mondatedt;
$duréep=$duree/86400;
?>

    <script id="code">
        var nodeDataArray = <?php echo $jsonNodeData; ?>;
    var linkDataArray = <?php echo $jsonLinkData; ?>;
   // PHP

// JavaScript
nodeDataArray.push(
    { key: 1, text: "Start", length: 0, earlyStart: new Date('<?php echo date('Y-m-d', $mondatedt); ?>').toISOString().split('T')[0], earlyFinish: new Date('<?php echo date('Y-m-d', $mondatedt); ?>').toISOString().split('T')[0], lateStart: new Date('<?php echo date('Y-m-d', $mondatedt); ?>').toISOString().split('T')[0], lateFinish: new Date('<?php echo date('Y-m-d', $mondatedt); ?>').toISOString().split('T')[0], critical: true },
    { key: 9, text: "Finish", length: 0, earlyStart: new Date('<?php echo date('Y-m-d', $maxdateft); ?>').toISOString().split('T')[0], earlyFinish: new Date('<?php echo date('Y-m-d', $maxdateft); ?>').toISOString().split('T')[0], lateStart: new Date('<?php echo date('Y-m-d', $maxdateft); ?>').toISOString().split('T')[0], lateFinish: new Date('<?php echo date('Y-m-d', $maxdateft); ?>').toISOString().split('T')[0], critical: true }
);




    function init() {

      // Since 2.2 you can also author concise templates with method chaining instead of GraphObject.make
      // For details, see https://gojs.net/latest/intro/buildingObjects.html
      const $ = go.GraphObject.make;  // for more concise visual tree definitions

      // colors used, named for easier identification
      var blue = "#0288D1";
      var pink = "#B71C1C";
      var pinkfill = "#F8BBD0";
      var bluefill = "#B3E5FC";

      myDiagram =
        new go.Diagram("myDiagramDiv",
          {
            initialAutoScale: go.Diagram.Uniform,
            layout: $(go.LayeredDigraphLayout, { alignOption: go.LayeredDigraphLayout.AlignAll })
          });

      // The node template shows the activity name in the middle as well as
      // various statistics about the activity, all surrounded by a border.
      // The border's color is determined by the node data's ".critical" property.
      // Some information is not available as properties on the node data,
      // but must be computed -- we use converter functions for that.
      myDiagram.nodeTemplate =
        $(go.Node, "Auto",
          $(go.Shape, "Rectangle",  // the border
            { fill: "white", strokeWidth: 2 },
            new go.Binding("fill", "critical", b => b ? pinkfill : bluefill),
            new go.Binding("stroke", "critical", b => b ? pink : blue)),
          $(go.Panel, "Table",
            { padding: 0.5 },
            $(go.RowColumnDefinition, { column: 1, separatorStroke: "black" }),
            $(go.RowColumnDefinition, { column: 2, separatorStroke: "black" }),
            $(go.RowColumnDefinition, { row: 1, separatorStroke: "black", background: "white", coversSeparators: true }),
            $(go.RowColumnDefinition, { row: 2, separatorStroke: "black" }),
            $(go.TextBlock, // earlyStart
              new go.Binding("text", "earlyStart"),
              { row: 0, column: 0, margin: 5, textAlign: "center" }),
            $(go.TextBlock,
              new go.Binding("text", "length"),
              { row: 0, column: 1, margin: 5, textAlign: "center" }),
            $(go.TextBlock,
              new go.Binding("text", "text"),
              {
                row: 1, column: 0, columnSpan: 3, margin: 5,
                textAlign: "center", font: "bold 14px sans-serif"
              }),
              $(go.TextBlock, // earlyStart
              new go.Binding("text", "earlyFinish"),
              {  row: 0, column: 2, margin: 5, textAlign: "center" }),
           
              $(go.TextBlock, // earlyStart
              new go.Binding("text", "lateStart"),
              {  row: 2, column: 0, margin: 5, textAlign: "center" }),
              $(go.TextBlock, // earlyStart
              new go.Binding("text", "slack"),
              { row: 2, column: 1, margin: 5, textAlign: "center" }),
           $(go.TextBlock, // lateFinish
              new go.Binding("text", "lateFinish"),
              { row: 2, column: 2, margin: 5, textAlign: "center" })
          )  // end Table Panel
        );  // end Node

      // The link data object does not have direct access to both nodes
      // (although it does have references to their keys: .from and .to).
      // This conversion function gets the GraphObject that was data-bound as the second argument.
      // From that we can get the containing Link, and then the Link.fromNode or .toNode,
      // and then its node data, which has the ".critical" property we need.
      //
      // But note that if we were to dynamically change the ".critical" property on a node data,
      // calling myDiagram.model.updateTargetBindings(nodedata) would only update the color
      // of the nodes.  It would be insufficient to change the appearance of any Links.
      function linkColorConverter(linkdata, elt) {
        var link = elt.part;
        if (!link) return blue;
        var f = link.fromNode;
        if (!f || !f.data || !f.data.critical) return blue;
        var t = link.toNode;
        if (!t || !t.data || !t.data.critical) return blue;
        return pink;  // when both Link.fromNode.data.critical and Link.toNode.data.critical
      }

      // The color of a link (including its arrowhead) is red only when both
      // connected nodes have data that is ".critical"; otherwise it is blue.
      // This is computed by the binding converter function.
      myDiagram.linkTemplate =
        $(go.Link,
          { toShortLength: 6, toEndSegmentLength: 20 },
          $(go.Shape,
            { strokeWidth: 4 },
            new go.Binding("stroke", "", linkColorConverter)),
          $(go.Shape,  // arrowhead
            { toArrow: "Triangle", stroke: null, scale: 1.5 },
            new go.Binding("fill", "", linkColorConverter))
        );
      // here's the data defining the graph
     
      
      myDiagram.model = new go.GraphLinksModel(nodeDataArray, linkDataArray);

      // create an unbound Part that acts as a "legend" for the diagram
      myDiagram.add(
        $(go.Node, "Auto",
          $(go.Shape, "Rectangle",  // the border
            { fill: "#EEEEEE" }),
          $(go.Panel, "Table",
            $(go.RowColumnDefinition, { column: 1, separatorStroke: "black" }),
            $(go.RowColumnDefinition, { column: 2, separatorStroke: "black" }),
            $(go.RowColumnDefinition, { row: 1, separatorStroke: "black", background: "#EEEEEE", coversSeparators: true }),
            $(go.RowColumnDefinition, { row: 2, separatorStroke: "black" }),
            $(go.TextBlock, "Early Start",
              { row: 0, column: 0, margin: 5, textAlign: "center" }),
            $(go.TextBlock, "Length",
              { row: 0, column: 1, margin: 5, textAlign: "center" }),
            $(go.TextBlock, "Early Finish",
              { row: 0, column: 2, margin: 5, textAlign: "center" }),

            $(go.TextBlock, "Activity Name",
              {
                row: 1, column: 0, columnSpan: 3, margin: 5,
                textAlign: "center", font: "bold 14px sans-serif"
              }),

            $(go.TextBlock, "Late Start",
              { row: 2, column: 0, margin: 5, textAlign: "center" }),
            $(go.TextBlock, "Slack",
              { row: 2, column: 1, margin: 5, textAlign: "center" }),
            $(go.TextBlock, "Late Finish",
              { row: 2, column: 2, margin: 5, textAlign: "center" })
          )  // end Table Panel
        ));
    }
    window.addEventListener('DOMContentLoaded', init);
  </script>

  <div id="sample">
  <p style="color: #3D3B40;
          font-size: 20px;">Pert Diagram:</p>

  <div id="myDiagramDiv" style="border: 1px solid black; width: 100%; height: 400px; position: relative; -webkit-tap-highlight-color: rgba(255, 255, 255, 0);"><canvas tabindex="0" width="1317" height="497" style="position: absolute; top: 0px; left: 0px; z-index: 2; user-select: none; touch-action: none; width: 1054px; height: 398px;"></canvas><div style="position: absolute; overflow: auto; width: 1054px; height: 398px; z-index: 1;"><div style="position: absolute; width: 1px; height: 1px;"></div></div></div>
 
</div>
</div>
</div>
<div style="width: 50%; border: 5px;">

<!-- gantt chart  -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<?php
function daysToMilliseconds($days) {
    return $days * 24 * 60 * 60 * 1000;
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tp_gl_final";
$projectId=$_GET['id'];


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$sql = "SELECT Id_tache, Nom_tache, Durée, Predecesseur, DTO, FTO FROM tache WHERE id_projet='$projectId'";
$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    $gantdata = array();

    // Loop through each row in the result set
    while($row = $result->fetch_assoc()) {
        // Format the data as needed for the Gantt chart
        $predecessors = explode(',', $row["Predecesseur"]);
        $gantdata[] = array(
            $row["Nom_tache"],
            $row["Nom_tache"],
            null,
            new DateTime($row["DTO"]),
            new DateTime($row["FTO"]),
            daysToMilliseconds($row["Durée"]),
            0,  // You may need to adjust this based on your data
            implode(',', $predecessors)
        );
    }
} else {
    echo "0 results";
}

// Close the database connection
$conn->close();
?>
<script type="text/javascript">
    google.charts.load('current', {'packages':['gantt']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task ID');
        data.addColumn('string', 'Task Name');
        data.addColumn('string', 'Resource');
        data.addColumn('date', 'Start Date');
        data.addColumn('date', 'End Date');
        data.addColumn('number', 'Duration');
        data.addColumn('number', 'Percent Complete');
        data.addColumn('string', 'Dependencies');

        // Add the rows dynamically based on PHP data
        data.addRows(<?php echo json_encode($gantdata); ?>);

        var options = {
            gantt: {
                trackHeight: 30, // Adjust the track height as needed
            },
            width: '100%', // Make the chart width 100% of the container
            criticalPathEnabled: true // Enable critical path highlighting
        };

        var chart = new google.visualization.Gantt(document.getElementById('chart_div'));

        // Event listener for window resize to redraw the chart
        window.addEventListener('resize', function() {
            chart.draw(data, options);
        });

        // Draw the chart initially
        chart.draw(data, options);

        // Calculate and display margins for each task
        displayTaskMargins(data);
    }
</script>

<p style="color: #3D3B40;
          font-size: 20px;">Gantt Chart:</p>
<div id="chart_div" style="border: 1px solid black; width: 100%; height: 400px; position: relative; -webkit-tap-highlight-color: rgba(255, 255, 255, 0);margin-top: 24px;">
<canvas tabindex="0" width="1317" height="497" style="position: absolute; top: 0px; left: 0px; z-index: 2; user-select: none; touch-action: none; width: 1054px; height: 398px;"></canvas>
<div style="position: absolute; overflow: auto; width: 1054px; height: 398px; z-index: 1;">
<div style="position: absolute; width: 1px; height: 1px;">
</div>
</div>
</div>

</div>
  
  </div>

<!-- marges total and libre -->
  <table>
    <th>Task Name</th><?php
    $stmt_tasks5 = $pdo->prepare("SELECT Nom_tache FROM tache WHERE id_projet='$projectId'");
       
      
       $stmt_tasks5->execute();
       while ($row5 = $stmt_tasks5->fetch(PDO::FETCH_ASSOC)) {
          echo ' 
          <td>' . $row5['Nom_tache'] . '</td>
          ';
       }?>
    <tr>
    <th>Free Slack</th>
    <?php
$stmt_tasks7 = $pdo->prepare("SELECT Nom_tache, FTO FROM tache WHERE id_projet='$projectId'");
$stmt_tasks7->execute();

while ($row7 = $stmt_tasks7->fetch(PDO::FETCH_ASSOC)) {
    $p = $row7['Nom_tache'];
    $date_ft1 = strtotime($row7['FTO']);

    $mk2 = $maxdateft - $date_ft1;
    $mk = $mk2 / 86400;

    $min_date_dt = PHP_INT_MAX;

    $stmt_tasks8 = $pdo->prepare("SELECT Id_tache, DTO FROM tache WHERE id_projet='$projectId' AND FIND_IN_SET(:p, Predecesseur) > 0");
    $stmt_tasks8->bindParam(':p', $p, PDO::PARAM_STR);
    $stmt_tasks8->execute();

    while ($row8 = $stmt_tasks8->fetch(PDO::FETCH_ASSOC)) {
        $date_dt1 = strtotime($row8['DTO']);
        $min_date_dt = min($min_date_dt, $date_dt1);
    }

    if ($min_date_dt != PHP_INT_MAX) {
        $mk = ($min_date_dt - $date_ft1) / 86400;
    }

    echo '<td>' . $mk . '</td>'; // line 381
}
?>

  
   
   
    </tr>
   <tr> <th>Total Slack</th>
   <?php $stmt_tasks6= $pdo->prepare("SELECT FTA , FTO, Nom_tache FROM tache WHERE id_projet='$projectId'");
       
      
       $stmt_tasks6->execute();
       while ($row6 = $stmt_tasks6->fetch(PDO::FETCH_ASSOC)) {
        $latestay788=strtotime($row6['FTA']);
        $latestay789=strtotime($row6['FTO']);
        $chq=array();
        $imade=$latestay788-$latestay789;
         $imadediv=$imade/86400;
       
         if ($imadediv==0){
        array_push($chq , $row6['Nom_tache']);
         }
          echo ' 
          <td>' . $imadediv. '</td>
          ';
       }?>
  </tr>
</thead>
<!-- CRITAL PATH -->
<?php
// استعلام SQL لاسترجاع المهام التي marge total = 0
$stmt_tasks_zero_marge = $pdo->prepare("SELECT Nom_tache, DTO FROM tache WHERE id_projet='$projectId' AND FTA - FTO = 0");
$stmt_tasks_zero_marge->execute();

$tasks_zero_marge = array();

while ($row_zero_marge = $stmt_tasks_zero_marge->fetch(PDO::FETCH_ASSOC)) {
    $tasks_zero_marge[$row_zero_marge['Nom_tache']] = strtotime($row_zero_marge['DTO']);
}

// رتب المهام وفقًا لتاريخ البداية
asort($tasks_zero_marge);

// عرض السلسلة
$chain_q = implode('-->', array_keys($tasks_zero_marge));
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
            <div class="card-body"style="background-color: #5f4dee;">
                    <h4 class="card-title"style="color: white;">Critical Path:</h4>
                    <h5 class="card-text"style="color: white;"><?php echo $chain_q; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-6 ">
            <div class="card">
                <div class="card-body"style="background-color: #5f4dee;">
                    <h4 class="card-title" style="color: white;">Project Length:</h4>
                    <h5 class="card-text"style="color: white;"><?php echo $duréep . ' Days'; ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
          color: #3D3B40;
          font-size: 20px;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
          color: white;
            background-color: #5f4dee;
        }

</style>
<script>
        function goBack() {
            window.history.back();
        }
        
    </script>
  </body>
  </html> <script src="js/jquery.min.js"></script> <!-- jQuery for Bootstrap's JavaScript plugins -->
    <script src="js/popper.min.js"></script> <!-- Popper tooltip library for Bootstrap -->
    <script src="js/bootstrap.min.js"></script> <!-- Bootstrap framework -->
    <script src="js/jquery.easing.min.js"></script> <!-- jQuery Easing for smooth scrolling between anchors -->
    <script src="js/swiper.min.js"></script> <!-- Swiper for image and text sliders -->
    <script src="js/jquery.magnific-popup.js"></script> <!-- Magnific Popup for lightboxes -->
    <script src="js/validator.min.js"></script> <!-- Validator.js - Bootstrap plugin that validates forms -->
    <script src="js/scripts.js"></script> <!-- Custom scripts -->
