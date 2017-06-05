<?php include 'header.php';?>
<?php include 'config.php';?>

<style>
input{
  width: 100% !important;
}
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
 <div class="row">
 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
 		   <div class="round_area">
 		   <div class="first_row">
     
<h3>Fare Rule.</h3>


     
  

<?php
$sql = 'SELECT * FROM `tbl_fare`';
$result=$link->query($sql);;

?>


  
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<div class="table-responsive">

   <table >    
    <thead>
      <tr>
        
      
        <th>Weight Multiplier</th>
        <th>Distance Multiplier</th>
        <th>Value of Good(%)</th>
         <th>Rating of D.P</th>
        <th>Rating of user</th>
        <th>Tax Multiplier</th>
        <th>Minimum Fare</th>
        <th>Delevery person share(%)</th>
        <th>Dnow Share</th>
        <th>Action</th>
       
        
        
        
      </tr>
    </thead>

      <tbody>
  
   
      
    
  <?php
    
       while($rows=$result->fetch_array()){ ?>
      	  
	     <tr>
   <form action="" method="post">
     <td><input type="text" name="weight_mul" value="<?php echo $rows['weight_mul'];?>"></td>
     <td><input type="text" name="distance_mul" value="<?php echo $rows['distance_mul'];?>"></td>
     <td><input type="text" name="val_goods_per" value="<?php echo $rows['val_goods_per'];?>"></td>
     <td><input type="text" name="dp_rating" value="<?php echo $rows['dp_rating'];?>"></td>
     <td><input type="text" name="user_rating" value="<?php echo $rows['user_rating'];?>"></td>
     <td><input type="text" name="tax_mul" value="<?php echo $rows['tax_mul'];?>"></td>
     <td><input type="text" name="min_fare" value="<?php echo $rows['min_fare'];?>"></td>
     <td><input type="text" name="dp_share" value="<?php echo $rows['dp_share'];?>"></td>
     <td><input type="text" name="dnow_share" value="<?php echo $rows['dnow_share'];?>"></td>
     <td><input type="submit" class="btn btn-primary" name="submit"></td>
     </form>
        
      </tr> 

      <?php }  ?>
   
    </tbody>
	      
</table> 
  </div>
  </div>
  </div>
  
      
</div>

 	
 	</div>
 	</div>
</div>
</div>
 
 

<?php include 'footer.php';?>


