<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class lmbMacroTokenizer
{
  protected $publicId;
  protected $observer;
  protected $rawtext;
  protected $position;
  protected $length;

  function __construct($observer)
  {
    $this->observer = $observer;
  }

  function getLineNumber()
  {
    return 1 + substr_count(substr($this->rawtext, 0, $this->position), "\n");
  }

  function getCurrentLocation()
  {
    return new lmbMacroSourceLocation($this->getPublicId(), $this->getLineNumber());
  }

  function getPublicId()
  {
    return $this->publicId;
  }

  /**
  * Moves the position forward past any whitespace characters
  */
  function ignoreWhitespace()
  {
    while($this->position < $this->length &&
        strpos(" \n\r\t", $this->rawtext{$this->position}) !== false)
      $this->position++;
  }

  /**
  * Begins the parsing operation, setting up any decorators, depending on
  * parse options invoking _parse() to execute parsing
  */
  function parse($data, $publicId = null)
  {
    $this->rawtext = $data;
    $this->length = strlen($data);
    $this->position = 0;
    $this->publicId = $publicId;

    do
    {
      $start = $this->position;
      $this->position = strpos($this->rawtext, '<%', $start);
      if($this->position === false)
      {
        if($start < $this->length)
          $this->observer->characters(substr($this->rawtext, $start));
        return;
      }

      if($this->position > $start)
      {
        $this->observer->characters(substr($this->rawtext, $start, $this->position - $start));
      }

      $this->position += 2;   // ignore '<%' string
      if($this->position >= $this->length)
      {
        $this->observer->unexpectedEOF('<%');
        return;
      }

      $element_pos = $this->position;
      $this->position += 1;

      switch($this->rawtext{$element_pos})
      {
        case '/':
          $start = $this->position;
          while($this->position < $this->length &&
                $this->rawtext{$this->position} != '%' &&
                $this->rawtext{$this->position+1} != '>')
            $this->position++;

          if($this->position >= $this->length)
          {
            $this->observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
            return;
          }

          $tag = substr($this->rawtext, $start, $this->position - $start);

          $this->observer->endElement($tag);
          $this->position += 2;   // ignore '%>' string
          break;

      default:
          while($this->position < $this->length && strpos("%/ \n\r\t", $this->rawtext{$this->position}) === false)
            $this->position++;

          if($this->position >= $this->length)
          {
            $this->observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
            return;
          }

          $tag = substr($this->rawtext, $element_pos, $this->position - $element_pos);
          $attributes = array();

          $this->ignoreWhitespace();

          //tag attributes
          while($this->position < $this->length &&
                $this->rawtext{$this->position} != '%' &&
                $this->rawtext{$this->position} != '/')
          {
            $start = $this->position;
            while($this->position < $this->length && strpos("%= \n\r\t", $this->rawtext{$this->position}) === false)
              $this->position++;

            if($this->position >= $this->length)
            {
              $this->observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
              return;
            }

            $attribute_name = substr($this->rawtext, $start, $this->position - $start);
            $attribute_value = null;

            $this->ignoreWhitespace();
            if($this->position >= $this->length)
            {
              $this->observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
              return;
            }

            if($this->rawtext{$this->position} == '=')
            {
              $attribute_value = "";

              $this->position++;
              $this->ignoreWhitespace();
              if($this->position >= $this->length)
              {
                $this->observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
                return;
              }

              $quote = $this->rawtext{$this->position};
              if($quote == '"' || $quote == "'")
              {
                $start = $this->position + 1;
                $this->position = strpos($this->rawtext, $quote, $start);
                if($this->position === false)
                {
                  $this->observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
                  return;
                }

                $attribute_value = substr($this->rawtext, $start, $this->position - $start);

                $this->position++;
                if($this->position >= $this->length)
                {
                  $this->observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
                  return;
                }

                if(strpos("% \n\r\t", $this->rawtext{$this->position}) === false)
                  $this->observer->invalidAttributeSyntax();

              }
              else
              {
                $start = $this->position;
                while($this->position < $this->length && strpos("% \n\r\t", $this->rawtext{$this->position}) === false)
                  $this->position++;

                if($this->position >= $this->length)
                {
                  $this->observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
                  return;
                }
                $attribute_value = substr($this->rawtext, $start, $this->position - $start);
              }
            }

            $attributes[$attribute_name] = $attribute_value;

            $this->ignoreWhitespace();
          }

          if($this->position >= $this->length)
          {
            $this->observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
            return;
          }

          //self closing tag check
          if($this->rawtext{$this->position} == '/' && $this->rawtext{$this->position + 1} == '%')
          {
            $this->position += 2;
            if($this->position >= $this->length)
            {
              $this->observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
              return;
            }

            if($this->rawtext{$this->position} != '>')
            {
              $start = $this->position;
              while($this->position < $this->length && $this->rawtext{$this->position} != '>')
                $this->position++;

              if($this->position >= $this->length)
              {
                $this->observer->invalidEntitySyntax(substr($this->rawtext, $element_pos - 2));
                break;
              }

              $this->observer->invalidEntitySyntax(substr($this->rawtext, $element_pos - 2,
                                                          $this->position - $element_pos + 2));
              $this->position += 1;
              break;
            }
            $this->observer->emptyElement($tag, $attributes);
          }
          else
          {
            $this->observer->startElement($tag, $attributes);
            //skipping %
            $this->position += 1;
          }

          $this->position += 1;

          break;
        }
    }
    while ($this->position < $this->length);
  }
}
?>
