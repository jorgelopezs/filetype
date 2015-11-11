<?php
	
	require_once(__DIR__ . "/../vendor/autoload.php");


	use filetype\ImageMIME;
  	


	ImageMIME::testImage(__DIR__ . '/test.jpg');

//echo MIME_Type::autoDetect();

?>