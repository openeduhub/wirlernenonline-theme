<?php
get_header();
?>
<?php

$accordionID = uniqid();

$current_query_args = array(
    'posts_per_page'	=> 8,
    'post_type' => 'portal',
    'post_status' => 'publish',
    'orderby'     => 'modified',
    'order'       => 'DESC',
    'meta_key' => 'collection_level',
    'meta_value' => '0'
);
$current_query = new WP_Query( $current_query_args );

$sliderId = uniqid('slider-');
$slidesToShow = 6;
$slidesToScroll = 6;
$showSliderDots = 'false';


// subjects for dropdown
$url = WLO_REPO . 'rest/search/v1/queries/local/mds_oeh/ngsearch/';
$body = '{
          "facets": [
            "ccm:taxonid"
          ],
          "facetMinCount": 1,
          "facetLimit": 10000,
          "criteria": [
            {
              "property": "ccm:taxonid",
              "values": [
                "*"
              ]
            }]
        }';

$subjects = callWloRestApi($url, 'POST', $body)->facets[0]->values;

$subjects_vocab = getWloVocaps('discipline')->hasTopConcept;
$subjects_mapped = array();
foreach ($subjects_vocab as $subject){
    $subjects_mapped[$subject->id] = $subject->prefLabel->de;
}

?>
    <div class="wlo-page">

        <div class="wlo-header wlo-frontpage-header">
            <div class="wlo-header-wrapper">
                <div class="wlo-frontpage-header-content">
                    <div class="wlo-frontpage-claim">Suchen. Finden. Mitmachen.</div>
                    <form id="wlo-frontpage-search-form" action="<?php echo WLO_SEARCH; ?>de/search" method="GET">
                        <div class="wlo-frontpage-search-params">

                            <div class="dropdown">
                                <button type="button" class="dropdown__selected" id="subject__dropdown__selected" tabindex="0">
                                    <span>Fach</span>
                                    <svg class="dropdown__arrow" width="10" height="5" viewBox="0 0 10 5" fill-rule="evenodd">
                                        <title>Open drop down</title>
                                        <path d="M10 0L5 5 0 0z"></path>
                                    </svg>
                                </button>

                                <div class="dropdown__list-container">
                                    <div class="dropdown__list">
                                        <?php foreach (array_slice($subjects, 0, 8) as $subject){ ?>
                                            <span class="dropdown__list-item" tabindex="0" id="<?php echo $subject->value; ?>">
                                                <?php echo $subjects_mapped[$subject->value]; ?>
                                            </span>
                                        <?php } ?>
                                    </div>

                                    <?php if (count($subjects) >= 9) { ?>
                                        <button class="wlo-button" id="hidden-button" type="button" onclick="toggleHiddenSubjects()">
                                            mehr
                                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down_white.svg" alt="">
                                        </button>
                                    <?php } ?>

                                    <div class="dropdown__list" id="hidden-subjects">
                                        <?php foreach (array_slice($subjects, 8) as $subject){ ?>
                                            <span class="dropdown__list-item" tabindex="0" id="<?php echo $subjects_mapped[$subject->value]; ?>">
                                                <?php echo $subjects_mapped[$subject->value]; ?>
                                            </span>
                                        <?php } ?>
                                    </div>

                                </div>
                            </div>

                            <span class="dropdown-seperator">|</span>

                            <div class="dropdown">
                                <button type="button" class="dropdown__selected" id="educontext__dropdown__selected" tabindex="0">
                                    <span>Bildungsstufe</span>
                                    <svg class="dropdown__arrow" width="10" height="5" viewBox="0 0 10 5" fill-rule="evenodd">
                                        <title>Open drop down</title>
                                        <path d="M10 0L5 5 0 0z"></path>
                                    </svg>
                                </button>

                                <div class="dropdown__list-container">
                                    <div class="dropdown__list">
                                        <span class="dropdown__list-item" tabindex="0" id="Primarstufe">
                                            Primarstufe
                                        </span>
                                        <span class="dropdown__list-item" tabindex="0" id="Sek-1">
                                            Sekundarstufe I
                                        </span>
                                        <span class="dropdown__list-item" tabindex="0" id="Sek-2">
                                            Sekundarstufe II
                                        </span>
                                        <span class="dropdown__list-item" tabindex="0" id="Berufliche-Bildung">
                                            Berufliche Bildung
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="wlo-frontpage-search">
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/icon_search.svg" alt="">
                            <input type="search" name="q" id="search" aria-label="Search" autocomplete="off" placeholder="Finde passende Inhalte, Tools & Quellen für den Unterricht…">
                            <input type="submit" value="Suche">
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="wlo-portals-header-bottom">
            <div class="wlo-accordion-wrapper" style="background: #D9E2EB;">
                <button class="wlo-accordion" id="wlo-accordion-<?php echo $accordionID; ?>">
                    <h2>Hier kannst Du in unseren Fachportalen stöbern.</h2>
                    <img class="wlo-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg"  alt="Inhalte ein odder ausklappen">
                </button>

                <div class="wlo-accordion-content" id="wlo-accordion-content-<?php echo $accordionID; ?>">


                    <div class="wlo-portals-accordion-slider" id="<?php echo $sliderId; ?>" style="opacity: 0;">

                        <!--
                        <?php
