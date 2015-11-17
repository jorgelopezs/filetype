<?php
	
	require_once(__DIR__ . "/../vendor/autoload.php");


	use filetype\FileMIME;
  	


	echo FileMIME::isImage(__DIR__ . '/test.jpg');

//echo MIME_Type::autoDetect();

?>