<?php

require_once("../../../../wp-load.php");

$maxItems = 25;
$skipCount = 0;

if ($_POST["maxItems"] ){
    $maxItems = $_POST["maxItems"];
}
if ($_POST["skipCount"]){
    $skipCount = $_POST["skipCount"];
}

echo getTableData($maxItems, $skipCount);
die();

function getTableData($maxItems, $skipCount){
    $url = WLO_REPO . 'rest/search/v1/queriesV2/-home-/mds_oeh/ngsearch/?maxItems='.$maxItems.'&skipCount='.$skipCount.'&propertyFilter=-all-';
    $search_criterias = '{"criterias":[{"property":"ccm:objecttype","values":["SOURCE"]}],"facettes":[]}';
    $response = callWloRestApi($url, 'POST', $search_criterias);

    $tableData = array('data');

    if($response) {

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

        foreach($response->nodes as $reference) {
            $prop = $reference->properties;
            $row = array();

            // Vorhandene Quellen
            $row['title'] = '<div class="wlo_name"><a target="_blank" href="'.$reference->content->url.'">'.$reference->title.'</a></div>';

            // Erfasste Inhalte
            $sourceCount = 0;
            if (!empty($sources)){

                foreach ($sources as $source){
                    //if (strtolower($source->key) == strtolower($prop->{'cclom:title'}[0])){
                    if ($source->key == $prop->{'ccm:general_identifier'}[0]){
                        $sourceCount = $source->doc_count;
                    }
                }
            }
            $row['count'] = '<div class="wlo_count">'.$sourceCount.'</div>';

            // Fächerzuordnung
            $subjects = '<div class="wlo_subjects">';
            if (!empty($prop->{'ccm:taxonid_DISPLAYNAME'})){
                foreach ($prop->{'ccm:taxonid_DISPLAYNAME'} as $subject) {
                    $subjects .= '<span class="wlo_subject">' . $subject . '</span>';
                }
            }
            $subjects .= '</div>';
            $row['subjects'] = $subjects;

            // Erschließungs-Status
            $status = '';
            if (empty($prop->{'ccm:editorial_checklist_DISPLAYNAME'}[0])){
                $status = '1. redaktionelle Sichtung anstehend';
            }else{
                $status = $prop->{'ccm:editorial_checklist_DISPLAYNAME'}[0];
            }

            $row['status'] = $status;

            // Qualitätskriterien-Check
            $check = '<div class="wlo_status">';

            // Zugangsbedingungen (Login)
            switch ($prop->{'ccm:conditionsOfAccess'}[0]){
                case 'http://w3id.org/openeduhub/vocabs/conditionsOfAccess/no_login':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ANMELDEN_GRUEN.svg" title="'.$prop->{'ccm:conditionsOfAccess_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/conditionsOfAccess/login_for_additional_features':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ANMELDEN_ORANGE.svg" title="'.$prop->{'ccm:conditionsOfAccess_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/conditionsOfAccess/login':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ANMELDEN_ROT.svg" title="'.$prop->{'ccm:conditionsOfAccess_DISPLAYNAME'}[0].'">';
                    break;
                default:
                    $check .= '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ANMELDEN_GRUEN.svg" title="Login unbekannt">';
            }

            // Enthält Werbung
            switch ($prop->{'ccm:containsAdvertisement'}[0]){
                case 'http://w3id.org/openeduhub/vocabs/containsAdvertisement/yes':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/WERBUNG_ROT.svg" title="Enthält Werbung: '.$prop->{'ccm:containsAdvertisement_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/containsAdvertisement/no':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/WERBUNG_GRUEN.svg" title="Enthält Werbung: '.$prop->{'ccm:containsAdvertisement_DISPLAYNAME'}[0].'">';
                    break;
                default:
                    $check .= '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/WERBUNG_GRUEN.svg" title="Werbung unbekannt">';
            }

            // Kosten
            switch ($prop->{'ccm:price'}[0]){
                case 'http://w3id.org/openeduhub/vocabs/price/no':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/KOSTEN_GRUEN.svg" title="Kosten: '.$prop->{'ccm:price_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/price/yes_for_additional':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/KOSTEN_ORANGE.svg" title="Kosten: '.$prop->{'ccm:price_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/price/yes':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/KOSTEN_ROT.svg" title="Kosten: '.$prop->{'ccm:price_DISPLAYNAME'}[0].'">';
                    break;
                default:
                    $check .= '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/KOSTEN_GRUEN.svg" title="Kosten unbekannt">';
            }

            // Barrierefreiheit
            switch ($prop->{'ccm:accessibilitySummary'}[0]){
                case 'http://w3id.org/openeduhub/vocabs/accessibilitySummary/a':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ACCASSIBILITY_GRUEN.svg" title="Barrierefreiheit: '.$prop->{'ccm:accessibilitySummary_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/accessibilitySummary/aa':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ACCASSIBILITY_ORANGE.svg" title="Barrierefreiheit: '.$prop->{'ccm:accessibilitySummary_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/accessibilitySummary/aaa':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ACCASSIBILITY_ROT.svg" title="Barrierefreiheit: '.$prop->{'ccm:accessibilitySummary_DISPLAYNAME'}[0].'">';
                    break;
                default:
                    $check .= '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ACCASSIBILITY_GRUEN.svg" title="Barrierefreiheit unbekannt">';
            }

            // DSGVO
            switch ($prop->{'ccm:dataProtectionConformity'}[0]){
                case 'http://w3id.org/openeduhub/vocabs/dataProtectionConformity/generalDataProtectionRegulation':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/DGSVO_GRUEN.svg" title="'.$prop->{'ccm:dataProtectionConformity_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/dataProtectionConformity/noGeneralDataProtectionRegulation':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/DGSVO_ROT.svg" title="'.$prop->{'ccm:dataProtectionConformity_DISPLAYNAME'}[0].'">';
                    break;
                default:
                    $check .= '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/DGSVO_GRUEN.svg" title="Nicht DSGVO geprüft">';
            }

            // FSK
            switch ($prop->{'ccm:fskRating'}[0]){
                case 'http://w3id.org/openeduhub/vocabs/fskRating/0':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK0.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/fskRating/6':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK6.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/fskRating/12':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK12.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/fskRating/16':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK16.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/fskRating/18':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK18.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                    break;
                default:
                    $check .= '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK_Gruen.svg" title="FSK unbekannt">';
            }

            // OER
            switch ($prop->{'ccm:license_oer'}[0]){
                case 'http://w3id.org/openeduhub/vocabs/oer/0':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/OER_Gruen.svg" title="'.$prop->{'ccm:license_oer_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/oer/1':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/OER_ORANGE.svg" title="'.$prop->{'ccm:license_oer_DISPLAYNAME'}[0].'">';
                    break;
                case 'http://w3id.org/openeduhub/vocabs/oer/2':
                    $check .= '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/OER_ROT.svg" title="'.$prop->{'ccm:license_oer_DISPLAYNAME'}[0].'">';
                    break;
                default:
                    $check .= '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/OER_Gruen.svg" title="OER unbekannt">';
            }
            $check .= '</div>';
            $row['check'] = $check;

            $tableData['data'][] = $row;
        }

    }
    return json_encode($tableData);
}
