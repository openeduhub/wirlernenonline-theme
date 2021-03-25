<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Quellenübersicht-Tabelle</div>';
} ?>

<?php
$fachportalContentId = uniqid('fachportalContentId-');
?>


<div class="fachportal-content-block" id="<?php echo $fachportalContentId; ?>">
    <p style="text-align: center;">Quellenübersicht-Tabelle wird geladen...</p>
    <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
</div>

<script type="text/javascript" >
    jQuery(document).ready(function($) {

        var data = {
            'action': 'source_table',
        };

        jQuery.post(ajaxurl, data, function(response) {
            jQuery('#<?php echo $fachportalContentId; ?>').html(response);
        });

    });

</script>



<?php if (is_admin()) {
    echo '</div>';
} ?>
