<?php
	$repository = new Repository;
	$repository->getRepository('account', DATA::getGet('account'));
	include('includes/'.DATA::getGet('account').'/indexController.php');	
?>