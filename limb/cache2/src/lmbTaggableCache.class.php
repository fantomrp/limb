<?php
lmb_require('limb/cache2/src/lmbCache.interface.php');

class lmbTaggableCache implements lmbCache
{
  /**
   * @var lmbCacheConnection
   */
  protected $connection;
  
  function __construct(lmbCacheConnection $connection)
  {
    $this->connection = $connection;
  }
  
  protected function _createContainer($value, $tags)
  {    
    $tags_values = $this->connection->get($tags);
    
    foreach($tags_values as $tag_key => $tag_value)
      if(is_null($tag_value))
      {
        $tags_values[$tag_key] = 0;
        $this->connection->add($tag_key, 0);
      }
    
    return array('tags' => $tags_values, 'value' => $value);
  }
  
  protected function _isTagsValid($tags)
  {    
    $tags_versions = $this->connection->get(array_keys($tags));
    
    foreach($tags_versions as $tag_key => $tag_version)
      if(is_null($tag_version) || $tags[$tag_key] !== $tag_version)      
        return false;
    
    return true;
  }
  
  protected function _getFromContainer($container)
  {
    if($this->_isTagsValid($container['tags']))
      return $container['value'];
    else 
      return NULL;
  }
    
  function set($key, $value, $tags_keys = false, $ttl = false)
  {
    if(!is_array($tags_keys))
      $tags_keys = array($tags_keys);
    
    return $this->connection->set($key, $this->_createContainer($value, $tags_keys), $ttl);
  }
  
  function get($key)
  {
    if(is_null($container = $this->connection->get($key)))
      return NULL;
      
    if(is_null($value = $this->_getFromContainer($container)))
      $this->connection->delete($key);
      
    return $value;
  }
  
  function delete($key)
  {
    $this->connection->delete($key);
  }
  
  function deleteByTag($tag)
  {
     $this->connection->increment($tag);
  }
  
  function flush()
  {
    $this->connection->flush();
  }
}