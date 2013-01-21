<?php
session_start();

$f3 = require("./lib/base.php");

$f3->route('GET /',
    function () {
        echo View::instance()->render("./index2.php");
    }
);

$f3->run();
?>
