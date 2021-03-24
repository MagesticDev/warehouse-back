<?php
	$repository = new Repository;
    $repository->getRepository('tchat', DATA::getGet('tchat'));
	include('includes/'.DATA::getGet('tchat').'/indexController.php');
	include('includes/websocket/index.php');
?>