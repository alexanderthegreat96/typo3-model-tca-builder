<?php
session_start();
require "autoload.php";
$session = \dthtoolkit\Session::getSession();
if(!\dthtoolkit\Session::getParam('mysql_db'))
{
    Requests::redirect('Params.php');
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.88.1">
    <title>Typo3 Model Builder  - From Database</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="js/datatables.min.css"/>
    <link rel="icon" type="image/x-icon" href="favicon.ico">

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
    <a class="navbar-brand" href="#">Typo3 Model Builder</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">From SQL</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="FromDatabase.php">From Database</a>
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
                <div class="card-header">Table view</div>
                <div class="card-body">
                    <?php
                    if(isset($_GET['go']) && isset($_GET['tables']) && isset($_GET['ext_key']))
                    {
                        $tables = $_GET['tables'];
                        $ext_key = strtolower($_GET['ext_key']);

                        $doctrine = new \LexSystems\Core\System\Factories\DoctrineModelFactory();
                        $tca = new \LexSystems\Core\System\Factories\TcaBuilder($ext_key);
                        $xlf = new \LexSystems\Core\System\Factories\XlfBuilder();
                        $xlfFe = new \LexSystems\Core\System\Factories\XlfBuilderFrontend();
                        $repo = new \LexSystems\Core\System\Factories\RepositoryFactory();

                        $tcaFiles = $tca->buildPreferential($tables);
                        $xlfFiles = $xlf->buildPreferential($tables);
                        $xlfFeFiles = $xlfFe->buildPreferential($tables);



                        $doctrineRepositoryFiles = $repo->build($tables);
                        echo '<h5>Doctrine repository builder was started...</h5><br/>';
                        echo '<ul>';
                        foreach($doctrineRepositoryFiles as $doctrineRepositoryFile)
                        {
                            echo '<li>'.$doctrineRepositoryFile.'.php</li>';
                        }
                        echo '</ul>';
                        echo '<hr/>';

                        $doctrineModels = $doctrine->buildPreferential($tables);
                        echo '<h5>Doctrine model builder was started...</h5><br/>';
                        echo '<ul>';
                        foreach($doctrineModels as $doctrineModel)
                        {
                            echo '<li>'.$doctrineModel.'.php</li>';
                        }
                        echo '</ul>';
                        echo '<hr/>';



                        echo '<h5>TCA Builder was started...</h5><br/>';
                        echo '<ul>';
                        foreach($tcaFiles as $tcaFile)
                        {
                            echo '<li>'.$tcaFile.'.php</li>';
                        }
                        echo '</ul>';
                        echo '<hr/>';



                        echo '<h5>XLF Builder was started...</h5><br/>';
                        echo '<ul>';
                        foreach($xlfFiles as $xlfFile)
                        {
                            echo '<li>'.$xlfFile.'.xml</li>';
                        }

                        foreach($xlfFeFiles as $xlfFeFile)
                        {
                            echo '<li>'.$xlfFeFile.'_frontend.xml</li>';
                        }
                        echo '</ul>';
                        echo '<hr/>';

                        echo '<h5>Generating archive...</h5>';
                        $filename = $ext_key.'_t3mf_'.time().'.zip';
                        $archive = new GoodZipArchive( __DIR__.'/Generated', __DIR__.'/temp/'.$filename);

                        echo '<a href="temp/'.$filename.'">Download Repositories + Models + TCA + XLF</a>';
                        echo '<hr/>';
                        echo '<h5>Deleting Generated Data</h5>';

                        echo '<ul>';
                        foreach ($doctrineRepositoryFiles as $doctrineRepositoryFile)
                        {
                            if(file_exists(__DIR__.'/Generated/Repositories/'.$doctrineRepositoryFile.'.php'))
                            {

                                if(unlink(__DIR__.'/Generated/Repositories/'.$doctrineRepositoryFile.'.php'))
                                {
                                    echo '<li>Generated/Models/'. $doctrineRepositoryFile.'.php</li>';
                                }
                                else
                                {
                                    echo '<li>UNABLE TO DELETE:: Generated/Repositories/'. $doctrineRepositoryFile.'.php</li>';
                                }
                            }
                            else
                            {
                                echo '<li>UNABLE TO DELETE:: Generated/Repositories/'. $doctrineRepositoryFile.'.php :: FILE NOT FOUND</li>';
                            }
                        }
                        echo '</ul><br/>';

                        echo '<ul>';
                        foreach ($doctrineModels as $doctrineModel)
                        {
                            if(file_exists(__DIR__.'/Generated/Models/'.$doctrineModel.'.php'))
                            {

                                if(unlink(__DIR__.'/Generated/Models/'.$doctrineModel.'.php'))
                                {
                                    echo '<li>Generated/Models/'. $doctrineModel.'.php</li>';
                                }
                                else
                                {
                                    echo '<li>UNABLE TO DELETE:: Generated/Models/'. $doctrineModel.'.php</li>';
                                }
                            }
                            else
                            {
                                echo '<li>UNABLE TO DELETE:: Generated/Models/'. $doctrineModel.'.php :: FILE NOT FOUND</li>';
                            }
                        }
                        echo '</ul><br/>';
                        echo '<ul>';
                        foreach ($tcaFiles as $tcaFile)
                        {
                            if(file_exists(__DIR__.'/Generated/TCA/'.$tcaFile.'.php'))
                            {

                                if(unlink(__DIR__.'/Generated/TCA/'.$tcaFile.'.php'))
                                {
                                    echo '<li>Generated/TCA/'. $tcaFile.'.php</li>';
                                }
                                else
                                {
                                    echo '<li>UNABLE TO DELETE:: Generated/TCA/'. $tcaFile.'.php</li>';
                                }
                            }
                            else
                            {
                                echo '<li>UNABLE TO DELETE:: Generated/TCA/'. $tcaFile.'.php :: FILE NOT FOUND</li>';
                            }
                        }
                        echo '</ul><br/>';


                        echo '<ul>';
                        foreach ($xlfFiles as $xlfFile)
                        {
                            if(file_exists(__DIR__.'/Generated/Xlf/'.$xlfFile.'.xml'))
                            {

                                if(unlink(__DIR__.'/Generated/Xlf/'.$xlfFile.'.xml'))
                                {
                                    echo '<li>Generated/Xlf/'. $xlfFile.'.xml</li>';
                                }
                                else
                                {
                                    echo '<li>UNABLE TO DELETE:: Generated/Xlf/'. $xlfFile.'.xml</li>';
                                }
                            }
                            else
                            {
                                echo '<li>UNABLE TO DELETE:: Generated/Xlf/'. $xlfFile.'.xml :: FILE NOT FOUND</li>';
                            }
                        }

                        foreach ($xlfFeFiles as $xlfFeFile)
                        {
                            if(file_exists(__DIR__.'/Generated/Xlf/'.$xlfFeFile.'_frontend.xml'))
                            {

                                if(unlink(__DIR__.'/Generated/Xlf/'.$xlfFeFile.'_frontend.xml'))
                                {
                                    echo '<li>Generated/Xlf/'. $xlfFeFile.'_frontend.xml</li>';
                                }
                                else
                                {
                                    echo '<li>UNABLE TO DELETE:: Generated/Xlf/'. $xlfFeFile.'_frontend.xml</li>';
                                }
                            }
                            else
                            {
                                echo '<li>UNABLE TO DELETE:: Generated/Xlf/'. $xlfFeFile.'_frontend.xml :: FILE NOT FOUND</li>';
                            }
                        }

                        echo '</ul><br/>';



                        echo '</hr>';
                    }
                    else
                    {

                        $tables = new \LexSystems\Core\System\Factories\AbstractModelFactory();
                        $tables = $tables->getTableNames();
                        if($tables)
                        {
                            ?>

                            <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="GET">
                                     Listing tables from : <b><?php echo \dthtoolkit\Session::getParam('mysql_db');?></b><br/>
                                    <button type="submit" name="go" class="btn btn-success">Go for it</button>
                                    <hr/>
                                    <div class="form-group">
                                        <label><b>Extension Key</b> (ext-key found in composer.json) <small>It will be used to generate the language entries</small></label>
                                        <input type="text" class="form-control form-control-sm" minlength="3" name="ext_key" required="" placeholder="ex: my_extension"/>
                                    </div>
                                    <hr/>
                                    <table class="data-view table-bordered table-hover table-sm table" id="table-cols">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tablename</th>
                                        </tr>
                                        </thead>
                                        <?php
                                        foreach($tables as $table)
                                        {
                                            ?>
                                            <tr>
                                                <td><input type="checkbox" name="tables[]" value="<?php echo $table;?>"/></td>
                                                <td><label><?php echo $table;?></label></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>

                            </form>
                            <?php

                        }
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

