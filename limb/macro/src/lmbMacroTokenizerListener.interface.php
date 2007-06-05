<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

interface lmbMacroTokenizerListener
{
  function startElement($tag_name, $attrs);
  function endElement($tag_name);
  function emptyElement($tag_name, $attrs);
  function characters($data);
  function unexpectedEOF($data);
  function invalidEntitySyntax($data);
  function invalidAttributeSyntax();
  function setTemplateLocator($locator);
}
?>