<?php
session_start();
session_unset();
session_destroy();

$f3 = require("../../lib/base.php");

$f3->reroute('http://localhost/acervo/');
?>