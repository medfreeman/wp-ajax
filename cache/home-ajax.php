<?php

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

/**
 * Archive Template
 *
 *
 * @file           home.php
 * @package        Responsive 
 * @author         Emil Uzelac - Mehdi Lahlou
 * @copyright      2003 - 2013 ThemeID
 * @license        license.txt
 * @version        Release: 1.1
 * @filesource     wp-content/themes/responsive/archive.php
 * @link           http://codex.wordpress.org/Theme_Development#Archive_.28archive.php.29
 * @since          available since Release 1.0
 */

 ?>

<div id="content-archive" class="<?php echo implode( ' ', responsive_get_content_classes() ); ?>">
	<?php if (have_posts()) :
                    
       sp_home_grid();

	else : 

		get_template_part( 'loop-no-posts' ); 

	endif; 
	?>  
      
</div><!-- end of #content-archive -->
        
<?php  ?>
