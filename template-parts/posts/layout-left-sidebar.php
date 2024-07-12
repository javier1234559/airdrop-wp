<?php

/**
 * Posts layout left sidebar.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

do_action('flatsome_before_blog');
?>

<div class="row row-large <?php if (get_theme_mod('blog_layout_divider', 1))
	echo 'row-divided '; ?>">

	<?php if (is_single()): ?>
		<div class="large-12 col">
			<h1><?php the_title(); ?></h1>
		</div>
	<?php endif; ?>

	<div class="post-sidebar large-3 col">
		<?php flatsome_sticky_column_open('blog_sticky_sidebar'); ?>

		<?php if (is_single()): ?>
			<div class="entry-header left">
				<div class="img-heading-post-detail text-center">
					<?php if (has_post_thumbnail()) {
						the_post_thumbnail('large'); // Thay 'large' bằng kích thước bạn muốn hiển thị
					} ?>

					<?php
					echo do_shortcode('[upvote post=123 ]');
					?>
					<?php
					$airdrop_detail = get_field('airdrop_detail');
					if ($airdrop_detail):
						$airdrop_link = $airdrop_detail['airdrop_link'];
						$airdrop_confirmed_or_not = $airdrop_detail['airdrop_confirmed_or_not'];
						$total_value = $airdrop_detail['total_value'];
						$platform_airdrop = $airdrop_detail['platform_airdrop'];
						?>
						<div class="airdrop-detail">
							<?php if ($airdrop_link): ?>
								<p><a href="<?php echo esc_url($airdrop_link); ?>" target="_blank">Airdrop </a></p>
							<?php endif; ?>

							<?php if ($airdrop_confirmed_or_not): ?>
								<p>Airdrop Confirmed: <?php echo esc_html($airdrop_confirmed_or_not ? 'Confirm' : 'NotConfirm'); ?></p>
							<?php endif; ?>

							<?php if ($total_value): ?>
								<p>Total Value: <?php echo esc_html($total_value); ?></p>
							<?php endif; ?>

							<?php if ($platform_airdrop): ?>
								<p>Platform: <?php echo esc_html($platform_airdrop); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php else: ?>
			<?php get_sidebar(); ?>
		<?php endif; ?>

		<?php flatsome_sticky_column_close('blog_sticky_sidebar'); ?>
	</div>

	<div class="large-9 col medium-col-first">
		<?php if (!is_single() && get_theme_mod('blog_featured', '') == 'content') {
			get_template_part('template-parts/posts/featured-posts');
		} ?>
		<?php
		if (is_single()) {
			get_template_part('template-parts/posts/single');
			comments_template();
		} elseif (get_theme_mod('blog_style_archive', '') && (is_archive() || is_search())) {
			get_template_part('template-parts/posts/archive', get_theme_mod('blog_style_archive', ''));
		} else {
			get_template_part('template-parts/posts/archive', get_theme_mod('blog_style', 'normal'));
		}
		?>

		<?php if (is_single()): ?>
			<?php
			$estimated_values = get_field('estimated_value_tokens_per_claim');
			if ($estimated_values):
				$estimated_value = $estimated_values['estimated_value'];
				$tokens_per_claim = $estimated_values['tokens_per_claim'];
				$max_participants = $estimated_values['max_participants'];
				?>



				<div class="estimated-value-tokens">
					<?php if ($estimated_value): ?>
						<p>Estimated Value: <?php echo esc_html($estimated_value); ?></p>
					<?php endif; ?>

					<?php if ($tokens_per_claim): ?>
						<p>Tokens per Claim: <?php echo esc_html($tokens_per_claim); ?></p>
					<?php endif; ?>

					<?php if ($max_participants): ?>
						<p>Max Participants: <?php echo esc_html($max_participants); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>

</div>

<?php
do_action('flatsome_after_blog');
?>