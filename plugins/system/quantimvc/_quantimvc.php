<?php
  class Mvc{
    private $content;
    private $cur;

    function __construct($file){
      $this->content = new Domdocument();
      $this->content->loadHTMLFile(str_replace(".php", ".qhtml",  $file));
    }

    function _($cur){
      $this->cur = $cur;
      return $this;
    }

    function setHTML($content){
      if(substr($this->cur, 0,1) == "#"){
        $this->content->getElementById(str_replace("#", "",$this->cur))->nodeValue = $content;
      }
      elseif(substr($this->cur, 0,1) == "."){
        $class  = str_replace(".", "",$this->cur);
        
        //$this->content->query("//*[contains(@class, '$classname')]")->nodeValue = $content;
        // $this->content->getElementsByClass(str_replace(".", "",$this->cur))->nodeValue = $content;
      }
      else{
        $this->content->getElementByTagName(str_replace(".", "",$this->cur))->nodeValue = $content;
      }
      return $this;
    }

    function getAll(){
      $var = $this->content->saveHTML();
      return $var;
    }
  }

  function getElementsByClassName($elements, $className) {
      $matches = array();
      foreach($elements as $element) {
          if (!$element->hasAttribute('class')) {
              continue;
          }
          $classes = preg_split('/\s+/', $element->getAttribute('class'));
          if ( ! in_array($className, $classes)) {
              continue;
          }
          $matches[] = $element;
      }
      return $matches;
  }

?>
