<?php get_header(); ?>
	<div class="col8" id="content">
		<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
		<div <?php post_class( '' ); ?> id="post-<?php the_ID(); ?>">
			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'slider' ); ?></a>
			<h1 class="title"><?php the_title(); ?></h1>
			<div class="postinfo">
				<?php if ( get_post_meta( get_the_ID(), '_length', true ) ) { ?>Length: <?php echo get_post_meta($post->ID, "_length", true); ?> | <?php } ?> <?php if ( get_post_meta( get_the_ID(), '_year', true ) ) { ?>Release Date: <?php echo get_post_meta($post->ID, "_year", true); ?> | <?php } ?> <?php if ( get_post_meta( get_the_ID(), '_itunes', true ) ) { ?>iTunes: <a href="<?php echo get_post_meta($post->ID, "_itunes", true); ?>">Download</a><?php } ?>
			</div><!-- END .postinfo -->
			<?php the_content(); ?>
		</div><!-- END .post -->
		<?php endwhile; ?>
		<?php else : ?>
		<?php endif; ?>
	</div><!-- END .col8 #content -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>