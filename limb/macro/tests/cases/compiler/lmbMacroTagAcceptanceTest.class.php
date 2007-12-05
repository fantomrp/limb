<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class MacroTagFooTest extends lmbMacroTag
{
  function generate($code)
  {
    $code->writeHtml('foo!');
  }
}

class MacroTagBarTest extends lmbMacroTag
{
  function generate($code)
  {
    $code->writeHtml('bar');
  }
}

class MacroTagZooTest extends lmbMacroTag
{
  // note that we overrided _generateContent since generate() methods pregenerates dynamic attributes
  function _generateContent($code)
  {
    $code->writePHP('echo ' . $this->getEscaped('attr') . ';');
  }
}

$foo_info = new lmbMacroTagInfo('foo', 'MacroTagFooTest');
$foo_info->setFile(__FILE__);
$bar_info = new lmbMacroTagInfo('bar', 'MacroTagBarTest');
$bar_info->setFile(__FILE__);
$zoo_info = new lmbMacroTagInfo('zoo', 'MacroTagZooTest');
$zoo_info->setFile(__FILE__);

lmbMacroTagDictionary :: instance()->register($foo_info);
lmbMacroTagDictionary :: instance()->register($bar_info);
lmbMacroTagDictionary :: instance()->register($zoo_info);

class lmbMacroTagAcceptanceTest extends lmbBaseMacroTest
{
  function testTemplateRendering()
  {
    $code = '<h1>{{foo/}}{{bar/}}</h1>';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $out = $tpl->render();
    $this->assertEqual($out, '<h1>foo!bar</h1>');
  }

  function testCompositeTagAttributes()
  {
    $code = '<h1>{{zoo attr="Test_{$#var}_{$#foo}"/}}</h1>';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', 'Result');
    $tpl->set('foo', 'Attribute');
    $out = $tpl->render();
    $this->assertEqual($out, '<h1>Test_Result_Attribute</h1>');
  }
}
