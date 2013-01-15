<?php
if (!isset($_POST)) {
    header('Location: index.php');
}

require_once('../DBManager.php');
$result = DBManager::dbexecute(ADD_COPY_TO_BOOK, $_POST);
echo json_encode($result);
?>