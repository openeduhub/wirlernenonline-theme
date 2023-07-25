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
                <select name="locationType">
                    <option value="all" selected disabled>Alle Orte</option>
                </select>
                <select name="region">
                    <option value="germany" selected>Deutschlandweit</option>
                    <option value="near-me">In meiner Nähe</option>
                </select>
            </fieldset>
        </form>
        <div class="map-container">
            <div id="<?php echo $mapId; ?>" class="map"></div>
        </div>
        <div id="<?php echo $NoticeOverlayId; ?>" class="notice-overlay-container">
            <p class="notice-overlay">Lade Veranstaltungsdaten...</p>
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

<?php
$locationTypeLabels = getWloVocabsValueLabelPairs('locationType');
?>

<script>
    (() => {
        /**
         * @typedef FilterType
         * @type {'locationType'}
         */

        /**
         * @type {FilterType[]}
         */
        const FILTER_TYPES = ['locationType'];

        /**
         * Provides access to UI elements on the container, encompassing the filter sidebar and map
         * view.
         */
        class ContainerUi {
            /** Hides the overlay notice that can hold the loading screen or an error notice. */
            hideOverlayNotice() {
                jQuery('#<?php echo $NoticeOverlayId; ?>').hide();
            }

            /** Displays an error notice as overlay above the map. */
            showErrorNotice() {
                this.showOverlayNotice(
                    'Da ist etwas schiefgelaufen.</br>' +
                    'Konnte die Veranstaltungsdaten nicht laden.',
                );
            }

            /** Displays the given notice as overlay above the map. */
            showOverlayNotice(html) {
                jQuery('#<?php echo $NoticeOverlayId; ?>')
                    .show()
                    .find('.notice-overlay')
                    .html(html);
            }
        }

        /**
         * Provides access to the map view and markers.
         */
        class MapsUi {
            GERMANY = {
                center: [50.485, 10.272],
                zoom: 6,
            };

            /** Leaflet map object. */
            _map;
            /** Markers currently displayed on the map. */
            _markers = [];

            init() {
                this._initializeMap();
            }

            /** Sets the map view to the given coordinates. */
            setLocation({
                center,
                zoom
            }) {
                this._map.setView(center, zoom);
            }

            /**
             * Adds a marker for the given location to the map.
             */
            addMarker(eventLocation) {
                const {
                    lat,
                    lon,
                    ...data
                } = eventLocation;
                const marker = L.marker({
                    lon,
                    lat
                }).bindPopup(this._getPopupHtml(data)).addTo(this._map);
                this._markers.push(marker);
            }

            /**
             * Removes all event-location markers from the map.
             */
            removeAllMarkers() {
                this._markers.forEach(m => m.remove());
                this._markers = [];
            }

            /**
             * Sets up an OpenStreetMap view on the div element with id `$mapId`.
             */
            _initializeMap() {
                // initialize Leaflet
                this._map = L.map('<?php echo $mapId; ?>');
                this.setLocation(this.GERMANY);

                // add the OpenStreetMap tiles
                L.tileLayer(ajaxurl + '?action=wloCardTile&z={z}&x={x}&y={y}&suffix=.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href=" https://openstreetmap.org/copyright">OpenStreetMap contributors</a>',
                }).addTo(this._map);

                // show the scale bar on the lower left corner
                L.control.scale({
                    imperial: false,
                    metric: true
                }).addTo(this._map);
            }

            /**
             * Generates HTML to be displayed as content of an event-location popup.
             */
            _getPopupHtml({
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
                        str = `${this._formatDate(begin)}`;
                    } else if (begin && end) {
                        str = `${this._formatDate(begin)} - ${this._formatDate(end)}`;
                    } else if (begin) {
                        str = `ab ${this._formatDate(begin)}`;
                    } else {
                        str = `bis ${this._formatDate(end)}`;
                    }
                    result += `<p class='date'>${str}</p>`;
                }
                if (url) {
                    result += `<p class='url'><a href="${url}" target="_blank">${url}</a></p>`;
                }
                return result;
            }


            /**
             * Formats the given date to be displayed to the user.
             */
            _formatDate(date) {
                return new Date(date).toLocaleDateString('de', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }
        }

        /**
         * Provides access to UI select elements used for filtering map data.
         */
        class FiltersUi {
            /**
             * @callback filterChangedCallback
             * @param {FilterType} type
             * @param {string} value
             * @returns {void}
             */

            /**
             * Will be called when the user selects a filter option.
             * 
             * @type {filterChangedCallback}
             */
            onFilterChanged;

            constructor(containerUi, mapsUi) {
                this._containerUi = containerUi;
                this._mapsUi = mapsUi;
            }

            init() {
                this._registerSelectFields();
            }

            /**
             * Updates the filter options displayed to the user.
             */
            updateFilterOptions(type, options) {
                const form = jQuery('#<?php echo $formId; ?>');
                const select = form.find(`select[name = "${type}"]`);
                const selectedValue = select.val();
                select.find('option').remove();
                for (const option of options) {
                    select.append(jQuery('<option>', {
                        value: option.value,
                        text: capitalizeFirstLetter(option.label),
                    }));
                }
                if (selectedValue) {
                    select.val(selectedValue);
                }
                select.attr('disabled', options.length <= 1);
            }

            /**
             * Registers change listeners on HTML select fields of the sidebar.
             */
            _registerSelectFields() {
                const self = this;
                const form = jQuery('#<?php echo $formId; ?>');
                form.find('select[name = "region"]').on('change', function() {
                    self._onRegionChange(this, this.value)
                });
                for (const type of FILTER_TYPES) {
                    form.find(`select[name = ${type}]`).on('change', function() {
                        self.onFilterChanged(type, this.value);
                    });
                }
            }

            /**
             * To be called when the user selects a region via the HTML select field.
             *
             * @param select the HTML select field
             * @param region the value of the selected option
             */
            _onRegionChange(select, region) {
                switch (region) {
                    case 'germany':
                        this._mapsUi.setLocation(this._mapsUi.GERMANY);
                        break;
                    case 'near-me':
                        this._containerUi.showOverlayNotice(
                            'Suche deinen Standort...' +
                            '<p>Bitte erlaube den Zugriff, um die Funktion nutzen zu können.</p>',
                        );
                        navigator.geolocation.getCurrentPosition(({
                            coords
                        }) => {
                            // Success
                            const lat = coords.latitude;
                            const lon = coords.longitude;
                            this._mapsUi.setLocation({
                                center: [lat, lon],
                                zoom: 12
                            });
                            this._containerUi.hideOverlayNotice();
                        }, () => {
                            // Failed / user rejected location access
                            this._containerUi.hideOverlayNotice();
                            select.value = 'germany';
                        });
                        break;
                }
            }
        }

        /**
         * Loads, displays, and filters location events.
         */
        class LocationEventsManager {
            /** All event locations to be displayed when there are no active filters. */
            _eventLocations = [];

            /** Currently selected filter value for each type. Set to `null` for 'all'. */
            _activeFilters = {
                locationType: null,
            }

            FILTER_LABELS = {
                locationType: {
                    all: 'Alle Orte',
                    event: 'Veranstaltung',
                    ...<?php echo json_encode($locationTypeLabels); ?>,
                }
            }

            constructor(containerUi, mapsUi, filtersUi) {
                this._containerUi = containerUi;
                this._mapsUi = mapsUi;
                this._filtersUi = filtersUi;
            }

            init() {
                this._filtersUi.onFilterChanged = (type, value) => this._setFilter(type, value);
            }

            /**
             * Loads location events from the API for the given location and displays them in the
             * map.
             *
             * Replaces previously displayed location events.
             */
            loadLocationEvents(jobProfiles) {
                this._mapsUi.removeAllMarkers();
                const data = {
                    action: 'wloEventLocations',
                    postId: '<?php echo $topicPagePostId; ?>',
                    jobProfileIds: jobProfiles.map(profile => profile.id),
                    // lat: center[0],
                    // lon: center[1],
                    // zoom,
                };
                jQuery.get(ajaxurl, data, (response) => {
                    this._containerUi.hideOverlayNotice();
                    this._eventLocations = this._filterLocationsGLobal(response.eventLocations);
                    for (const eventLocation of this._eventLocations) {
                        this._mapsUi.addMarker(eventLocation);
                    }
                    this._updateFilterOptions();
                }).fail((e) => {
                    this._containerUi.showErrorNotice();
                });
            }

            _filterLocationsGLobal(locations) {
                return locations.filter((location) => {
                    // Dismiss events in the past
                    if (location.end && new Date(location.end) < new Date()) {
                        return false;
                    } else {
                        return true;
                    }
                })
            }

            /**
             * Gets available options for the filter of the given type.
             *
             * @param {FilterType} type the filter to get options for,
             * corresponds to a select field
             * @returns {{ value: string, label: string }[]}
             */
            _getFilterOptions(type) {
                let values = [];
                for (const location of this._eventLocations) {
                    const locationValues = this._mapFilterOption(type, location);
                    for (const value of locationValues) {
                        if (!values.includes(value)) {
                            values.push(value);
                        }
                    }
                }
                values = ['all', ...values.sort()]
                return values.map((value) => {
                    const occurrences = this._getOccurrences(type, value);
                    if (value === 'all' || occurrences > 0) {
                        return {
                            value,
                            label: `${this._getFilterOptionLabel(value, type)} (${occurrences})`,
                        };
                    }
                }).filter((option) => !!option);
            }

            /**
             * Filters locations by the given filter option.
             * 
             * Option labels should be updated afterwards.
             */
            _setFilter(type, value) {
                if (value === 'all') {
                    this._activeFilters[type] = null;
                } else {
                    this._activeFilters[type] = value;
                }
                this._mapsUi.removeAllMarkers();
                const locations = this._eventLocations
                    .filter(location => this._isVisibleAfterFilter(location));
                for (const location of locations) {
                    this._mapsUi.addMarker(location);
                }
                this._updateFilterOptions()
            }

            /**
             * Populates or updates the filter UI's options for all filter types.
             */
            _updateFilterOptions() {
                for (const type of FILTER_TYPES) {
                    this._filtersUi.updateFilterOptions(type, this._getFilterOptions(type));
                }
            }

            /**
             * Get the number of occurrences to be shown for the given filter option.
             * 
             * Considers active filters of other types when counting occurrences.
             */
            _getOccurrences(type, value) {
                const filters = {
                    ...this._activeFilters,
                    [type]: value === 'all' ? null : value,
                };
                const result = this._eventLocations.reduce((count, location) => {
                    if (this._isVisibleAfterFilter(location, filters)) {
                        count++;
                    }
                    return count;
                }, 0);
                return result;
            }

            /**
             * Returns true iff the given location should be displayed when applying the given
             * filters.
             * @param {{[type in FilterType]: string}} filters active filter value per type
             */
            _isVisibleAfterFilter(location, filters = this._activeFilters) {
                return Object.entries(filters).every(([type, value]) => {
                    if (value) {
                        const locationValues = this._mapFilterOption(type, location);
                        return locationValues.includes(value);
                    } else {
                        return true;
                    }
                });
            }

            /**
             * Maps location data to values used for filtering.
             * 
             * @param {FilterType} type
             * @returns {string[]} filter values
             */
            _mapFilterOption(type, location) {
                const result = (() => {
                    switch (type) {
                        case 'locationType':
                            if (location.begin || location.end) {
                                return ['event'];
                            } else {
                                return location[type] ?? [];
                            }
                        default:
                            return location[type] ?? [];
                    }
                })();
                if (typeof result === 'string') {
                    return [result];
                } else if (Array.isArray(result)) {
                    return result.filter(entry => typeof entry === 'string');
                } else {
                    return [];
                }
            }

            /**
             * Returns the label to display to the user for the given filter value.
             * @param {string} value
             * @param {FilterType} type
             * @returns {string}
             */
            _getFilterOptionLabel(value, type) {
                return this.FILTER_LABELS[type]?.[value] ?? value;
            }
        }

        // From https://stackoverflow.com/a/1026087
        function capitalizeFirstLetter(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        jQuery(document).ready(() => {
            const containerUi = new ContainerUi();
            const mapsUi = new MapsUi();
            const filtersUi = new FiltersUi(containerUi, mapsUi);
            const locationEventsManager = new LocationEventsManager(containerUi, mapsUi, filtersUi);

            mapsUi.init();
            filtersUi.init();
            locationEventsManager.init();
            window.jobProfilesSubject.subscribe(jobProfiles => {
                if (jobProfiles !== null) {
                    locationEventsManager.loadLocationEvents(jobProfiles);
                }
            })
        });
    })();
</script>