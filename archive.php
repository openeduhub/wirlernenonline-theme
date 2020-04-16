<?php
/**
* The template for displaying archive pages
*
* Used to display archive-type pages if nothing more specific matches a query.
* For example, puts together date-based pages if no date.php file exists.
*
* If you'd like to further customize these archive views, you may create a
* new template file for each one. For example, tag.php (Tag archives),
* category.php (Category archives), author.php (Author archives), etc.
*
* @link https://codex.wordpress.org/Template_Hierarchy
*
* @package WordPress
* @subpackage Twenty_Fifteen
* @since Twenty Fifteen 1.0
*/
get_header(); ?>
<section class="row">
	<div class="medium-12 columns">		
		<?php get_template_part('templates/partials/archive-loop' ); ?>
	</div>
</section>
<?php get_footer(); ?>