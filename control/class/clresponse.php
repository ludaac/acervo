<?php
if (!isset($_POST)) {
	header('Location: /biblos/index.php');
}
require_once('../DBManager.php');
$rows = DBManager::dbexecute(GET_ALL_CLASSIFICATIONS, false, true);

#$rows = $rs[0];
echo json_encode($rows);
?>