<?php session_start(); header('Content-Type: text/html; charset=UTF-8'); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Sistema de Control de Biblioteca</title>
		<meta charset="utf-8">
		
        <link rel="stylesheet" href="css/redmond/jquery-ui-1.8.20.custom.css" />
		<link rel="stylesheet" href="css/960.css" />
        
        <link rel="stylesheet" href="./style/bootstrap.min.css" />
        
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
		<script src="js/behaviour.js" type="text/javascript"></script>
        
        <script src="./script/libs/require-min.js" data-main="./script/acervo.js"></script>
        
        <script src="./script/libs/underscore-min.js"></script>
        <script src="./script/libs/backbone-min.js"></script>
    </head>
    <body>
		<div class="container">
            <div class="row">
                <div class="span12">
                    <img src="img/logo_head.png" width="940" />
                </div>
            </div>
            <div class="row">
                <div class="span12">
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
			</div>
			
			<!-- Contenido -->
            <div class="row">
                <div class="span12" style="background-color: white; height: 350px;">
                    <?php if(isset($_GET['login'])) { ?>
                    <div class="row">
                        <div class="span10 offset1">
                            <p class="ui-state-error">
                                El usuario y/o la contraseña no son correctos
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span12">
                        <?php } ?>
                        <?php
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
            <div class="row">
                <div class="span12">
                    <img src="img/logo_foot.jpg" />
                </div>
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