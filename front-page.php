<?php get_header();?>
<?php
while ( have_posts() ) : the_post(); ?>
<div class="grid-container">
  <div class="grid-x grid-margin-x">
    <div class="medium-12 cell">
      <h1>vweog otwe oitrhb iortw</h1>
      <a class="button" href="#">rstgrtg</a>
    </div>
  </div>
</div>
<?php the_content(); ?>
<?php endwhile;
?>
<?php get_footer(); ?>
