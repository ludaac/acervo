<?php
if (!isset($_POST)) {
    header('Location: index.php');
}

require_once('../DBManager.php');
$args = array('idb' => $_POST['idb']);
unset($_POST['idb']);
$copy = DBManager::dbexecute(GET_COPY_FROM_BOOK_ID, $args, true);
if(!is_numeric($copy[0]['cpid'])) $copy = null;
$result = DBManager::dbexecute(ADD_BORROW,
								array(
									'cpid' => $copy[0]['cpid'],
									'memcd' => $_POST['memcd'],
									'memnm' => $_POST['memnm'],
									'findt' => $_POST['findt'],
									'uid' => $_POST['uid']
									));
echo json_encode($result);
?>