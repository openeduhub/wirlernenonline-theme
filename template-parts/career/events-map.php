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
        /**
         * Loads, displays, and filters location events.
         */
        class LocationEventsManager {
            /** All event locations to be displayed when there are no active filters. */
            _eventLocations = [];
            /** Markers currently displayed on the map. */
            _markers = [];

            /**
             * Loads location events from the API for the given location and displays them in the
             * map.
             *
             * Replaces previously displayed location events.
             */
            loadLocationEvents(center = GERMANY.center, zoom = GERMANY.zoom) {
                this._removeAllMarkers();
                const data = {
                    action: 'wloEventLocations',
                    postId: '<?php echo $topicPagePostId; ?>',
                    lat: center[0],
                    lon: center[1],
                    zoom,
                };
                jQuery.get(ajaxurl, data, (response) => {
                    // console.log('response', response);
                    hideOverlayNotice();
                    this._eventLocations = response.eventLocations
                    for (const eventLocation of this._eventLocations) {
                        this._addMarker(eventLocation);
                    }

                }).fail(() => {
                    showErrorNotice();
                });
            }

            _addMarker(eventLocation) {
                const {
                    lat,
                    lon,
                    ...data
                } = eventLocation;
                console.log(data, lat, lon);
                const marker = L.marker({
                    lon,
                    lat
                }).bindPopup(getPopupHtml(data)).addTo(map);
                this._markers.push(marker);
            }

            /** Removes all event-location markers from the map. */
            _removeAllMarkers() {
                this._markers.forEach(m => m.remove());
                this._markers = [];
            }
        }

        const GERMANY = {
            center: [50.485, 10.272],
            zoom: 6,
        };
        /** Leaflet map object. */
        let map;
        let locationEventsManager = new LocationEventsManager();

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
            locationEventsManager.loadLocationEvents(center, zoom);
        }

        function formatDate(date) {
            return new Date(date).toLocaleDateString('de', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }


        /** Generates HTML to be displayed as content of an event-location popup. */
        function getPopupHtml({
            title,
            location,
            begin,
            end,
            description,
            url
        }) {
            let result = `<p class='title'>${title}</p>`;
            if (location) {
                result += `<p class='location'>${location}</p>`;
            }
            if (description) {
                result += `<p class='description'>${description}</p>`;
            }
            if (begin || end) {
                let str;
                if (begin === end) {
                    str = `${formatDate(begin)}`;
                } else if (begin && end) {
                    str = `${formatDate(begin)} - ${formatDate(end)}`;
                } else if (begin) {
                    str = `ab ${formatDate(begin)}`;
                } else {
                    str = `bis ${formatDate(end)}`;
                }
                result += `<p class='date'>${str}</p>`;
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