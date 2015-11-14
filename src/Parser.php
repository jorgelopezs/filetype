<?php
	namespace filetype;

	class Parser{
		protected static $imgSize = 0;
		protected static $magicNumbers = array();

		public static function testImage($filePath){
			// self::$imgSize = filesize('test.jpg');
			//echo self::$imgSize;

			// open the file in reading and byte mode 'rb'
			$imgHandle = fopen('test.jpg', 'rb');
			if($imgHandle == FALSE){
				//TODO: throw an exception
				return false;
			}

		  	//detect the file mime type
			//echo self::verifyJPEG($imgHandle);

			//echo self::verifyJPEG($imgHandle)? "true":"false";
			self::verifyPNG($imgHandle);
			//close the open image file
			fclose($imgHandle);

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