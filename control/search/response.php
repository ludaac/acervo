<?php
if (!isset($_POST)) {
	header('Location: /biblos/index.php');
}
require_once('../DBManager.php');
$res = DBManager::dbexecute(QUERY_BOOKS, array('keys' => $_POST['keys']), true);

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_SERVER['PATH_INFO'];

// Forcefully overriding response (for testing purposes)
$res = array();

for ($i = 0; $i < 10; $i++) {
	$index = "".($i + 1)."";
	$item = array(
		'idbook' => $index,
		'clmain' => $index,
		'clsub' => $index,
		'code' => $index,
		'isbn' => $index,
		'title' => "Libro ".$index,
		'author' => "Author ".$index,
		'editorial' => "Editorial ".$index,
		'publ_place' => "Lugar ".$index,
		'publ_year' => "Año ".$index,
		'edition' => "1",
		'av' => "1",
		'tt' => "1"
	);
	$res[] = $item;
}

echo json_encode($res);
?>