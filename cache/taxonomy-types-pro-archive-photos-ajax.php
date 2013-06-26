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
<?php global $wp_query; ?>
<?php if ($wp_query->get('gallery')) : ?>
<?php if(function_exists('bcn_display')) : ?>
						<div class="breadcrumbs">
							<?php bcn_display(); ?>
						</div>
<?php endif; ?>
<?php else: ?>
<h1>Galeries professionnelles - photos</h1>
	<?php if(function_exists('bcn_display')) : ?>
		<div class="breadcrumbs">
			<?php bcn_display(); ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
	<?php ork_init_ngg_rewrite(); ?>
	<?php echo do_shortcode('[album id=1 template=supersized gallery=supersized]'); ?>