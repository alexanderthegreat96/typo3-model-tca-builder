<?php
namespace LexSystems\Core\System\Factories;

class TcaBuilder extends AbstractModelFactory
{
    public function writeFile(string $tablename = '', array $columns = [], array $columnNames = [])
    {
        $LLL = 'LLL:EXT:'.$this->extKey.'/Resources/Private/Language/locallang_db.xlf';

        $syntax =
            "<?php
return [
        'ctrl' => [
        'title' => '".$LLL.":".$tablename."',
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'searchFields' => 'uid',
        'iconfile' => 'EXT:".$this->extKey."/Resources/Public/Icons/Extension.svg',
    ],
    'types' => [
        '0' =>
            [
                'showitem' => '".implode(',',$columnNames)."'
            ],
    ],
    ";



        $syntax .= "
        'columns' => 
        [
            ";
        foreach ($columns as $column)
        {
            switch ($column['type'])
            {
                case 'int':
                    $syntax .= "
                    '".$column['name']."' => [
                    'label' => '".$LLL.":".$tablename.".".$column['name']."',
                    'config' => [
                        'type' => 'text',
                        'size' => '50',
                        'eval' => 'required',
                    ],
                ],
                   ";
                    break;
                default:
                   $syntax .= "
                    '".$column['name']."' => [
                    'label' => '".$LLL.":".$tablename.".".$column['name']."',
                    'config' => [
                        'type' => 'text',
                        'size' => '50',
                        'eval' => 'required',
                    ],
                ],
                   ";

            }

        }
        $syntax .= "
        ],
        ];
        ";

        if(!file_exists(__DIR__ . '/../../Generated/TCA/' .$tablename.'.php'))
        {
            $try = file_put_contents(__DIR__ . '/../../Generated/TCA/' .$tablename.'.php',$syntax);
            if($try)
            {
                return $tablename . " has been created in TCA \n";
            }
            else
            {
                return $tablename . " could not be written, permission issues are most likely the cause!\n";
            }
        }
        else
        {
            return $tablename . " skipped, as it already exists.!\n";
        }
    }

    /**
     * @param array $tableNames
     */
    public function buildPreferential(array $tableNames = [])
    {
        if($tableNames)
        {
            foreach ($tableNames as $tableName)
            {
                $className = $this->convertTableName($tableName);
                $columns = $this->showTableColums($tableName,false);
                $columnNames = $this->showTableColums($tableName);
                $this->writeFile($tableName,$columns,$columnNames);
            }
            return $tableNames;
        }
        else
        {
            print("No tables provided\n");
        }
    }
}