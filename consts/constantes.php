<?php
    //authentification API
    define('API', array(
        'SECRET_KEY' => '18BA608A63CE09EFA62492D98CCC00476BEA12282B6D0C882FF27A82DA43994B',
        'AUDIENCE_CLAIM' => 'Les bouquineurs',
        'ISSUER_CLAIM' => 'Les bouquineurs',
        'ISSUEDAT_CLAIM' => time(), // issued at
        'NOT_BEFORE_CLAIM' => time() + 10, //not before in seconds
        'EXPIRE_CLAIM' => time() + 60, // expire time in seconds,
        'PORT_FRONT' => ':4200',
        'URL_API' => 'http://magestic.test'
    ));
?>