<?php $row_count = 0; ?>
<?php if($widgets): ?>
	<?php foreach ($widgets as $rows): ?>
		<div class="row-<?php echo $row_count; ?> <?php echo $rows['class']; ?>">
			<div class="container">
				<div class="row">
					<?php foreach($rows['cols'] as $cols): ?>
						<?php
							$class="";
							if(isset($cols['format'])){
								$class="col-sm-12 col-md-". $cols['format'];
							}
						?>
						<div class="<?php echo $class; ?>">
							<?php if(isset($cols['info'])): ?>
								<?php foreach($cols['info'] as $modules): ?>
									<?php echo $modules; ?>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
					<?php $row_count++; ?>
				</div>
			</div>
		</div><!-- end row <?php echo $row_count; ?>-->
	<?php endforeach; ?>
<?php endif; ?>