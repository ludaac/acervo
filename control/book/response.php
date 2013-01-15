<?php
if (!isset($_POST)) {
	header('Location: /biblos/index.php');
}
ini_set('display_errors', 1);
require_once('../DBManager.php');
if(isset($_GET['page']))
	$page = $_GET['page'];
else
	$page = 1;
$params = array('page' => $page);

if(isset($_GET['isSearch']) && $_GET['isSearch'] == "true" && $_GET['searchString'] != '') {
	$rs = DBManager::dbexecute(FILTER_BOOKS, 
		array('keys' => $_GET['searchString'], 'page' => $page), true);
	$count = DBManager::dbexecute(FILTER_BOOKS_COUNT,
		array('keys' => $_GET['searchString']), true);
}
else {
	$rs = DBManager::dbexecute(GET_SELECT_PAGE, array('tblName' => 'books', 'page' => $page), true);
	$count = DBManager::dbexecute(GET_COUNT_FROM_TABLE, array('tblName' => 'books'), true);
}	

$count = $count[0]['count'] ;
$total = ceil($count / 10);
$rows = array();
foreach($rs as $row) {
	$rows[] = array('id' => $row['idbook'],
					'cell' => array($row['code'],
									$row['isbn'],
									$row['title'],
									$row['author'],
									$row['editorial'],
									$row['edition'],
									$row['name']));
}
$res = array('total' => "".$total."", 'page' => $page, 'records' => "".$count."",
			 'rows' => $rows);
echo json_encode($res);
?>
