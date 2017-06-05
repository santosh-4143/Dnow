<?php


 $data = array(
                  'userId'           => $_GET['id'],
                  'isBlocked'         => '0'
                  
                );
 

  $curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "http://13.228.26.230/blockUser");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));//Setting post data as xml
$result = curl_exec($curl);
curl_close($curl);
$result = json_decode($result);
echo '<script>window.location = "http://localhost/dnow/user_manage.php";</script>';




?>