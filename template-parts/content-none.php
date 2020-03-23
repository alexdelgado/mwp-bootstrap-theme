<section>
	<header>
		<h2><?php esc_html_e( 'Whoops.. something went wrong.', '_bootstrap' ); ?></h2>
	</header>
	<div>
		<?php if ( is_search() ) : ?>

			<p><?php esc_html_e( 'Nothing matched your search terms. Try different keywords.', '_bootstrap' ); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', '_bootstrap' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>
	</div>
</section>
