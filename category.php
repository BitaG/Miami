<?php get_header(); ?>
<section>
	<div class="container">
		<div class="row">
			<div>
				<h1><?php single_cat_title(); ?></h1>
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<?php get_template_part('loop'); ?>
				<?php endwhile;
				else: echo '<p>Нет записей.</p>'; endif; ?>	 
				<?php pagination(); ?>
			</div>
		</div>
	</div>
</section>
<?php get_footer(); ?>