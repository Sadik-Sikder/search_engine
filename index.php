<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="index_style.css">
<link rel="icon" type="icon" href="icon.png">

<title>Skate search engine</title>
</head>
<body>

<div id="container">
<div id="content">
<br><br><br><br><br><br><br><br>


<h1>Skate</h1>

<div class="form">
   <form autocomplete="off" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">


     <input type="text" id="myInput" name="name" autofocus placeholder="search"><br>
     <input type="submit" value="search" >
 
   </div>
   </form> 
</div>

<?php
  function test_input($data) {
       $data = trim($data);
       $data = stripslashes($data);
       $data = htmlspecialchars($data);
       return $data;
  }

  function highlight($line,$search_keyword){

       $number_of_words = str_word_count($search_keyword);
       $words = str_word_count($search_keyword,1);

       if($number_of_words==1){
       $line = str_replace(" " . $words[0] . " ", " <mark>" . $words[0] . "</mark> ",$line);
       }
  return $line;
  }


  if($_SERVER["REQUEST_METHOD"] == "POST"){

       $search_keyword = test_input($_POST["name"]);


       echo "<br><br>";

       $txt = file_get_contents("database.txt");

       $keyword_in_start_title = "/" . "<b>" . $search_keyword . "" . "/i";

       $keyword = "/" . $search_keyword . "/i";
       $keyword_with_space = "/" . " " . $search_keyword . " " . "/i";

      foreach(preg_split("/((\r?\n)|(\r\n?))/",$txt) as $line){
         if(preg_match($keyword_in_start_title,$line)){
           if(!($search_keyword=="")){
             echo "<div class='item'>";
             $line = highlight($line,$search_keyword);
             echo $line;
             echo "</div>";
            }
            
          }
      }

      foreach(preg_split("/((\r?\n)|(\r\n?))/",$txt) as $line){
         if(preg_match($keyword_with_space,$line)&&!(preg_match($keyword_in_start_title,$line))){
           if(!($search_keyword=="")){
             echo "<div class='item'>";
             $line = highlight($line,$search_keyword);
             echo $line;
             echo "</div>";
            }
          }
      }

      foreach(preg_split("/((\r?\n)|(\r\n?))/",$txt) as $line){
         if(preg_match($keyword,$line)&&!(preg_match($keyword_with_space,$line))&&!(preg_match($keyword_in_start_title,$line))){
           if(!($search_keyword=="")){ 
             echo "<div class='item'>";
             $line = highlight($line,$search_keyword);
             echo $line;
             echo "</div>";
            }
          }
      }


  }
?>

</div>
</div>



</body>
</html>
