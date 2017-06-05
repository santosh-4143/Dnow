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
     
<h3>Financial Management</h3>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

</div>
</div>

<?php
$sql = "select tbl_userpayment.balance,date_format(tbl_userpayment.last_updated,'%Y-%m-%d') datewise from tbl_userpayment where tbl_userpayment.user_id='1' and id in (select max(tbl_userpayment.id) from tbl_userpayment where tbl_userpayment.user_id='1' and tbl_userpayment.last_updated between DATE_ADD(NOW(), INTERVAL -7 DAY) AND NOW() group by date_format(tbl_userpayment.last_updated,'%Y-%m-%d'))";

$query = $link->query($sql);


?>


  
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<div class="table-responsive">
   <table >    
    <thead>
      <tr>
        
        <th>Balence</th>
        <th>Date</th>
        
        
      </tr>
    </thead>
    
  

	
	  
	   	<tbody>
    
      <?php while($value=$query->fetch_array()){  ?>
      <tr>
       
           <td><?php echo $value['balance'];?></td>
       <td><?php echo $value['datewise'] ;?></td>
   
      
        
      </tr> 
      <?php  }  ?>
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

