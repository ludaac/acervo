<?php
if (!isset($_POST)) {
    header('Location: index.php');
}

require_once('../DBManager.php');
$result = DBManager::dbexecute(TERMINATE_BORROW, $_POST);
echo json_encode($result);
?>