/*                        if ($current_query->have_posts()) : */?>

                            <?php
/*                            while ($current_query->have_posts()) :
                                $current_query->the_post(); */?>

                                <div class="wlo-portals-slider-tile">
                                    <a class="wlo-portals-tile" href="<?php /*the_permalink(); */?>" aria-label="Zum-Fachportal: <?php /*echo get_the_title(); */?>">
                                        <?php /*if (has_post_thumbnail()){ */?>
                                            <img src="<?php /*echo get_the_post_thumbnail_url(); */?>" alt="Icon: <?php /*echo get_the_title(); */?>">
                                        <?php /*} */?>
                                        <div class="wlo-portals-tile-title"><?php /*echo get_the_title(); */?></div>
                                    </a>
                                </div>
                            <?php /*endwhile; */?>

                        <?php /*endif;
                        wp_reset_query();
                        */?>
                        -->

                        <!--<div class="maintenance-tile">
                            <h2>Wartungsarbeiten!</h2>
                            <p>Wir führen derzeit Wartungsarbeiten an unseren Fachportalen und Themenseiten durch.
                                <br>In Kürze sind sie wieder für dich da!</p>
                        </div>-->

                        <div class="wlo-portals-slider-tile">
                            <a class="wlo-portals-tile" href="<?php echo get_page_link(6506); ?>" aria-label="Zum-Fachportal: Physik">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/Fach-Icons/icon_physik.svg" alt="">
                                <div class="wlo-portals-tile-title">Physik</div>
                            </a>
                        </div>
                        <div class="wlo-portals-slider-tile">
                            <a class="wlo-portals-tile" href="<?php echo get_page_link(6526); ?>" aria-label="Zum-Fachportal: Deutsch">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/Fach-Icons/icon_deutsch.svg" alt="">
                                <div class="wlo-portals-tile-title">Deutsch</div>
                            </a>
                        </div>
                        <div class="wlo-portals-slider-tile">
                            <a class="wlo-portals-tile" href="<?php echo get_page_link(6463); ?>" aria-label="Zum-Fachportal: Medienbildung">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/Fach-Icons/icon_medienbildung.svg" alt="">
                                <div class="wlo-portals-tile-title">Medienbildung</div>
                            </a>
                        </div>
                        <div class="wlo-portals-slider-tile">
                            <a class="wlo-portals-tile" href="<?php echo get_page_link(21348); ?>" aria-label="Zum-Fachportal: Religion">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/Fach-Icons/icon_religion.svg" alt="">
                                <div class="wlo-portals-tile-title">Religion</div>
                            </a>
                        </div>
                        <div class="wlo-portals-slider-tile">
                            <a class="wlo-portals-tile" href="<?php echo get_page_link(20324); ?>" aria-label="Zum-Fachportal: Sport">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/Fach-Icons/icon_sport.svg" alt="">
                                <div class="wlo-portals-tile-title">Sport</div>
                            </a>
                        </div>
                        <div class="wlo-portals-slider-tile">
                            <a class="wlo-portals-tile" href="<?php echo get_page_link(9930); ?>" aria-label="Zu den Fachportalen">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/Fach-Icons/icon_portals.svg" alt="">
                                <div class="wlo-portals-tile-title">Alle 28 Fachportale</div>
                            </a>
                        </div>

                    </div>

                </div>
                <div class="wlo-accordion-bottom"></div>
            </div>
        </div>


        <div class="wlo-wrapper">
            <?php the_content(); ?>
        </div>

    </div>

    <script>
        jQuery('#wlo-accordion-<?php echo $accordionID; ?>').click(function(){
            jQuery(this).find("img").toggleClass("wlo-accordion-icon-active");
            jQuery('#wlo-accordion-content-<?php echo $accordionID; ?>').slideToggle();
        });

        jQuery(function () {
            // Handler for .ready() called. Put the Slick Slider etc. init code here.
            function loadWLOSlider() {
                if (typeof jQuery().slick === "function") {
                    jQuery('#<?php echo $sliderId?>').not('.slick-initialized').slick({
                        infinite: false,
                        slidesToShow: <?php echo $slidesToShow; ?>,
                        slidesToScroll: <?php echo $slidesToScroll; ?>,
                        arrows: true,
                        dots:  <?php echo $showSliderDots; ?>,
                        zIndex: 0,
                        focusOnChange: false,
                        accessibility: false,
                        responsive: [
                            {
                                breakpoint: 900,
                                settings: {
                                    slidesToShow: 4,
                                    slidesToScroll: 4
                                }
                            },
                            {
                                breakpoint: 700,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 3
                                }
                            },
                            {
                                breakpoint: 600,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 2
                                }
                            },
                            {
                                breakpoint: 500,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1
                                }
                            }
                        ]
                    });
                }
            }

            loadWLOSlider();

            jQuery('#<?php echo $sliderId?>').css('opacity', '1');

            jQuery(window).on('resize', function(){
                jQuery('#<?php echo $sliderId?>').slick( 'refresh' );
            });
        });

        function handleSubmit(event) {
            event.preventDefault();

            const data = new FormData(event.target);
            let subject = jQuery('#subject__dropdown__selected span').attr('data-subject');
            let educationalContext = jQuery('#educontext__dropdown__selected span').html();

            if (subject === 'Fach'){
                subject = null;
            }

            if (educationalContext === 'Bildungsstufe'){
                educationalContext = null;
            }

            let search = '';
            if (data.get('q')){
                search += 'q='+data.get('q');
            }

            let filter = '';
            if (subject){
                filter += '"discipline":["'+subject+'"]';
            }
            if (educationalContext){
                if (subject){
                    filter += ',';
                }
                filter += '"educationalContext":["'+educationalContext+'"]';
            }
            if (filter){
                filter = 'filters={'+filter+'}&';
            }

            window.open("<?php echo WLO_SEARCH; ?>de/search?"+filter+search,"_self");
        }

        const form = document.querySelector('#wlo-frontpage-search-form');
        form.addEventListener('submit', handleSubmit);


        function toggleHiddenSubjects(){
            if (window.innerWidth <= 900){
                jQuery('#hidden-subjects').css('display', 'grid');
            }else{
                jQuery('#hidden-subjects').css('display', 'flex');
            }
            jQuery('#hidden-button').hide();
        }


        const SPACEBAR_KEY_CODE = [0, 32];
        const ENTER_KEY_CODE = 13;
        const DOWN_ARROW_KEY_CODE = 40;
        const UP_ARROW_KEY_CODE = 38;
        const ESCAPE_KEY_CODE = 27;


        const list = document.querySelectorAll('.dropdown__list');
        const listContainer = document.querySelectorAll(".dropdown__list-container");
        const dropdownArrow = document.querySelectorAll(".dropdown__arrow");
        const listItems = document.querySelectorAll(".dropdown__list-item");
        const dropdownSelectedNode = document.querySelectorAll(".dropdown__selected");
        const listItemIds = [];



        dropdownSelectedNode.forEach(item => item.addEventListener("keydown", e =>
            onKeyDown(e)
        ));


        listItems.forEach(item => listItemIds.push(item.id));

        listItems.forEach(item => {
            item.addEventListener("click", e => {
                setSelectedListItem(e);
                closeList();
            });

            item.addEventListener("keydown", e => {
                switch (e.keyCode) {
                    case ENTER_KEY_CODE:
                        setSelectedListItem(e);
                        closeList();
                        return;

                    case DOWN_ARROW_KEY_CODE:
                        focusNextListItem(DOWN_ARROW_KEY_CODE);
                        return;

                    case UP_ARROW_KEY_CODE:
                        focusNextListItem(UP_ARROW_KEY_CODE);
                        return;

                    case ESCAPE_KEY_CODE:
                        closeList();
                        return;

                    default:
                        return;
                }
            });
        });

        window.addEventListener('click', function(e) {
            var target = jQuery(e.target);
            let targetIsButton = jQuery(target).closest('.dropdown').length;
            let targetIsOpen = jQuery(target).closest('.dropdown').find('.open').length;
            let targetIsMenu = jQuery(target).closest('.dropdown__list-container').length;

            if (targetIsMenu){
                return;
            }

            closeList();
            if (targetIsButton){
                if (!targetIsOpen){
                    openList(e);
                }
            }

        });

        function setSelectedListItem(e) {
            let selectedTextToAppend = document.createTextNode(e.target.innerText);
            e.target.closest('.dropdown').querySelector('.dropdown__selected span').innerHTML = null;
            e.target.closest('.dropdown').querySelector('.dropdown__selected span').appendChild(selectedTextToAppend);
            e.target.closest('.dropdown').querySelector('.dropdown__selected span').setAttribute("data-subject", e.target.id);
        }

        function closeList() {
            dropdownArrow.forEach(item => item.classList.remove("expanded"));
            listContainer.forEach(item => item.classList.remove("open"));
        }

        function openList(e){
            const listContainer = jQuery(e.target).closest('.dropdown').find('.dropdown__list-container');
            const dropdownArrow = jQuery(e.target).closest('.dropdown').find('.dropdown__arrow');

            listContainer.addClass("open");
            dropdownArrow.addClass("expanded");

        }

        function onKeyDown(e) {
            let openDropDown = SPACEBAR_KEY_CODE.includes(e.keyCode) || e.keyCode === ENTER_KEY_CODE;

            if (e.keyCode === ESCAPE_KEY_CODE) {
                closeList();
            }

            if (openDropDown) {
                //openList(e);
            }

            if (e.keyCode === DOWN_ARROW_KEY_CODE) {
                focusNextListItem(DOWN_ARROW_KEY_CODE);
                e.stopPropagation();
                e.preventDefault();
            }

            if (e.keyCode === UP_ARROW_KEY_CODE) {
                focusNextListItem(UP_ARROW_KEY_CODE);

                e.stopPropagation();
                e.preventDefault();
            }
        }

        function focusNextListItem(direction) {
            const activeElementId = document.activeElement.id;
            if (activeElementId === "dropdown__selected") {
                document.querySelector(`#${listItemIds[0]}`).focus();
            } else {
                const currentActiveElementIndex = listItemIds.indexOf(
                    activeElementId
                );
                if (direction === DOWN_ARROW_KEY_CODE) {
                    const currentActiveElementIsNotLastItem =
                        currentActiveElementIndex < listItemIds.length - 1;
                    if (currentActiveElementIsNotLastItem) {
                        const nextListItemId = listItemIds[currentActiveElementIndex + 1];
                        document.querySelector(`#${nextListItemId}`).focus();
                    }
                } else if (direction === UP_ARROW_KEY_CODE) {
                    const currentActiveElementIsNotFirstItem =
                        currentActiveElementIndex > 0;
                    if (currentActiveElementIsNotFirstItem) {
                        const nextListItemId = listItemIds[currentActiveElementIndex - 1];
                        document.querySelector(`#${nextListItemId}`).focus();
                    }
                }
            }
        }
    </script>

<?php
get_footer();

