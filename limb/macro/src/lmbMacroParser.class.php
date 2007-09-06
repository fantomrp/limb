<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTokenizer.class.php');
lmb_require('limb/macro/src/lmbMacroTokenizerListener.interface.php');
lmb_require('limb/macro/src/lmbMacroLiteralParsingState.class.php');
lmb_require('limb/macro/src/lmbMacroTagParsingState.class.php');

/**
 * class lmbMacroParser.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroParser implements lmbMacroTokenizerListener
{
  protected $active_parsing_state;
  protected $tag_parsing_state;
  protected $literal_parsing_state;

  /**
   * @var lmbMacroTreebuilder
   */
  protected $tree_builder;

  /**
   * @var lmbMacroTemplateLocator
   */
  protected $template_locator;

  protected $tokenizer;

  function __construct($tree_builder, $template_locator, $tag_dictionary)
  {
    $this->tokenizer = new lmbMacroTokenizer($this);

    $this->tree_builder = $tree_builder;
    $this->template_locator = $template_locator;

    $this->tag_parsing_state = $this->_createTagParsingState($tag_dictionary);
    $this->literal_parsing_state = $this->_createLiteralParsingState();
  }

  function getCurrentLocation()
  {
    return $this->tokenizer->getCurrentLocation();
  }

  // for testing purposes
  protected function _createTagParsingState($tag_dictionary)
  {
    return new lmbMacroTagParsingState($this, $this->tree_builder, $tag_dictionary);
  }

  // for testing purposes
  protected function _createLiteralParsingState()
  {
    return new lmbMacroLiteralParsingState($this, $this->tree_builder);
  }

  /**
  * Used to parse the source template.
  * Initially invoked by the CompileTemplate function,
  * the first component argument being a root node.
  */
  function parse($file_name, $root_node)
  {
    $source_file_path = $this->template_locator->locateSourceTemplate($file_name);

    if(empty($source_file_path))
      throw new lmbMacroException('Template source file not found', array('file_name' => $file_name));

    $tags_before_parse = $this->tree_builder->getExpectedTagCount();

    $this->tree_builder->setCursor($root_node);

    $this->changeToTagParsingState();

    $this->setTemplateLocator($this->template_locator);

    $content = $this->template_locator->readTemplateFile($source_file_path);

    $this->tokenizer->parse($content, $source_file_path);

    if($tags_before_parse != $this->tree_builder->getExpectedTagCount())
    {
      $location = $this->tree_builder->getExpectedTagLocation();
      throw new lmbMacroException('Missing close tag',
                              array('tag' => $this->tree_builder->getExpectedTag(),
                                    'file' => $location->getFile(),
                                    'line' => $location->getLine()));
    }
  }

  function getActiveParsingState()
  {
    return $this->active_parsing_state;
  }
  
  function changeToTagParsingState()
  {
    $this->active_parsing_state = $this->tag_parsing_state;
  }  

  function changeToLiteralParsingState($tag)
  {
    $this->active_parsing_state = $this->literal_parsing_state;
    $this->active_parsing_state->setLiteralTag($tag);
  }
  
  function setTemplateLocator($template_locator)
  {
    $this->literal_parsing_state->setTemplateLocator($template_locator);
    $this->tag_parsing_state->setTemplateLocator($template_locator);
  }  

  function startElement($tag, $attrs)
  {
    $this->active_parsing_state->startElement($tag, $attrs);
  }

  function endElement($tag)
  {
    $this->active_parsing_state->endElement($tag);
  }

  function emptyElement($tag, $attrs)
  {
    $this->active_parsing_state->emptyElement($tag, $attrs);
  }

  function characters($text)
  {
    $this->active_parsing_state->characters($text);
  }

  function unexpectedEOF($text)
  {
    $this->active_parsing_state->unexpectedEOF($text);
  }

  function invalidEntitySyntax($text)
  {
    $this->active_parsing_state->invalidEntitySyntax($text);
  }

  function invalidAttributeSyntax($text)
  {
    $this->active_parsing_state->invalidAttributeSyntax($text);
  }
}


