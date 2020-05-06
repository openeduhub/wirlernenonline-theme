
    <?php
    $posts = get_posts(array(
        'post_type' => array('edusource'),
        'numberposts' => -1,
        'post_status'       => 'any',
    ));
    if($posts)
    {

    // get the school subjects via graphQL
    $data = '{ search { facets { facet buckets { key doc_count } } } }';
    $curl_post_data = array("query" => $data);
    $data_string =  json_encode($curl_post_data);
    $url = 'https://suche.wirlernenonline.de/relay/graphql';

    try {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json; charset=utf-8'
            )
        );
        $response = curl_exec($curl);
        if($response === false) {
            echo 'curl error';
            trigger_error(curl_error($curl), E_USER_WARNING);
            return false;
        }
    } catch (Exception $e) {
        echo 'curl error: '.$e->getMessage();
        trigger_error($e->getMessage(), E_USER_WARNING);
        return false;
    }
    curl_close($curl);
    $response = json_decode($response);
    $sources = $response->data->search->facets[5]->buckets;

        ?>
        <table class="wlo_source_dev js-sort-table">
            <thead>
            <tr>
                <th class="wlo_big_header js-sort-string clickable" colspan="2">Quelle</th>
                <th class="wlo_big_header js-sort-string clickable" colspan="2">In unserer Suche</th>
                <th class="wlo_big_header js-sort-string clickable">1. Schritt: Check</th>
                <th class="wlo_big_header js-sort-string clickable" colspan="4">2. Schritt: Analyse</th>
                <th class="wlo_big_header js-sort-string clickable" colspan="4">3. Schritt: Erschließungs-Zustand</th>
                <th class="wlo_big_header js-sort-number clickable">Fortschritt</th>
            </tr>
            <tr>
                <th class="clickable">Name</th>
                <th class="clickable js-sort-number">Inhalte</th>

                <th class="clickable">Quelle</th>
                <th class="clickable">Quellen-Inhalten</th>

                <th class="clickable"></th>

                <th class="clickable">Redaktion</th>
                <th class="clickable">Jurist</th>
                <th class="clickable">IT</th>
                <th class="clickable">Finanzierung</th>

                <th class="clickable">Rohdaten</th>
                <th class="clickable">Rohdaten-Test</th>
                <th class="clickable">1. Verbesserung</th>
                <th class="clickable">2. Verbesserung</th>

                <th class="clickable"></th>
            </tr>
            </thead>
            <tbody>

                <?php foreach($posts as $post) { ?>
                <tr>
                    <?php

                    //update_field( 'verfuegbar_als_quelle', 1, $post->ID );

                    $positiv = 0;

                    echo '<td class="wlo_name"><a target="_blank" href="'.get_page_link($post->ID).'">'.get_the_title($post->ID).'</a></td>';

                    if ( get_field('filtername', $post->ID) ){
                        $sourceName = get_field('filtername', $post->ID);
                        $sourceCount = 0;
                        foreach ($sources as $source){
                            if ($source->key == $sourceName){
                                $sourceCount = $source->doc_count;
                            }
                        }
                        echo '<td class="wlo_count">'.$sourceCount.'</td>';
                    }else{
                        echo '<td class="wlo_count"></td>';
                    }



                    //In unserer Suche
                    if( get_field('verfuegbar_als_quelle', $post->ID) ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    if( get_field('verfuegbar_mit_inhalten', $post->ID) ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }

                    //Vorprüfung abgeschlossen
                    if( get_field('redaktion', $post->ID) && get_field('recht', $post->ID) && get_field('it', $post->ID) && get_field('projektmanagement', $post->ID) ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';;
                    }

                    //Planung
                    if( get_field('redaktion_planung', $post->ID) ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    if( get_field('recht_planung', $post->ID) ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    if( get_field('it_planung', $post->ID) ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    if( get_field('projektmanagement_planung', $post->ID) ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }

                    //Erschließung
                    if( get_field('roherschliesung', $post->ID) ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    if( get_field('test', $post->ID) ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    if( get_field('metadatenverbesserung', $post->ID) ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';;
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    if( get_field('metadatenverbesserung_manuell', $post->ID) ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }

                    //Auswertung
                    echo '<td>'.$positiv.'</td>';
                    ?>

                <?php }
                echo '</tr>';
                }
                ?>

            </tbody>
        </table>

