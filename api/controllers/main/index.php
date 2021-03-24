<?php
    $repository = new Repository;
    $repository->getRepository('main', DATA::getGet('main'));
    include('includes/indexController.php');
?>