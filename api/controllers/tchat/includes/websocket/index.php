<?php
    function isEnabled($func) {
        return is_callable($func) && false === stripos(ini_get('disable_functions'), $func);
    }

    if (isEnabled('shell_exec')) {
        shell_exec('php '.dirname(__FILE__).'\serveur.php > tchat.log ');
    }
?>