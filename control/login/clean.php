<?php
if (!isset($_POST)) {
    header('Location: ../../index.php');
}

require_once('../DBManager.php');
$result = DBManager::dbexecute(USER_LOGIN, $_POST, true);

if(count($result) > 0) {
    session_start();
    $_SESSION['active'] = true;
    $_SESSION['uid'] = $result[0]['iduser'];
    $_SESSION['unm'] = $result[0]['usfname'];
    header('Location: ../../index.php');
} else {
	header('Location: ../../index.php?login=false');
}
?>
