jQuery(document).ready(function() { 
  jQuery('.edu-filter').select2({ multiple: true});

  var filterStore = {
    search: '',
    fields: [],
    roles: [],
    licenses: [],
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
      var fieldsResult = selectFilterResults('fields', $this);
      var rolesResult = selectFilterResults('roles', $this);
      var licensesResult = selectFilterResults('licenses', $this);

      return searchResult && fieldsResult && rolesResult && licensesResult;
    },
  }); 

  $grid.isotope('on', 'arrangeComplete', function(filteredItems) {
    var $noMatchEl = $grid.find('.edu-filter__nomatch');
    console.log($noMatchEl);
    if (filteredItems.length === 0) {
      if ($noMatchEl.length === 0) {
        $grid.append('<div class="edu-filter__nomatch">Leider wurden keine Treffer gefunden</div>');
      }
    } else {
      $grid.find('.edu-filter__nomatch').remove();
    }
  });

  resetFilters();

  function triggerSearch() {
    var searchInput = jQuery('.edu-filter__search').val();
    filterStore.search = new RegExp(searchInput, 'gi'); 

    $grid.isotope();
  }

  function selectFilterResults(type, $el) {
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

