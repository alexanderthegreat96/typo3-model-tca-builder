<?php
namespace LexSystems\Core\System\Factories;

class XlfBuilder extends AbstractModelFactory
{
    public function writeFile(string $tablename = '', array $columns = [], array $columnNames = [])
    {
        $syntax =
            '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<xliff version="1.0">
    <file source-language="en" datatype="plaintext" original="messages" date="2022-03-07UTC07:41:070">
        <header>
            <authorName>MyAuthor</authorName>
            <authorEmail>myEmail@someEmail.com</authorEmail>
        </header>
        <body>
            ';
        foreach ($columns as $column)
        {
            $syntax .= '
            <trans-unit id="'.$tablename.'.'.$column['name'].'">
                <source>'.$this->convertColName($column['name']).'</source>
            </trans-unit>
            ';
        }
        $syntax .= "
        </body>
    </file>
</xliff>
        ";


        if(!file_exists(__DIR__ . '/../../Generated/Xlf/' .$tablename.'.php'))
        {
            $try = file_put_contents(__DIR__ . '/../../Generated/Xlf/' .$tablename.'.php',$syntax);
            if($try)
            {
                return $tablename . " has been created in XLF \n";
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