<?php
class Panel{
    private $size;
    private $content;
    private $special;

    public function __construct($size, $content, $special = '')
    {
        $this->size = $size;
        $this->content = $content;
        $this->special = $special;
    }

    public function __toString()
    {
        if ($this->special == 'plugins') {
			$image = 'plugins/'.$this->content['name'].'/image.png';
            if (file_exists($image)) {
                $image = '<img height="152px" src="plugins/'.$this->content['name'].'/image.png" alt="'.$this->content['name'].'"/>';
            } else {
                $image = '';
            }

			$activate = array();
			if($this->content['installed'] == 1){
				$activate[0] = "admin.php?p=" . $_GET['p'] . "disable=" . $this->content['name'];
				$activate[1] = 'Disable';
				$activate[2] = 'disable';
			}
			else{
				$activate[0] = "admin.php?p=" . $_GET['p'] . "activate=" . $this->content['name'];
				$activate[1] = 'Activate';
				$activate[2] = 'activate';
			}

            return
        	'<div class="panel '.$this->size.'">'.
				'<div class="image">' . $image . '</div>'.
				'<h3 class="name">' . ucfirst($this->content['name']) . '</h3>'.
				'<p class="description">' . ucfirst($this->content['description']) . '</p>'.
				'<p class="author"> Author: <a href="'.$this->content['website'].'">' . ucfirst($this->content['author']) . '</a></p>'.
				'<a data-modal="' . $activate[2] . $this->content['name']. '" class="button">' . $activate[1] . '</a>'.
				'<div id="modelarea_' . $this->content['name']. '"></div>' .
        	'</div>';
        }
		elseif ($this->special == 'themes') {
				$image = 'themes/'.$this->content['name'].'/image.png';
	            if (!file_exists($image)) {
	                $image = '';
	            }
				if(system_getsetting("theme") == $this->content['name']){
					$enable = "Already is active";
					$enableid = 'isactive';
				}
				else{
					$enable = "Set as default theme";
					$enableid = 'enable' .  $this->content['name'];
				}


	            return
	        	'<div class="panel '.$this->size.'">'.
					'<div class="theme-image" style="background-image: url(' . $image  .')"></div>'.
					'<h3 class="name">' . ucfirst($this->content['name']) . '</h3>'.
					'<p class="description">' . ucfirst($this->content['description']) . '</p>'.
					'<p class="author"> Author: <a href="'.$this->content['website'].'">' . ucfirst($this->content['author']) . '</a></p>'.
					'<a class="button" id="' . $enableid .  '" href="#">' . $enable . '</a>' .
	        	'</div>';
		}
		elseif ($this->special == ' ') {

		}
		else {
            return '<div class="panel '.$this->size.'">'.$this->content.'</div>';
        }
    }
}
