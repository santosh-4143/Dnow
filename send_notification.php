<?php 
//ini_set("display_errors", "1");
//error_reporting(E_ALL);?>
<?php include 'header.php';

?>


<?php 
if($_POST['notification']){

   $data = array(
                  'type'            => 'Panel Notification',
                  'usertype'         => $_POST['user_type'],
                  'title'           => $_POST['title'],
                  'body'          => $_POST['body']
                  
                );
 // echo "<pre>"; print_r($data);die;
  $curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "http://13.228.26.230/pushNotification");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));//Setting post data as xml
$result = curl_exec($curl);
curl_close($curl);
$result = json_decode($result);
echo "<pre>";
print_r($result);die;

if($result->error >0){
 
}

echo '<script>window.location = "http://localhost/dnow/promocode.php";</script>';
}



?>
<style>
/* Full-width input fields */
input[type=text], input[type=password] {
    width: 50%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

/* Set a style for all buttons */
button {
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    cursor: pointer;
    width: 100%;
}

/* Extra styles for the cancel button */
.cancelbtn {
    padding: 14px 20px;
    background-color: #ed1c24;
    color:white;
}

/* Float cancel and signup buttons and add an equal width */
.cancelbtn,.signupbtn {
    float: left;
    width: 50%;
}

/* Add padding to container elements */
.container {
    padding: 16px;
}

/* Clear floats */
.clearfix::after {
    content: "";
    clear: both;
    display: table;
}
.select-style {
    border: 1px solid #ccc;
    width: 120px;
    border-radius: 3px;
    overflow: hidden;
    background: #fafafa url("img/icon-select.png") no-repeat 90% 50%;
}

.select-style select {
    padding: 5px 8px;
    width: 130%;
    border: none;
    box-shadow: none;
    background: transparent;
    background-image: none;
    -webkit-appearance: none;
}

.select-style select:focus {
    outline: none;
}

/* Change styles for cancel button and signup button on extra small screens */
/*@media screen and (max-width: 300px) {
    .cancelbtn, .signupbtn {
       width: 100%;
    }*/

</style>

<div class="content-wrapper">

 	<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
 		   <div class="round_area">
 		   <div class="first_row">

 		   <h2>Send Notification</h2>

<form action="" method="post" style="border:1px solid #ccc" >
  <div class="container">
   
  <label><b>Title</b></label></br>
     <input type="text" placeholder="title" name="title" style="width: 25%;" required> </br>                

    <label><b>User Type</b></label></br>
  <div class="select-style">
    <select name="user_type">
    	<option>Select User Type</option>
    	<option value="1">All</option>
    	<option value="2">Courier Boy</option>
    	<option value="3">Customer</option>
    </select>

    </div>

    </br>  <label><b>Body </b></label></br>
    <textarea type="text" name='body' size="25" rows="5" cols="50" class="form-control" style="width: 50%;"></textarea></br>   
      <input type="submit" name="notification" class="cancelbtn" value="Send Notification" />
     
    
  </div>
</form>



 		   </div>
 		   </div>
 		   </div>
 		   </div>




<?php include 'footer.php';?>

	


