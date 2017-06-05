<?php include 'header.php';?>
<?php include 'config.php';?>
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
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
 		   <div class="round_area">
 		   <div class="first_row">
      
<h3>Payment History Details</h3></br>

     <?php
     $uesrtype='NA';
        $id = $_GET['id'];
       
        $sql = "SELECT P.* , U.username , U.email , U.phone ,U.user_type FROM tbl_userpayment  AS P
				LEFT JOIN tbl_user AS U ON U.user_id = P.user_id 
				WHERE P.orderId ='".$id."'";
            

        $query = $link->query($sql);
?>

    <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="table-responsive">
   <table >    
    <thead>
      <tr>
        
      
        <th>User Info</th>
        
        <th>Transaction ID</th>
        <th>Credit/Debit</th>
  
        <th>Amount</th>
        <th>Balence</th>
   
        <th>Last Update</th>

        
        
      </tr>
    </thead>
    
  

	
	  
	   	<tbody>
   <?php   while($value=$query->fetch_array()){ ;?>
      <tr>
       <?php if($value['user_id']=='0'){?>
        <td> Tax Deduct</td>
       <?php }else{ ?>
       <td>Name: <?php echo $value['username'];?></br>
       	   Email: <?php echo $value['email']?></br>
       	   Phone: <?php echo $value['phone']?></br>
           <?php if($value['user_type']=='1')
       {$uesrtype = 'All';}
       elseif($value['user_type']=='2')
        {$uesrtype = 'Courier Boy';}
       elseif($value['user_type']=='3')
        {$uesrtype = 'Customer';}?>
           User Type: <b><?php echo $uesrtype;?></b>
       </td>
       <?php } ?>
      
       <td><?php echo $value['txnId']?></td>
       <td><?php echo $value['operation']?></td>
       <td><?php echo $value['amount']?></td>
       <td><?php echo $value['balance']?></td>
       <?php $date=date_create($value['last_updated']);?>
       <td><?php echo date_format($date,"Y/m/d H:i:s");?></td>
        
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


