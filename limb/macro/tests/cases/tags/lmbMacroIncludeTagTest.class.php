<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/macro/src/tags/include.tag.php');
lmb_require('limb/fs/src/lmbFs.class.php');

class lmbMacroTagIncludeTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/tpl');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/tpl/compiled');
  }

  function testSimpleInclude()
  {
    $bar = '<body><%include file="foo.html"/%></body>';
    $foo = '<p>Hello, <%include file="name.html"/%></p>';
    $name = "Bob";

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');
    $name_tpl = $this->_createTemplate($name, 'name.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Hello, Bob</p></body>');
  }

  function testIncludePassVariables()
  {
    $bar = '<body><?php $var2=2;?><%include file="foo.html" var1="1" var2="$var2"/%></body>';
    $foo = '<p>Numbers: <?=$var1?> <?=$var2?></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Numbers: 1 2</p></body>');
  }

  protected function _createMacro($file)
  {
    $base_dir = LIMB_VAR_DIR . '/tpl';
    $cache_dir = LIMB_VAR_DIR . '/tpl/compiled';
    $macro = new lmbMacroTemplate($file,
                                  $cache_dir,
                                  new lmbMacroTemplateLocator($base_dir, $cache_dir));
    return $macro;
  }

  protected function _createTemplate($code, $name)
  {
    $file = LIMB_VAR_DIR . '/tpl/' . $name;
    file_put_contents($file, $code);
    return $file;
  }
}

