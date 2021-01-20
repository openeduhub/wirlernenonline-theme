<?php
function acf_autoload_discipline_field_choices( $field ) {

    // reset choices
    $field['choices'] = array();

    $transient = 'vocab_discipline';
    $discipline_vocab_json = null;
    if ( false === ( $value = get_transient( $transient ) ) ) {
        // this code runs when there is no valid transient set
        // Get Select-Field Options from Vocab Scheme
        $json = file_get_contents('https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/discipline/index.json');
        $discipline_vocab_json = json_decode($json);
        set_transient( $transient, $discipline_vocab_json, 60*60*12 );
    } else{
        $discipline_vocab_json = get_transient( $transient );
    }

    $choices = $discipline_vocab_json->hasTopConcept;
    if( is_array($choices) ) {

        foreach( $choices as $choice ) {

            $idStr = $choice->id;
            $lastSlash = strrpos($idStr, "/");
            $idNr = substr($idStr, $lastSlash + 1);
            $field['choices'][ $idNr ] = $choice->prefLabel->de;

        }

    }
    return $field;

}
add_filter('acf/load_field/name=fachgebiet', 'acf_autoload_discipline_field_choices');
add_filter('acf/load_field/name=discipline', 'acf_autoload_discipline_field_choices');

function acf_autoload_edu_context_field_choices( $field ) {

    // reset choices
    $field['choices'] = array();

    $transient = 'vocab_eduContext';
    $educationalContext_vocab_json = null;
    if ( false === ( $value = get_transient( $transient ) ) ) {
        // this code runs when there is no valid transient set
        // Get Select-Field Options from Vocab Scheme
        $json = file_get_contents('https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/educationalContext/index.json');
        $educationalContext_vocab_json = json_decode($json);
        set_transient( $transient, $educationalContext_vocab_json, 60*60*12 );
    } else{
        $educationalContext_vocab_json = get_transient( $transient );
    }

    $choices = $educationalContext_vocab_json->hasTopConcept;
    if( is_array($choices) ) {

        foreach( $choices as $choice ) {

            $idStr = $choice->id;
            $lastSlash = strrpos($idStr, "/");
            $id = substr($idStr, $lastSlash + 1);
            $field['choices'][ $id ] = $choice->prefLabel->de;

        }

    }
    return $field;

}

add_filter('acf/load_field/name=educationalContext', 'acf_autoload_edu_context_field_choices');

function acf_autoload_teaching_methods_field_choices( $field ) {
    return buildTaxonomyForField(
        $field,
        'Unterrichtsmethoden',
        'teachingMethods',
        'http://w3id.org/openeduhub/vocabs-playground/teachingMethods/index.json');
}

add_filter('acf/load_field/name=teachingMethods', 'acf_autoload_teaching_methods_field_choices');

function acf_autoload_digital_competencies_field_choices( $field ) {
    return buildTaxonomyForField(
        $field,
        'Digitale Kompetenzen',
        'digitalCompetencies',
        'https://openeduhub.github.io/oeh-metadata-vocabs-playground/w3id.org/openeduhub/vocabs-playground/digitalCompetencies/index.json');
}

add_filter('acf/load_field/name=digitalCompetencies', 'acf_autoload_digital_competencies_field_choices');


function acf_autoload_intended_user_role_field_choices( $field ) {

    // reset choices
    $field['choices'] = array();

    $transient = 'vocab_intendedEndUserRole';
    $intendedEndUserRole_vocab_json = null;
    if ( false === ( $value = get_transient( $transient ) ) ) {
        // this code runs when there is no valid transient set
        // Get Select-Field Options from Vocab Scheme
        $json = file_get_contents('https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/intendedEndUserRole/index.json');
        $intendedEndUserRole_vocab_json = json_decode($json);
        set_transient( $transient, $intendedEndUserRole_vocab_json, 60*60*12 );
    } else{
        $intendedEndUserRole_vocab_json = get_transient( $transient );
    }

    $choices = $intendedEndUserRole_vocab_json->hasTopConcept;

    if( is_array($choices) ) {

        foreach( $choices as $choice ) {

            $idStr = $choice->id;
            $lastSlash = strrpos($idStr, "/");
            $id = substr($idStr, $lastSlash + 1);
            $field['choices'][ $id ] = $choice->prefLabel->de;

        }

    }
    return $field;

}

