**.htaccess**

RewriteEngine on
RewriteBase /
# Avatar SigCache
RewriteRule ^avatar/([0-9]*)\.jpg$ avatar_cache.php?character_id=$1

====================

**avatar_cache.php**

<?php
$cacheDir = __DIR__ . "/files/avatarcache/";

$cacheTimeInHours = 1;

$cacheTimeStamp = date('YmdH');

$cID = $_GET['character_id'];

$cachedAvatarFile = $cID . "_" . $cacheTimeStamp . ".jpg"; 

// Just display the image if its there
if(!file_exists($cacheDir . $cachedAvatarFile)){
	// Search for old image
	$oldCacheTimeStamp = date('YmdH',strtotime('-'.$cacheTimeInHours.' hours'));
	$oldCachedAvatarFile = $cID . "_" . $cacheTimeStamp . ".jpg"; 
	if(file_exists($cacheDir . $oldCachedAvatarFile) === true){
		unlink($cacheDir . $oldCachedAvatarFile);
	}
	// DOING PARSING STUFF
	// $parsedAvatarFile
	//
	copy($parsedAvatarFile,$cacheDir . $cachedAvatarFile);
}
$file = $cacheDir . $cachedAvatarFile;
$type = 'image/jpeg';
header('Content-Type:'.$type);
header('Content-Length: ' . filesize($file));
readfile($file);
