<?php
/**
 * created by: Yuval Leshem
 * yuval.leshem@gmail.com
 * http://simplegalleryma.sourceforge.net/
**/


// resize an image and write to jpeg file. parameters - 
//	src - source image ref
//	name - name of source image
//	height - new height to resize (If set to zero, function will just copy the image to $destpath)
//	dest - destination file name/ path	
//	
//	NOTE: works for jpg, png and gif only

function resize_image($src, $srcname, $newheight, $destpath)
{
	$extension =  getExtension($srcname);

	if (($extension != "jpg") && ($extension != "jpeg")
		&& ($extension != "png") && ($extension != "gif")) {	
		die ("unknown image extension");
	}
	if (($extension == "jpg") || ($extension == "jpeg")) {
		$data = imagecreatefromjpeg($src);
	} else if (extension == "png") {
		$data = imagecreatefrompng($src);		
	} else {// gif
		$data = imagecreatefromgif($src);
	}
	
	list($width,$height)=getimagesize($src);
	if ($newheight < $height && $newheight > 0) 
	{
		$newwidth = ($width / $height) * $newheight;		
		// create new image
		$tmp = imagecreatetruecolor($newwidth, $newheight);
		// resize
		imagecopyresampled($tmp,$data,0,0,0,0,$newwidth,$newheight, $width,$height);
		// write to file
		imagejpeg($tmp, $destpath);
		// cleanup
		imagedestroy($tmp);
	} else { // just copy the file into destination
		copy($src, $destpath);
	}
}

// get file extension. returns lowercase
function getExtension($str) 
{
	$i = strrpos($str,".");
	if (!$i) { 
		return ""; 
	}
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return strtolower($ext);
}

// delete $dir directory recursively
function rrmdir($dir) 
{ 
	if (is_dir($dir)) 
	{ 
		$objects = scandir($dir); 
		foreach ($objects as $object) 
		{ 
			if ($object != "." && $object != "..") { 
				if (filetype($dir."/".$object) == "dir") {
					rrmdir($dir."/".$object); 
				}
				else {
					unlink($dir."/".$object); 
				}
			} 
		} 
		reset($objects); 
		rmdir($dir); 
	} 
}

// prevent response caching (IE8 aggressive cache policy fix)
function cacheFix()
{
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');
	header('Expires: ' . gmdate(DATE_RFC1123, time()-1));
}

?>