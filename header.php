<?php  
$base_url     = "http://".$_SERVER['HTTP_HOST']."/dnow/";
session_start();
if(empty($_SESSION)){
  header('Location: '.$base_url.'index.php');
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Dnow | Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="images/DNOW.png"/>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/bootstrap-datetimepicker.css" type="text/css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link href="css/sweetalert.css" rel="stylesheet">
  <!-- iCheck -->
  
  <!-- Date Picker -->
  
  <link href="dist/css/bikash_style.css" rel="stylesheet"/>

   

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

  <![endif]-->
  <script src="js/sweetalert.min.js"></script>
</head>
<style>
.sweet-alert fieldset input[type="text"] {
display: none;
}
/* Full-width input fields */
input[type=text], input[type=password] {
    width: 100%;
    padding: 12px 20px;
    
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
}
select.form-control.s_1{
      width: 100%;
    padding: 12px 24px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
    height: auto;
}

/* Set a style for all buttons */
button {
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    cursor: pointer;
    width: 100%;
}

/* Extra styles for the cancel button */
.cancelbtn {
    padding: 14px 20px;
    background-color: #ed1c24;
    color: white;
    margin-bottom: 27px;
    margin-top: 15px;
}

/* Float cancel and signup buttons and add an equal width */
.cancelbtn,.signupbtn {
    float: left;
    
}

/* Add padding to container elements */
.container {
    padding: 16px;
}

/* Clear floats */
.clearfix::after {
    content: "";
    clear: both;
    display: table;
}
.select-style {
    border: 1px solid #ccc;
    width: 120px;
    border-radius: 3px;
    overflow: hidden;
    background: #fafafa url("img/icon-select.png") no-repeat 90% 50%;
}

.select-style select {
    padding: 5px 8px;
    width: 130%;
    border: none;
    box-shadow: none;
    background: transparent;
    background-image: none;
    -webkit-appearance: none;
}

.select-style select:focus {
    outline: none;
}

/* Change styles for cancel button and signup button on extra small screens */
/*@media screen and (max-width: 300px) {
    .cancelbtn, .signupbtn {
       width: 100%;
    }*/

</style>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="index2.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>A</b>LT</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Admin</b>LTE</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav navbar-right right_one">
            <li>
            <h6><a href="#"><i class="fa fa-user"></i> Welcome as admin</a></h6>
            </li>
            <li>
            <div class="date_area">
            <?php $date = date('Y-m-d H:i:s');?>
          
            <h6 class="span_date"> <?php echo $date?></h6>
            </div>
           
            </li>
            
            <li>
            <div class="change_password">
            <h6><a href="<?php echo $base_url;?>change_password.php">Change Password</a></h6>
            </div>
            <div class="log_in">
            <h6><a href="<?php echo $base_url;?>logout.php">Logout</a></h6>
            </div>
            </li>
            </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="dist/img/Logo/logo.png" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>Alexander Pierce</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        
        <li class="active treeview">
          <a href="<?php echo $base_url;?>">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            <span class="pull-right-container">
           
            </span>
          </a>
         
        </li>
       
      
        <li class="treeview">
          <a href="#">
            <i class="fa fa-pie-chart"></i>
            <span>Manage</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="user_manage.php"><i class="fa fa-circle-o"></i>Users</a></li>
            <!-- <li><a href="delivery_request_list.php"><i class="fa fa-circle-o"></i>Delivery Request List</a></li> -->
            
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-laptop"></i>
            <span>Operation</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
             <li><a href="<?php echo $base_url;?>send_notification.php"><i class="fa fa-circle-o"></i>Send Notification</a></li>
            
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-edit"></i> <span>Reports</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
             <li><a href="financial.php"><i class="fa fa-circle-o"></i>Financial</a></li>
            <li><a href="<?php echo $base_url;?>payout_listing.php"><i class="fa fa-circle-o"></i>Payout</a></li>
            <li><a href="#"><i class="fa fa-circle-o"></i>Complain</a></li>
            <li><a href="#"><i class="fa fa-circle-o"></i>Wallet</a></li>
            <li><a href="delivery_request_list.php"><i class="fa fa-circle-o"></i>Delivery</a></li>
            <li class="<?php echo ($_SERVER['PHP_SELF'] == '/about' ? ' promocode' : '');?>" ><a href="<?php echo $base_url;?>promocode.php"><i class="fa fa-circle-o"></i>Promo Code</a></li>
            <li><a href="fare_rule.php"><i class="fa fa-circle-o"></i>Fare Rule</a></li>
            <li><a href="payment_history.php"><i class="fa fa-circle-o"></i>Payment History</a></li>
          </ul>
        </li>
        
      
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>