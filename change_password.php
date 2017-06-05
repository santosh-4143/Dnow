<?php include 'header.php';?>

 <div class="content-wrapper">
 <div class="row">
 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
 	 <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
 		   <div class="round_area">
 		   <div class="first_row">
       <h4>Please Enter Current Password & New Password.</h4>
 	<form action="" method="post">
 	<input type="password" name="current_pass" placeholder="Enter Current Password." style="width: 25%;">
 	<input type="password" name="new_pass" placeholder="Enter New Password." style="width: 25%;"></br>
 	
 	<input type="submit" name="change" class="cancelbtn" value="Change" style="width: 25%;" />
 	</form>
 	
 	</div>
 	</div>
 	</div>
</div>
 </div>
 </div>
 <?php

 	$change = $_POST['change'];
 	if(!empty($change)){


     $data = array(
     			  'userId'			   => '1',
                  'password'           => $_POST['new_pass'],
                  'oldPassword'        => $_POST['current_pass']
                  
                );
 

  $curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "http://13.228.26.230/resetPassword");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));//Setting post data as xml
$result = curl_exec($curl);
curl_close($curl);
//echo $result; exit;
$result = json_decode($result);
//echo "<pre>"; print_r($result);die;
if($result->error ==1){
	echo '<script type="text/javascript">';
      echo 'swal("Oppss!", "Current Password is wrong!", "error");';
      echo '</script>';
 
}else{

$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $current_url = explode('?', $actual_link);

         ?>
            <script type="text/javascript">
            window.location.href = '<?php echo $base_url."logout.php"?>';
          </script>
        <?php


}


 	}	


 ?>

<?php include 'footer.php';?>