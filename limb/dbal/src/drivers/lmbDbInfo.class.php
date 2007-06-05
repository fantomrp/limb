<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/exception/lmbDbException.class.php');

abstract class lmbDbInfo
{
  protected $tables = array();
  protected $name;

  function __construct($name)
  {
    $this->name = $name;
  }

  function getName()
  {
    return $this->name;
  }

  function getTable($name)
  {
    if(!$this->hasTable($name))
    {
      throw new lmbDbException("Table '$name' does not exist");
    }
    return $this->tables[$name];
  }

  function hasTable($name)
  {
    $this->loadTables();
    return array_key_exists($name, $this->tables);
  }

  function getTableList()
  {
    $this->loadTables();
    return array_keys($this->tables);
  }

  abstract function loadTables();
}

?>
