<?php
/** Fallback for anything the views don't cover (future blog posts, pages). */
defined( 'ABSPATH' ) || exit;
get_header(); ?>
<div class="shell" style="padding-block: 48px 80px; max-width: 820px">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article>
			<h1><?php the_title(); ?></h1>
			<div class="prose"><?php the_content(); ?></div>
		</article>
	<?php endwhile; endif; ?>
</div>
<?php get_footer();
