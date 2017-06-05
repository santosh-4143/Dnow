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
    text-align: center;
    padding: 1px;
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
     
<h3>Listing of Payout.</h3>

     
  

<?php
$uesrtype='NA';
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "http://13.228.26.230/showPayoutRequest?type=ALL");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$curlresult = curl_exec($curl);
curl_close($curl);
$result = json_decode($curlresult);
// echo "<pre>";
// print_r($result);die;
?>


  
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

   <table >    
    <thead>
      <tr>
        
      
       
        <th>User Info</th>
        <th>User Type</th>
        <th>Account Holder Name</th>
        <th>Account Number</th>
        <th>IFSC Number</th>
        <th>Description</th>
        <th>Amount</th>
        <th>Requsted Time</th>
        <th>Accepted time</th>
        <th>Status</th>
        <th>Action</th>
        
        
      </tr>
    </thead>
    
  

	
	  
  	<tbody>
      <?php $i = 1;?>
      <?php foreach ($result->value as $value) { ?>
      <tr>
   
       
       <td>Name: <?php echo $value->username ?></br>
           Email: <?php echo $value->email ?></br>
           Phone: <?php echo $value->phone ?></br>
       </td>
       
        <?php if($value->user_type=='1')
       {$uesrtype = 'All';}
       elseif($value->user_type=='2')
        {$uesrtype = 'Courier Boy';}
       elseif($value->user_type=='3')
        {$uesrtype = 'Customer';}?>
       <td><?php echo $uesrtype ?></td>
       <td><?php echo $value->acc_holder_name ?></td>
       <td><?php echo $value->acc_number ?></td>
       <td><?php echo $value->ifsc_no?></td>
       <td><?php echo $value->description ?></td>
       <td><?php echo 'Rs. '.$value->amount ?></td>
        <td><?php if($value->requsted_time){ echo date('d/m/Y', strtotime($value->requsted_time))  ;}else{echo "N/A";} ?></td>
       <td><?php if($value->accepted_time){ echo $value->accepted_time;}else{echo "N/A";} ?></td>
       <td><?php echo $value->status ?></td>
       <?php if($value->status == 'REQUESTED'){?>
      <td><a href="payout_approved.php?payout_id=<?php echo $value->id?>&user_id=<?php echo $value->user_id?>" class="cancelbtn">Approve Payout</a></td> 
      <?php } else{ ?>
          <td><a href="#" class="cancelbtn">Approved</a></td> 

     <?php   }?>
      
        
      </tr> 
      <?php $i++; }  ?>
    </tbody> 
	     <!--  <tbody>
          <tr>
            <td>ghg</td>  
            <td>aaa</td>  
            <td>kkk</td>  
            <td>rrr</td>  
            <td>eee</td>  
            <td>klk</td>  
            <td>nmn</td>  
            <td>pop</td>  
            <td>lll</td>  
            <td>rtr</td>  
            <td><a href="#" class="cancelbtn c_1" style="display:block;">Approve Payout</a>
                <a href="#" class="cancelbtn c_2" style="display:none;">Approved </a>
            </td>          

          </tr>
        </tbody> -->
</table> 
 
  </div>
  </div>


 	
 	</div>
 	</div>
</div>
</div>
 </div>
 

<?php include 'footer.php';?>





