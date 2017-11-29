<?php

class validator extends coreApp {

/* + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - 
+ - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - + - */
function upload($file){
	if(!file_exists($file)) return false;

	list($type, $mime) = explode('/', $this->mediaMimeType($file));

	# IMAGE
	#	
	if($type == 'image'){
		$tmp = @getimagesize($file);
		return (intval($tmp[0]) > 0);
	}else
	
	# VIDEO
	#
	if($type == 'video'){
		$movie  = new ffmpeg_movie($file);
		return (intval($movie->getFrameHeight()) > 0);
	}
	
	# ???? => NO
	else{
		return false;
	}

}

function isImage($obj) {
	
	if (!is_array($obj)) return false;
	
	if ($obj['height'] > 0 && $obj['width'] > 0 ) return true;
	return false;	
}


} ?>