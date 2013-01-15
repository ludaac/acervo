<?php
if (!isset($_POST)) {
    header('Location: index.php');
}

require_once('../DBManager.php');

$args = array('pyr' => null, 'ppl' => null);
$action = ADD_NEW_BOOK;
foreach ($_POST as $key => $value) {
    $args[$key] = utf8_decode(trim($value));
}

//if ((!is_numeric($args['ed']) && $args['ed'] != '') ||
//        (!is_numeric($args['ncp']) && $args['ncp'] != '')) {
//   header('Location: ../../index.php?page=alta_libros&stat=' . ILLEGAL_PARAMETER);
//}

if (isset($_POST['id'])) {
    $args['idb'] = $_POST['id'];
	unset($args['id']);
    $action = UPDATE_BOOK;
}
if($args['isbn'] == '')
	$args['isbn'] = null;
if ($args['ed'] == '')
    $args['ed'] = 1;
if (!isset($_POST['id']) && $args['ncp'] == '')
    $args['ncp'] == 1;
$result = DBManager::dbexecute($action, $args);
echo json_encode($result);
?>
