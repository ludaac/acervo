<?php session_start(); header('Content-Type: text/html; charset=UTF-8'); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Sistema de Control de Biblioteca</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		
        <link rel="stylesheet" href="css/redmond/jquery-ui-1.8.20.custom.css" />
		<link rel="stylesheet" href="css/960.css" />
		<link rel="stylesheet" href="css/ui.jqgrid.css" />
		<link rel="stylesheet" href="css/general.css" />
        
		<style>
			.ui-widget { font-size: 0.8em; }
		</style>
        <script src="js/jquery-1.7.2.min.js"></script>
        <script src="js/jquery-ui-1.8.20.custom.min.js"></script>
        <script src="js/jquery.ui.datepicker-es.js"></script>
		<script src="js/jquery.jqGrid.min.js"></script>
		<script src="js/grid.locale-es.js"></script>
		<script src="js/jquery.validate.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/underscore@1.13.7/underscore-umd-min.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/backbone@1.6.0/backbone-min.min.js"></script>
		<script src="js/behaviour.js" type="text/javascript"></script>
    </head>
    <body>
		
		<div class="container_12">
			<!-- Header -->
			<div class="grid_12">
				<img src="img/logo_head.png" width="940" />
			</div>
			<!-- Barra de herramientas -->
			<div class="grid_12">
				<div class="toolbar ui-state-default">
				<?php if(!isset($_SESSION['uid'])) { ?>
					<!-- Iniciar sesión -->
					<button id="login">Entrar</button>
				<?php } else { ?>
					<button id="search">Búsqueda</button>
					<button id="class">Clasificación</button>
					<button id="books">Libros</button>
					<button id="borrow">Préstamos</button>
					<button id="users">Usuarios</button>
					<a id="exit" href="control/login/exit.php">Salir</a>
				<?php } ?>
				</div>
			</div>
			
			<!-- Contenido -->
			<div class="grid_12">
				<div class="content" style="min-height: 400px;">
				<?php
				if(isset($_GET['login'])) {
				?>
				<div class="grid_10 push_1 alpha omega">
					<p class="ui-state-error">
						El usuario y/o la contraseña no son correctos
					</p>
				</div>
				<?php
				}
				include("view/search.php");
				
				if(isset($_SESSION['uid'])) {
					include("view/class.php");
					include("view/book.php");
					include("view/borrow.php");
					include("view/user.php");
				}
				?>
				</div>
			</div>
			<div class="grid_12">
				<img src="img/logo_foot.jpg" />
			</div>
		</div>
		
		<!-- Login -->
		<div id="loginDialog" title="Entrar">
			<p id="logMsg"></p>
			<form id="logFrm" method="post" action="control/login/clean.php">
				<label>Usuario:</label>
				<input id="user" type="text" name="uname" />
				<label>Contraseña:</label>
				<input id="pswd" type="password" name="upswd" />
			</form>
		</div>			
        <script>
			$("input:submit").button();
        </script>
    </body>
</html>