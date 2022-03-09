<?php

function wlo_add_swimlane_content($contentArray, $slidesToShow = 4, $slidesToScroll = 4, $contentInfo){
    $sliderId = uniqid('slider-');
    $showSliderDots = 'true';
    if (count($contentArray) <= 4 && $slidesToShow >= 3){
        $showSliderDots = 'false';
    }

    $content = '';
    $content .= '<div class="content" id="'.$sliderId.'">';

    if (!empty($contentArray)){
        foreach ($contentArray as $contentItem) {
            $slideCss = $slidesToShow == 1 ? 'margin: 12px 110px; max-width: 350px;' : '';
            $data = '';
            if (!empty($contentItem['educationalcontext'])){
                $data .= 'data-educationalcontext="';
                foreach ($contentItem['educationalcontext'] as $item) {
                    $data .= preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($item)).' ';
                }
                $data .= '"';
            }
            if (!empty($contentItem['enduserrole'])){
                $data .= ' data-enduserrole="';
                foreach ($contentItem['enduserrole'] as $item) {
                    $data .= preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($item)).' ';
                }
                $data .= '"';
            }
            if ($contentItem['oer']){
                $data .= ' data-oer="oer"';
            }else{
                $data .= ' data-oer="no-oer"';
            }

            $content .= '<div class="widget-content" '.$data.' style="'.$slideCss.'">';
            $content .= '<button onclick="showContentPopup(\''.$contentItem['id'].'\')">';
            if (!empty($contentItem['image_url'])) {
                $content .= '<img class="main-image" src="'.$contentItem['image_url'].'&crop=true&maxWidth=300&maxHeight=300" alt="Cover: '.$contentItem['title'].'">';
            }
            $content .= '<div class="content-info">';
            $content .= '<div class="content-header">';
            if ($contentItem['source'] && false){
                $content .= '<p class="content-source">'.$contentItem['source'].'</p>';
            }
            $content .= '<img class="badge" src="'.get_template_directory_uri().'/src/assets/img/badge_green.svg"  alt="Auszeichnung: geprüfter Inhalt">';
            if ($contentItem['oer']){
                $content .= '<div class="badge ">OER</div>';
            }
            $content .= '</div>';
            $content .= '<div class="content-title">'.$contentItem['title'].'</div>';
            $content .= '<p class="content-description">'.$contentItem['description'].'</p>';
            $content .= '<div class="content-meta">';
            if (!empty($contentItem['resourcetype'])){
                $content .= '<img src="'. get_template_directory_uri() .'/src/assets/img/img_icon.svg" alt="Materialart">';
                $content .= '<p>';
                $i = 0;
                foreach ($contentItem['resourcetype'] as $type){
                    if(++$i === count($contentItem['resourcetype'])) {
                        $content .= $type;
                    }else{
                        $content .= $type.', ';
                    }
                }
                $content .= '</p>';
            }
            $content .= '</div>';
            $content .= '<div class="content-meta">';
            if (!empty($contentItem['subjects'])){
                $content .= '<img src="'. get_template_directory_uri() .'/src/assets/img/subject_icon.svg" alt="Fächer">';
                $content .= '<p>';
                $i = 0;
                foreach ($contentItem['subjects'] as $subject) {
                    if(++$i === count($contentItem['subjects'])) {
                        $content .= $subject;
                    }else{
                        $content .= $subject.', ';
                    }
                }
                $content .= '</p>';
            }
            $content .= '</div>';
            $content .= '<div class="content-meta">';
            if (!empty($contentItem['educationalcontext'])){
                $content .= '<img src="'. get_template_directory_uri() .'/src/assets/img/class_icon.svg" alt="Bildungsebene">';
                $content .= '<p>';
                $i = 0;
                foreach ($contentItem['educationalcontext'] as $subject) {
                    if(++$i === count($contentItem['educationalcontext'])) {
                        $content .= $subject;
                    }else{
                        $content .= $subject.', ';
                    }
                }
                $content .= '</p>';
            }
            $content .= '</div>';
            $content .= '<a class="content-button" href="'.$contentItem['content_url'].'" target="_blank" aria-label="Zum-Inhalt: '.$contentItem['title'].'">Zum Inhalt</a>';
            $content .= '</div>';
            $content .= '</button>';
            $content .= '</div>';
        }
    }else{
        $contentTitle = 'Mitmachen!';
        $buttonText = 'Inhalte vorschlagen';
        $addContentUrl = get_page_link($contentInfo['addContentPageID']) . '?collectionID=' . $contentInfo['collectionID'] . '&headline=' . $contentInfo['$pageTitle'] .'&pageDiscipline=' . $contentInfo['$pageDiscipline'];

        $content .= '<div class="widget-content no-widget-content">';
        $content .= '<button onclick="showNoContentPopup()">';
        $content .= '<img class="main-image" src="'.get_template_directory_uri().'/src/assets/img/mitmachen-2.png" alt="Cover: Keine Inhalte">';
        $content .= '<div class="content-info no-content-info">';
        $content .= '<div class="content-title">'.$contentTitle.'</div>';
        $content .= '<p class="content-description">Füge Inhalte zu diesem Thema hinzu...</p>';
        $content .= '<a class="content-button no-content-button" href="'.$addContentUrl.'" target="_blank">';
        $content .= '<img src="'.get_template_directory_uri().'/src/assets/img/plus.svg" alt="Icon: Plus"> '.$buttonText;
        $content .= '</a>';
        $content .= '</div>';
        $content .= '</button>';
        $content .= '</div>';
    }
    $content .= '</div>';

    $content .= '<script type="text/javascript">        

        jQuery(function () {
            // Handler for .ready() called. Put the Slick Slider etc. init code here.
            function loadWLOSlider() {
                if (typeof jQuery().slick === "function") {
                    jQuery("#'.$sliderId.'").not(".slick-initialized").slick({
                        infinite: false,
                        slidesToShow: '.$slidesToShow.',
                        slidesToScroll: '.$slidesToScroll.',
                        arrows: true,
                        dots:  '.$showSliderDots.',
                        zIndex: 0,
                        responsive: [
                            {
                                breakpoint: 1230,
                                settings: {
                                    slidesToShow: '.min($slidesToShow, 3).',
                                    slidesToScroll: '.min($slidesToScroll, 3).'
                                }
                            },
                            {
                                breakpoint: 950,
                                settings: {
                                    slidesToShow: '.min($slidesToShow, 2).',
                                    slidesToScroll: '.min($slidesToScroll, 2).',
                                }
                            },
                            {
                                breakpoint: 750,
                                settings: {
                                    slidesToShow: '.min($slidesToShow, 1).',
                                    slidesToScroll: '.min($slidesToScroll, 1).',
                                }
                            }
                        ]
                    });
                }
            }

            loadWLOSlider();

            jQuery(window).on("resize", function(){
                jQuery("#'.$sliderId.'").slick( "refresh" );
            });
        });
    </script>';

    return $content;
}

function wloFilterSwimlane($contentArray, $vocabs){
    $filtered_content = array();
    foreach ($contentArray as $key => $content) {
        $added = false;
        foreach ($content['oeh_lrt'] as $lrt){
            if (!empty(array_search($lrt, array_column($vocabs, 'id'))) && !$added){
                $added = true;
                $contentArray[$key]['added'] = true;
                $filtered_content[] = $content;
            }
        }
    }
    return array("filtered_content"=>$filtered_content, "contentArray"=>$contentArray);
}
