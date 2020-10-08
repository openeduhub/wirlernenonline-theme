<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Ergebniss Auswahl</div>';
};
?>

<div class="portal_switcher">
    <button class="portal_switch_button active" id="portal-showSearch" onclick="showSearch()">Suchergebnisse</button>
    <button class="portal_switch_button" id="portal-showQuality" onclick="showQuality()">Qualitätsgesicherte Inhalte</button>
</div>

<script type="application/javascript">
    function showQuality() {
        jQuery('.portal_search_results').hide('slow');
        jQuery('.portal_collection_group').show('slow');
        jQuery('#portal-showQuality').addClass('active');
        jQuery('#portal-showSearch').removeClass('active');
        jQuery(window).trigger('resize');
    }
    
    function showSearch() {
        jQuery('.portal_search_results').show('slow');
        jQuery('.portal_collection_group').hide('slow');
        jQuery('#portal-showQuality').removeClass('active');
        jQuery('#portal-showSearch').addClass('active');
    }
</script>

<?php if (is_admin()) {
    echo '</div>';
}; ?>

