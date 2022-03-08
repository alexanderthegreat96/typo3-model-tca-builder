<?php
namespace LexSystems\Core\System\Factories;

use dthtoolkit\Session;
use LexSystems\Core\System\Helpers\Debugger;
use Requests;

class AbstractModelFactory
{
    /**
     * Abstract model factory contstuctor
     */
    public function __construct()
    {
        $this->mysql_host = Session::getParam('mysql_host');
        $this->mysql_user = Session::getParam('mysql_user');
        $this->mysql_pass = Session::getParam('mysql_pass');
        $this->mysql_db = Session::getParam('mysql_db');
        
        $this->connection  =  mysqli_connect($this->mysql_host,$this->mysql_user,$this->mysql_pass,$this->mysql_db);
        if(!$this->connection)
        {
            die(mysqli_error($this->connection));
        }
    }
    /**
     * @param array $tables
     * @return array
     */
    public function getTableNames(array &$tables = [])
    {
        $query = mysqli_query($this->connection,'show tables;');
        if($query)
        {
            while($a = mysqli_fetch_assoc($query))
            {
                $tables[] = $a['Tables_in_'.$this->mysql_db];
            }
        }

        return $tables;
    }

    /**
     * @param string $string
     * @return string
     */
    public function generateClassNameFromString(string $string = '')
    {
        if(explode('_',$string))
        {
            $parts = explode('_',$string);
            $parts = array_filter($parts);
            return ucfirst(end($parts));
        }
        else
        {
            return $string;
        }
    }
    /**
     * @param string $tablename
     * @return array|false|string[]|void|null
     */
    public function showTableColums(string $tablename = '',bool $justNames = true, array &$columns = [])
    {
        $ignoreNames =
            [
                'uid',
                'pid',
                'id',
                'tstamp',
                'crdate',
                'cruser_id',
                'sorting',
                'deleted',
                'hidden',
                'starttime',
                'endtime',
                'fe_group',
                'sys_language_uid',
                'l10n_parent',
                'l10n_diffsource',
                'l10n_source',
                'l10n_state',
                't3ver_oid',
                't3ver_id',
                't3_origuid',
                't3ver_wsid',
                't3ver_label',
                't3ver_state',
                't3ver_stage',
                't3ver_count',
                't3ver_tstamp',
                't3ver_move_id'
            ];
        $query = mysqli_query($this->connection, 'SHOW COLUMNS FROM '.$tablename.';');
        if($query)
        {
            while($a = mysqli_fetch_assoc($query))
            {
                if(!in_array($a['Field'],$ignoreNames))
                {
                    if($justNames)
                    {
                        $columns[] = $a['Field'];
                    }
                    else
                    {
                        $columns[] = ['name' => $a['Field'],'type' => preg_replace("/\([^)]+\)/","",$a['Type'])];
                    }
                }
            }
        }

        return $columns;
    }

    /**
     * @param string $tablename
     * @return string
     */
    public function convertTableName(string $tablename = '')
    {
        $names = explode("_",$tablename);
        if($names)
        {
            foreach($names as $name)
            {
                $renamed[] = ucfirst($name);
            }

            return implode("",$renamed);
        }
        else
        {
            return $tablename;
        }
    }

    /**
     * @param string $colname
     * @return string
     */
    public function convertColName(string $colname = '')
    {
        $names = explode("_",$colname);
        if($names)
        {
            foreach($names as $name)
            {
                $renamed[] = ucfirst($name);
            }

            return lcfirst(implode("",$renamed));
        }
        else
        {
            return $colname;
        }
    }


}