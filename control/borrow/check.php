<?php
require_once('../DBManager.php');
$result = DBManager::dbexecute(CHECK_BORROWS, false, false);

echo json_encode($result);
?>