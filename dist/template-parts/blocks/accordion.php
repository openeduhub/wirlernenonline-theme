<ul class="accordion accordion" data-accordion data-multi-expand="true" data-allow-all-closed="true">
  <?php while (have_rows('accordion')) : the_row(); ?>
    <li class="accordion-item" data-accordion-item>
      <a href="#" class="accordion-title"><?php the_sub_field('accordion_title'); ?></a>
      <div class="accordion-content" data-tab-content >
        <?php the_sub_field('accordion_content'); ?>
     </div>
    </li>
  <?php endwhile; ?>
</ul>
