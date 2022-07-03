<?php
include "../includes/db.php";
require 'PaypalPayment.php';

//error_reporting(0);
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['id'])) {
    echo 'it seems that you are not logged anymore. Please contact rzy in order to receive your access manually.';
exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="yourdomain.yourdomain">
        <meta name="keywords" content="minecraft,reach,lunar,modification">
        <meta name="author" content="rzy">
        
        <!-- Title -->
        <title>yourdomain.yourdomain</title>

        <!-- Styles -->
        <link href="https://fonts.googleapis.com/css?family=Lato:400,700,900&amp;display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&amp;display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">
        <link href="../../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="../../assets/plugins/font-awesome/css/all.min.css" rel="stylesheet">

      
        <!-- Theme Styles -->
        <link href="../../assets/css/connect.min.css" rel="stylesheet">
        <link href="../../assets/css/dark_theme.css" rel="stylesheet">

    </head>

    <body class="dark-theme">
        <div class='loader'>
            <div class='spinner-border text-info' role='status'>
                <span class='sr-only'>Loading...</span>
            </div>
        </div>

        <?php

            $user_id = $_SESSION["id"];

            $result = mysqli_query($con, "SELECT * FROM `users` WHERE `id` = '". mysqli_real_escape_string($con,$user_id) ."' " ) or die(mysqli_error($con));
            if ($result->num_rows > 0) 
            {
               while($row = mysqli_fetch_array($result))
               {
                   $classicnweb_access = $row["access"];
                   $lite_access = $row["lite_access"];
                   $clicker_access = $row["clicker_access"];
                   $username = $row["username"];
               }
            }
     

            $registeredusers = mysqli_query($con, "SELECT  COUNT(*) as count FROM `users`" ) or die(mysqli_error($con));
            while($row = mysqli_fetch_array($registeredusers)) { $registeredusercountvar = $row['count']; }
        

        ?>

        <div class="connect-container align-content-stretch d-flex flex-wrap">
            <div class="page-sidebar">
                <div class="logo-box"><a href="yourdomain.yourdomain" class="logo-text">yourdomain</a></div>
                <div class="page-sidebar-inner slimscroll">
                    <ul class="accordion-menu">
                        <li class="sidebar-title">
                            Home
                        </li>
                        <li>
                            <a href="https://yourdomain.yourdomain/pages/dash.php"><i class="material-icons-outlined">video_label</i>Dashboard</a>
                        </li>
                        <?php
                        if(!($lite_access >= 1 && $classicnweb_access >= 1) || $_SESSION["id"] = 1)
                        {
                            echo '
                            <li>
                                <a href="https://yourdomain.yourdomain/pages/purchase.php"><i class="material-icons-outlined">shopping_cart</i>Purchase</a>
                            </li>';
                        }
                        
                        if($lite_access >= 1 || $classicnweb_access >= 1 || $clicker_access >= 1)
                        {
                            echo '
                            <li>
                                <a href="https://yourdomain.yourdomain/pages/download.php"><i class="material-icons-outlined">backup</i>Download</a>
                            </li>';
                        }

                        if($classicnweb_access >= 1)
                        {
                            echo '<li class="sidebar-title">
                                WebGui (UNAVAILABLE)
                            </li>
                            <li>
                                <a href="#"><i class="material-icons">app_settings_alt</i>Access<i class="material-icons has-sub-menu">add</i></a>
                                <ul class="sub-menu">
                                    <li>
                                        <a href="#">Gui</a>
                                    </li>
                                    <li>
                                        <a href="#">Cloud Configs</a>
                                    </li>
                                    <li>
                                        <a href="#">Private Configs</a>
                                    </li>
                                </ul>
                            </li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="page-container">
                <div class="page-header">
                    <nav class="navbar navbar-expand">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                            <ul class="navbar-nav">
                                <li class="nav-item small-screens-sidebar-link">
                                    <a href="#" class="nav-link"><i class="material-icons-outlined">menu</i></a>
                                </li>
                                <li class="nav-item nav-profile dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span><?php echo $username ?></span><i class="material-icons dropdown-icon">keyboard_arrow_down</i>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="#">Settings</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#">Log out</a>
                                    </div>
                                </li>
                            </ul>
                            
                    </nav>
                </div>

                <div class="page-content">
                    <div class="main-wrapper">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="page-title">

                                <?php
                                    $verify = new PaypalPayment();
                                    $ret = $verify->verify();
                                    $object = json_decode($ret);
                                    ///
                                    /// payer informations
                                    ///
                                    $payer_email = $object->payer->payer_info->email;
                                    $total = $object->transactions[0]->amount->total;
                                    ///
                                    /// transactions object
                                    ///
                                    $state = $object->state;
                                    $transaction_id = $object->transactions[0]->related_resources[0]->sale->id;
                                    ///
                                    /// session_id
                                    ///
                                    $custom = $object->transactions[0]->custom;

                                    $custom = explode(':',$custom);
                                    $sesid = $custom[0];
                                    $referrer = $custom[1];

                                    if ($state != "approved" || $total != "10.99" && $total != "9.99") 
                                    {
                                        echo $state;
                                        echo 'An error occured. If you bought yourdomain and get this error, contact rzy on discord.';
                                    } else if ($state == "approved") {
                                        $result = mysqli_query($con, "SELECT * FROM `paypal_payments` WHERE `session_id` = '" . mysqli_real_escape_string($con, $sesid) . "' ");
                                        if ($result->num_rows > 0) {
                                            while ($row = mysqli_fetch_array($result)) 
                                            {
                                                if ($row["claimed"] == 0) {
                                                    $updateclaimed = mysqli_query($con, "UPDATE paypal_payments set claimed='1' WHERE session_id='" . $sesid . "'");
                                                    $updateaccess = mysqli_query($con, "UPDATE users set access='1' WHERE id='" . $sesid . "'");
                                                    echo '<p class="page-desc">Success, you successfully got your access !</p>';

                                                    if($referrer != 0)
                                                    {
                                                        $mediaresult = mysqli_query($con, "SELECT * FROM `creator_code` WHERE `id` = '". mysqli_real_escape_string($con,$referrer) ."' " ) or die(mysqli_error($con));
                                                        if ($mediaresult->num_rows > 0) 
                                                        {
                                                            while($rowmedia = mysqli_fetch_array($mediaresult))
                                                            {
                                                                $newused = $rowmedia["used"] + 1.0;
                                                                $newowed = $rowmedia["owed"] + 0.5;
                                                                $updateusedcode = mysqli_query($con, "UPDATE creator_code set used='$newused' WHERE id='" . $referrer . "'");
                                                                $updateowed = mysqli_query($con, "UPDATE creator_code set owed='$newowed' WHERE id='" . $referrer . "'");        
                                                            }
                                                        }
                                                    }
    
                                                } 
                                                else if ($row["claimed"] == 1) 
                                                {
                                                    echo '<p class="page-desc">An error occured (Code 0x2).</p>';
                                                }
                                            }
                                        } else {
                                            $create = "INSERT INTO paypal_payments (state, email, session_id, transaction_id, claimed)
                                                                                            VALUES ('$state', '$payer_email', '$sesid', '$transaction_id', '1')";
                                            if ($con->query($create)) 
                                            {
                                                $updateaccess = mysqli_query($con, "UPDATE users set access='1' WHERE id='" . $sesid . "'");

                                                if($referrer != 0)
                                                {
                                                    $mediaresult = mysqli_query($con, "SELECT * FROM `creator_code` WHERE `id` = '". mysqli_real_escape_string($con,$referrer) ."' " ) or die(mysqli_error($con));
                                                    if ($mediaresult->num_rows > 0) 
                                                    {
                                                        while($rowmedia = mysqli_fetch_array($mediaresult))
                                                        {
                                                            $newused = $rowmedia["used"] + 1;
                                                            $newowed = $rowmedia["owed"] + 0.5;

                                                        }
                                                    }
                                                    $updateusedcode = mysqli_query($con, "UPDATE creator_code set used='$newused' WHERE id='" . $referrer . "'");
                                                    $updateowed = mysqli_query($con, "UPDATE creator_code set owed='$newowed' WHERE id='" . $referrer . "'");
                                                }

                                                echo '<p class="page-desc">Success, you successfully got your access !</p>';



                                            } else {
                                                echo 'flut';
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Javascripts -->
        <script src="../../assets/plugins/jquery/jquery-3.4.1.min.js"></script>
        <script src="../../assets/plugins/bootstrap/popper.min.js"></script>
        <script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="../../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../../assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="../../assets/plugins/apexcharts/dist/apexcharts.min.js"></script>
        <script src="../../assets/plugins/blockui/jquery.blockUI.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.time.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.symbol.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.resize.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.tooltip.min.js"></script>
        <script src="../../assets/js/connect.min.js"></script>
        <script src="../../assets/js/pages/dashboard.js"></script>
    </body>
</html>
