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
$result = DBManager::dbexecute(CHECK_BORROWS, false, false);

$rs = DBManager::dbexecute(GET_SELECT_PAGE, array('tblName' => 'borrows', 'page' => $page), true);
$count = DBManager::dbexecute(GET_COUNT_FROM_TABLE, array('tblName' => 'borrows'), true);

$count = $count[0]['count'] ;
$total = ceil($count / 10);
$rows = array();
foreach($rs as $row) {
	if($row['status'] == 1) $row['status'] = 'A tiempo';
	else $row['status'] = "Retrasado";
	$rows[] = array('id' => $row['idborrow'],
					'cell' => array($row['mem_code'],
									$row['mem_name'],
									$row['init_date'],
									$row['final_date'],
									$row['title'],
									$row['copy_num'],
									$row['usfname'],
									$row['status']));
}
$res = array('total' => "".$total."", 'page' => $page, 'records' => "".$count."",
			 'rows' => $rows);
echo json_encode($res);
?>
