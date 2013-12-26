<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title><?php if ($request->display_entry && isset($post) || $request->display_page && isset($post)  || $request->display_article && isset($article) || $request->display_single_media && isset($media) || $request->display_reading && isset($reading)) { echo "{$post->title} - "; } else { echo $title; } ?><?php Options::out( 'title' ) ?></title>
    <meta name="description" content="The website of Hear in Dallas">
    <meta name="author" content="League of Beards">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">                        

	<link rel="stylesheet" href="<?php Site::out_url( 'theme' ); ?>/css/base.css">
	<link rel="stylesheet" href="<?php Site::out_url( 'theme' ); ?>/css/skeleton.css">
	<link rel="stylesheet" type="text/css" media="screen" href="<?php Site::out_url( 'theme' ); ?>/style.css">
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	<link rel="Shortcut Icon" href="<?php Site::out_url( 'theme' ); ?>/favicon.ico">
		
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script type="text/javascript">
		if ( typeof(HEAR) == "undefined" ) { HEAR = {}; }
		HEAR.url = "<?php Site::out_url('habari'); ?>";
		HEAR.theme = "<?php Site::out_url('theme'); ?>";
	</script>
	
	<?php echo $theme->header(); ?>
</head>
<header <?php if($request->display_entry ) { echo 'class="single"'; } ?>>
	<div class="container">
		<div class="columns sixteen">
			<div class="columns eleven">
				<h1><a href="<?php Site::out_url('habari'); ?>" title="Hear In Dallas">Hear In Dallas</a></h1>
			</div>
			<div class="columns four" style="padding-right:0px;">
				<dl>
					<dd>Hours: 9:00a &mdash; 5:00p CST</dd>
					<dd>(214) 902-0996</dd>
				</dl>
			</div>
		</div>
		<div class="columns sixteen">
		<nav>
			<ul>
				<li><a href="<?php URL::out('display_page', array('slug' => 'about')); ?>" title="">About</a></li>
				<li><a href="<?php URL::out('display_page', array('slug' => 'approach')); ?>" title="">Our Approach</a></li>
				<li><a href="<?php URL::out('display_page', array('slug' => 'services')); ?>" title="">Services</a></li>
				<li><a href="<?php URL::out('display_page', array('slug' => 'testimonials')); ?>" title="">Testimonials</a></li>
				<li><a href="<?php URL::out('display_page', array('slug' => 'resources')); ?>" title="">Resources</a></li>
				<li><a href="<?php URL::out('display_page', array('slug' => 'careers')); ?>" title="">Careers</a></li>
				<li><a href="<?php URL::out('display_page', array('slug' => 'contact')); ?>" title="">Contact Us</a></li>
			</ul>
		</nav>
	</div>
	</div>
</header>