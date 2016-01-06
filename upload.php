<?php

$dir = date("Y-m-d");
@mkdir($dir, 0777);

if (! is_uploaded_file($_FILES['f']['tmp_name'])) { die("Upload fail: Missing file " . $_FILES["f"]["name"]); }

$name = pathinfo($_FILES['f']['name'], PATHINFO_FILENAME);
$extension = strtolower(pathinfo($_FILES['f']['name'], PATHINFO_EXTENSION));
$increment = ''; //start with no suffix

while(file_exists("$dir/" . $name . '-' . $increment . '.' . "jpg")) { $increment++; }

$incname = "$dir/" . $name . '-' . $increment . '.' . $extension;

if (fnmatch("jp*", $extension)) {
	move_uploaded_file($_FILES["f"]['tmp_name'], $incname);
	exec("jhead -autorot $incname", $output, $return);
	if ($return) { unlink($incname); die("Not a JPEG"); }
	$fdir = getcwd() . '/' . $dir;
	exec("docker run --rm -v $fdir:/data:ro openalpr -j -c sg " . basename($incname), $output, $return);
	$alpr = json_decode($output[0], true);
	$alpr["img"] = $incname;

	$co = $alpr["results"][0]["coordinates"];

	if (! empty($co)) {

		$min_x = min( array_column( $co, 'x' ) );
		$min_y = min( array_column( $co, 'y' ) );
		$max_x = max( array_column( $co, 'x' ) );
		$max_y = max( array_column( $co, 'y' ) );

		// Create a blank image and add some text
		$im = imagecreatefromjpeg($incname );

		$to_crop_array = array('x' => $min_x , 'y' => $min_y, 'width' => $max_x-$min_x, 'height'=> $max_y-$min_y);
		$thumb_im = imagecrop($im, $to_crop_array);
		$imgthumb = "$dir/thumb-" . basename($incname);
		imagejpeg($thumb_im, $imgthumb, 100);
		$alpr["imgthumb"] = $imgthumb;
	}

	$json = json_encode($alpr, JSON_PRETTY_PRINT);
} else {
	die("unknown extension: ". $extension);
}

@rmdir($dir); // remove directory if empty

$url = "http://" . $_SERVER["HTTP_HOST"] . '/' . basename($dir) . '/' . basename($incname);

if (isset ($_POST["after"])) {
	header("Location: http://" . $_SERVER["HTTP_HOST"] . "/?json=" . urlencode($json));
} else {
	header('Content-Type: application/json');
	echo $json;
}

?>
