<?php
if (!isset($_POST)) {
    header('Location: index.php');
}

require_once('../DBManager.php');
$args = array();
$action = ADD_NEW_CLASSIFICATION;
$fullNumber = $_POST['clnum'];
$fullName = utf8_decode(trim($_POST['clname']));

if (isset($_POST['id'])) {
    $args['cid'] = $_POST['id'];
    $action = UPDATE_CLASSIFICATION;
}

$classes = explode('.', $fullNumber);

$args['cmain'] = $classes[0];
$args['cname'] = $fullName;
if (isset($classes[1]))
    $args['csub'] = $classes[1];
else
    $args['csub'] = "DEFAULT";

$result = DBManager::dbexecute($action, $args);
echo json_encode($result);
?>
