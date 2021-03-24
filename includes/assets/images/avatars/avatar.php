<?php
	$jpg = '../../img/avatars/'.$_GET['getavatar'].'.jpg';
	$gif = '../../img/avatars/'.$_GET['getavatar'].'.gif';
	$jpeg = '../../img/avatars/'.$_GET['getavatar'].'.jpeg';
	$png = '../../img/avatars/'.$_GET['getavatar'].'.png';
	if(file_exists($jpg)) {
		$lien_avatar = $jpg;
	}
	elseif(file_exists($gif)) {
		$lien_avatar = $gif;
	}
	elseif(file_exists($jpeg)) {
		$lien_avatar = $jpeg;
	}
	elseif(file_exists($png)) {
		$lien_avatar = $png;
	} else {
		$lien_avatar = '../../img/avatars/no-avatar.png';
	}
	
	header('location: '.$lien_avatar);
?>