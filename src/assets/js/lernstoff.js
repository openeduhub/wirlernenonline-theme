jQuery(document).ready(function() {
  jQuery('.edu-filter').select2({ multiple: true});

  var filterStore = {
    search: '',
    fields: [],
    roles: [],
    licenses: [],
    schooltype: [],
    sourcetype: [],
    tags: [],
  };

  var $grid = jQuery('.edu-list');
  $grid.isotope({
    itemSelector: '.edu-item',
    masonry: {
      columnWidth: '.edu-item',
      horizontalOrder: true
    },
    filter: function() {
      var $this = jQuery(this);
      var searchResult = filterStore.search ?
        $this.find('.edu-item__title').html().match(filterStore.search) :
        true;
      var fieldsResult = selectFilterResultsOr('fields', $this);
      var rolesResult = selectFilterResultsOr('roles', $this);
      var licensesResult = selectFilterResultsOr('licenses', $this);
      var schooltypeResult = selectFilterResultsOr('schooltype', $this);
      var sourcetypeResult = selectFilterResultsOr('sourcetype', $this);
      var tagsResult = selectFilterResultsOr('tags', $this);

      return searchResult && fieldsResult && rolesResult && licensesResult && schooltypeResult && sourcetypeResult && tagsResult;
    },
  });

  $grid.isotope('on', 'arrangeComplete', function(filteredItems) {
    var $noMatchEl = $grid.find('.edu-filter__nomatch');
    if (filteredItems.length === 0) {
      if ($noMatchEl.length === 0) {
        $grid.append('<div class="edu-filter__nomatch callout alert">Leider wurden keine Treffer gefunden</div>');
      }
    } else {
      $grid.find('.edu-filter__nomatch').remove();
    }
  });

  resetFilters();
  readFromURL(filterStore, $grid);

  function triggerSearch() {
    var searchInput = jQuery('.edu-filter__search').val();
    filterStore.search = new RegExp(searchInput, 'gi');

    $grid.isotope();
  }

  function selectFilterResultsAnd(type, $el) {
    if (filterStore[type].length !== 0) {
      return filterStore[type].every(function(entry) {
        var entries = $el.data(type);
        entries = entries.toString();
        if (entries === '') return false;
        entries = entries.includes(',') ? entries.split(',') : [entries];
        return entries.includes(entry);
      })
    } else {
      return true;
    }
  }

  function selectFilterResultsOr(type, $el) {
    if (filterStore[type].length !== 0) {
      return filterStore[type].some(function(entry) {
        var entries = $el.data(type);
        entries = entries.toString();
        if (entries === '') return false;
        entries = entries.includes(',') ? entries.split(',') : [entries];
        return entries.includes(entry);
      })
    } else {
      return true;
    }
  }

  function resetFilters() {
    jQuery('.edu-filter__search').val('').trigger('change');
    jQuery('.edu-filter').each(function() {
      jQuery(this).val(null).trigger('change');
    });

    filterStore = {
      search: '',
      fields: [],
      roles: [],
      licenses: [],
      schooltype: [],
      sourcetype: [],
      tags: [],
    };
  }

  jQuery('.edu-filter__search').on('keyup change',function(e) {
    triggerSearch();
  });

  jQuery('.edu-filter').change(function(e) {
    var $this = jQuery(this);
    var data = $this.select2('data');
    data = data.map(function(d) {
      return d.id;
    })
    var filter = $this.data('filter');

    filterStore[filter] = data;
    $grid.isotope();
  });

  jQuery('.edu-filter__reset').click(function(e) {
    resetFilters();
  });
});

// Get query variable
function getQueryVariable(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
            var pair = vars[i].split("=");
            if(pair[0] == variable){return pair[1];}
    }
    return(false);
}

function readFromURL(store, $grid) {
  var qVars = ['fields', 'roles', 'licenses', 'schooltype', 'sourcetype', 'tags'];

  for (var qv of qVars) {
    var read = getQueryVariable(qv);
    console.log(read);
    if (read) {
      store[qv] =  read.split(',');
      jQuery('.edu-filter[data-filter="' + qv + '"]').val(store[qv]).trigger('change');
    }
  }

  $grid.isotope();
}
