<?php $i = 0; ?>
<?php $theme->display('header'); ?>
<?php $theme->display('hero'); ?>
<section id="tagline">
	<div class="container">
		<div class="three-thirds column offset-by-one">
			<h2>Hear In Dallas</h2>
			<p>Striving to teach individuals with hearing loss and other communication disorders to use spoken language for socialization, education, employment and life!</p>
		</div>
	</div>
</section>

<section id="news">
	<div class="container">
		<?php foreach( $featured as $feature ) { $i++; ?>
			<?php if( $i == 1 || $i == 3 ) { ?>
			<div class="row">
			<?php } ?>
				<?php
					if( $i == 2 ) {
						$class = ' offset-by-two';
					} else {
						$class = '';
					}
				?>
				<?php if( $i == 1 || $i == 2 ) { ?>
					<div class="columns seven<?php echo $class; ?>">
						<h2><?php echo $feature->title_out; ?></h2>					
						<img src="<?php echo $feature->info->image; ?>" class="scale-with-grid">
						<?php echo $feature->content_excerpt; ?>
						<p><a class="btn" href="<?php echo $feature->permalink; ?>" title="View <?php echo $feature->title; ?>">View <?php echo $feature->title; ?> <i class="fa fa-arrow-right"></i></a></p>
					</div>
				<?php } else { ?>
					<div id="bigone" class="columns sixteen">
						<hr>					
						<div class="eight columns alpha">
							<img src="<?php echo $feature->info->image; ?>" class="scale-with-grid">
						</div>
						<div class="eight columns omega">
							<h2><?php echo $feature->title_out; ?></h2>
							<?php echo $feature->content_excerpt; ?>
							<p><a class="btn" href="<?php echo $feature->permalink; ?>" title="View <?php echo $feature->title; ?>">View <?php echo $feature->title; ?> <i class="fa fa-arrow-right"></i></a></p>
						</div>
					</div>
				<?php } ?>
			<?php if( $i == 2 || $i == 3 ) { ?>
			</div>
			<?php } ?>
		<?php } ?>
	</div>
</section>
<?php $theme->display('footer'); ?>