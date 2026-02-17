<?php
if (!isset($_GET['t']))
	return false;

$temp = filter_var($_GET['t'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);

if (!preg_match('/^-?(\d){0,3}$/', $temp))
	return false;

$outputPath = 'cache/favicon'.$temp.'.png';

if (file_exists($outputPath)) {
	return_icon($outputPath);
} else {
	generate_icon($outputPath, $temp);
}

function return_icon($outputPath) {

	// open the file in a binary mode
	$fp = fopen($outputPath, 'rb');

	// send the right headers
	header("Content-Type: image/png");
	header("Content-Length: " . filesize($outputPath));

	// dump the picture and stop the script
	fpassthru($fp);
	exit;
}

function generate_icon($outputPath, $temp) {
	try {
		$imagePath = 'src/favicon.png'; // Replace with your image path
		$fontPath = 'src/UbuntuCondensed-Regular.ttf'; // Replace with your font path
	    
	    // Load the image
	    $imagick = new Imagick(realpath($imagePath));

	    // Create a new ImagickDraw object
	    $draw = new ImagickDraw();

	    // Set text properties
	    $draw->setFillColor('white'); // White text color
	    $draw->setFont($fontPath);    // Specify font file
	    $draw->setFontSize(70);       // Set font size
	    $draw->setGravity(Imagick::GRAVITY_CENTER); // Center the text

	    // Add text to the image
	    // X and Y coordinates are relative to gravity when set
	    $imagick->annotateImage($draw, 0, 0, 0, $temp);

	    // Save the modified image
	    $imagick->writeImage($outputPath);

		return_icon($outputPath);

	} catch (ImagickException $e) {
	    echo "Error: " . $e->getMessage();
	}
}
?>