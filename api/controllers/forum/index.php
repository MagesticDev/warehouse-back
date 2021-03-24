<?php
    $repository = new Repository;
    $repository->getRepository('forum', DATA::getGet('forum'));
    include('includes/'.DATA::getGet('forum').'/indexController.php');
?>