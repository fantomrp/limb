<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/i18n/src/charset/lmbUTF8IconvDriver.class.php');
lmb_require(dirname(__FILE__) . '/lmbMultiByteStringDriverTestBase.class.php');

class lmbUTF8IconvDriverTest extends lmbMultiByteStringDriverTestBase
{
  function _createDriver() {
      if(!function_exists('iconv_strlen'))
          return null;

      return new lmbUTF8IconvDriver();
  }
}

?>