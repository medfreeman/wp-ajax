<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Toolbox
 * @since Toolbox 0.1
 */
 ?>
<h1>Galeries professionelles - vidéos</h1>
<?php
$args = array(
	'tax_query' => array(
		array(
			'taxonomy' => 'types',
			'field' => 'slug',
			'terms' => 'pro'
		)
	),
	'post_type' => 'video',
	'orderby'   => 'menu_order',
    'order'     => 'ASC'
);
?>
<div class="breadcrumbs">
	<?php if(function_exists('bcn_display'))
	{
		bcn_display();
	}?>
</div>
<?php $the_query = new WP_Query( $args ); ?>
<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
	<div class="post-box">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if (has_post_thumbnail( $post->ID ) ): ?>
			<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
				<figure>
				  <a href="<?php the_permalink(); ?>"><img src="<?php echo $image[0]; ?>" /></a>
				  <figcaption><?php the_title(); ?></figcaption>
				</figure>
			<?php endif; ?>
		</article>
	</div>
<?php endwhile; ?>
<?php wp_reset_postdata(); ?>
<?php  ?>