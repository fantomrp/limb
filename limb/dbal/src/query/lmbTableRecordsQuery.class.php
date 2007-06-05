<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/query/lmbSelectQuery.class.php');

class lmbTableRecordsQuery extends lmbSelectQuery
{
  function __construct($table, $conn)
  {
    $table = $conn->quoteIdentifier($table);
    $sql = "SELECT {$table}.* %fields% FROM {$table} %tables% ".
                                  "%left_join% %where% %group% %having% %order%";

    parent :: __construct($sql, $conn);
  }
}
?>
