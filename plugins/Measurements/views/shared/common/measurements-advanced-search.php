<div class="field">
		<div class="two columns alpha">
				<?php echo $this->formLabel('measurements_term', __('Measurements Search')); ?>
		</div>
		<div class="inputs five columns omega">
				<p class="explanation">
				<?php
					echo __('You may enter a number or a number range consisting of two numbers, '.
									'separated by a hypen ("-"); for example, you may enter "42" or "40-45". '.
									'You may also select one or more units that you defined to limit the search to.'
					);
				?>
				</p>
				<p>
				<?php
					echo $this->formSelect('measurements_units', @$_GET['measurements_units'], array('multiple' => true, 'size' => 6), $tripleSelect);
				?>
				</p>
				<p>
				<?php echo $this->formText('measurements_term', @$_GET['measurements_term'], null, array('size' => 10)); ?>
				</p>
		</div>
</div>
