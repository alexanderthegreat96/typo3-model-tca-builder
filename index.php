<?php
session_start();
require "autoload.php";
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.88.1">
    <title>Typo3 Model + TCA Builder  - Parse SQL</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="js/datatables.min.css"/>

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
                <a class="nav-link active" href="index.php">From SQL</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="FromDatabase.php">From Database</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="Params.php">Change MySQL Config</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <div class="row mt-2">
        <div class="col-lg">
            <div class="card">
                <div class="card-header">SQL Parser</div>
                <div class="card-body">
                    <?php
                        if(Requests::hasArgument('parse','POST') &&
                            Requests::hasArgument('sql_code','POST') &&
                            Requests::getArgument('sql_code','POST') &&
                            Requests::hasArgument('ext_key','POST') &&
                            Requests::getArgument('ext_key','POST')
                        )
                        {
                            $sql_code = Requests::getArgument('sql_code','POST');
                            $ext_key = strtolower(Requests::getArgument('ext_key','POST'));

                            echo '<h6>Extension key:';
                            echo '<b>'.$ext_key.'</b>';

                            echo '<h6>Formatted SQL:</h6>';
                            echo \dthtoolkit\SqlFormatter::format($sql_code);
                            $factory = new \LexSystems\Core\System\Factories\AbstractModelFactorySql($sql_code,$ext_key);
                            $tables = $factory->getTableNames();

                            if($tables)
                            {
                                echo '<form action="GenerateFromSql.php" method="POST">';
                                echo '<input type="hidden" name="sql_code" value="'.$sql_code.'">';
                                echo '<input type="hidden" name="ext_key" value="'.$ext_key.'">';
                                echo '<button type="submit" name="go" class="btn btn-success btn-sm">Go for it</button>';
                                echo '<hr/>';
                                echo '<ul>';
                                foreach ($tables as $table)
                                {
                                    echo '<li><input type="checkbox" name="tables[]" value="'.$table.'"/><br/> '.$table.'<br/>';
                                    $cols = $factory->showTableColums($table,false);
                                    if($cols)
                                    {

                                        echo '<table class="table table-bordered table-sm">';
                                        echo '<thead><tr><th>Name</th><th>Type</th></tr></thead>';
                                        echo '<tbody>';
                                        foreach($cols as $col)
                                        {
                                            echo '<tr>';
                                            echo '<td>';
                                            echo $col['name'];
                                            echo '</td>';
                                            echo '<td>';
                                            echo $col['type'];
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                        echo '<tbody>';
                                        echo '</table>';
                                    }
                                    echo '</li>';
                                }

                                echo '</ul>';

                                echo '</form>';
                            }
                            else
                            {
                                echo '<h5>Unable to find any tables. Your structure must be bad!</h5>';
                            }
                        }
                        else
                        {
                            ?>
                            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
                                <table class="table table-bordered table-hover">
                                    <tr>
                                        <td class="w-25">Extension Key (ext-key found in composer.json) <small>It will be used to generate the language entries</small></td>
                                        <td><input type="text" class="form-control form-control-sm" minlength="3" name="ext_key" required="" placeholder="ex: my_extension"/></td>
                                    </tr>
                                    <tr>
                                        <td class="w-25">Paste <b>ext_tables.sql</b> structure  <br/><small class="text-danger">No comments allowed.</small> :</td>
                                        <td>
                                            <textarea name="sql_code" placeholder="Paste your code here..." class="form-control" style="min-height: 400px; resize: none;" required=""></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <button type="submit" name="parse" class="btn btn-primary btn-sm">Parse SQL</button>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                            <?php
                        }
                    ?>


                </div>
            </div>

        </div>
    </div>
</div>


<script src="js/jquery-3.4.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/data-tables/datatables.js"></script>
<script>
    $(document).ready(function() {
        var table = $('table.data-view').DataTable( {
            responsive: true,
            "autoWidth": false,
            lengthChange: false,
            pageLength: 50,

            dom: 'Bflrtip',
            buttons: true,
            buttons: [
                { extend: 'copy', text: 'Copiaza' },
                { extend: 'csv', text: 'Exporta CSV', title: 'data_export_<?php echo time();?>',footer: true,
                    exportOptions: {
                        columns: ':visible'
                    }},
            ]


        } );


    } );
</script>

</body>
</html>

