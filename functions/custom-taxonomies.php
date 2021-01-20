<?php

function recurseBuildTaxonomy($taxonomyId, $skos, int $parent){
    foreach($skos as $s){
        $idStr = $s->id;
        $lastSlash = strrpos($idStr, "/");
        $id = substr($idStr, $lastSlash + 1);
        //$field['choices'][ $id ] = $choice->prefLabel->de;
        $parent = wp_insert_term($s->prefLabel->de, $taxonomyId, [ 'slug' => $id, 'parent' => $parent])['term_id'];
        if($s -> narrower){
            recurseBuildTaxonomy($taxonomyId, $s -> narrower, $parent);
        }
    }
}
function buildTaxonomyForField($field, $taxonomyName, $taxonomyId, $vocabUrl){

    $transient = 'vocab_'.$taxonomyId;
    $vocab_json = null;
    if (false === ( $value = get_transient( $transient ) ) ) {
        // this code runs when there is no valid transient set
        // Get Select-Field Options from Vocab Scheme
        $json = file_get_contents($vocabUrl);
        $vocab_json = json_decode($json);
        set_transient( $transient, $vocab_json, 60*60*12 );
    } else{
        $field['taxonomy'] = $taxonomyId;
        return;
    }

    register_taxonomy($taxonomyId, 'method', array(
        'hierarchical' => true,
        'labels' => array(
            'name' => _x( $taxonomyName, 'taxonomy general name' ),
        ),
        'show_ui' => false,
        'show_in_rest' => true,
        'show_admin_column' => false,
        'query_var' => true,
    ));

    // cleanup terms

    $terms = get_terms( array(
        'taxonomy' => $taxonomyId,
        'hide_empty' => false,
    ));
    foreach ( $terms as $value ) {
        wp_delete_term( $value->term_id, $taxonomyId);
    }

    $choices = $vocab_json->hasTopConcept;
    if( is_array($choices) ) {
        foreach( $choices as $choice ) {

            $idStr = $choice->id;
            $lastSlash = strrpos($idStr, "/");
            $id = substr($idStr, $lastSlash + 1);
            //$field['choices'][ $id ] = $choice->prefLabel->de;
            $parent = wp_insert_term($choice->prefLabel->de, $taxonomyId, [ 'slug' => $id ])['term_id'];
            if($choice -> narrower){
                recurseBuildTaxonomy($taxonomyId, $choice -> narrower, $parent);
            }
        }
    }
    $field['taxonomy'] = $taxonomyId;
    return $field;

}