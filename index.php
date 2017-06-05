
<?php  
$base_url     = "http://".$_SERVER['HTTP_HOST']."/dnow/";
session_start();
if(!empty($_SESSION)){
  header('Location: '.$base_url.'dashboard.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Dnow | Login</title>

  <link rel="shortcut icon" type="image/png" href="images/DNOW.png"/>
  <!-- Bootstrap core CSS -->

  <link href="css/bootstrap.min.css" rel="stylesheet">

  <link href="fonts/css/font-awesome.min.css" rel="stylesheet">
  <link href="css/animate.min.css" rel="stylesheet">

  <!-- Custom styling plus plugins -->
  <link href="css/custom.css" rel="stylesheet">
  <link href="css/sweetalert.css" rel="stylesheet">
  <link href="css/icheck/flat/green.css" rel="stylesheet">


  <script src="js/jquery.min.js"></script>
  <script src="js/sweetalert.min.js"></script>

  <!--[if lt IE 9]>
        <script src="../assets/js/ie8-responsive-file-warning.js"></script>
        <![endif]-->

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

</head>

<body style="background:#F7F7F7;">
<div class="log_back">
<form action="" method="post">
<div class="log_main_area">
<div class="first_row_1">
<span class="ico_area">
<i class="fa fa-user u1"></i>
<input type="text" name="uname" class="form-control user" size="30" placeholder="User name">
</span>
<span class="ico_area">
<i class="fa fa-lock u1"></i>
<input type="password" name="password" class="form-control user" size="30" placeholder="Password">
</span>
<div class="remember_area">
<div class="remem_area">
<div class="check_area">
<input type="checkbox" class="form-control"/>
</div>
<div class="text_check">
<h6> Remember Password</h6>
</div>
</div>
<div class="forgot_area">
<h6><a href="#">Forgot Password</a></h6>
</div>
</div>
<div class="log_in_button">
<input type="submit" name="submit" class="btn btn-primary l_1" value="login" />
</div>
</div>

</div>
</form>
</div>   

</body>

</html>

<?php 
if($_POST['submit']){

   $data = array(
                  'username'           => $_POST['uname'],
                  'password'         => $_POST['password']                  
                );
 

  $curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "http://13.228.26.230/login");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));//Setting post data as xml
$result = curl_exec($curl);
curl_close($curl);
$result = json_decode($result);

if($result->error == 1){

  echo '<script type="text/javascript">';
      echo 'swal("Oppss!", "Invalid credentials!", "error");';
      echo '</script>';
}elseif($result->respCode==1){
   echo '<script type="text/javascript">';
      echo 'swal("Good Job", "Login Successfully!", "success");';
      echo '</script>';
      session_start();
      $_SESSION['email'] = $result->email; 
      $_SESSION['phone'] = $result->phone; 
      header('Location: '.$base_url.'dashboard.php');
}

}