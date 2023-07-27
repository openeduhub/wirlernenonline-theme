"use strict";

jQuery(document).ready(function () {
  hideLoading();
  jQuery('select.portfolio-filter').select2({
    multiple: true
  });
  jQuery('.filter-dimension').ionRangeSlider({
    onFinish: function onFinish(data) {
      displayLoading();
      var dimension = data.input.context.name;
      var values = {
        min: data.from,
        max: data.to
      };
      var filter = data.input.context.dataset.filter;
      addActiveFilter(filter);
      filterStore.dimensions[dimension] = values;
      $grid.isotope();
    }
  });
  resetGroups();
  resetTerms(); // Isotope setup

  $grid.isotope({
    itemSelector: '.filter-item',
    masonry: {
      columnWidth: '.filter-item',
      horizontalOrder: true
    },
    filter: function filter() {
      var isMatched = true;
      var $this = jQuery(this);

      if (activeFilters.length == 0) {
        return true;
      } else {
        for (var prop in activeFilters) {
          var filter = activeFilters[prop];
          filter = filterFns[filter] || filter;

          if (filter) {
            isMatched = isMatched && $this.is(filter);
          }

          if (!isMatched) {
            break;
          }
        } // let searchResult = filterStore.search ? $this.find('h3').html().match(filterStore.search) : true;


        var searchResult = true;

        if (filterStore.search) {
          searchResult = $this.find('h3').html().match(filterStore.search);
          var terms = $this.data('terms');

          if (terms.length >= 0) {
            for (var _prop in terms) {
              var term = terms[_prop];
              searchResult = searchResult || term.match(filterStore.search);
            }
          }
        }

        return isMatched && searchResult;
      }
    },
    getSortData: {
      name: function name(el) {
        return jQuery(el).find('h3').html();
      },
      length: function length(el) {
        return parseFloat(jQuery(el).data('specs').length);
      },
      width: function width(el) {
        return parseFloat(jQuery(el).data('specs').width);
      },
      height: function height(el) {
        return parseFloat(jQuery(el).data('specs').height);
      },
      viewCount: function viewCount(el) {
        return parseFloat(jQuery(el).data('specs').viewCount == null ? 0 : jQuery(el).data('specs').viewCount);
      }
    }
  });
  $grid.isotope('updateSortData').isotope();
  $imgs.lazyload({
    failure_limit: Math.max($imgs.length - 1, 0),
    event: 'lazylazy'
  }); // on arrangeComplete event set the bsf_filter Cookie with the settings stored in filterStore

  $grid.isotope('on', 'arrangeComplete', function (filteredItems) {
    loadVisible($imgs, 'lazylazy');

    if (filteredItems.length == 0) {
      jQuery('.filter-empty-message').removeClass('hide');
    } else {
      jQuery('.filter-empty-message').addClass('hide');
    }

    hideLoading();
  });
  var group = getQueryVariable('group');

  if (group) {
    setGroup(group);
  }

  var devicetype = getQueryVariable('devicetype');

  if (devicetype) {
    setTerm(devicetype, 'device-type');
  }

  var playfunction = getQueryVariable('playfunction');

  if (playfunction) {
    setTerm(playfunction, 'play-function');
  }

  var industry = getQueryVariable('industry');

  if (industry) {
    setTerm(industry, 'industry');
  }

  console.log(group, devicetype, playfunction, industry);
});
var $grid = jQuery('.filter-list');
var $imgs = jQuery('img.lazyload');
jQuery(window).scroll(function () {
  loadVisible($imgs, 'lazylazy');
});

function loadVisible($els, trigger) {
  $els.filter(function () {
    var rect = this.getBoundingClientRect();
    return rect.top >= 0 && rect.top <= window.innerHeight;
  }).trigger(trigger);
} // Get query variable


function getQueryVariable(variable) {
  var query = window.location.search.substring(1);
  var vars = query.split("&");

  for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split("=");

    if (pair[0] == variable) {
      return pair[1];
    }
  }

  return false;
}

function setGroup(option) {
  jQuery('select#filter-group').val(option).trigger('change');
}

function setTerm(option, tax) {
  jQuery('select#filter-terms-' + tax).val(option).trigger('change');
}

var filterStore = {
  search: '',
  groups: [],
  terms: [],
  dimensions: {
    length: {
      min: null,
      max: null
    },
    width: {
      min: null,
      max: null
    },
    height: {
      min: null,
      max: null
    }
  }
}; // stores the activeFilter (they are used to loop through in isotope.filter)

var activeFilters = []; // is used to set activeFilters on Events

function addActiveFilter(filter) {
  filter = activeFilters[filter] || filter;

  if (!activeFilters.includes(filter)) {
    activeFilters.push(filter);
  }
}

