<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class lmbMacroParser implements lmbMacroTokenizerListener
{
  protected $active_parsing_state;
  protected $component_parsing_state;
  protected $literal_parsing_state;

  /**
   * @var lmbMacroConfig
   */
  protected $config;

  /**
   * @var lmbMacrotree_builder
   */
  protected $tree_builder;

  /**
   * @var lmbMacroTemplateLocator
   */
  protected $template_locator;

  function __construct($tree_builder, $config, $template_locator, $tag_dictionary)
  {
    $this->tree_builder = $tree_builder;

    $this->config = $config;
    $this->template_locator = $template_locator;

    $this->component_parsing_state = $this->_createComponentParsingState($tag_dictionary);
    $this->literal_parsing_state = $this->_createLiteralParsingState();

    $this->changeToComponentParsingState();
  }

  // for testing purposes
  protected function _createComponentParsingState($tag_dictionary)
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

    $this->changeToComponentParsingState();

    $tokenizer = new lmbMacroTokenizer($this);

    $this->setTemplateLocator($parser);

    $content = $this->template_locator->readTemplateFile($source_file_path);

    $tokenizer->parse($content, $source_file_path);

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
  
  function changeToComponentParsingState()
  {
    $this->active_parsing_state = $this->component_parsing_state;
  }  

  function changeToLiteralParsingState($tag)
  {
    $this->active_parsing_state = $this->literal_parsing_state;
    $this->active_parsing_state->setLiteralTag($tag);
  }
  
  function setTemplateLocator($template_locator)
  {
    $this->literal_parsing_state->setTemplateLocator($template_locator);
    $this->component_parsing_state->setTemplateLocator($template_locator);
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

  function invalidAttributeSyntax()
  {
    $this->active_parsing_state->invalidAttributeSyntax();
  }
}

?>
