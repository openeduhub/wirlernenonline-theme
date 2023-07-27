
    <?php

    $post_types = array();
    foreach (get_field('type') as $type){
        $post_types[] = $type['value'];
    }

    if (get_field('post_status')){
        $post_status = array();
        foreach (get_field('post_status') as $status){
            $post_status[] = $status['value'];
        }
    }else{
        $post_status = 'any';
    }


    $posts = get_posts(array(
        'post_type' => $post_types,
        'numberposts' => -1,
        'post_status'       => $post_status,
    ));
    if($posts) {
        $type = get_field('type')[0]['label'];
        if (count($post_types) > 1){
            $type = 'Quellen / Tools';
        }

        // get the school subjects via graphQL
        $data = '{facet(facet:source){buckets{key, doc_count}}}';
        $sources = callWloGraphApi($data)->data->facet->buckets;


        ?>
        <table class="wlo_source_dev js-sort-table">
            <thead>
            <tr>
                <th class="wlo_big_header" colspan="3"><?php echo $type; ?></th>
                <th class="wlo_big_header" colspan="2">In unserer Suche</th>
                <th class="wlo_big_header">1. Schritt: Check</th>
                <th class="wlo_big_header" colspan="4">2. Schritt: Analyse</th>
                <th class="wlo_big_header" colspan="4">3. Schritt: Erschließungs-Zustand</th>
                <th class="wlo_big_header">Fortschritt</th>
            </tr>
            <tr>
                <th class="clickable js-sort-string">Name</th>
                <th class="clickable js-sort-string">OER</th>
                <th class="clickable js-sort-number">Inhalte</th>

                <th class="clickable js-sort-string">Quelle</th>
                <th class="clickable js-sort-string">Quellen-Inhalten</th>

                <th class="clickable js-sort-string">Vorprüfung komplett</th>

                <th class="clickable js-sort-string">Redaktion</th>
                <th class="clickable js-sort-string">Jurist</th>
                <th class="clickable js-sort-string">IT</th>
                <th class="clickable js-sort-string">Finanzierung</th>

                <th class="clickable js-sort-string">Rohdaten</th>
                <th class="clickable js-sort-string">Rohdaten-Test</th>
                <th class="clickable js-sort-string">1. Verbesserung</th>
                <th class="clickable js-sort-string">2. Verbesserung</th>

                <th class="clickable js-sort-number"></th>
            </tr>
            </thead>
            <tbody>

                <?php foreach($posts as $post) { ?>
                <tr>
                    <?php

                    /*
                     * $values = array(
                        'verfuegbar_als_quelle'	=>	1
                    );
                    $values2 = array(
                        'redaktion_planung'	=>	1
                    );
                    update_field( 'verfuegbar_als_quelle_group', $values, $post->ID );
                    update_field( 'redaktion_planung_group', $values2, $post->ID );
                    */

                    $positiv = 0;

                    echo '<td class="wlo_name"><a target="_blank" href="'.get_page_link($post->ID).'">'.get_the_title($post->ID).'</a></td>';

                    if ( get_field('licence', $post->ID) ){
                        $licences = get_field( 'licence', $post->ID );
                        if( $licences ){
                            foreach ($licences as $licence){
                                switch ($licence['value']){
                                    case 10:
                                        echo '<td class="wlo-green">'.$licence['label'].'</td>';
                                        break;
                                    case 11:
                                        echo '<td class="wlo-yellow">'.$licence['label'].'</td>';
                                        break;
                                    case 12:
                                        echo '<td class="wlo-red">'.$licence['label'].'</td>';
                                        break;
                                }
                            }
                        }
                    }else{
                        echo '<td></td>';
                    }

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
                    $verfuegbar_als_quelle = get_field('verfuegbar_als_quelle_group', $post->ID);
                    if( $verfuegbar_als_quelle['verfuegbar_als_quelle'] ) {
                        if ($verfuegbar_als_quelle['verfuegbar_als_quelle_date']){
                            echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja <span class="wlo_date">('.$verfuegbar_als_quelle['verfuegbar_als_quelle_date'].')</span></td>';
                        }else{
                            echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        }
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    $verfuegbar_mit_inhalten = get_field('verfuegbar_mit_inhalten_group', $post->ID);
                    if( $verfuegbar_mit_inhalten['verfuegbar_mit_inhalten'] ) {
                        if ($verfuegbar_mit_inhalten['verfuegbar_mit_inhalten_date']){
                            echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja <span class="wlo_date">('.$verfuegbar_mit_inhalten['verfuegbar_mit_inhalten_date'].')</span></td>';
                        }else{
                            echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        }
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }

                    //Vorprüfung abgeschlossen
                    $redaktion = get_field('redaktion_group', $post->ID);
                    $recht = get_field('recht_group', $post->ID);
                    $it = get_field('it_group', $post->ID);
                    $projektmanagement = get_field('projektmanagement_group', $post->ID);
                    if( $redaktion['redaktion'] && $recht['recht'] && $it['it'] && $projektmanagement['projektmanagement'] ) {
                        if ($redaktion['redaktion_date']){
                            echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja <span class="wlo_date">('.$redaktion['redaktion_date'].')</span></td>';
                        }else{
                            echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        }
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';;
                    }

                    //Planung
                    $redaktion_planung = get_field('redaktion_planung_group', $post->ID);
                    if( $redaktion_planung['redaktion_planung'] ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    $recht_planung = get_field('recht_planung_group', $post->ID);
                    if( $recht_planung['recht_planung'] ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    $it_planung = get_field('it_planung_group', $post->ID);
                    if( $it_planung['it_planung'] ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    $projektmanagement_planung = get_field('projektmanagement_planung_group', $post->ID);
                    if( $projektmanagement_planung['projektmanagement_planung'] ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }

                    //Erschließung
                    $roherschliesung = get_field('roherschliesung_group', $post->ID);
                    if( $roherschliesung['roherschliesung'] ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    $test = get_field('test_group', $post->ID);
                    if( $test['test'] ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    $metadatenverbesserung = get_field('metadatenverbesserung_group', $post->ID);
                    if( $metadatenverbesserung['metadatenverbesserung'] ) {
                        echo '<td class="wloTrue"><span class="wlo_checkmark">&#10004;</span>Ja</td>';;
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse"><span class="wlo_error">×</span>Nein</td>';
                    }
                    $metadatenverbesserung_manuell = get_field('metadatenverbesserung_manuell_group', $post->ID);
                    if( $metadatenverbesserung_manuell['metadatenverbesserung_manuell'] ) {
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

