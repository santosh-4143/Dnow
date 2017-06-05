<?php include 'header.php';?>
<style>
table {
    border-collapse: collapse;
    width: 100%;
 }
 table, th, td {
    border: 1px solid black;
}

th, td {
    text-align: left;
    padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2}

th {
    background-color: #ed1c24;
    color: white;
}
tr:hover {background-color: #ffffff}
</style>
 <div class="content-wrapper">
<!-- <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"></div> -->

 	<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
 		   <div class="round_area">
 		   <div class="first_row">
      <div class="first_row"></div>
<h3>Listing of Promocode.</h3>
<div class="first_row"></div>
      
    <a href="<?php echo $base_url;?>add_promocode.php"><h4>Add New Promocode.</h4></a>

<?php
$uesrtype='NA';
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "http://13.228.26.230/getPromoCodeList");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$curlresult = curl_exec($curl);
curl_close($curl);
$result = json_decode($curlresult);
// echo "<pre>";
// print_r($result->response);die;
?>


  


   <table >    
    <thead>
      <tr>
        <th>SL.NO</th>
        <th>Promo Code</th>
        <th>User Type</th>
        <th>Description</th>
        <th>Money Value</th>
        <th>Start Date</th>
        <th>Expiry Date</th>
        <th>Action</th>
        
      </tr>
    </thead>
    
  

	
	  
	   	<tbody>
      <?php $i=1;?>
      <?php foreach ($result->response as $value) { ?>
      <tr>
       <td><?php echo $i?></td>
       <td><?php echo $value->promocode ?></td>
       <?php if($value->forUserType=='1')
       {$uesrtype = 'All';}
       elseif($value->forUserType=='2')
        {$uesrtype = 'Courier Boy';}
       elseif($value->forUserType=='3')
        {$uesrtype = 'Customer';}?>
       <td><?php echo $uesrtype ?></td>
       <td><?php echo $value->description ?></td>
       <td><?php echo 'Rs. '.$value->moneyValue ?></td>
       <td><?php echo $value->startDate ?></td>
       <td><?php echo $value->expiryDate ?></td>
       <td>
      
       	<a href="<?php echo $base_url;?>promocode_delete.php?id=<?php echo $value->id;?>">Delete</a>
       </td>
        
      </tr> 
      <?php $i++; }  ?>
    </tbody>
	      
</table>
      


 	
 	</div>
 	</div>
</div>

 </div>
 

<?php include 'footer.php';?>


<!-- <div class="container">
  <h2>Vertical (basic) form</h2>
  <form>
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" class="form-control" id="email" placeholder="Enter email">
    </div>
    <div class="form-group ">
      <label for="pwd">Password:</label>
      <input type="password" class="form-control" id="pwd" placeholder="Enter password">
    </div>
    <div class="checkbox">
      <label><input type="checkbox"> Remember me</label>
    </div>
    <button type="submit" class="btn btn-default">Submit</button>
  </form>
</div> -->