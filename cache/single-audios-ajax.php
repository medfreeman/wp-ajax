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
				<div class="post-box">
<?php
global $wp_query;
$args = array(
	'post_type' => 'audio',
	'name' => $wp_query->get('gallery')
);
?>
<?php $the_query = new WP_Query( $args ); ?>
<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
	<article>
		<header>
			<h1><?php the_title(); ?></h1>
				<div class="breadcrumbs">
						<?php if(function_exists('bcn_display'))
						{
							bcn_display();
						}?>
					</div>
		</header>
		<?php the_content(); ?>
	</article>
<?php endwhile; ?>
<?php wp_reset_postdata(); ?>
			</div><!-- .post-box -->
<?php  ?>