<?php
namespace dthtoolkit;
use iamcal\SQLParser as SqlParserAlias;
class SqlParser
{
    /**
     * @param string $sql
     * @return array|void
     */
    public static function getTableDefinition(string $sql = '')
    {
        if($sql)
        {
            $sql  = rawurldecode($sql);
            $parser = new SqlParserAlias();
            return $parser->parse($sql);
        }
    }
}