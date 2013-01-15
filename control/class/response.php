<?php
if (!isset($_POST)) {
	header('Location: /biblos/index.php');
}
require_once('../DBManager.php');
if(isset($_GET['page']))
	$page = $_GET['page'];
else
	$page = 1;
$params = array('tblName' => 'class', 'page' => $page);

$rs = DBManager::dbexecute(GET_SELECT_PAGE, $params, true);

$count = DBManager::dbexecute(GET_COUNT_FROM_TABLE, array('tblName' => 'class'), true);
$count = $count[0]['count'] ;
$total = ceil($count / 10);
$rows = array();
foreach($rs as $row) {
	$fullClass = "".$row['clmain'].".".$row['clsub'];
	$rows[] = array('id' => $row['idclass'], 
					'cell' => array($fullClass,	$row['name']));
}
$res = array('total' => "".$total."", 'page' => $page, 'records' => "".$count."",
			 'rows' => $rows);
echo json_encode($res);
?>
