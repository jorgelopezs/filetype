<?php
	namespace filetype;
	use AhoCorasick\MultiStringMatcher;

	class ImageMIME{
		
		//holds the magic numbers
    	protected static $magicNumbers = array(
			'image/jpeg' => "\xFF\xD8",
			'image/png'  => "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A",
			'image/gif' => "\x47\x49\x46\x38\x39\x61",
			'image/bmp' => "\x42\x4D"
    		);

		// public static function testImage($filePath){
			
		// 	echo self::getMIMEType("test.jpg");

		// }

		public static function getMIMEType($fileHandle){// parameter can be a resource or path

			
			//check if a file resource or path was passed
			if(!is_string($fileHandle) && !is_resource($fileHandle))return false;
			else if(is_string($fileHandle)){

				$inFileHandle = fopen($fileHandle, "rb");
			}

			//resource type is not stream
			if(is_resource($fileHandle) && get_resource_type($fileHandle) != "stream")return false;
			
			//determine the largest header in bytes
			//get the length of each value in $magicNumbers
			$magicNumbersLengths = array_map("strlen", self::$magicNumbers);
			//get the longest magic number
			$longestMagicNumber = max($magicNumbersLengths);

			//select which handle to read from
			//either the passed $fileHandle or the newly created $inFileHandle
			$handleToUse = (is_resource($fileHandle)) ? $fileHandle : $inFileHandle; 

			//read the largest amount of bytes from magic numbers
			$head = fread($handleToUse, $longestMagicNumber);
			

			//$st = microtime(true);
			// retrive only the values in magicNumbers array
    		$needles = array_values(self::$magicNumbers);

    		//add the needles to MultiStringMatcher
			$keywords = new MultiStringMatcher($needles);

			//perform the byte search
			$res = $keywords->searchIn( $head);

			//close the resource if it was opened locally
			if(is_resource($inFileHandle)){

				//close the resource
				fclose($inFileHandle);
	
			}
			
			//check if the result is empty - if so return
			if(empty($res))return false;
			
			//check the 1st value in the result array 
			//0 index has the position that the needle was found - in our case it has to be 0 - the beginning
			//1 index contains the needle that was matched
			if($res[0][0] == 0){
				//search the found needle in $magicNumbers array to retrieve the MIME type
				$foundNeedle = $res[0][1];

				//return the mime type
				return  (string) array_search($foundNeedle, self::$magicNumbers);
			}else return false;
			
		}
	    public static function isImage($imgPath){
	        if(!$imgPath || empty($imgPath))return false;

	        // open the file in reading and byte mode 'rb'
			$fileHandle = fopen($imgPath, 'rb');
			if($fileHandle == FALSE){
				//TODO: throw an exception
				return false;
			}
			
			//get the mime type of the file
			$mimeType = self::getMIMEType($fileHandle);

			//close the open image file
			fclose($fileHandle);

			//check if the received mimetype exists in our images $magicNumbers
			if(array_key_exists($mimeType, self::$magicNumbers))return true;
			else return false;
			
		}
		public function verifyJPEG($imgHandle){
			if(ftell($imgHandle) != 0){
	    		
	    		rewind($imgHandle);

	    	}

			//read the first 2 bytes
			//The 2 bytes should be hexadecimal 0xffd8
			$jpegHeaderBytes = fread($imgHandle, 2);
			

			//test if the header equals the magic number
			//the header in string raw bytes
			//It has to be in double quotes
			if($jpegHeaderBytes == "\xff\xd8"){
				

				//the header matched
				//now match the end magic number
				//the last 2 bytes of a jpeg file is 0xffd9
				
				//go to the last 2 bytes of the file
				fseek($imgHandle, -2, SEEK_END);

				//read the last 2 bytes
				$jpegEndBytes = fread($imgHandle, 2);

				echo $jpegEndBytes;
				//compare the last two bytes
				if($jpegEndBytes == "\xff\xd9"){

					return true;

				}else return false;
			}else return false;
		}


	    public function verifyJPEGUnpack($imgHandle){

	        //open the file for reading as binary
			//$fileHandle = fopen($imgPath, 'rb');

			//read all the bytes of the file - only for jpeg
			
			$fileHeaderBytes = fread($imgHandle, self::$imgSize);

			//ONE WAY OF TESTING FOR JPEG - have to do performance test
			/*
				if (substr($bytes6,0,3)=="\xff\xd8\xff") return 'image/jpeg';
		        if ($bytes6=="\x89PNG\x0d\x0a") return 'image/png';
		        if ($bytes6=="GIF87a" || $bytes6=="GIF89a") return 'image/gif';
			 */
			/*if(substr($fileHeader, 0, 3) == "\xff\xd8\xff"){
				echo "IT is JPEG";
			}else{
				echo "not equal";
			}*/

			//the first 4 bytes contain the header value id
			//a jpeg file starts with hex 0xffd8
			// get the hex value of the beginning 4 bytes
			$magicHeaderHex = self::unpackBytes($fileHeaderBytes, 4);

			//verify if it starts with 0xffd8
			if($magicHeaderHex['magic'] == 'ffd8'){
				//continue testing for the end
				// A JPEG file must end with 0xffd9
				
				// go to the last 4 bytes
				fseek($imgHandle, -2, SEEK_END);

				$fileEndBytes = fread($imgHandle, 2);
				//echo $fileEnd;

				$jpegEndHex = self::unpackBytes($fileEndBytes, 4);
				echo $jpegEndHex['magic'];

				// verify that hex is 0xffd9
				if($jpegEndHex['magic'] == "ffd9"){
					return true;
				}else return false;
			}else{
				//the header didn't match return immediately
				return false;
			}
		
	    }
	    function unpackBytes($fileHandle, $bytesToRead){
	    	// format the first parameter to the unpack method
	    	// read the bytes in $bytesToRead to Hex value
	    	$unpackFormat = sprintf("H%smagic/", $bytesToRead);

	    	//TEST THE BYTES USING php's unpack method
			//unpack the file's binary data into hexadecimal
			//the first 6 bytes are stored in the 
			$unpacked = unpack($unpackFormat,  $fileHandle);

			return $unpacked;
	    }

	    public function verifyPNG($imgHandle){

	    	// rewind the file pointer to 0
	    	if(ftell($imgHandle) != 0){
	    		
	    		rewind($imgHandle);

	    	}

	    	//PNG header in hex is 89 50 4E 47 0D 0A 1A 0A
	    	
	    	//read the first 8 bytes
	    	$pngHeaderBytes = fread($imgHandle, 8);

	    	//echo $pngHeaderBytes;

	    	//compare the headers
	    	if($pngHeaderBytes == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"){
	    		return true;
	    	}else return false;
	    }


		}

?>