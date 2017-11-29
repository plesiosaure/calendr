<?php

class calendrierMap extends calendrier{

	protected   $param      = array();
	protected   $url        = '';
	protected   $notEscaped = array('center');
	protected   $required   = array('center', 'size', 'zoom', 'sensor');

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function __construct(){
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function url($url=NULL){
		if($url == NULL) return $this->url;
		$this->url = $url;
		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function param($key=NULL, $value=NULL){

		if($key == NULL && $value == NULL){
			return $this->param;
		}else
		if(is_string($key) && $key != NULL && $value == NULL){
			return $this->param[$key];
		}else
		if(is_string($key) && $key != NULL && $value != NULL){
			$key = array($key => $value);
			unset($value);
		}

		if(is_array($key) && $value == NULL){
			foreach($key as $k => $v){
				$this->param[$k] = $v;
			}
		}

		ksort($this->param);

		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function image(){

		$param = $this->param();

		foreach($this->required as $key){
			if(!array_key_exists($key, $param)){
				throw new Exception('Parameter "'.$key.'" is required, but MISSING');
			}else
			if($param[$key] == ''){
				throw new Exception('Parameter "'.$key.'" is required, but NULL');
			}
		}

		$url = 'http://maps.googleapis.com/maps/api/staticmap?';
		foreach($param as $k => $v){
			$url .= '&'.$k.'=';
			$url .= in_array($k, $this->notEscaped) ? $v : urlencode($v);
		}

		$this->url($url);

		return $this;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function hash(){
		return crc32($this->url());
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	private function file(){
		$hash   = $this->hash();
		$folder = MEDIA.'/upload/map/'.implode('/', str_split($hash, 2));
		$file   = $folder.'/'.$hash.'.png';
		return $file;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function save(){

		$url    = $this->url();
		$file   = $this->file();
		$folder = dirname($file);

		umask(0);

		if(file_exists($file)){
			unlink($file);
		}else
		if(!is_dir($folder)){
			mkdir($folder, 0755, true);
		}

		$handle = curl_init();

		curl_setopt_array($handle, array(
			CURLOPT_URL				=> $url,
			CURLOPT_HEADER 			=> true,
			CURLOPT_VERBOSE 		=> true,
			CURLOPT_RETURNTRANSFER 	=> true,
			CURLOPT_FOLLOWLOCATION 	=> true,
			CURLOPT_USERAGENT      	=> "Mozilla/4.0 (compatible;)"
		));

		$result = curl_exec($handle);

		if($result !== false){
			$stats			= curl_getinfo($handle);
			$contentType	= curl_getinfo($handle, CURLINFO_CONTENT_TYPE);
			$size			= curl_getinfo($handle, CURLINFO_HEADER_SIZE);
			$headers		= mb_substr($result, 0, $size);
			$body			= mb_substr($result,	$size);

			curl_close($handle);

		#	$this->pre("stats", $stats, "type", $contentType, "size", $size, "headers", $headers);

			if($stats['http_code'] == 200){
				file_put_contents($file, $body);
				chmod($file, 0755);

				return true;
			}
		}

		curl_close($handle);
		return false;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function cache(){
		$file = $this->file();
		if(!file_exists($file)) $this->save();
		return $file;
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function coordinates($gps){
		return number_format($gps[0], 14, '.', '').','.number_format($gps[1], 14, '.', '');
	}

//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
//-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -
	public  function html(){

		$url = $this->cache();
		$src = str_replace(KROOT, '', $url);
		list($w, $h) = explode('x', $this->param('size'));

		return 'src="'.$src.'" height="'.$h.'" width="'.$w.'"';

	}
}