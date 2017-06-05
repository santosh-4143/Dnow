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
    <div class="row">

  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
       <div class="round_area">
       <div class="first_row">
     
<h3>User Management</h3>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      
  <form action="" method="get"  >
 
    <label><b>Email & Username & Phone</b></label></br>
    <input type="text" placeholder="Enter Email" name="email" value="<?php echo $_GET['email'];?>" style="width: 25%;" >

    <input type="text" placeholder="Enter Username" value="<?php echo $_GET['uname'];?>" style="width: 25%;" name="uname" >
      
     <input type="text" placeholder="Enter Phone" value="<?php echo $_GET['phone'];?>" style="width: 25%;" name="phone" >
    
      <input type="submit" name="filter" class="cancelbtn" value="Filter" style="width: 38%;" />
      <input type="submit" name="reset" class="cancelbtn" value="Reset" style="width: 38%;" />
     
    

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
$uesrtype='NA';
$reset  = $_GET['reset'];
$filter = $_GET['filter'];
$email = $_GET['email'];
$uname = $_GET['uname'];
$phone = $_GET['phone'];
$custurl =  "http://13.228.26.230/searchUser/?limit=".$limit."&offset=".$start;

if(isset($filter)){


if(isset($email)){
  $custurl = $custurl."&email=".$email;
} 
if(isset($uname)){
  $custurl = $custurl."&username=".$uname;
} 
if(isset($phone)){
  $custurl = $custurl."&phone=".$phone;
}

}elseif (!empty($reset)) {
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $current_url = explode('?', $actual_link);

         ?>
            <script type="text/javascript">
            window.location.href = '<?php echo $current_url[0];?>';
          </script>
        <?php
        
      }
  $curl1 = curl_init();
  $custurl1 = "http://13.228.26.230/searchUser?email=".$email."&username=".$uname."&phone=".$phone;
curl_setopt($curl1, CURLOPT_URL, $custurl1 );
//echo $custurl1;
curl_setopt($curl1, CURLOPT_RETURNTRANSFER, 1);
$curlresult = curl_exec($curl1);
curl_close($curl1);
$result1 = json_decode($curlresult);
$count = count($result1->value);
//echo $count;
$total=ceil($count/$limit);
 
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $custurl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$curlresult = curl_exec($curl);
curl_close($curl);
$result = json_decode($curlresult);


?>


  
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<div class="table-responsive">
   <table >    
    <thead>
      <tr>
        
        <th>User Name</th>
        <th>Email</th>
        <th>User Type</th>
        <th>Image</th>
        <th>Phone</th>
        <th>Create Date</th>
     
        <th>Action</th>
        
      </tr>
    </thead>
    
  

	
	  
	   	<tbody>
      
      <?php foreach ($result->value as $value) { ?>
      <tr>
       
           <td><?php echo $value->username ?></td>
       <td><?php echo $value->email ?></td>
   
       <?php if($value->user_type=='1')
       {$uesrtype = 'All';}
       elseif($value->user_type=='2')
        {$uesrtype = 'Courier Boy';}
       elseif($value->user_type=='3')
        {$uesrtype = 'Customer';}?>
       <td><?php echo $uesrtype ?></td>
       <td><img src="http://13.228.26.230/<?php echo $value->image ?>" style="width: 60px;height: 50px;"></td>
       <td><?php echo $value->countryCode.$value->phone ?></td>
       <td><?php echo $value->create_date ?></td>
       <td>
      <?php if($value->is_blocked=='0'){?>
       	<a href="<?php echo $base_url;?>block_user.php?id=<?php echo $value->user_id ;?>">Block</a>
         <?php } elseif($value->is_blocked=='1'){?>
            <a href="<?php echo $base_url;?>unblock_user.php?id=<?php echo $value->user_id;?>">Unlock</a>
        <?php }?>
       </td>
        
      </tr> 
      <?php $i++; }  ?>
    </tbody>
	      
</table>

</div>
      
</div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <?php
 if($offset>1)
{ 

echo "<a href='?limit=5&offset=".($offset-1)."&email=".$email."&uname=".$uname."&phone=".$phone."' class='cancelbtn' style='float:left;width:25%;'><< PREVIOUS</a>";

}
if($offset!=$total)
{ 
echo "<a href='?limit=5&offset=".($offset+1)."&email=".$email."&uname=".$uname."&phone=".$phone."' class='cancelbtn' style='float:right;width:25%;'>NEXT >></a>";
} 

?>

    </div>
    </div>
 	
 	</div>
 	</div>
</div>

</div>

 </div>
 

<?php include 'footer.php';?>

