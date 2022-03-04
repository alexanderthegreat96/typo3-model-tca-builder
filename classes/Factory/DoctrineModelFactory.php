<?php
namespace LexSystems\Core\System\Factories;
use LexSystems\Core\System\Helpers\Debugger;

class DoctrineModelFactory extends AbstractModelFactory
{
    /**
     * @param string $className
     * @param string $tablename
     * @param array $columns
     * @return string
     */
    private function writeClass(string $className = '',string $tablename = '', array $columns = [], array $columnNames = [])
    {
        $template =
            '<?php
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
 
namespace MyVendor\MyExtension\Domain\Model;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
class '.$className.' extends AbstractEntity
{
';
        foreach ($columns as $column)
        {
            switch ($column['type'])
            {
                case 'int':
                    $template .= '
    /**
     * @var int
    */
    protected $'.$this->convertColName($column['name']).';   
';
                    break;
                case 'decimal':
                    $template .= '
    /**
     * @var int
    */
    protected $'.$this->convertColName($column['name']).';  
';
                    break;
                default:
                    $template .= '
    /**
     * @var string
    */
    protected $'.$this->convertColName($column['name']).';   
';
            }

        }
        foreach ($columns as $column)
        {
            switch ($column['type'])
            {
                case 'int':
                    $columnVars[] = 'int $'.$this->convertColName($column['name']).' = 0';
                    break;
                case 'decimal':
                    $columnVars[] = 'int $'.$this->convertColName($column['name']).' = 0';
                    break;
                default:
                    $columnVars[] = 'string $'.$this->convertColName($column['name']).' = ""';
            }
        }
        $columnVars = implode(',',$columnVars);

        $template .= '
     /**
      ';
        foreach ($columns as $column)
        {
            switch ($column['type'])
            {
                case 'int':
                    $param = 'int $'.$this->convertColName($column['name']);
                    break;
                case 'decimal':
                    $param = 'int $'.$this->convertColName($column['name']);
                    break;
                default:
                    $param = 'string $'.$this->convertColName($column['name']);
            }
        $template .= ' * @param '.$param.'    
        ';
        }
      $template .= '*/';


$template .='
    public function __construct('.$columnVars.')
    {';
foreach($columnNames as $columnName)
{
    $template .='
        $this->set'.$this->convertTableName($columnName).'($'.$this->convertColName($columnName).');';
}
$template .= '
    }';
        foreach ($columns as $column)
        {
            switch ($column['type']) {
                case 'int':
                    $template .= '
    /**
    * @param int $' . $this->convertColName($column['name']) . ' 
    * @return void
    */
    public function set' . $this->convertTableName($column['name']) . '(int $' . $this->convertColName($column['name']) . ' = 0):void
    {
        $this->' . $this->convertColName($column['name']) . ' = $' . $this->convertColName($column['name']) . ';
    }
    
    /** 
    * @return int
    */
    public function get' . $this->convertTableName($column['name']) . '():int
    {
        return $this->' . $this->convertColName($column['name']) . ';
    }
';
                    break;

                case 'decimal':
                    $template .= '
    /**
    * @param int $' . $this->convertColName($column['name']) . ' 
    * @return void
    */
    public function set' . $this->convertTableName($column['name']) . '(int $' . $this->convertColName($column['name']) . ' = 0):void
    {
        $this->' . $this->convertColName($column['name']) . ' = $' . $this->convertColName($column['name']) . ';
    }
    
    /** 
    * @return int
    */
    public function get' . $this->convertTableName($column['name']) . '():int
    {
        return $this->' . $this->convertColName($column['name']) . ';
    }
';
                    break;

                default:
                    $template  .= ' 
    /**
    * @param string $'.$this->convertColName($column['name']).' 
    * @return void
    */
    public function set'.$this->convertTableName($column['name']).'(string $'.$this->convertColName($column['name']).' = ""):void
    {
        $this->'.$this->convertColName($column['name']).' = $'.$this->convertColName($column['name']).';
    }
    
    /** 
    * @return string
    */
    public function get'.$this->convertTableName($column['name']).'():string
    {
        return $this->'.$this->convertColName($column['name']).';
    }
';
            }

        }
$template.= '
}
?>
';
        if(!file_exists(__DIR__ . '/../../Generated/Models/' .$className.'.php'))
        {
            $try = file_put_contents(__DIR__ . '/../../Generated/Models/' .$className.'.php',$template);
            if($try)
            {
                return $className . " has been created in Models \n";
            }
            else
            {
                return $className . " could not be written, permission issues are most likely the cause!\n";
            }
        }
        else
        {
            return $className . " skipped, as it already exists.!\n";
        }

    }

    /**
     * Build models
     */
    public function build()
    {
        /**
         * Get table names first
         */
        $tableNames = $this->getTableNames();
        if($tableNames)
        {
            print("Doctrine Model Factory has been started...\r");
            $i = 1;
            foreach ($tableNames as $tableName)
            {
                $className = $this->convertTableName($tableName);
                $columns = $this->showTableColums($tableName,false);
                $columnNames = $this->showTableColums($tableName);
                print ($i.'.'.$this->writeClass($className,$tableName,$columns,$columnNames) ."\n");
                $i++;
            }
        }
        else
        {
            print("No tables found in the database\n");
        }
    }

    public function buildPreferential(array $tableNames = [])
    {
        if($tableNames)
        {
            $i = 1;
            foreach ($tableNames as $tableName)
            {

                $className = $this->convertTableName($tableName);
                $columns = $this->showTableColums($tableName,false);
                $columnNames = $this->showTableColums($tableName);
                $this->writeClass($className,$tableName,$columns,$columnNames);
                $models[] = $className;
            }

            return $models;

        }
        else
        {
            print("No tables provided\n");
        }
    }
}