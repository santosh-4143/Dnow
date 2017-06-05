<?php


 include 'header.php';?>

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
<!-- <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"></div> -->
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
       <div class="round_area">
       <div class="first_row">
      
<h3>Delivery Request List</h3></br>

      <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
  <form action="" method="get"  >
 
    <label><b>Email & Username & Phone</b></label></br>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
    <input type="text" placeholder="Enter Email" name="email" value="<?php echo $_GET['email'];?>""  >
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
    <input type="text" placeholder="Enter Username" name="uname" value="<?php echo $_GET['uname'];?>" >
    </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
     <input type="text" placeholder="Enter Phone" name="phone" value="<?php echo $_GET['phone'];?>" >
      </div>
       </div>
       <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
    <div class="col-md-3 text_input_padding">
    <label><b>Start Date</b></label>
    
     
     <div class='input-group date' id='datetimepicker1'>
              <input type='text' name="startdate"   placeholder="Select Start Date."/>
              <span class="input-group-addon ">
                 <i class="glyphicon glyphicon-calendar"></i>
              </span>
    </div>
              </div>
    

    <div class="col-md-3 text_input_padding">
    <label><b>End Date</b></label>
    <div class="form-group">
      
     <div class='input-group date' id='datetimepicker2' >
              <input type='text' name="enddate"   placeholder="Select End Date."/>
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
              </span>
              </div>
            </div> 
         </div>  
        
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



  
 <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="table-responsive">
   <table >    
    <thead>
      <tr>
        <th>Sent From</th>
        <th>Sent To</th>
        <th>From</th>
        <th>To</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Date</th>
        <th>Action</th>
        
        
      </tr>
    </thead>
    
  

	
	  
	   	<tbody>
      <?php 
        $uname = $_GET['uname'];
        $email = $_GET['email'];
        $phone = $_GET['phone'];
        $filter = $_GET['filter'];
        $reset = $_GET['reset'];
        $startdate = $_GET['startdate'];
        $enddate  = $_GET['enddate'];

        if(!empty($filter)){
          //echo "<pre>"; print_r($_GET);
          $sql = "SELECT U.username,U.email,U.phone ,D.id, D.person_name , D.from_txt , D.to_txt,D.estPrice ,D.lastUpdated,D.status
              FROM tbl_delivery_request AS D
              JOIN tbl_user AS U ON U.user_id = D.user_id";
           if(!empty($uname) || !empty($email) || !empty($phone) || !empty($startdate) || !empty($enddate)){
              $sql .= " WHERE";
             
              if(!empty($uname)){
                $sql .=" AND U.username LIKE '%".$uname."%' ";
                $sql = str_replace("WHERE AND", "WHERE",$sql );
              }

              if(!empty($email)){
                $sql .= " AND U.email LIKE '%".$email."%'";
                $sql = str_replace("WHERE AND", "WHERE",$sql );
              }

              if(!empty($phone)){
                $sql .= " AND U.phone LIKE '%".$phone."%'";
                $sql = str_replace("WHERE AND", "WHERE",$sql );
              }
              if(!empty($startdate) && !empty($enddate)){
                $sql .=" AND (D.lastUpdated between '".$startdate."' and '".$enddate."')";
                $sql = str_replace("WHERE AND", "WHERE",$sql );
              }

              //   $sql = "SELECT U.username ,D.id, D.person_name , D.from_txt , D.to_txt,D.estPrice ,D.lastUpdated
              // FROM tbl_delivery_request AS D
              // JOIN tbl_user AS U ON U.user_id = D.user_id
              //  WHERE U.username LIKE '%".$uname."%' AND U.email LIKE '%".$email."%' AND U.phone LIKE '%".$phone."%' AND (D.lastUpdated between '".$startdate."' and '".$enddate."')";
              // echo $sql;

              

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
      $sql = "SELECT U.username,U.email,U.phone ,D.id, D.person_name , D.from_txt , D.to_txt,D.estPrice ,D.lastUpdated,D.status
              FROM tbl_delivery_request AS D
              JOIN tbl_user AS U ON U.user_id = D.user_id";
           }   
      $result = $link->query($sql);
    
     
        ?>
      <?php while($value=$result->fetch_array()){ ?>
      <tr>
        <td>Name: <b><?php echo $value['username']?></b></br>
            Phone: <b><?php echo $value['phone']?></b></br>
            Email: <b><?php echo $value['email']?></b>
        </td>
        <td><?php echo $value['person_name']?></td>
        <td><?php echo $value['from_txt']?></td>
        <td><?php echo $value['to_txt']?></td>
        <td><?php echo $value['status']?></td>
        <td><?php echo $value['estPrice']?></td>

        <td><?php echo $value['lastUpdated']?></td>
        <td><a href="details_delivery_requestlist.php?id=<?php echo $value['id'];?>">Details</a></td>
        
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

  <script type="text/javascript">

$('#datetimepicker1,#datetimepicker2').datetimepicker({

    format: 'YYYY-MM-DD HH:mm:ss'

});

</script>

