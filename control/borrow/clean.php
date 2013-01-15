<?php
if (!isset($_POST)) {
    header('Location: index.php');
}

require_once('../DBManager.php');

$args = array();
foreach ($_POST as $key => $value) {
    $args[$key] = trim($value);
}

$result = DBManager::dbexecute(ADD_BORROW, $args);
header('Location: ../../index.php?page=check_borrow&stat='.$result);
?>
