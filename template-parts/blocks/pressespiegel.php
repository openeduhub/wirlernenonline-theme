<?php

$title = get_field('title');
$numberposts = get_field('numberposts');

if ($title) {
    echo '<h1>' . $title . '</h1>';
}

$partner_query = array(
    'post_type' => 'presse',
    'numberposts' => $numberposts,
    'post_status'       => 'publish',
);

?>

<div class="presseBlock">

    <?php
    $posts = get_posts($partner_query);
    if($posts) {
    foreach($posts as $post) {

    $image = get_field('thumbnail', $post->ID);
    $image_alt = get_the_title($post->ID);
    if($image['alt']){
        $image_alt = $image['alt'];
    }
    ?>

    <div class="presseCell">
        <a href="<?php echo get_field('url', $post->ID); ?>" target="_blank"><img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo $image_alt; ?>"></a>
        <a href="<?php echo get_field('url', $post->ID); ?>" target="_blank"><h2 class="edu-item__title"><?php echo get_the_title($post->ID); ?></h2></a>
        <?php
        $descr = get_field( 'short_description', $post->ID );
        if( $descr ){
            echo '<p class="edu-item__content">'.$descr.'</p>';
        }
        ?>
    </div>

    <?php
        }
    }
    ?>

</div>
