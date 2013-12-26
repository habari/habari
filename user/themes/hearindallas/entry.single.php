<?php $theme->display('header'); ?>
<?php $others = Posts::get( array('content_type' => Post::type('entry'), 'limit' => 3, 'not:id' => $post->id) ); ?>

<section id="news"<?php if($request->display_entry ) { echo ' class="single"'; } ?>>
	<div class="container">
		<div id="content" class="columns ten">
			<h2><?php echo $post->title_out; ?></h2>
			<img src="<?php echo $post->info->image; ?>" class="scale-with-grid">
			<?php echo $post->content_out; ?>
			<p class="social">
				<span class="share">Share?</span>
				<a class="btn" href="#" onclick="window.open('https://twitter.com/intent/tweet?original_referer=<?php echo rawurlencode(rtrim($post->permalink, '/')); ?>&text=<?php echo urlencode($post->title_out); ?>&url=<?php echo rtrim($post->permalink, '/'); ?>', '_blank', 'toolbar=no, scrollbars=yes, resizable=yes, width=500, height=400');return false;"><i class="fa fa-twitter"></i></a>
				<a class="btn" href="#" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode(rtrim($post->permalink, '/')); ?>', '_blank', 'toolbar=no, scrollbars=yes, resizable=yes, width=480, height=400');return false;"><i class="fa fa-facebook"></i></a>
				<a class="btn" href="#" onclick="window.open('http://www.linkedin.com/shareArticle?mini=true&url=<?php echo rawurlencode(rtrim($post->permalink, '/')); ?>&title=<?php echo urlencode($post->title_out); ?>&summary=<?php echo urlencode(strip_tags($post->content_except)); ?>&source=<?php Site::out_url('habari'); ?>', '_blank', 'toolbar=no, scrollbars=yes, resizable=yes, width=480, height=400');return false;"><i class="fa fa-linkedin"></i></a>
			</p>
		</div>
		<div id="sidebar" class="columns offset-by-one four">
			<h5>Recent News</h5>
			<ul>
			<?php foreach( $others as $other ) { ?>
				<li>
					<a href="<?php echo $other->permalink; ?>">
						<div class="image"><img src="<?php echo $other->info->image; ?>" class="scale-with-grid"></div>
						<?php echo $other->title_out; ?>
					</a>
				</li>
			<?php } ?>
			</ul>
		</div>
	</div>
</section>
<?php $theme->display('footer'); ?>