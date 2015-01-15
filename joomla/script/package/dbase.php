<?php
//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

class One_Script_Package_Dbase extends One_Script_Package
{
	function launchImageCrop(){

		$declaration = 'launchImageCrop()';

		One_Vendor::getInstance()
			->loadStyle(JURI::base() . '/media/js/plugins/imgareaselect/css/imgareaselect-default.css', 'head', 200)
			->loadScript(JURI::base() . '/media/js/plugins/imgareaselect/js/jquery.imgareaselect.pack.js', 'head', 200)
			->loadScript(JURI::base() . '/media/js/custom.js', 'head', 210)
			->loadScriptDeclaration($declaration, 'onload', 220);

		$sourcepath = JRequest::getString('sourcepath', '', 'POST');

		if($sourcepath != ''){

			$cropx = JRequest::getInt('cropx', 0, 'POST');
			$cropy = JRequest::getInt('cropy', 0, 'POST');
			$cropwidth = JRequest::getInt('cropwidth', 0, 'POST');
			$cropheight = JRequest::getInt('cropheight', 0, 'POST');

			self::cropImage($sourcepath, $cropx, $cropy, $cropwidth, $cropheight);

		}

	}

	function cropImage($sourcepath = '', $cropx = 0, $cropy = 0, $cropwidth = 0, $cropheight = 0){

		$sourcefile = file($sourcepath);

		var_dump($sourcefile['mime']);

		/*$sourceimage = imagecreatefromstring(file_get_contents($sourcepath));
		$sourcewidth = imagesx($sourceimage);
		$sourceheight = imagesy($sourceimage);

		$cropimage = imagecreatetruecolor($cropwidth, $cropheight);

		imagecopyresampled($cropimage, $sourceimage, $cropx, $cropy, 0, 0, $cropwidth, $cropheight, $sourcewidth, $sourceheight);

		$extension = strtolower(substr(strrchr($sourcepath,"."), 1));

		switch ($extension){
			case 'jpg':
			case 'jpeg':
				imagejpeg($cropimage, $sourcepath);
				break;
			case 'png':
				imagepng($cropimage, $sourcepath);
				break;
			case 'gif':
				imagepng($cropimage, $sourcepath);
				break;
			case 'bmp':
				imagewbmp($cropimage, $sourcepath);
				break;
		}*/

	}
}