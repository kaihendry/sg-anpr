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
	} else {
		echo "<h1>No plate found.</h1>";
	}

	if (! empty($json["imgthumb"])) {
	echo "<img src=" . $json["imgthumb"] . ">";
	}
	echo "<!-- " . json_encode($json, JSON_PRETTY_PRINT) . "-->";
}
?>

<form name="myForm" action="/upload.php" method="post" enctype="multipart/form-data">
<div id="yourBtn" onclick="getFile()">Click to upload <?php if (isset($_GET["json"])) { echo "another"; } else { echo "a"; } ?> photo of a 🇲🇨 number plate</div>
<!-- i used the onchange event to fire the form submission-->
<div style='height: 0px;width: 0px; overflow:hidden;'><input id="upfile" type="file"  name=f onchange="sub(this)"/></div>
<input type=hidden name=after value=true>
</form>

<p><a href=https://www.youtube.com/watch?v=fpj6vptUbCA>Video demonstration</a></p>

<h3>curl API</h3>
<pre>
wget http://s.natalian.org/2016-01-06/SCR5199Y.jpg
curl -F "f=@SCR5199Y.jpg" http://anpr.dabase.com/upload.php
</pre>

<h3>Known issues</h3>
<ul>
<li><a href=https://groups.google.com/forum/#!msg/openalpr/oWU2CvTR7yU/TEsz9LUgBQAJ>Cannot grok 2 line plates</a> - more training images needed</li>
<li>Does not seem to like night photos - more training images needed</li>
</ul>

<p><a href=https://en.wikipedia.org/wiki/Vehicle_registration_plates_of_Singapore>Wikipedia article on Singaporean number plates</a></p>
<p>Email <a href=mailto:hendry+anpr@iki.fi>Kai Hendry</a> for enquiries</p>

</body>
</html>
