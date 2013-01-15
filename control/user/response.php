<?php
if (!isset($_POST)) {
	header('Location: /biblos/index.php');
}
require_once('../DBManager.php');
if(isset($_GET['page']))
	$page = $_GET['page'];
else
	$page = 1;
$params = array('page' => $page);

$rs = DBManager::dbexecute(GET_SELECT_PAGE, array('tblName' => 'users', 'page' => $page), true);
$count = DBManager::dbexecute(GET_COUNT_FROM_TABLE, array('tblName' => 'users'), true);

$count = $count[0]['count'] ;
$total = ceil($count / 10);
$rows = array();
foreach($rs as $row) {
	if($row['usstat'] == 1) $row['usstat'] = 'Activo';
	else $row['usstat'] = 'Inactivo';
	$rows[] = array('id' => $row['iduser'],
					'cell' => array($row['usfname'],
									$row['usuname'],
									$row['usstat']));
}
$res = array('total' => "".$total."", 'page' => $page, 'records' => "".$count."",
			 'rows' => $rows);
echo json_encode($res);
?>