add_filter('acf/load_field/name=intendedEndUserRole', 'acf_autoload_intended_user_role_field_choices');

function acf_autoload_learning_resource_type_choices( $field ) {

    // reset choices
    $field['choices'] = array();

    $transient = 'vocab_learningResourceTypes';
    $learningResourceTypes_vocab_json = null;
    if ( false === ( $value = get_transient( $transient ) ) ) {
        // this code runs when there is no valid transient set
        // Get Select-Field Options from Vocab Scheme
        $json = file_get_contents('https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/learningResourceType/index.json');
        $learningResourceTypes_vocab_json = json_decode($json);
        set_transient( $transient, $learningResourceTypes_vocab_json, 60*60*12 );
    } else{
        $learningResourceTypes_vocab_json = get_transient( $transient );
    }


    $choices = $learningResourceTypes_vocab_json->hasTopConcept;
    if( is_array($choices) ) {

        foreach( $choices as $choice ) {

            $idStr = $choice->id;
            $lastSlash = strrpos($idStr, "/");
            $id = substr($idStr, $lastSlash + 1);
            $field['choices'][ $id ] = $choice->prefLabel->de;

        }

    }
    return $field;

};

add_filter('acf/load_field/name=learningResourceTypes', 'acf_autoload_learning_resource_type_choices');

function acf_autoload_oeh_widgets_choices( $field ) {

    // reset choices
    $field['choices'] = array();

    $transient = 'vocab_oehWidgets';
    $oehWidgets_vocab_json = null;
    if ( false === ( $value = get_transient( $transient ) ) ) {
        // this code runs when there is no valid transient set
        // Get Select-Field Options from Vocab Scheme
        $json = file_get_contents('https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/widgets/index.json');
        $oehWidgets_vocab_json = json_decode($json);
        set_transient( $transient, $oehWidgets_vocab_json, 60*60*12 );
    } else{
        $oehWidgets_vocab_json = get_transient( $transient );
    }

    $choice_topics = $oehWidgets_vocab_json->hasTopConcept;
    if( is_array($choice_topics) ) {
        foreach ($choice_topics as $choice_topic){
            $choices = $choice_topic->narrower;
            foreach( $choices as $choice ) {
                $idStr = $choice->id;
                $lastSlash = strrpos($idStr, "/");
                $id = substr($idStr, $lastSlash + 1);
                $field['choices'][ $id ] = $choice->prefLabel->de;

            }
        }


    }
    return $field;

};

add_filter('acf/load_field/name=oehWidgets', 'acf_autoload_oeh_widgets_choices');

function get_acf_post_id(){
    $postID = null;

    if (function_exists( 'acf_maybe_get_POST' ) ) {
        $postID = intval( acf_maybe_get_POST( 'post_id' ) );
    }
    else {
        global $post;
        if(!is_null($post)){
            $postID = intval($post->ID);
        }
    };

    return $postID;
}

function render_discipline_field( $field ) {
    $postID = get_acf_post_id();

    if(!is_admin() || is_null($postID))
        return;

    $disciplines = (!empty(get_post_meta($postID, 'discipline', false)[0])) ? get_post_meta($postID, 'discipline', false)[0] : null;
    if(!is_null($disciplines) && !empty($disciplines))
    {
        echo '<div class="acf-preset">';
        echo '<p class="acf-preset-desc">Voreinstellung: </p></br>';
        foreach ($field['choices'] as $value => $label) {
            if(in_array($value, $disciplines))
            {
                echo '<div class="acf-preset-field">' . $label . '</div>';
            }
        }
        echo '</div>';
    }

    return $field;
}

add_action('acf/render_field/name=discipline', 'render_discipline_field');

