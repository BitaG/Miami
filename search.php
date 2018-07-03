<?php get_header(); ?>
<section>
	<div class="container">
		<div class="row">
			<div>
				<h1><?php printf('Поиск по строке: %s', get_search_query()); ?></h1>
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