	<div id="blue">
	    <div class="container">
			<div class="row">
				<h3><?php echo content_get_page_title();?></h3>
			</div>
	    </div>
	</div>

	<div class="container mtb">
	 	<div class="row">
			<div class="col-lg-8">
				<!-- -- Blog Post 1 ---->
				<?php echo content_get_content();?>
			</div></div>

			<?php
				if(!isset($_GET['p'])){
					echo '</div></div>';
				}
				if(isset($_GET['p'])){
					if($_GET['p'] == 'Binnenland'){
						echo '</div>';
					}
				}
			?>
			<div class="col-lg-4">
				<!-- -- Blog Post 1 ---->
				<?php echo content_get_sidebar();?>
			</div>
		</div>
	</div>
