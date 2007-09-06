<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTemplateLocator.class.php');
lmb_require('limb/macro/src/lmbMacroCompiler.class.php');
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');

/**
 * class lmbMacroTemplate.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplate
{
  protected $file;
  protected $cache_dir;
  protected $vars = array();

  function __construct($file, $cache_dir, $locator = null)
  {
    $this->file = $file;
    $this->cache_dir = $cache_dir;
    $this->locator = $locator;
    if(!$this->locator)
      $this->locator = new lmbMacroTemplateLocator();
  }

  function set($name, $value)
  {
    $this->vars[$name] = $value;
  }

  function render()
  {
    ob_start();

    $compiler = $this->_createCompiler();
    list($class, $func, $compiled_file) = $compiler->compile($this->file);

    include_once($compiled_file);
    $executor = new $class($this->vars);
    $executor->$func();

    $out = ob_get_contents();
    ob_end_clean();
    return $out;
  }

  protected function _createCompiler()
  {
    $tag_dictionary = lmbMacroTagDictionary :: instance();
    $compiler = new lmbMacroCompiler($tag_dictionary, $this->locator);
    return $compiler;
  }
}

