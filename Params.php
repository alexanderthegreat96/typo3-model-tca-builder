<?php
session_start();
require "autoload.php";
$session = \dthtoolkit\Session::getSession();
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.88.1">
    <title>Typo3 Model + TCA Builder  - Config</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">


    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>


    <!-- Custom styles for this template -->
    <link href="starter-template.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Typo3 Model + TCA Builder Beta</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">From SQL</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="FromDatabase.php">From Database</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="Params.php">Change MySQL Config</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <div class="row mt-2">
        <div class="col-lg">
            <div class="card">
                <div class="card-header">Set up your mysql connection</div>
                <div class="card-body">
                    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
                        <div class="form-group">
                            <label>Mysql Server</label>
                            <input type="text" name="mysql_host" class="form-control form-control-sm" placeholder="localhost" value="<?php echo \dthtoolkit\Session::getParam('mysql_host');?>" required=""/>
                        </div>
                        <div class="form-group">
                            <label>Mysql User</label>
                            <input type="text" name="mysql_user" class="form-control form-control-sm" placeholder="username" value="<?php echo \dthtoolkit\Session::getParam('mysql_user');?>" required=""/>
                        </div>
                        <div class="form-group">
                            <label>Mysql Pass</label>
                            <input type="text" name="mysql_pass" class="form-control form-control-sm" placeholder="password" value="<?php echo \dthtoolkit\Session::getParam('mysql_pass');?>" required=""/>
                        </div>
                        <div class="form-group">
                            <label>Mysql Database</label>
                            <input type="text" name="mysql_db" class="form-control form-control-sm" placeholder="database" value="<?php echo \dthtoolkit\Session::getParam('mysql_db');?>" required=""/>
                        </div>
                        <hr/>
                        <button type="submit" class="btn btn-primary" name="save_config">Save Config</button>
                        <a href="<?php $_SERVER['PHP_SELF'];?>?test" class="btn btn-info">Test Connection</a>
                    </form>
                </div>
            </div>
            <?php

                if(Requests::hasArgument('save_config','POST'))
                {
                    $params =
                        [
                                'mysql_host' => Requests::getArgument('mysql_host','POST'),
                                'mysql_user' => Requests::getArgument('mysql_user','POST'),
                                'mysql_pass' => Requests::getArgument('mysql_pass','POST'),
                                'mysql_db' => Requests::getArgument('mysql_db','POST')
                        ];
                    $set = \dthtoolkit\Session::sendTheseToSession($params);
                    if($set)
                    {
                        Requests::redirect($_SERVER['PHP_SELF']);
                    }
                }


            if(Requests::hasArgument('test','GET'))
            {
                $connect = mysqli_connect(\dthtoolkit\Session::getParam('mysql_host'),\dthtoolkit\Session::getParam('mysql_user'),\dthtoolkit\Session::getParam('mysql_pass'),\dthtoolkit\Session::getParam('mysql_db'));
                if($connect)
                {
                    ?>
                    <div class="alert alert-success">Connection succesful!</div>
                    <?php
                }
                else
                {
                    ?>
                    <div class="alert alert-danger">Unable to connect. Wrong credentials! <?php echo mysqli_error($connect);?></div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>





<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="/docs/4.6/assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="/docs/4.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>


</body>
</html>

