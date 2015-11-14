<?php
	namespace filetype;
	class Utils{
		public static function apply_filters(){
			$args = func_get_args();
			$args[0] = "ImageMime" . $args[0];
			
		}
	}
?>