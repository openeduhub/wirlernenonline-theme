<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Block: Banner</div>';
};

if (get_field('active')) {


    if (get_the_id()) {
        $postID = get_the_id();
    } else {
        $postID = acf_editor_post_id();
    }


    $count = 5;
    if (get_field('count')) {
        $count = intval(get_field('count'));
    }

    //Disciplines
    $query_var_disciplines = (!empty(get_query_var('discipline', null))) ? explode(";", get_query_var('discipline', null)) : [];
    $block_var_disciplines = (!empty(get_field('discipline'))) ? get_field('discipline') : [];
    $portal_var_disciplines = get_field('discipline', $postID);

    $disciplines = (!empty($portal_var_disciplines)) ? $portal_var_disciplines : [];
    $disciplines = (!empty($block_var_disciplines)) ? $block_var_disciplines : $disciplines;
    $disciplines = (!empty($query_var_disciplines)) ? $query_var_disciplines : $disciplines;

    //EducationalContext
    $query_var_educationalContexts = (!empty(get_query_var('educationalContext', null))) ? explode(";", get_query_var('educationalContext', null)) : [];
    $block_var_educationalContexts = (!empty(get_field('educationalContext'))) ? get_field('educationalContext') : [];
    $portal_var_educationalContexts = get_field('educationalContext', $postID);

    $educationalContexts = (!empty($portal_var_educationalContexts)) ? $portal_var_educationalContexts : [];
    $educationalContexts = (!empty($block_var_educationalContexts)) ? $block_var_educationalContexts : $educationalContexts;
    $educationalContexts = (!empty($query_var_educationalContexts)) ? $query_var_educationalContexts : $educationalContexts;

    //intendedEndUserRole
    $query_var_intendedEndUserRoles = (!empty(get_query_var('intendedEndUserRole', null))) ? explode(";", get_query_var('intendedEndUserRole', null)) : [];
    $block_var_intendedEndUserRoles = (!empty(get_field('intendedEndUserRole'))) ? get_field('intendedEndUserRole') : [];
    $portal_var_intendedEndUserRoles = get_field('intendedEndUserRole', $postID);

    $intendedEndUserRoles = (!empty($portal_var_intendedEndUserRoles)) ? $portal_var_intendedEndUserRoles : [];
    $intendedEndUserRoles = (!empty($block_var_intendedEndUserRoles)) ? $block_var_intendedEndUserRoles : $intendedEndUserRoles;
    $intendedEndUserRoles = (!empty($portal_var_intendedEndUserRoles)) ? $portal_var_intendedEndUserRoles : $intendedEndUserRoles;

    //OER
    $query_var_oer = get_query_var('oer', false);
    $block_var_oer = get_field('oer');
    $portal_var_oer = get_field('oer', $postID);

    $oer = ($portal_var_oer) ? $portal_var_oer : false;
    $oer = ($block_var_oer) ? $block_var_oer : $oer;
    $oer = ($query_var_oer) ? $query_var_oer : $oer;


    //Category
    $category_id = get_cat_ID('Themenportal');

    $count = -1;
    if (get_field('count')) {
        $count = get_field('count');
    }

    $args = array(
        'posts_per_page' => $count,
        'cat' => $category_id,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'discipline',
                'value' => serialize(array_column($disciplines, 'value')),
                'compare' => 'IN'
            ),
            array(
                'key' => 'educationalContext',
                'value' => serialize(array_column($educationalContexts, 'value')),
                'compare' => 'IN'
            ),
            array(
                'key' => 'intendedEndUserRole',
                'value' => serialize(array_column($intendedEndUserRoles, 'value')),
                'compare' => 'IN'
            )
        )
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) :
        echo '<div class="portal_banner_slider">';
        while ($query->have_posts()) : $query->the_post();
            ?>
            <div>
                <div class="portal_banner_slider_content">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                    <div class="portal_banner_slider_content_text">
                        <h3><?php the_title(); ?></h3>
                        <p><?php the_excerpt(); ?></p>
                        <div class="text-right">
                            <a href="<?php the_permalink(); ?>" class="button">Weiterlesen</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php
        endwhile;

        ?>

        <?php
        echo '</div>';
    endif;
    wp_reset_query();
}

?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.portal_banner_slider').slick({
            autoplay: true,
            arrows: false,
            dots: true
        });
    });
</script>
<?php if (is_admin()) {
    echo '</div>';
}; ?>
