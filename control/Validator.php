<?php
require_once('DBManager.php');
//if(!isset($_POST)) {
//	header('Location: *.php'); //TODO: Change location.
//}

$action = $_POST['cmd'];
$location = $_POST['loc'];
$select = false;
if (isset($_POST['select'])) {
    $select = true;
    unset($_POST['select']);
}
unset($_POST['cmd']);
unset($_POST['loc']);
$result = DBManager::dbexecute($action, $_POST, $select);   

switch ($action) {
    case USER_LOGIN: logIn();
        break;
    case ADD_NEW_CLASSIFICATION:
    case ADD_NEW_BOOK:
    case UPDATE_CLASSIFICATION:
    case ADD_NEW_USER:
    case UPDATE_USER:
    case UPDATE_BOOK:
    case UPDATE_CLASSIFICATION:
    case ADD_BORROW:
    case ADD_USER:
    case 202: case 203:
        dbInfo();
        break;
}

function logIn() {
    global $result;

    $start = 'false';
    if (isset($result[0])) {
        session_start();
        $_SESSION['active'] = true;
        $_SESSION['uid'] = $result[0]['iduser'];
        $_SESSION['unm'] = $result[0]['usfname'];
        $start = 'true';
    }
    header('Location: ../index.php?start=' . $start);
}

function cleanParams() {
    foreach ($_POST as $elems) {
        $elems = trim($elems);
    }
}

function dbInfo() {
    global $result, $location;
    $msg = 'false';
    if ($result == STATEMENT_SUCCESS) {
        $msg = 'true';
    }
    if ($result == -1) {
        $msg = 'false';
    }
    header('Location: ../index.php?page=' . $location . '&msg=' . $msg . '&res=' . $result);
}

// TODO: Break classification to main and sub.
?>
