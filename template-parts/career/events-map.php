<?php
[
    'topicPagePostId' => $topicPagePostId,
    'topic' => $topic,
] = $args;

$formId = uniqid('form-');
$mapId = uniqid('map-');
$NoticeOverlayId = uniqid('notice-overlay-');


$fooArray = array();
$barArray = array();
?>

<div class="wlo-events-map">
    <h2>Veranstaltungen und Lernorte zum Thema <?php echo $topic; ?></h2>
    <p class="heading-description">
        Hier findest du Lernorte und Veranstaltungen, die zu den Berufsprofilen passen. Vom Tag der offenen Tür, Ausstellungen, Messen und Aktionstagen bis hin zu Ausbildungsbetrieben, Universitäten und mehr.
    </p>
    <div class="events-map-widget-container">
        <form class="sidebar" id="<?php echo $formId; ?>">
            <fieldset>
                <legend>Veranstaltungen und Lernorte</legend>
                <select name="foo" disabled onchange="filterContentTiles(this, 'foo', this.value)">
                    <option value="label" selected disabled>Veranstaltungen</option>
                    <option disabled>──────────</option>
                    <?php foreach ($fooArray as $key => $value) { ?>
                        <option value="<?php echo preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($key)); ?>">
                            <?php echo $key . ' (' . $value . ')'; ?>
                        </option>
                    <?php } ?>
                </select>
                <select name="bar" onchange="filterContentTiles(this, 'bar', this.value)">
                    <option value="label" selected disabled>Ausbildungsbetriebe</option>
                    <option disabled>──────────</option>
                    <?php foreach ($barArray as $key => $value) { ?>
                        <option value="<?php echo preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($key)); ?>">
                            <?php echo $key . ' (' . $value . ')'; ?>
                        </option>
                    <?php } ?>
                </select>
                <select name="region">
                    <option value="germany" selected>Deutschlandweit</option>
                    <option value="near-me">In meiner Nähe</option>
                </select>
            </fieldset>
        </form>
        <div class="map-container">
            <div id="<?php echo $mapId; ?>" class="map"></div>
            <div id="<?php echo $NoticeOverlayId; ?>" class="notice-overlay-container">
                <p class="notice-overlay">Lade Veranstaltungsdaten...</p>
            </div>
        </div>
    </div>
    <?php
    $suggestEventLink = get_page_link(ADD_CONTENT_PAGE_ID)
        . '?collectionID=' . wlo_getPortalIdByPostId($topicPagePostId)
        . '&pageDiscipline=' . get_field('discipline', $topicPagePostId)[0]['value']
        . '&type=' . 'event';
    ?>
    <a
        class="wlo-chip-add-content"
        href="<?php echo $suggestEventLink; ?>"
        target="_blank">
        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/plus.svg" alt="">
        <span>Ort oder Veranstaltung vorschlagen</span>
    </a>
</div>

<script>
    (() => {
        const GERMANY = {
            center: [50.485, 10.272],
            zoom: 6,
        };
        /** Leaflet map object. */
        let map;
        /** Markers currently displayed on the map. */
        let markers = [];

        jQuery(document).ready(() => {
            initializeMap();
            registerSelectFields();
        });

        /**
         * Sets up an OpenStreetMap view on the div element with id `$mapId`.
         */
        function initializeMap() {
            // initialize Leaflet
            map = L.map('<?php echo $mapId; ?>');

            setLocation(GERMANY);

            // add the OpenStreetMap tiles
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href=" https://openstreetmap.org/copyright">OpenStreetMap contributors</a>',
            }).addTo(map);

            // show the scale bar on the lower left corner
            L.control.scale({
                imperial: false,
                metric: true
            }).addTo(map);
        }

        /**
         * Registers change listeners on HTML select fields of the sidebar.
         */
        function registerSelectFields() {
            const form = jQuery('#<?php echo $formId; ?>');
            form.find('select[name = "region"]').on('change', function() {
                onRegionChange(this, this.value)
            });
        }

        /**
         * To be called when the user selects a region via the HTML select field.
         *
         * @param select the HTML select field
         * @param region the value of the selected option
         */
        function onRegionChange(select, region) {
            switch (region) {
                case 'germany':
                    setLocation(GERMANY);
                    break;
                case 'near-me':
                    showOverlayNotice('Suche deinen Standort...' +
                        '<p>Bitte erlaube den Zugriff, um die Funktion nutzen zu können.</p>')
                    navigator.geolocation.getCurrentPosition(({
                        coords
                    }) => {
                        // Success
                        const lat = coords.latitude;
                        const lon = coords.longitude;
                        setLocation({
                            center: [lat, lon],
                            zoom: 12
                        });
                    }, () => {
                        // Failed / user rejected location access
                        hideOverlayNotice();
                        select.value = 'germany';
                    });
                    break;
            }
        }

        /** Sets the map view to the given coordinates and loads fitting location events. */
        function setLocation({
            center,
            zoom
        }) {
            map.setView(center, zoom);
            loadLocationEvents(center, zoom);
        }

        /**
         * Loads location events from the API for the given location and displays them in the map.
         *
         * Replaces previously displayed location events.
         */
        function loadLocationEvents(center = GERMANY.center, zoom = GERMANY.zoom) {
            removeAllMarkers();
            const data = {
                action: 'wloEventLocations',
                postId: '<?php echo $topicPagePostId; ?>',
                lat: center[0],
                lon: center[1],
                zoom,
            };
            jQuery.get(ajaxurl, data, function(response) {
                // console.log('response', response);
                hideOverlayNotice();
                for (const eventLocation of response.eventLocations) {
                    const {
                        lat,
                        lon,
                        title,
                        location,
                        description,
                        url,
                    } = eventLocation;
                    const marker = L.marker({
                        lon,
                        lat
                    }).bindPopup(getPopupHtml(title, location, description, url)).addTo(map);
                    markers.push(marker);
                }
            }).fail(() => {
                showErrorNotice();
            });
        }

        /** Removes all event-location markers from the map. */
        function removeAllMarkers() {
            markers.forEach(m => m.remove());
            markers = [];
        }

        /** Generates HTML to be displayed as content of an event-location popup. */
        function getPopupHtml(title, location, description, url) {
            let result = `<p class='title'>${title}</p>`;
            if (location) {
                result += `<p class='location'>${location}</p>`;
            }
            if (description) {
                result += `<p class='description'>${description}</p>`;
            }
            if (url) {
                result += `<p class='url'><a href="${url}" target="_blank">${url}</a></p>`;
            }
            return result;
        }

        /** Hides the overlay notice that can hold the loading screen or an error notice. */
        function hideOverlayNotice() {
            jQuery('#<?php echo $NoticeOverlayId; ?>').hide();
        }

        /** Displays an error notice as overlay above the map. */
        function showErrorNotice() {
            showOverlayNotice(
                'Da ist etwas schiefgelaufen.</br>' +
                'Konnte die Veranstaltungsdaten nicht laden.',
            );
        }

        /** Displays the given notice as overlay above the map. */
        function showOverlayNotice(html) {
            jQuery('#<?php echo $NoticeOverlayId; ?>')
                .show()
                .find('.notice-overlay')
                .html(html);
        }
    })();
</script>