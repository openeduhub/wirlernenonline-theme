  <div class="grid-container home-hero">
    <div class="grid-x grid-margin-x">
      <div class="medium-12 cell">
        <h1><?php the_field('description') ?></h1>
      </div>
      <div class="cell medium-10 medium-offset-1 large-8 large-offset-2">

        <form action="/lernstoff" method="GET">
          <div class="search-container">
            <p><?php the_field('search_description'); ?></p>
            <div class="input-group">
              <input class="input-group-field" type="search" name="search" autocomplete="off">
              <div class="input-group-button">
                <input type="submit" class="button success" value="Suche">
              </div>
            </div>
            <fieldset class="grid-x">
              <div class="cell-shrink home-hero__checkbox">
                <input id="material" type="checkbox" name="sourcetype" value="004"><label for="material">Lehrmaterial</label>
                <span class="home-hero__checkbox__alias"></span>
              </div>
              <div class="cell-shrink home-hero__checkbox">
                <input id="methods" type="checkbox" name="sourcetype" value="005,007"><label for="methods" name="fields">Lern-Tools</label>
                <span class="home-hero__checkbox__alias"></span>
              </div>
              <div class="cell-shrink home-hero__checkbox">
                <input id="databases" type="checkbox" name="sourcetype" value="001,002,003"><label for="databases">Medien</label>
                <span class="home-hero__checkbox__alias"></span>
              </div>
            </fieldset>
          </div>
        </form>

      </div>
    </div>
  </div>
