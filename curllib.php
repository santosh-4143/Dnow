<?php

function call_curl($url='',$param=array(),$method='GET') {  

		  $options 										= 	array(
														        CURLOPT_RETURNTRANSFER 		=> true,   // return web page
														        CURLOPT_HEADER        		=> false,  // don't return headers
														        CURLOPT_FOLLOWLOCATION 		=> true,   // follow redirects
														        CURLOPT_MAXREDIRS      		=> 10,     // stop after 10 redirects
														        CURLOPT_ENCODING       		=> "",     // handle compressed
														        CURLOPT_AUTOREFERER    		=> true,   // set referrer on redirect
														        CURLOPT_CONNECTTIMEOUT 		=> 120,    // time-out on connect
														        CURLOPT_TIMEOUT        		=> 120
														    ); 
		if(empty($param)){
			if($method=='POST'){
				$options[CURLOPT_POST]					=	true;
			}
		}else{
		  	$query		=	http_build_query($param);
		  	if($method=='GET'){
		  		$url  	.= 	"?".$query;
		  	}else{
		  		$options[CURLOPT_POST]					=	true;
		  		$options[CURLOPT_POSTFIELDS]			=	$query;
		  	}
		}
		log_message('debug', 'Curl URL: '.$url);
		log_message('debug', 'Curl Parameters: '.json_encode($param));
		log_message('debug', 'Curl Method: '.$method);
	    $ch 											=	curl_init($url);
	    curl_setopt_array($ch, $options);
	    $returnArray['content']  						=	curl_exec($ch);
	    log_message('debug', 'Response: '.$method);	    
	    $returnArray['headerInfo']						=	curl_getinfo($ch);
	    curl_close($ch);
	    print_r($returnArray['content']);die;
	    return $returnArray;
	}


?>