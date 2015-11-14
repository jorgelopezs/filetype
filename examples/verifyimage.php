<?php
	
	require_once(__DIR__ . "/../vendor/autoload.php");


	use filetype\FileMIME;
  	


	FileMIME::getMIMEType(__DIR__ . '/test.jpg');

//echo MIME_Type::autoDetect();

?>