			<div class="post-box">
				<h1>
					<?php if (is_day()) : ?>
						<?php printf(__('Daily Archives: %s', 'reverie'), get_the_date()); ?>
					<?php elseif (is_month()) : ?>
						<?php printf(__('Monthly Archives: %s', 'reverie'), get_the_date('F Y')); ?>
					<?php elseif (is_year()) : ?>
						<?php printf(__('Yearly Archives: %s', 'reverie'), get_the_date('Y')); ?>
					<?php else : ?>
						<?php single_cat_title(); ?>
					<?php endif; ?>
				</h1>
				<?php if(function_exists('bcn_display')) : ?>
						<div class="breadcrumbs">
							<?php bcn_display(); ?>
						</div>
				<?php endif; ?>
				<?php get_template_part('loop', 'category'); ?>
			</div>