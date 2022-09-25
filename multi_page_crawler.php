<!DOCTYPE html>
<html>
<head>
<title>multi_page_crawler</title>

<link rel="stylesheet" href="single_page_crawler.css">
</head>
<body>

<div id="container">
<div id="content">
<br><br><br><br>
<br><br><br><br>
<h1>Skate</h1>

<div class="form">
	//php_self to store the values in super-global
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <input type="text" id="name" name="name" autofocus placeholder="search">
  <input type="submit" value="search">
</form> 
</div>

<?php

    function extract_title($data){
         $start = "<title";
 		 $end = "</title>";
		 $b = strpos($data,$start);
		 $a = strpos($data,$end,$b);
		 $length = $a - $b+10;
		 $title_temp = substr($data,$b,$length);

		 $start = ">";
		 $end = "</title>";
		 $b = strpos($title_temp,$start);
		 $a = strpos($title_temp,$end,$b);
		 $length = $a - $b+7;
		 $title = substr($title_temp,$b+1,$length);

        return $title;
    }

function extract_description($data){
        $description = "";
    foreach(preg_split("/[<>]+/",$data) as $line){
        $description_txt = "/" . "description" . "/i";
		$content = "/" . "content=" . "/i";
		if(preg_match($description_txt,$line)&&preg_match($content,$line)){
		    $start = "content=";
			$end = '"';
			$b = strpos($line,$start);
			$a = strpos($line,$end,$b+9);
			$length = $a - $b-9;
			$sub_line = substr($line,$b+9,$length);

			$sub_line_txt = "/" . $sub_line . "/i";
	        if(!(preg_match($sub_line_txt,$description))){ 
		    	$description .= $sub_line . " ";
	        }
        }
    }
return $description;
}

function extract_paragraph($data){
		$start = "<p>";
		$end = "</p>";
		$b = strpos($data,$start);
		if(!$b){
			return false;
		}
		$a = strpos($data,$end,$b+3);
		$length = $a - $b-6;
		$title = substr($data,$b,$length);

		$paragraph = "";
		foreach(preg_split("/[<]+/",$title) as $line){
			$start2 = ">";
			$b2 = strpos($line,$start2);
			$sub_line = trim(substr($line,$b2+1));
			$paragraph .= $sub_line . " ";
		}
		return $paragraph;
}

function extract_links($data){

		$addresses = "";
		$link_string =  "/" . "<a" . "/i";		
		while(preg_match($link_string,$data)){
		   		$start = "<a";
		   		 $end = ">";
				$beginningPos = strpos($data, $start);
				$b = $beginningPos;
				$endPos = strpos($data, $end,$b);
				$a = $endPos;
				$lengte = $a-$b+1;
				$linkje = trim(substr($data, $b+1,$lengte));
		
		
				$http_string =  "/" . "http:" . "/i";
				$https_string =  "/" . "https:" . "/i";				
			if(preg_match($http_string,$linkje)||preg_match($https_string,$linkje)){
		
				$start = "href=";
				$end = '"';
				$beginningPos = strpos($linkje, $start);
				$b = $beginningPos;
				$endPos = strpos($linkje, $end,$b+6);
				$a = $endPos;
				$lengte = $a-$b-6;
				$linkje_small = trim(substr($linkje, $b+6,$lengte));
		
				$start = "http";
				$end = "?";
				$beginningPos = strpos($linkje_small, $start);
				$b = $beginningPos;
				
				if(strlen($linkje_small)< $b+6){
					$endpos=false;
				  }
				  else{
  
					$endPos = strpos($linkje_small, $end,$b+6);
				  }
		  
				$a = $endPos;
				$lengte = $a-$b;
				$linkje_small2 = trim(substr($linkje_small, 0,$lengte));
				if($linkje_small2 == ""){
					$real_link = $linkje_small;
				}
				else{
					$real_link = $linkje_small2;
				}
			
				$link_start = substr($real_link, 0,4);
					
				$counter=0;
				foreach(preg_split("/((\r?\n)|(\r\n?))/",$addresses) as $line){
					if(!strcmp($line,$real_link)){
						$counter++;
						break;	
					}
			
					
				}
				if($counter==0){
					if($link_start=="http"){
						$addresses .= ($real_link . "\n");		
					}
				}
		    }
		
		    $remove = $linkje;
		    $data = str_replace($remove, "",$data);
		}

				
   		return  $addresses;

}



if($_SERVER["REQUEST_METHOD"] == "POST"){


	$fn=fopen("links.txt","r");

 	$counter=file_get_contents('counter.txt');
 	$counter2=$counter+1;
 	file_put_contents('counter.txt',$counter2);
 	$x=$counter;
 	$naam;
 	for($y=0;$y<$x;$y++){
    	$naam=fgets($fn);
    }

	$address=trim($naam);

    echo $address;

	if($address==null){
		echo 'No address available';
		exit;
	}

	
	//sending user agent with the header so some wbsite which cause problem in http request

	$opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n")); 
	$context = stream_context_create($opts);
	$html = file_get_contents($address,false,$context);
		   


	echo "<br><br>";

	$title = extract_title($html);
	$description = extract_description($html);
	if($description ==""){
		$description = extract_paragraph($html);
	}

	$links = extract_links($html);

	$txt = file_get_contents('database.txt');

	echo $title;
	echo "<br>";
    echo $description;
	echo "<br><br>";
	echo $links;



	$title = substr($title,0,200);
	$description = substr($description,0,400);


	$txt .= "\n" . "<b>" . $title . "</b><br>" . "<a href='" . $address . "'><i>" . $address . "</i></a>" . "<br>" . $description;

    file_put_contents('database.txt',$txt);


    $list_of_links = file_get_contents('links.txt');  
    $list_of_links .= $links; 
    file_put_contents('links.txt',$list_of_links);

}

?>

</div>
</div>


</body>
</html>