function resetActiveFilter() {
  activeFilters.length = 0;
} // Utility to reset filters


function resetSelect2($el) {
  $el.val(null).trigger('change');
}

function resetSlider(slider) {
  jQuery(slider).data('ionRangeSlider').reset();
}

function resetTextInput($el) {
  $el.val('');
} // Filter reset functions


function resetSearch() {
  resetTextInput(jQuery('input.filter-search'));
  filterStore.search = '';
}

function resetGroups() {
  jQuery('select#filter-group').each(function () {
    resetSelect2(jQuery(this));
  });
  filterStore.groups.length = 0;
}

function resetTerms() {
  jQuery('select.portfolio-filter-terms').each(function () {
    resetSelect2(jQuery(this));
  });
  filterStore.terms.length = 0;
} //TODO reimplement for ionRangeSlider


function resetDimensions() {
  jQuery('.filter-dimension').each(function () {
    resetSlider(this);
  });

  for (var dim in filterStore.dimensions) {
    filterStore[dim] = {
      min: null,
      max: null
    };
  }
}

function resetAll() {
  displayLoading();
  resetSearch();
  resetGroups();
  resetTerms();
  resetDimensions();
} // filter function for Isotope


var filterFns = {
  termFilter: function termFilter() {
    var terms = jQuery(this).data('terms'); // terms of current Element

    return filterStore.terms.length == 0 ? true : filterStore.terms.every(function (term) {
      return terms.includes(term);
    });
  },
  groupFilter: function groupFilter() {
    var group = jQuery(this).data('specs').group;
    return filterStore.groups.length == 0 ? true : filterStore.groups.includes(group);
  },
  lengthFilter: function lengthFilter() {
    var length = jQuery(this).data('specs').length;

    if (length) {
      return parseFloat(length) >= parseFloat(filterStore.dimensions.length.min) && parseFloat(length) <= parseFloat(filterStore.dimensions.length.max);
    } else {
      return false;
    }
  },
  widthFilter: function widthFilter() {
    var width = jQuery(this).data('specs').width;

    if (width) {
      return parseFloat(width) >= parseFloat(filterStore.dimensions.width.min) && parseFloat(width) <= parseFloat(filterStore.dimensions.width.max);
    } else {
      return false;
    }
  },
  heightFilter: function heightFilter() {
    var height = jQuery(this).data('specs').height;

    if (height) {
      return parseFloat(height) >= parseFloat(filterStore.dimensions.height.min) && parseFloat(height) <= parseFloat(filterStore.dimensions.height.max);
    } else {
      return false;
    }
  },
  resetFilter: function resetFilter() {
    return true;
  }
}; // // onClick Events

jQuery('.filter-reset').click(function (event) {
  displayLoading();
  resetAll();
  resetActiveFilter();
  $grid.isotope();
});

function triggerSearch() {
  displayLoading();
  var searchTerm = jQuery('.filter-search').val();
  filterStore.search = new RegExp(searchTerm, 'gi');
  $grid.isotope();
} // search events
// jQuery('.filter-search-submit').click(function(event) {
//     triggerSearch();
// });


jQuery('input.filter-search').keyup(function (event) {
  // if (event.which == 13) {
  //     triggerSearch();
  // }
  debounce(triggerSearch(), 250);
}); // change event for groups and terms (portfolio-filter)

jQuery('select.portfolio-filter').change(function (event) {
  displayLoading();
  var $this = jQuery(this);
  var data = $this.select2('data');
  data = data.map(function (i) {
    return i.id;
  });
  var filter = $this.data('filter');
  var filterType = $this.data('filtertype');
  addActiveFilter(filter);
  filterStore[filterType] = data;
  $grid.isotope();
});
jQuery(window).resize(function () {
  jQuery('select.portfolio-filter').each(function () {
    var parent = jQuery(this).parent();
    var width = parent.outerWidth();
    parent.find('.select2-container').css('width', width + 'px');
  });
}); // sort events

jQuery('select#filter-sort-by').on('change', function () {
  displayLoading();
  $grid.isotope({
    sortBy: jQuery(this).val()
  });
});
jQuery('select#filter-sort').on('change', function () {
  displayLoading();
  $grid.isotope({
    sortAscending: jQuery(this).val() == 'true'
  });
});

function displayLoading() {
  jQuery('.filter-loading').show();
}

function hideLoading() {
  jQuery('.filter-loading').hide();
} // https://davidwalsh.name/javascript-debounce-function
// Returns a function, that, as long as it continues to be invoked, will not
// be triggered. The function will be called after it stops being called for
// N milliseconds. If `immediate` is passed, trigger the function on the
// leading edge, instead of the trailing.


function debounce(func, wait, immediate) {
  var timeout;
  return function () {
    var context = this,
        args = arguments;

    var later = function later() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };

    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
}

;