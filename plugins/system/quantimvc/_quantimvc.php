<?php
  class Mvc{
    private $content;
    private $cur;

    function __construct($file){
      $this->content = new Domdocument();
      if (strpos($file,'.php') !== false) {
          $this->content->loadHTMLFile(str_replace(".php", ".qhtml",  $file));
          $this->__fixforms();
      }
      else{
        $this->content->loadHTMLFile($file);
      }
      return $this;
    }

    function _($cur){
      $this->cur = $cur;
      return $this;
    }

    function __give_element(){
      if(substr($this->cur, 0,1) == "#"){
        return $this->content->getElementById(str_replace("#", "",$this->cur));
      }
      elseif(substr($this->cur, 0,1) == "."){
        $class  = str_replace(".", "",$this->cur);

        //$this->content->query("//*[contains(@class, '$classname')]")->nodeValue = $content;
        // $this->content->getElementsByClass(str_replace(".", "",$this->cur))->nodeValue = $content;
      }
      else{
        return $this->content->getElementsByTagName($this->cur);
      }
    }

    function set_html($content){
      $this->__give_element()->nodeValue = $content;
      return $this;
    }

    function set_value($value){
      $this->__give_element()->setAttribute("value", $value);
      return $this;
    }

    function set_class($value){
      $this->__give_element()->setAttribute("class",  $value);
      return $this;
    }

    function add_class($value){
      $this->__give_element()->setAttribute("class", $this->__give_element()->getAttribute("class") . $value);
      return $this;
    }

    function add_style($value){
      $this->__give_element()->setAttribute("style", $this->__give_element()->getAttribute("style") . $value);
      return $this;
    }

    function set_style($value){
      $this->__give_element()->setAttribute("style",  $value);
      return $this;
    }

    function hide(){
        $this->__give_element()->setAttribute("style", $this->__give_element()->getAttribute("style") .  "display: none;");
        return $this;
    }

    function remove_classes(){
      $this->__give_element()->setAttribute("class", "");
      return $this;
    }

    function set_placeholder($value){
      $this->__give_element()->setAttribute("placeholder",  $value);
      return $this;
    }

    function get_all(){
      echo $this->content->saveHTML();
    }

    function __fixforms(){
      $inputs = $this->content->getElementsByTagName("input");
      foreach($inputs as $input){
        if($input->hasAttribute("type")){
          if(!strtolower($input->getAttribute("type") == "text")){
            return;
          }
        }
        $input->setAttribute("class",  "form-control");
        $this->content->saveHTML();
      }
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
