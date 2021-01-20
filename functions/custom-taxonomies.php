<?php

function recurseBuildTaxonomy($taxonomy, $skos, int $parent){
    $taxonomyId = $taxonomy->name;
    foreach($skos as $s){
        $idStr = $s->id;
        $lastSlash = strrpos($idStr, "/");
        $id = substr($idStr, $lastSlash + 1);
        $term = get_term_by('slug', $id, $taxonomyId);
        if($term){
            $parent2 = $term->term_id;
            wp_update_term($term->term_id, $taxonomyId, [
                'parent' => $parent,
                'slug' => $id,
                'name' => $s->prefLabel->de
            ]);
        } else {
            $parent2 = wp_insert_term($s->prefLabel->de, $taxonomyId, [ 'slug' => $id ])['term_id'];
        }
        if($s -> narrower){
            recurseBuildTaxonomy($taxonomy, $s -> narrower, $parent2);
        }

    }
}
function buildTaxonomyForField($field, $taxonomyName, $taxonomyId, $vocabUrl){
    $taxonomy=register_taxonomy($taxonomyId, 'method', array(
        'hierarchical' => true,
        'labels' => array(
            'name' => _x( $taxonomyName, 'taxonomy general name' ),
        ),
        'show_ui' => false,
        'show_in_rest' => true,
        'show_admin_column' => false,
        'query_var' => true,
    ));
    $transient = 'vocab_'.$taxonomyId;
    $vocab_json = null;
    if (false === ( $value = get_transient( $transient ) ) ) {
        // this code runs when there is no valid transient set
        // Get Select-Field Options from Vocab Scheme
        $json = file_get_contents($vocabUrl);
        $vocab_json = json_decode($json);
        set_transient( $transient, $vocab_json, 60*60*12 );
    } else{
        //$vocab_json = get_transient( $transient );
        $field['taxonomy'] = $taxonomyId;
        return $field;
    }
    // cleanup terms: NO! Will invalid all previously set values!!!
    /*
    $terms = get_terms( array(
        'taxonomy' => $taxonomyId,
        'hide_empty' => false,
    ));
    foreach ( $terms as $value ) {
        wp_delete_term( $value->term_id, $taxonomyId);
    }
    */

    $choices = $vocab_json->hasTopConcept;
    if( is_array($choices) ) {
        recurseBuildTaxonomy($taxonomy, $choices, 0);
    }
    $field['taxonomy'] = $taxonomyId;
    return $field;

}