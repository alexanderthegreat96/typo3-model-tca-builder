<?php
namespace LexSystems\Core\System\Factories;

use dthtoolkit\Debugger;
use dthtoolkit\SqlParser;

class AbstractModelFactorySql
{
    /**
     * @var
     */
    protected $tableDefinitions;

    /**
     * @var string
     */

    protected $extKey;

    /**
     * @var string
     */

    protected $vendor;

    /**
     * @param string $sql
     * @param string $extKey
     */

    public function __construct(string $sql = '', string $extKey = '', string $vendor = '')
    {
        $this->tableDefinitions = SqlParser::getTableDefinition($sql);
        $this->extKey = $extKey ? $extKey : 'my_extension';
        $this->vendor = $vendor ? $vendor : 'MyVendor';
    }

    /**
     * @param string $extkey
     * @return array|string|string[]
     */
    public function convertExtKeyToPluginName(string $extkey = '')
    {
        return str_replace('_','',$extkey);
    }
    /**
     * @param array $tables
     * @return array
     */
    public function getTableNames(array &$tables = [])
    {
        if(is_array($this->tableDefinitions))
        {
            foreach ($this->tableDefinitions as $definition)
            {
                $tables[] = $definition['name'];
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
     * @return string|null
     */
    public function detectExtensionName(string $tablename = '')
    {
        if(strpos($tablename,'tx_'))
        {
            if(explode('_',$tablename))
            {
                $nameparts = explode('_',$tablename);
                $name = $nameparts[1];
                return $name;
            }
            else
            {
                return null;
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * @param string $tablename
     * @param bool $justNames
     * @param array $columns
     * @return array
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
        if(is_array($this->tableDefinitions))
        {
            if(isset($this->tableDefinitions[$tablename]) && isset($this->tableDefinitions[$tablename]['fields']))
            {
                foreach($this->tableDefinitions[$tablename]['fields'] as $field)
                {
                    if(!in_array($field['name'],$ignoreNames))
                    {
                        if($justNames)
                        {
                            $columns[] = $field['name'];
                        }
                        else
                        {
                            $columns[] =  ['name' => $field['name'],'type' => strtolower($field['type'])];
                        }
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
     * @return string
     */
    public function generateNamespace()
    {
        $parts =
            [
                ucfirst($this->vendor),
                $this->convertTableName($this->extKey),
                'Domain'
            ];

        return implode("\\",$parts);
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