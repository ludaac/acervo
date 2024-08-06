<?php
if (!isset($_POST)) {
	header('Location: /biblos/index.php');
}
require_once('../DBManager.php');
$res = DBManager::dbexecute(QUERY_BOOKS, array('keys' => $_POST['keys']), true);
echo json_encode($res);
?>