<?php
	
	require_once(__DIR__ . "/../vendor/autoload.php");


	use filetype\FileMIME;
  	


	echo FileMIME::getMIMEType(__DIR__ . '/test.jpg');

//echo MIME_Type::autoDetect();

?>