<?php

[
    'collectionId' => $collectionId,
    // Add a separate last breadcrumb with the post title in addition to the last collection
    // element.
    'appendPostElement' => $appendPostElement,
] = $args + [
    // Defaults
    'collectionId' => null,
    'appendPostElement' => false,
];

$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$portalTitle = get_the_title($postID);

$author_ids = get_field('authors', $postID);
if (empty($author_ids)) {
    $author_ids = array();
}
?>

<div class="fachportal-header-bar">
    <div class="fachportal-header-bar-wrapper">
        <div class="portal-breadcrumbs">
            <ul id="portal-breadcrumbs-list" class="portal-breadcrumbs-list">
                <li class='portal-breadcrumbs-list-item'>
                    <a href='<?php echo get_page_link(55115); ?>'>Fachportale</a>
                    <span class='material-icons'>chevron_right</span>
                </li>
                <li class='portal-breadcrumbs-list-item'>
                    <a href='<?php echo get_page_link(); ?>'><?php echo $portalTitle; ?></a>
                    <span class='material-icons'>chevron_right</span>
                </li>
            </ul>
        </div>

        <div class="wlo-tile-team">
            <p>Redaktion <?php echo $portalTitle; ?>:</p>
            <?php foreach ($author_ids as $author_id) {
                echo $author_id['user_avatar'];
            } ?>
            <div class="wlo-verified-flag-container">
                <img
                    class="wlo-verified-flag"
                    src="<?php
                            echo get_template_directory_uri();
                            ?>/src/assets/img/QS-Faehnchen.svg">
            </div>
        </div>
    </div>
</div>

<?php if (!empty($collectionId)) { ?>
    <script>
        const appendPostElement = <?php echo $appendPostElement ? 'true' : 'false'; ?>;
        jQuery(document).ready(() => {
            const list = jQuery('#portal-breadcrumbs-list');
            jQuery.getJSON('/wp-json/portal/v1/breadcrumbs', {
                collectionId: '<?php echo $collectionId; ?>',
            }, (response) => {
                const listItem = list.children().first();
                if (appendPostElement) {
                    list.children().first().remove();
                } else {
                    list.children().remove();
                }
                for (const breadcrumb of response.reverse()) {
                    const breadcrumbItem = listItem.clone();
                    const linkElement = breadcrumbItem.find('a');
                    linkElement.attr('href', breadcrumb[1]);
                    linkElement.text(breadcrumb[0]);
                    list.prepend(breadcrumbItem);
                }
            });
        });
    </script>
<?php } ?>