<?php
function acf_autoload_discipline_field_choices( $field ) {

    // reset choices
    $field['choices'] = array();

    // Get Select-Field Options from Vocab Scheme
    $json = file_get_contents('https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/discipline/index.json');
    $obj = json_decode($json);

    $choices = $obj->hasTopConcept;
    if( is_array($choices) ) {

        foreach( $choices as $choice ) {

            $idStr = $choice->id;
            $lastSlash = strrpos($idStr, "/");
            $idNr = substr($idStr, $lastSlash + 1);
            $field['choices'][ intval($idNr) ] = $choice->prefLabel->de;

        }

    }
    return $field;

}

add_filter('acf/load_field/name=discipline', 'acf_autoload_discipline_field_choices');

function acf_autoload_edu_context_field_choices( $field ) {

    // reset choices
    $field['choices'] = array();

    // Get Select-Field Options from Vocab Scheme
    $json = file_get_contents('https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/educationalContext/index.json');
    $obj = json_decode($json);

    $choices = $obj->hasTopConcept;
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

function acf_autoload_intended_user_role_field_choices( $field ) {

    // reset choices
    $field['choices'] = array();

    // Get Select-Field Options from Vocab Scheme
    $json = file_get_contents('https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/intendedEndUserRole/index.json');
    $obj = json_decode($json);

    $choices = $obj->hasTopConcept;
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

    // Get Select-Field Options from Vocab Scheme
    $json = file_get_contents('https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/learningResourceType/index.json');
    $obj = json_decode($json);

    $choices = $obj->hasTopConcept;
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

add_filter('acf/load_field/name=learningResourceTypes', 'acf_autoload_learning_resource_type_choices');

