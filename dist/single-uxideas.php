<?php
get_header();
?><div class="grid-container">
	<div class="grid-x grid-margin-x">
		<div class="medium-12 cell">
            <form class="backlink">
                <input class="button" type="button" value="zurück" onclick="history.back()">
            </form>

			<h1 class="page-title"><?php the_title(); ?></h1>
		</div>
		<?php
		while ( have_posts() ) : the_post(); ?>
		<div class="medium-8 cell">
			<?php
            $images = get_field('gallery');
            $size = 'full'; // (thumbnail, medium, large, full or custom size)

            if( $images ): ?>
            <ul class="wlo_ux_gallery">
                <?php foreach( $images as $image ): ?>
                    <li>
                        <div class="wlo_ux_gallery_content">
                            <img src="<?php echo esc_url($image['sizes']['thumbnail']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <script type="text/javascript">
                jQuery(document).ready(function(){
                    jQuery('.wlo_ux_gallery').slick();
                });
            </script>

		<?php
		$value = get_field( "description" );
		if ( $value ) :?>
		<div class="long-text-container lead margin-bottom-2">
			<?php echo $value; ?>
		</div>
		<?php endif; ?>

        <?php
        $tags = get_field( 'metadata' );
        if( $tags ):?>
            <div class="cell medium-6">
                <hr>
                <p>Metadaten:</p>

                <?php foreach ($tags as $tag):?>
                    <span class="multi-select"><?php echo $tag->name; ?></span>
                <?php endforeach;
                ?>
            </div>
        <?php endif; ?>

            <?php
            $url = get_field( 'url' );
            if( $url ):?>
                <div class="cell medium-6">
                    <hr>
                    <p>Link:</p>
                    <a href="<?php echo $url; ?>"><?php echo $url; ?></a>
                </div>
            <?php endif; ?>

</div>


<?php endwhile;
?>
</div>
</div>
</div>
<?php
get_footer();