function render_fachgebiet_field( $field ) {
    $postID = get_acf_post_id();

    if(is_null($postID))
        return;

    $disciplines = (!empty(get_query_var('discipline', null))) ? explode(";", get_query_var('discipline', null)) : [];
    if(!is_null($disciplines) && !empty($disciplines))
    {
        foreach ($field['choices'] as $value => $label) {
            if(in_array($value, $disciplines))
            {
                $field['default_value'] = $value;
            }
        }
    }

    return $field;
}

add_action('acf/load_field/name=fachgebiet', 'render_fachgebiet_field');

function render_educationalContext_field( $field ) {
    $postID = get_acf_post_id();

    if(!is_admin() || is_null($postID))
        return;

    $eduContexts = (!empty(get_post_meta($postID, 'educationalContext', false)[0])) ? get_post_meta($postID, 'educationalContext', false)[0] : null;
    if(!is_null($eduContexts) && !empty($eduContexts))
    {
        echo '<div class="acf-preset">';
        echo '<p class="acf-preset-desc">Voreinstellung: </p><br/>';
        foreach ($field['choices'] as $value => $label) {
            if(in_array($value, $eduContexts))
            {
                echo '<div class="acf-preset-field">' . $label . '</div>';
            }
        }
        echo '</div>';
    }

    return $field;
}

add_action('acf/render_field/name=educationalContext', 'render_educationalContext_field');

function render_intendedEndUserRole_field( $field ) {
    $postID = get_acf_post_id();

    if(!is_admin() || is_null($postID))
        return;

    $intendedEndUserRoles = (!empty(get_post_meta($postID, 'intendedEndUserRole', false)[0])) ? get_post_meta($postID, 'intendedEndUserRole', false)[0] : null;
    if(!is_null($intendedEndUserRoles) && !empty($intendedEndUserRoles))
    {
        echo '<div class="acf-preset">';
        echo '<p class="acf-preset-desc">Voreinstellung: </p><br/>';
        foreach ($field['choices'] as $value => $label) {
            if(in_array($value, $intendedEndUserRoles))
            {
                echo '<div class="acf-preset-field">' . $label . '</div>';
            }
        }
        echo '</div>';
    }

    return $field;
}
add_action('acf/render_field/name=intendedEndUserRole', 'render_intendedEndUserRole_field');

function render_topic_field( $field ) {
    $postID = get_acf_post_id();

    if(!is_admin() || is_null($postID))
        return;

    $topic = (!empty(get_post_meta($postID, 'topic', false)[0])) ? get_post_meta($postID, 'topic', true) : null;
    if(!is_null($topic) && !empty($topic))
    {
        echo '<div class="acf-preset">';
        echo '<p class="acf-preset-desc">Voreinstellung: </p><br/>';
        echo '<div class="acf-preset-field">' . $topic . '</div>';
        echo '</div>';
    }

    return $field;
}
add_action('acf/render_field/name=topic', 'render_topic_field');

function render_collection_url_field( $field ) {
    $postID = get_acf_post_id();

    if(!is_admin() || is_null($postID))
        return;

    $collection_url = (!empty(get_post_meta($postID, 'collection_url', false)[0])) ? get_post_meta($postID, 'collection_url', true) : null;
    if(!is_null($collection_url) && !empty($collection_url))
    {
        echo '<div class="acf-preset">';
        echo '<p class="acf-preset-desc">Voreinstellung: </p><br/>';
        echo '<div class="acf-preset-field">' . $collection_url . '</div>';
        echo '</div>';
    }

    return $field;
}
add_action('acf/render_field/name=collection_url', 'render_collection_url_field');

function render_oer_field( $field ) {
    $postID = get_acf_post_id();

    if(!is_admin() || is_null($postID))
        return;

    $oer = (!empty(get_post_meta($postID, 'oer', false)[0])) ? get_post_meta($postID, 'oer', true) : null;
    if(!is_null($oer) && !empty($oer))
    {
        echo '<div class="acf-preset">';
        echo '<p class="acf-preset-desc">Voreinstellung: </p><br/>';
        echo '<div class="acf-preset-field">' . (($oer == true) ? 'Ja' : 'Nein') . '</div>';
        echo '</div>';
    }

    return $field;
}
add_action('acf/render_field/name=oer', 'render_oer_field');
