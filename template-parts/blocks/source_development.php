
    <?php
    $posts = get_posts(array(
        'post_type' => array('edusource'),
        'numberposts' => -1,
        'post_status'       => 'any',
    ));
    if($posts)
    {
        ?>
        <table class="wlo_source_dev js-sort-table">
            <thead>
            <tr>
                <th class="wlo_big_header js-sort-string clickable">Quelle</th>
                <th class="wlo_big_header" colspan="2">In unserer Suche</th>
                <th class="wlo_big_header">1. Check done</th>
                <th class="wlo_big_header" colspan="4">2. Machbar</th>
                <th class="wlo_big_header" colspan="4">3. Erschlossen</th>
                <th class="wlo_big_header js-sort-number clickable">Fortschritt</th>
            </tr>
            <tr>
                <th></th>

                <th>Quelle</th>
                <th>Quellen-Inhalten</th>

                <th></th>

                <th>Redaktion</th>
                <th>Jurist</th>
                <th>IT</th>
                <th>Finanzierung</th>

                <th>Rohdaten</th>
                <th>Rohdaten-Test</th>
                <th>1. Verbesserung</th>
                <th>2. Verbesserung</th>

                <th></th>
            </tr>
            </thead>
            <tbody>

                <?php foreach($posts as $post) { ?>
                <tr>
                    <?php

                    //update_field( 'verfuegbar_als_quelle', 1, $post->ID );

                    $positiv = 0;

                    echo '<td class="wlo_name"><a target="_blank" href="'.get_page_link($post->ID).'">'.get_the_title($post->ID).'</a></td>';

                    //In unserer Suche
                    if( get_field('verfuegbar_als_quelle', $post->ID) ) {
                        echo '<td class="wloTrue">drin</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse">-</td>';
                    }
                    if( get_field('verfuegbar_mit_inhalten', $post->ID) ) {
                        echo '<td class="wloTrue">drin</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse">-</td>';
                    }

                    //Vorprüfung abgeschlossen
                    if( get_field('redaktion', $post->ID) && get_field('recht', $post->ID) && get_field('it', $post->ID) && get_field('projektmanagement', $post->ID) ) {
                        echo '<td class="wloTrue">GO</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse">-</td>';
                    }

                    //Planung
                    if( get_field('redaktion_planung', $post->ID) ) {
                        echo '<td class="wloTrue">Go</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse">-</td>';
                    }
                    if( get_field('recht_planung', $post->ID) ) {
                        echo '<td class="wloTrue">Go</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse">-</td>';
                    }
                    if( get_field('it_planung', $post->ID) ) {
                        echo '<td class="wloTrue">Go</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse">-</td>';
                    }
                    if( get_field('projektmanagement_planung', $post->ID) ) {
                        echo '<td class="wloTrue">Go</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse">-</td>';
                    }

                    //Erschließung
                    if( get_field('roherschliesung', $post->ID) ) {
                        echo '<td class="wloTrue">Done</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse">-</td>';
                    }
                    if( get_field('test', $post->ID) ) {
                        echo '<td class="wloTrue">Done</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse">-</td>';
                    }
                    if( get_field('metadatenverbesserung', $post->ID) ) {
                        echo '<td class="wloTrue">Done</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse">-</td>';
                    }
                    if( get_field('metadatenverbesserung_manuell', $post->ID) ) {
                        echo '<td class="wloTrue">-</td>';
                        $positiv++;
                    }else{
                        echo '<td class="wloFalse">-</td>';
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

