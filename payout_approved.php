<?php

//echo $_GET['payout_id'];die;
 $data = array(
                  'id'           => $_GET['payout_id'],
                  'user_id'      => $_GET['user_id']
                );
// echo "<pre>"; print_r($data);die;

  $curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "http://13.228.26.230/acceptPayout");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));//Setting post data as xml
$result = curl_exec($curl);
curl_close($curl);
$result = json_decode($result);
?>

 <script type="text/javascript">
            window.location.href = '<?php echo $base_url."payout_listing.php";?>';
          </script> <?php






?>