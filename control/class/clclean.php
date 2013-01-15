<?php
if (!isset($_POST)) {
    header('Location: index.php');
}

require_once('../DBManager.php');
$action = CHANGE_BOOK_CLASSIFICATION;

$result = DBManager::dbexecute($action, $_POST);
echo json_encode($result);
?>