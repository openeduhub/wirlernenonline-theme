<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Quellenübersicht-Tabelle</div>';
} ?>
<?php

$url = WLO_REPO . 'rest/search/v1/queriesV2/-home-/mds_oeh/ngsearch/?maxItems=5000&skipCount=0&propertyFilter=-all-';
$search_criterias = '{"criterias":[{"property":"ccm:objecttype","values":["SOURCE"]}],"facettes":[]}';
$response = callWloRestApi($url, 'POST', $search_criterias);

    if($response) {

        //$data = '{facet(facet:source){buckets{key, doc_count}}}';
        $data = '{
                  facet(
                    facet: source
                    skipOutputMapping: true
                  ) {
                    buckets {
                      key
                      doc_count
                    }
                  }
                }';
        $sources = callWloGraphApi($data)->data->facet->buckets;
        //var_dump($sources);

        //echo "<script>console.log('".print_r($sources, true)."')</script>";

        ?>

    <div class="filterable">
        <table class="wlo_source_dev table js-sort-table">
            <thead>
            <tr>
                <th class="wlo_big_header">Vorhandene Quellen</th>
                <th class="wlo_big_header js-sort-number">Erfasste Inhalte</th>
                <th class="wlo_big_header">Fächerzuordnung</th>
                <th class="wlo_big_header">Erschließungs-Status</th>
                <th class="wlo_big_header">Qualitätskriterien-Check</th>
            </tr>
            <tr class="filters">
                <th><input type="text" placeholder="Suche..." disabled></th>
                <th class="js-sort-number"></th>
                <th><input type="text" placeholder="Suche..." disabled></th>
                <th><input type="text" placeholder="Suche..." disabled></th>
                <th></th>
            </tr>

            </thead>
            <tbody>

                <?php
                foreach($response->nodes as $reference) {
                    $prop = $reference->properties;
                    ?>
                <tr>
                    <?php

                    echo '<td class="wlo_name"><a target="_blank" href="'.$reference->content->url.'">'.$reference->title.'</a></td>';

                    $sourceCount = 0;
                    if (!empty($sources)){

                        foreach ($sources as $source){
                            //if (strtolower($source->key) == strtolower($prop->{'cclom:title'}[0])){
                            if ($source->key == $prop->{'ccm:general_identifier'}[0]){
                                $sourceCount = $source->doc_count;
                            }
                        }
                    }
                    echo '<td class="wlo_count">'.$sourceCount.'</td>';
                    //echo '<script>console.log("'.$prop->{"ccm:general_identifier"}[0].'");</script>';

                    echo '<td class="wlo_subjects">';
                    if (!empty($prop->{'ccm:taxonid_DISPLAYNAME'})){

                            foreach ($prop->{'ccm:taxonid_DISPLAYNAME'} as $subject) {
                                echo '<span class="wlo_subject">' . $subject . '</span>';
                            }

                    }

                    '</td>';


                    if (empty($prop->{'ccm:editorial_checklist_DISPLAYNAME'}[0])){
                        echo '<td>1. redaktionelle Sichtung anstehend</td>';
                    }else{
                        echo '<td>'.$prop->{'ccm:editorial_checklist_DISPLAYNAME'}[0].'</td>';
                    }


                    echo '<td>';

                    // Zugangsbedingungen (Login)
                    switch ($prop->{'ccm:conditionsOfAccess'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/conditionsOfAccess/no_login':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ANMELDEN_GRUEN.svg" title="'.$prop->{'ccm:conditionsOfAccess_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/conditionsOfAccess/login_for_additional_features':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ANMELDEN_ORANGE.svg" title="'.$prop->{'ccm:conditionsOfAccess_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/conditionsOfAccess/login':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ANMELDEN_ROT.svg" title="'.$prop->{'ccm:conditionsOfAccess_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ANMELDEN_GRUEN.svg" title="Login unbekannt">';
                    }

                    // Enthält Werbung
                    switch ($prop->{'ccm:containsAdvertisement'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/containsAdvertisement/yes':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/WERBUNG_ROT.svg" title="Enthält Werbung: '.$prop->{'ccm:containsAdvertisement_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/containsAdvertisement/no':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/WERBUNG_GRUEN.svg" title="Enthält Werbung: '.$prop->{'ccm:containsAdvertisement_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/WERBUNG_GRUEN.svg" title="Werbung unbekannt">';
                    }

                    // Kosten
                    switch ($prop->{'ccm:price'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/price/no':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/KOSTEN_GRUEN.svg" title="Kosten: '.$prop->{'ccm:price_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/price/yes_for_additional':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/KOSTEN_ORANGE.svg" title="Kosten: '.$prop->{'ccm:price_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/price/yes':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/KOSTEN_ROT.svg" title="Kosten: '.$prop->{'ccm:price_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/KOSTEN_GRUEN.svg" title="Kosten unbekannt">';
                    }

                    // Barrierefreiheit
                    switch ($prop->{'ccm:accessibilitySummary'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/accessibilitySummary/a':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ACCASSIBILITY_GRUEN.svg" title="Barrierefreiheit: '.$prop->{'ccm:accessibilitySummary_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/accessibilitySummary/aa':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ACCASSIBILITY_ORANGE.svg" title="Barrierefreiheit: '.$prop->{'ccm:accessibilitySummary_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/accessibilitySummary/aaa':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ACCASSIBILITY_ROT.svg" title="Barrierefreiheit: '.$prop->{'ccm:accessibilitySummary_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ACCASSIBILITY_GRUEN.svg" title="Barrierefreiheit unbekannt">';
                    }

                    // DSGVO
                    switch ($prop->{'ccm:dataProtectionConformity'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/dataProtectionConformity/generalDataProtectionRegulation':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/DGSVO_GRUEN.svg" title="'.$prop->{'ccm:dataProtectionConformity_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/dataProtectionConformity/noGeneralDataProtectionRegulation':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/DGSVO_ROT.svg" title="'.$prop->{'ccm:dataProtectionConformity_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/DGSVO_GRUEN.svg" title="Nicht DSGVO geprüft">';
                    }

                    // FSK
                    switch ($prop->{'ccm:fskRating'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/fskRating/0':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK0.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/fskRating/6':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK6.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/fskRating/12':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK12.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/fskRating/16':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK16.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/fskRating/18':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK18.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK_Gruen.svg" title="FSK unbekannt">';
                    }

                    // OER
                    switch ($prop->{'ccm:license_oer'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/oer/0':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/OER_Gruen.svg" title="'.$prop->{'ccm:license_oer_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/oer/1':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/OER_ORANGE.svg" title="'.$prop->{'ccm:license_oer_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/oer/2':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/OER_ROT.svg" title="'.$prop->{'ccm:license_oer_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/OER_Gruen.svg" title="OER unbekannt">';
                    }


                    echo '</td>';

                }
                echo '</tr>';
                }
                ?>

            </tbody>
        </table>
</div>

<script>
    function checkval() {
        1 == jQuery("tbody tr:visible").length && "No result found" == jQuery("tbody tr:visible td").html() ? jQuery("#rowcount").html("0") : jQuery("#rowcount").html(jQuery("tr:visible").length - 1);
    }
    function filterReady() {
        const t = jQuery(".filterable")
        const e = t.find(".filters input");
        const l = t.find(".table tbody");
        1 == e.prop("disabled") ? (e.prop("disabled", !1), e.first()) : (e.val("").prop("disabled", !0), l.find(".no-result").remove(), l.find("tr").show()), jQuery("#rowcount").html(jQuery(".filterable tr").length - 1);
    }
    jQuery(document).ready(function () {
        jQuery("#rowcount").html(jQuery(".filterable tr").length - 1),
            jQuery(".filterable .filters input").keyup(function (t) {
                if ("9" != (t.keyCode || t.which)) {
                    var e = jQuery(this),
                        l = e.val().toLowerCase(),
                        n = e.parents(".filterable"),
                        i = n.find(".filters th").index(e.parents("th")),
                        r = n.find(".table"),
                        o = r.find("tbody tr"),
                        d = o.filter(function () {
                            return -1 === jQuery(this).find("td").eq(i).text().toLowerCase().indexOf(l);
                        });
                    r.find("tbody .no-result").remove(),
                        o.show(),
                        d.hide(),
                    d.length === o.length && r.find("tbody").prepend(jQuery('<tr class="no-result text-center"><td colspan="' + r.find(".filters th").length + '">No result found</td></tr>'));
                }
                jQuery("#rowcount").html(jQuery("tr:visible").length - 1), checkval();
            });
        filterReady();
    });
</script>

<?php if (is_admin()) {
    echo '</div>';
} ?>
