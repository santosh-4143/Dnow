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
      
<h3>Listing of Payment History</h3></br>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<form action="" method="get"  >
 
    <label><b>Email & Username & Phone & Paymode</b></label></br>
    

    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
    <input type="text" placeholder="Enter Email" name="email" value="<?php echo $_GET['email'];?>" >
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
    <input type="text" placeholder="Enter Username" value="<?php echo $_GET['uname'];?>"  name="uname" >
     </div> 
     <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
     <input type="text" placeholder="Enter Phone" value="<?php echo $_GET['phone'];?>"  name="phone" >
     </div>
     <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
     <select name="paymode" class="form-control s_1" >
       
       <option value="" >None</option>
       <option value="DEPOSIT" >DEPOSIT</option>
       <option value="PAID" >PAID</option>
       <option value="RECEIVED" >RECEIVED</option>
     </select>
    </div>
    
    <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
      <input type="submit" name="filter" class="cancelbtn" value="Filter" style="width: 100%;" />
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
      <input type="submit" name="reset" class="cancelbtn" value="Reset" style="width: 100%;" />
      </div>
     
</div>
     </div>
</form>

     </div>
    </div>
      <?php 
      $offset = 1;
      $start=0;
      $limit=5;
      if(isset($_GET['offset']))
        {
        $offset=$_GET['offset'];
        $start=($offset-1)*$limit;
        }
   
      ?>


     <?php
        $uname = $_GET['uname'];
        $email = $_GET['email'];
        $phone = $_GET['phone'];
        $paymode = $_GET['paymode'];
        $filter = $_GET['filter'];
        $reset = $_GET['reset'];
       // $page= $_GET['page'];

        if(!empty($filter) ){
        if(!empty($uname) || !empty($email) || !empty($phone) || !empty($paymode)){
          if(!empty($uname) && !empty($email) && !empty($phone) && !empty($paymode)){
            $sql = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND U.username LIKE '%".$uname."%' AND U.email LIKE '%".$email."%' AND U.phone LIKE '%".$phone."%' AND P.operation = '".$paymode."' LIMIT ".$start.", ".$limit."";
            $sql1 = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND U.username LIKE '%".$uname."%' AND U.email LIKE '%".$email."%' AND U.phone LIKE '%".$phone."%' AND P.operation = '".$paymode."' ";
          }elseif (!empty($uname) && !empty($email) && !empty($phone)) {
            $sql = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND U.username LIKE '%".$uname."%' AND U.email LIKE '%".$email."%' AND U.phone LIKE '%".$phone."%' LIMIT ".$start.", ".$limit."";
            $sql1 = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND U.username LIKE '%".$uname."%' AND U.email LIKE '%".$email."%' AND U.phone LIKE '%".$phone."%' ";
          }elseif (!empty($uname) && !empty($email) ) {
            $sql = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND U.username LIKE '%".$uname."%' AND U.email LIKE '%".$email."%' LIMIT ".$start.", ".$limit."";
            $sql1 = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND U.username LIKE '%".$uname."%' AND U.email LIKE '%".$email."%' ";
          }elseif (!empty($uname) ) {
            $sql = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND U.username LIKE '%".$uname."%' LIMIT ".$start.", ".$limit." ";
            $sql1 = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND U.username LIKE '%".$uname."%'  ";
          }elseif (!empty($email) ) {
             $sql = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND U.email LIKE '%".$email."%' LIMIT ".$start.", ".$limit." ";
             $sql1 = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND U.email LIKE '%".$email."%'  ";
          }elseif (!empty($phone) ) {
             $sql = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND U.phone LIKE '%".$phone."%' LIMIT ".$start.", ".$limit." ";
             $sql1 = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND U.phone LIKE '%".$phone."%' ";
          }elseif (!empty($paymode) ) {
             $sql = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND P.operation = '".$paymode."' LIMIT ".$start.", ".$limit." ";
             $sql1 = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') AND P.operation = '".$paymode."'  ";
          }
           
        }
      }elseif (!empty($reset)) {
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $current_url = explode('?', $actual_link);

         ?>
            <script type="text/javascript">
            window.location.href = '<?php echo $current_url[0];?>';
          </script>
        <?php
        
      }else{
        $sql = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') LIMIT ".$start.", ".$limit."";
        $sql1 = "SELECT P.* , U.username , U.email , U.phone , D.status AS last_status FROM tbl_userpayment AS P JOIN tbl_user AS U ON U.user_id = P.user_id LEFT JOIN tbl_delivery_request AS D ON D.id = P.orderId WHERE P.user_id NOT IN('0','1') ";
            }   
        $query = $link->query($sql);
        $query1 = $link->query($sql1);
        $count = mysqli_num_rows($query1);
        $total=ceil($count/$limit);
      
?>

    <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="table-responsive">
   <table >    
    <thead>
      <tr>
        
      
        <th>User Info</th>
        <th>Transaction Id</th>
        <th>Order Status</th>
        <th>Amount</th>
        <th>Balence</th>
   
       <!--  <th>Discount Amount</th> -->
       
        <th>Transaction Status</th>
      <!--   <th>Payment Type</th> -->
        <th>Delivery Status</th>
        <th>Last Update</th>

        
        
      </tr>
    </thead>
    
  

	
	  
	   	<tbody>

   <?php  
   
    while($value=$query->fetch_array()){ 
      
     ?>
  <tr>
        <td>Name: <?php echo $value['username']?></br>Email:<?php echo $value['email'] ?></br>Phone:<?php echo $value['phone']?></td>
        <td><?php if($value['txnId']){echo $value['txnId'];}else{echo "NA";}?></td>
        <td><?php if($value['orderId']=='' && $value['operation']=='DEPOSIT'){echo 'ADD WALLET';}elseif($value['orderId']){echo $value['orderId']."<b><a href=paymennt_history_details.php?id=".$value['orderId']."> Click Here</a></b>";}else{echo "NA";}?></td>
        <td><?php if($value['amount']){echo $value['amount'];}else{echo "NA";}?></td>
        <td><?php if($value['balance']){echo $value['balance'];}else{echo "NA";}?></td>
      <!--  <td><?php// if($value['discountAmount']){echo $value['discountAmount'];}else{echo "NA";}?></td> -->
      
       <td><?php if($value['operation']){echo $value['operation'];}else{echo "NA";}?></td>
       <!--  <td><?php //if($value['payment_type']){echo $value['payment_type'];}else{echo "NA";}?></td> -->
      <td><?php if($value['orderId']=='' && $value['operation']=='DEPOSIT'){echo "DEPOSIT TO WALLET";}elseif($value['last_status']){echo $value['last_status'];}else{echo "NA";}?></td>
       <td><?php if($value['last_updated']){echo $value['last_updated'];}else{echo "NA";}?></td>
       
        
      </tr> 
     
      <?php
      
     
     
       }
      
       ?>
    </tbody>
	      
</table> 

         <?php
 if($offset>1)
{ 

echo "<a href='?limit=5&offset=".($offset-1)."&email=".$email."&uname=".$uname."&phone=".$phone."&paymode=".$paymode."&filter=".$filter."' class='cancelbtn' style='float:left;width:25%;'><< PREVIOUS</a>";

}
if($offset!=$total)
{ 
echo "<a href='?limit=5&offset=".($offset+1)."&email=".$email."&uname=".$uname."&phone=".$phone."&paymode=".$paymode."&filter=".$filter."' class='cancelbtn' style='float:right;width:25%;'>NEXT >></a>";
} 

?>

  </div>
</div>
</div>

 	
 	</div>
 	</div>
</div>
</div>
 </div>
 

<?php include 'footer.php';?>


