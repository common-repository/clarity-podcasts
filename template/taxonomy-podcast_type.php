<?php get_header(); ?>
	<div class="col12 center">
		<h2><?php $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); ?><?php echo $term->name; ?></h2>
	</div><!-- END .col12.center -->
	<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
	<div class="col2 videos">
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'podcasts' ); ?></a>
		<?php if ( get_post_meta( get_the_ID(), '_year', true ) ) { ?>
		<span>Release Date: <?php echo get_post_meta($post->ID, "_year", true); ?></span>
		<?php } ?>
	</div>			
	<?php endwhile; ?>
	<?php else : ?>
	<div class="post">
		<h1><?php _e( 'No results were found', 'clarity-theme' ); ?></h1>
			<p><?php _e( 'Sorry, but you are looking for something that is not here', 'clarity-theme' ); ?></p>
	</div><!-- END .post -->
	<?php endif; ?>
<?php get_footer(); ?>