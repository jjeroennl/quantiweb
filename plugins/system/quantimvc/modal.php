<?php
	class modal{
		private $title;
		private $content;
		function __construct($name, $title, $content, $footer = null){
			$this->name = $name;
			$this->title = $title;
			$this->content = $content;
			if($footer != "null"){
				$footer = '<div class="windowfooter"> <a class="cancelmodal button">Cancel</a>' . $footer .'</div>';
			}
			$this->footer = $footer;
		}

		function __toString(){
			if(is_array($this->content)){
				$panelcontent = "";
				foreach($this->content as $size=>$content){
					$panelcontent .= '
						<div class="windowpanel ' . $size . '">
							' . $content .'
						</div>
					';
				}
				if($this->footer != null){
					$panelcontent.= $this->footer;
				}
				return '
				<div id="' . $this->name . '" class="window">
					<div class="windowtitle">
						<div class="title">' . $this->title .'</div>
						<div class="closebutton"><i class="fa fa-times"></i></div>
					</div>
					<div class="windowcontent">
						' . $panelcontent . '
					</div>
				</div>
				';
			}
			else{
				if($this->footer != null){
					$panelcontent = $this->content . $this->footer;
				}
				else{
					$panelcontent = $this->content;
				}
				return '
				<div id="' . $this->name . '" class="window">
					<div class="windowtitle">
						<div class="title">' . $this->title .'</div>
						<div class="closebutton"><i class="fa fa-times"></i></div>
					</div>
					<div class="windowcontent">
						' . $panelcontent . '
					</div>
				</div>
				';
			}
		}
	}
 ?>
