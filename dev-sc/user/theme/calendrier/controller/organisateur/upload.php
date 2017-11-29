<?php

$date = date("Ymd_His");
umask(0);

error_reporting(E_ALL | E_STRICT);
require(MEDIA.'/ui/vendor/jquery-file-upload/server/php/UploadHandler.php');
$upload_handler = new UploadHandler(array(
	'upload_dir' => MEDIA.'/upload/supercalendrier/temp/'.$date.'/',
	'upload_url' => '/media/upload/supercalendrier/temp/'.$date.'/',
	'access_control_allow_methods' => array(
		'POST'
	),
	'image_versions' => array(
		'thumbnail' => array(
			// Uncomment the following to use a defined directory for the thumbnails
			// instead of a subdirectory based on the version identifier.
			// Make sure that this directory doesn't allow execution of files if you
			// don't pose any restrictions on the type of uploaded files, e.g. by
			// copying the .htaccess file from the files directory for Apache:

			'upload_dir' => MEDIA.'/upload/supercalendrier/temp/'.$date.'/thumbnail/',
			'upload_url' => '/media/upload/supercalendrier/temp/'.$date.'/thumbnail/',

			// Uncomment the following to force the max
			// dimensions and e.g. create square thumbnails:
			//'crop' => true,
			'max_width' => 80,
			'max_height' => 80
		)
	)
));
