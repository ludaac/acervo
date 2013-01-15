<?php
if (!isset($_POST)) {
	header('Location: /biblos/index.php');
}
require_once('../DBManager.php');
$res = DBManager::dbexecute(DELETE_FROM_BOOK, array('rid' => $_POST['sid']), false);
echo json_encode($res);
?>
