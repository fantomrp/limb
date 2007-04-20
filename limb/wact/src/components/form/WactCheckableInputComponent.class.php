<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
 */

/**
* Represents an HTML input type="radio" tag
* Represents an HTML input type="checkbox" tag
*/
class WactCheckableInputComponent extends WactFormElementComponent
{
  function getName()
  {
    $name = parent :: getName();
    return str_replace('[]', '', $name) ;
  }

  function renderAttributes()
  {
    if($this->isChecked())
      $this->setAttribute('checked', "checked");
    else
      $this->removeAttribute('checked');

    parent :: renderAttributes();
  }

  function isChecked()
  {
    $value = $this->getValue();

    // Here we really hard try to guess if it's checked or not...
    if(is_array($value) && in_array($this->getAttribute('value'), $value))
      return true;
    elseif(is_scalar($value) && $value && $value == $this->getAttribute('value'))
      return true;
    elseif($value && !$this->getAttribute('value'))
      return true;
    elseif($this->getBoolAttribute('checked') && is_null($value))
      return true;
    elseif($value && $value != $this->getAttribute('value'))
      return false;

    return false;
  }
}
?>