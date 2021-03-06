<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="row">
			<div class="col-xs-4">
				<ul class="pager small">
					<li class="previous prev_link">
						<a href="<?php echo $prev['link'];?>" onclick="unsetexercise(event)"><b>
							<span class="glyphicon glyphicon-chevron-left"></span>
							<span class="breadcumb-title"><?php echo $prev['name'];?></span></b>
						</a>
					</li>
				</ul><?php

				if (count($easier) > 0) {?>

				<ul class="pager pager-success small dropdown">
					<li class="previous prev_link">

						<a href="#" onclick="unsetexercise(event)" data-toggle="dropdown"><b>
							<span class="glyphicon glyphicon-chevron-left"></span>
							<span class="breadcumb-title">Könnyebb</span></b>
						</a>

						<ul class="dropdown-menu dropdown-menu-left"><?php

								foreach ($easier as $exercise) {?>
							<li class="dropdown-menu-li">
								<a href="<?php echo $exercise['link'];?>">
									<img class="star" src="<?php echo base_url().'assets/images/star'.$exercise['progress']['stars'][0].'.png';?>" alt="star"  width="15px">
									<img class="star" src="<?php echo base_url().'assets/images/star'.$exercise['progress']['stars'][1].'.png';?>" alt="star"  width="15px">
									<img class="star star-last" src="<?php echo base_url().'assets/images/star'.$exercise['progress']['stars'][2].'.png';?>" alt="star"  width="15px">
									<?php echo $exercise['name'];?>
								</a>
							</li><?php

							}?>

						</ul>

					</li>
				</ul><?php

				}?>

			
			</div>
			<div class="col-xs-4">
			</div>
			<div class="col-xs-4">
				<ul class="pager small">
					<li class="next next_link">
						<a href="<?php echo $next['link'];?>" onclick="unsetexercise(event)"><b>
							<span class="breadcumb-title"><?php echo $next['name'];?></span>
							<span class="glyphicon glyphicon-chevron-right"></span></b>
						</a>
					</li>
				</ul><?php

				if (count($harder) > 0) {?>

				<ul class="pager pager-danger small dropdown">
					<li class="next next_link">

						<a href="#" onclick="unsetexercise(event)" data-toggle="dropdown"><b>
							<span class="breadcumb-title">Nehezebb</span>
							<span class="glyphicon glyphicon-chevron-right"></span></b>
						</a>

						<ul class="dropdown-menu dropdown-menu-right"><?php

								foreach ($harder as $exercise) {?>
							<li class="dropdown-menu-li">
								<a href="<?php echo $exercise['link'];?>">
									<img class="star" src="<?php echo base_url().'assets/images/star'.$exercise['progress']['stars'][0].'.png';?>" alt="star"  width="15px">
									<img class="star" src="<?php echo base_url().'assets/images/star'.$exercise['progress']['stars'][1].'.png';?>" alt="star"  width="15px">
									<img class="star star-last" src="<?php echo base_url().'assets/images/star'.$exercise['progress']['stars'][2].'.png';?>" alt="star"  width="15px">
									<?php echo $exercise['name'];?>
								</a>
							</li><?php

							}?>

						</ul>

					</li>
				</ul><?php

				}?>

			
			</div>
		</div>
	</div>
</div>