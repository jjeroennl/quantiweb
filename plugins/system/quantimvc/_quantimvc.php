<?php
  class Mvc{
    private $content;
    private $cur;

    function __construct($file, $filelocation = null){
      $this->content = new Domdocument();
      $this->content->formatOutput=true;
      $this->content->preserveWhitespace=true;
      $this->content->createElement("panel");
      if ($filelocation == null) {
          libxml_use_internal_errors(true);
          $this->content->loadHTMLFile(str_replace(".php", ".qhtml",  $file));
          libxml_use_internal_errors(false);
          $this->__fixforms();
          $this->__fixpanels();
      }
      else{
        $file = substr($filelocation, 0, strrpos($filelocation, '/')) . "/$file";
        libxml_use_internal_errors(true);
        $this->content->loadHTMLFile($file);
        libxml_use_internal_errors(false);
        $this->__fixforms();
        $this->__fixpanels();
      }
      return $this;
    }

    function _($cur){

      $inputs = $this->content->getElementsByTagName("panel");
      for($i = 0; $i < $inputs->length; $i++){
        $inputs->item($i)->parentNode->removeChild($inputs->item($i));
      }

      $this->content->saveHTML();

      $this->cur = $cur;
      return $this;
    }

    function __give_element(){
      if(substr($this->cur, 0,1) == "#"){
        $id = $this->content->getElementById(str_replace("#", "",$this->cur));
        if($id != null){
			return $id;
        }
		else{
			echo "Dit element bestaat niet. (" . $this->cur . ")";
			return $this->content->createElement("no");
		}
      }
      elseif(substr($this->cur, 0,1) == "."){
        $class  = str_replace(".", "",$this->cur);
      }
      else{
        return $this->content->getElementsByTagName($this->cur);
      }
    }

    function set_var($variable, $content){
      $domnode = new DOMXPath($this->content);
      $nodes = $domnode->evaluate('//text()[contains(., "'.$variable.'")]');
      foreach ($nodes as $node) {
        $node->nodeValue = str_replace($variable, $content, $node->nodeValue);
      }
      $this->content->saveHtml();
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
		$this->__give_element()->parentNode->removeChild($this->__give_element());
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

    function set_attribute($attribute, $value){
      $this->__give_element()->setAttribute($attribute,  $value);
      return $this;
    }

    function get_all(){
      echo preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $this->content->saveHTML());
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
    function __fixpanels(){
      $content = $this->content->saveHTML();
      $content = str_replace("<panel large>", '<div class="panel large">', $content);
      $content = str_replace("<panel small>", '<div class="panel small">', $content);
      $content = str_replace("<panel normal>", '<div class="panel normal">', $content);
      $content = str_replace("<panel huge>", '<div class="panel huge">', $content);
      $content = str_replace("</panel>", '</div>', $content);
      $this->content->loadHTML($content);
      return;
      $inputs = $this->content->getElementsByTagName("panel");
      foreach($inputs as $input){
          $content = $this->content->saveHTML($input);
          $content = str_replace("</panel>", "", $content);
          $content = str_replace("<panel large>", "", $content);
          $content = str_replace("<panel small>", "", $content);
          $content = str_replace("<panel medium>", "", $content);
          $content = str_replace("<panel huge>", "", $content);

          $contenthtml = $this->content->createDocumentFragment();
          $contenthtml->appendXML($content);

          $new = $this->content->createElement("div");
		  libxml_use_internal_errors(true);
		  $new->appendChild($contenthtml);
		  libxml_use_internal_errors(false);


          if($input->hasAttribute("large")){
            $new->setAttribute("class", "panel large");
          }
          elseif($input->hasAttribute("medium")){
            $new->setAttribute("class", "panel medium");
          }
          elseif($input->hasAttribute("small")){
            $new->setAttribute("class", "panel small");
          }
          elseif($input->hasAttribute("huge")){
            $new->setAttribute("class", "panel huge");
          }
          $input->parentNode->appendChild($new);
          unset($new);
          unset($content);
      }
      $this->content->saveHTML();

      $inputs = $this->content->getElementsByTagName("panel");
      for($i = 0; $i < $inputs->length; $i++){
        $inputs->item($i)->parentNode->removeChild($inputs->item($i));
      }
      $inputs = $this->content->getElementsByTagName("panel");
      for($i = 0; $i < $inputs->length; $i++){
        $inputs->item($i)->parentNode->removeChild($inputs->item($i));
      }
      $this->content->saveHTML();

      $this->content->loadHTML($this->content->saveHTML());
    }

    function add_list($list = array()){
      $stringlist = "<ul>";
      foreach($list as $item){
        $stringlist.= "<li>" . $item . "</li>";
      }
      echo "</ul>";
      $this->__give_element()->nodeValue = $stringlist;
      return $this;
    }

    function append($html){
		$child_array = array();
	    $html = '<div id="html-to-dom-input-wrapper">' . $html . '</div>';
     	$hdoc = new DOMDocument();
		libxml_use_internal_errors(true);
		$hdoc->loadHTML($html);
		libxml_use_internal_errors(false);
		try {
			$children = $hdoc->getElementById('html-to-dom-input-wrapper')->childNodes;
			foreach($children as $child) {
				$child = $this->content->importNode($child, true);
				$this->__give_element()->appendChild($child);

			}
		} catch (Exception $ex) {
			error_log($ex->getMessage(), 0);
		}


    }

	function add_controller($function){
		$function($_POST + $_GET);
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

  include 'modal.php';
  include 'panel.php';

?>
