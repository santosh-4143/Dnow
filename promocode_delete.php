<?php

$id = $_GET['id'];
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "http://13.228.26.230/deletePromocode?promoId=$id");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$curlresult = curl_exec($curl);
curl_close($curl);
$result = json_decode($curlresult);

?>

 <script type="text/javascript">
            window.location.href = '<?php echo $base_url."promocode.php";?>';
          </script> <?php
?>