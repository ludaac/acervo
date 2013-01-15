<?php
if (!isset($_POST)) {
    header('Location: index.php');
}

require_once('../DBManager.php');

$action = ADD_NEW_USER;
foreach ($_POST as $key => $value) {
    $_POST[$key] = utf8_decode(trim($value));
}

if(isset($_POST['id'])) {
	if(isset($_POST['npass']))
		$action = CHANGE_USER_PASSWORD;
	else
		$action = UPDATE_USER;
}

$result = DBManager::dbexecute($action, $_POST);
echo json_encode($result);
?>
