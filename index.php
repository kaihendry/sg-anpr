<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Singapore automatic number plate recognition</title>
<meta name="viewport" content="minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no">
<style>
body { font-family: "Gill Sans", sans-serif; }
#yourBtn{
   width: 80%
   text-align: center;
   background-color: #DDD;
   padding: 2em;
   border-radius:6px;
  }

</style>
<script>
 function getFile(){
   document.getElementById("upfile").click();
 }
 function sub(obj){
    var file = obj.value;
    var fileName = file.split("\\");
    document.getElementById("yourBtn").innerHTML = fileName[fileName.length-1];
    document.myForm.submit();
    event.preventDefault();
  }
</script>
</head>
<body>
<?php
if (isset($_GET["json"])) { 
	$json = json_decode(urldecode($_GET['json']), true);
	if (! empty($json["results"][0]["plate"])) {
	echo "<h1>" . $json["results"][0]["plate"] . "</h1>";
	}
	if (! empty($json["imgthumb"])) {
	echo "<img src=" . $json["imgthumb"] . ">";
	}
	echo "<!-- " . json_encode($json, JSON_PRETTY_PRINT) . "-->";
}
?>

<form name="myForm" action="/upload.php" method="post" enctype="multipart/form-data">
<div id="yourBtn" onclick="getFile()">Click to upload <?php if (isset($_GET["json"])) { echo "another"; } else { echo "a"; } ?> photo of a number plate</div>
<!-- i used the onchange event to fire the form submission-->
<div style='height: 0px;width: 0px; overflow:hidden;'><input id="upfile" type="file"  name=f onchange="sub(this)"/></div>
<input type=hidden name=after value=true>
</form>

<!--
curl -f -F "f=@/tmp/numberplate.jpg" http://<?php echo $_SERVER["HTTP_HOST"]; ?>/upload.php
-->

</body>
</html>
