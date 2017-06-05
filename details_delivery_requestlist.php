<?php include 'header.php';?>
<?php include 'config.php';?>
<style>
	
	h3 {
    text-decoration: underline;
    color: red;
    font-weight: bold;
}
</style>

<div class="content-wrapper">
<!-- <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"></div> -->

  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
       <div class="round_area">
       <div class="first_row">
      <div class="first_row"></div>


<?php
$uesrtype='NA';
$sql = "SELECT D.*,U.*,DR.email AS driver_email, DR.phone AS driver_phone, DR.image AS driver_img,DR.username AS driver_uname FROM tbl_delivery_request AS D JOIN tbl_user AS U ON U.user_id = D.user_id LEFT JOIN tbl_user AS DR ON DR.user_id = D.driver_id WHERE D.id = '".$_GET['id']."'";
	//echo $sql;

		$query = $link->query($sql);
		$result = mysqli_fetch_array($query);
		//echo "<pre>"; print_r($result);die;
		
?>
	 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
		<h3>Details of Sender:</h3></br>
	<h4><b>User Name:</b> <?php echo $result['username']?></h4></br>
	<h4><b>User Email:</b> <?php echo $result['email']?></h4></br>
	<h4><b>User Phone:</b> <?php echo $result['countryCode'].$result['phone']?></h4></br>
	<h4><b>User Profile Image:</b> <img src="http://13.228.26.230/<?php echo $result['image'] ?>" style="width: 100px;height: 80px;"></h4></br>
	
	<?php if($result['user_type']=='1')
       {$uesrtype = 'All';}
       elseif($result['user_type']=='2')
        {$uesrtype = 'Courier Boy';}
       elseif($result['user_type']=='3')
        {$uesrtype = 'Customer';}
	?>
	<h4><b>User Type:</b> <?php echo $uesrtype;?></h4></br>


	</div>
	
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">

		<h3>Details of Driver</h3></br>
		<?php if(!empty($result['driver_uname'])) {?>
			<h4><b>Username:</b> <?php echo $result['driver_uname']?></h4></br>
			<h4><b>Phone:</b> <?php echo $result['driver_phone']?></h4></br>
			<h4><b>Email:</b> <?php echo $result['driver_email']?></h4></br>
			<h4><b>Driver Image:</b> <img src="http://13.228.26.230/<?php echo $result['driver_img'] ?>" style="width: 100px;height: 80px;"></h4></br>
	
	
	
	
	
	
	   <?php }else{ ?>

	   		<h4>Driver Not Assigned!</h4>
	 <?php  	} ?>
	 </div>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<h3>Details of Consignment</h3></br>
	  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
	
		<h4><b>Delivery Sent To:</b> <?php echo $result['person_name']?></h4></br>
		<h4><b>Value Of Goods:</b> <?php echo $result['value']?></h4></br>
		<h4><b>Weight:</b> <?php echo $result['weight'].$result['weight_unit']?></h4></br>
		<h4><b>Dimension:</b> <?php echo $result['height']."*".$result['width']."*".$result['depth']." cubic ".$result['dimension_unit']?></h4></br>
		<h4><b>Courier Mode:</b> <?php echo $result['courier_mode']?></h4></br>
	<h4><b>Consignment Image:</b> <img src="http://13.228.26.230/<?php echo $result['pic'] ?>" style="width: 100px;height: 80px;"></h4></br>
	
	 </div>
	 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
	<h4><b>Item Type:</b> <?php echo $result['item_type']?></h4></br>
	<h4><b>From:</b> <?php echo $result['from_txt']?></h4></br>
	<h4><b>To:</b> <?php echo $result['to_txt']?></h4></br>
	<h4><b>Estimated Distance:</b> <?php echo $result['estimatedDistance']?></h4></br>
	<h4><b>Estimated Price:</b> <?php echo $result['estPrice']?></h4></br>
	 </div>
	</div>

	
</div>
</div>
</div>

</div>
<?php include 'footer.php';?>