<nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
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
                        <a class="nav-link page-scroll" href="#header">Home<span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link page-scroll" href="#video">Tutorial</a>
                    </li>
                    <?php if (isset($_SESSION["user"])): ?>
                    <li class="nav-item">
                        <a class="nav-link page-scroll" href="mesProjects.php">My Projects</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <style>
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropbtn {
        background-color: #007bff;
        color: white;
        padding: 10px;
        border: none;
        cursor: pointer;
    }

    .dropdown-content {
        border-radius: 5px;
        display: none;
        position: absolute;
        
        background-color:rgba(255, 255, 255, 0.9);
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        transition-delay: 2s;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #5f4dee;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown:hover .dropbtn {
        background-color: #5f4dee;
        color: white;
    }
</style>

<span class="nav-item">
    <?php if (!isset($_SESSION["user"]) || $_SESSION["user"] == ''): ?>
        <a class="btn-outline-sm" href="log-in.php">Log In</a>
    <?php else: ?>
        <div class="dropdown" >
            <button class="btn-outline-sm">My Account</button>
            <div class="dropdown-content">
            <a href="#" id="myAccountLink">Account Options</a>
                <a href="logout.php">Log Out</a>
            </div>
        </div>
    <?php endif; ?>
</span>

            </div>
        </div> <!-- end of container -->
    </nav>

    <style>
       

        #signupForm {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 10px;
            display: none;
            z-index: 2;
        }

        #signupForm h3 {
            text-align: center;
        }

        #signupForm .box {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #signupForm .btn {
            width: 100%;
            padding: 10px;
            background-color: #5f4dee;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-btn {
            width: 100%;
            padding: 10px;
            background-color: #5f4dee; /* Green color for the Edit button */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 20px;
        cursor: pointer;
        background-color: transparent;
        border: none;
        color: #5f4dee;
    }
        /* Add any additional styling as needed */

    </style>


    <!-- Your existing navigation code here -->
    <form method="post" action="update_profile.php" id="signupForm" style="background-color: #F8F8F8;" >
        <h3>Account Options</h3>
        <?php
        $userId = $_SESSION['user'];
            $sql = "SELECT u_email , u_pass FROM user WHERE  Id_u = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i',$userId );
            $stmt->execute();
            $stmt->bind_result($username, $password);
    $stmt->fetch();
        ?>
        <button type="button" class="close-btn" onclick="closeForm()">x</button>
        <input type="email" placeholder="Email" class="box" name="email" " required>
        <input type="password" placeholder="Old Password" class="box" name="password" required>
    <input type="password" placeholder="New Password" class="box" name="2password" required>
    <input type="password" placeholder="Verify Password" class="box" name="3password" required>

    <input type="submit" value="Update Password" class="btn" name="update_account">
&nbsp; <!-- Non-breaking space -->
<button type="submit" class="btn" onclick="deleteAccount()" style="background-color: red;">Delete Account</button>

    </form>
    <script>
        function deleteAccount() {
        var confirmDelete = confirm("Are you sure you want to delete your account?");
        if (confirmDelete) {
            // If the user confirms deletion, redirect to delete_account.php or handle deletion logic
            window.location.href = "delete_account.php";
        }
    }
         document.getElementById('myAccountLink').addEventListener('click', function() {
        document.getElementById('signupForm').style.display = 'block';
    });
    function closeForm() {
        document.getElementById('signupForm').style.display = 'none';
    }
    </script>
