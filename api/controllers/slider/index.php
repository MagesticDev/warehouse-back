<?php
    $sliderOne = array(
        'url' => 'https://previews.123rf.com/images/erika8213/erika82131508/erika8213150800066/44229917-vintage-background-avec-de-vieux-livres-retour-au-concept-d-%C3%A9cole.jpg',
        'title' => 'First slide label',
        'description' => 'Nulla vitae elit libero, a pharetra augue mollis interdum.'
    );

    $sliderTwo = array(
        'url' => 'https://cdn.pixabay.com/photo/2014/09/05/18/32/old-books-436498_1280.jpg',
        'title' => 'Second slide label',
        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    );

    $sliderTree = array(
        'url' => 'https://cdn.pixabay.com/photo/2016/03/27/19/32/blur-1283865_1280.jpg',
        'title' => 'Third slide label',
        'description' => 'Praesent commodo cursus magna, vel scelerisque nisl consectetur.'
    );

    $sliderFoor = array(
        'url' => 'https://cdn.pixabay.com/photo/2016/03/27/19/32/blur-1283865_1280.jpg',
        'title' => 'Foor slide label',
        'description' => 'Praesent commodo cursus magna, vel scelerisque nisl consectetur.'
    );

    $sliderFive = array(
        'url' => 'https://cdn.pixabay.com/photo/2014/09/05/18/32/old-books-436498_1280.jpg',
        'title' => 'Five slide label',
        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    );

    $arr = array();
    
    array_push($arr, $sliderOne, $sliderTwo, $sliderTree, $sliderFoor, $sliderFive);

    echo json_encode($arr);
?>