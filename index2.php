<!DOCTYPE html>
<html>
    
    <head>
        <title>Sistema de Control de Biblioteca</title>
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="css/redmond/jquery-ui-1.8.20.custom.css" />
        <link rel="stylesheet" href="css/ui.jqgrid.css" />
        <link rel="stylesheet" href="css/general.css" />
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
        <style>
            .ui-widget {
                font-size: 0.8em;
            }
        </style>
        <script src="js/jquery-1.7.2.min.js"></script>
        <script src="js/jquery-ui-1.8.20.custom.min.js"></script>
        <script src="js/jquery.ui.datepicker-es.js"></script>
        <script src="js/jquery.jqGrid.min.js"></script>
        <script src="js/grid.locale-es.js"></script>
        <script src="js/jquery.validate.min.js"></script>
        <script src="js/behaviour.js"></script>
        
        <script src="./script/libs/require-min.js" data-main="./script/acervo.js"></script>
        <script src="./script/libs/underscore-min.js"></script>
        <script src="./script/libs/backbone-min.js"></script>
        
        <!-- Latest compiled and minified JavaScript -->
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    </head>
    
    <body>
        <nav class="navbar navbar-default" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/acervo">Acervo</a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="main-navbar">
                    <form id="srch" class="navbar-form navbar-right" role="search">
                        <div class="form-group">
                            <input id="keys" name="keys" type="text" class="form-control" placeholder="Search" />
                        </div>
                        <button type="submit" class="btn btn-default">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                    </form>
                    <?php if(!isset($_SESSION[ 'uid'])) { ?>
                    <button id="login" type="button" class="btn btn-default navbar-btn">Entrar</button>
                    <?php } else { ?>
                    <button id="class" type="button" class="btn btn-default navbar-btn">Clasificación</button>
                    <button id="books" type="button" class="btn btn-default navbar-btn">Libros</button>
                    <button id="borrow" type="button" class="btn btn-default navbar-btn">Préstamos</button>
                    <button id="users" type="button" class="btn btn-default navbar-btn">Usuarios</button>
                    <a id="exit" type="button" class="btn btn-default navbar-btn" href="control/login/exit.php">Salir</a>
                    <?php } ?>
                </div>
            </div>
        </nav>
        <div class="container">
            <!-- Contenido -->
            <div class="row">
                <div class="col-md-12" style="background-color: white; height: 350px;">
                    <?php if(isset($_GET[ 'login'])) { ?>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <p class="ui-state-error">El usuario y/o la contraseña no son correctos</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php } ?>
                            <?php include("view/search.php"); if(isset($_SESSION[ 'uid'])) { include( "view/class.php"); include( "view/book.php"); include( "view/borrow.php"); include( "view/user.php"); } ?>
                        </div>
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
        </div>
        <script>
            $("input:submit").button();
        </script>
    </body>
</html>
