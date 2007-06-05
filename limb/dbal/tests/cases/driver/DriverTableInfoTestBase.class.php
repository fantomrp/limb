<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once(dirname(__FILE__) . '/DriverMetaTestBase.class.php');

abstract class DriverTableInfoTestBase extends DriverMetaTestBase
{
  var $table;

  function setUp()
  {
    $dbinfo = $this->connection->getDatabaseInfo();
    $this->table = $dbinfo->getTable('founding_fathers');
  }

  function tearDown()
  {
    unset($this->table);
    parent::tearDown();
  }

  function testGetDatabase()
  {
    $db = $this->table->getDatabase();
    $this->assertIsA($db, 'lmbDbInfo');
  }

  function testGetName()
  {
    $this->assertEqual($this->table->getName(), 'founding_fathers');
  }

  function testHasColumn()
  {
    $this->assertTrue($this->table->hasColumn('id'));
    $this->assertTrue($this->table->hasColumn('first'));
    $this->assertTrue($this->table->hasColumn('last'));
  }

  function testGetColumn()
  {
    $column = $this->table->getColumn('last');
    $this->assertIsA($column, 'lmbDbColumnInfo');
  }

  function testGetColumnList()
  {
    $this->assertEqual($this->table->getColumnList(),
          array('id' => 'id', 'first' => 'first', 'last' => 'last'));
  }
}

?>
