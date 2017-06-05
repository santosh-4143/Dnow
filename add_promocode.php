<?php 
//ini_set("display_errors", "1");
//error_reporting(E_ALL);die;?>
<?php include 'header.php';

?>


<?php 
if($_POST['promo_sub']){


$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "http://13.228.26.230/getPromoCodeList");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$curlresult = curl_exec($curl);
curl_close($curl);
$result = json_decode($curlresult);
$promo = array();
foreach ($result->response as $value) {
  array_push($promo, $value->promocode);
}
if(in_array($_POST['promocode'], $promo)){
 
   echo '<script type="text/javascript">';
      echo 'swal("Oppss!", "Promo code already exist!", "error");';
      echo '</script>';
}else{

     $data = array(
                  'promocode'           => $_POST['promocode'],
                  'description'         => $_POST['details'],
                  'forUserType'         => $_POST['user_type'],
                  'moneyValue'          => $_POST['m_value'],
                  'startDate'           => date('Y-m-d H:i',strtotime('-5 hour -30 minutes',strtotime($_POST['startdate']))) ,
                  'expiryDate'          => date('Y-m-d H:i',strtotime('-5 hour -30 minutes',strtotime($_POST['enddate'])))
                );
 

  $curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "http://13.228.26.230/addPromoCode");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));//Setting post data as xml
$result = curl_exec($curl);
curl_close($curl);
$result = json_decode($result);

if($result->error >0){
 
} ?>

 <script type="text/javascript">
            window.location.href = '<?php echo $base_url."promocode.php";?>';
          </script> <?php
}

}




?>


<div class="content-wrapper">
<div class="row">
 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
 		   <div class="round_area">
 		   <div class="first_row">

 		   <h2>Add Promocode</h2>
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<form action="" method="post"  >
  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
    <label><b>Promocode  &&  Money Value</b></label></br>
    <input type="text" placeholder="Enter Promocode" name="promocode" style="width: 36%;" required>

    <input type="text" placeholder="Enter Money value" style="width: 36%;" name="m_value" required>
    </div>
    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
    <div class="col-md-3 text_input_padding">
    <label><b>Start Date</b></label>
    <div class="form-group">
     
     <div class='input-group date' id='datetimepicker1'>
              <input type='text' name="startdate"   placeholder="Select Start Date."/>
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
              </span>
              </div>
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
       <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">   
      <label><b>Select User Type</b></label>
      <select name="user_type" class="form-control s_1">
      <option>Select User Type</option>
      <option value="1">All</option>
      <option value="2">Courier Boy</option>
      <option value="3">Customer</option>
     </select>
      </div> 

      <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
  
     <label><b>Description </b></label></br>
    <textarea type="text" name='details' size="25" rows="5" cols="50" class="form-control" style="width: 50%;"></textarea>
    </div>  
    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
      <input type="submit" name="promo_sub" class="cancelbtn" value="Add Promocode" />
    </div> 
    
  
</form>
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

