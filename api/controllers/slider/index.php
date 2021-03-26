<?php
    $repository = new Repository;
    $repository->getRepository('slider', DATA::getGet('slider'));

    $sliderRepository = new SliderRepository;
    if($sliderRepository->getSlider()){
        echo json_encode($sliderRepository->getSlider());
    }
?>