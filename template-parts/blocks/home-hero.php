
<?php
require_once(get_template_directory().'/functions/wlo-config.php');

// get the school subjects via graphQL
$data = '{
  subjectsPortals(size: 1000, language: de) {
    grundschule {
      ...subjectsPortalDiscipline
    }
    sekundarstufe_1 {
      ...subjectsPortalDiscipline
    }
    sekundarstufe_2 {
      ...subjectsPortalDiscipline
    }
    berufliche_bildung {
      ...subjectsPortalDiscipline
    }
  }
}

fragment subjectsPortalDiscipline on SubjectsPortalDiscipline {
  id
  url
  doc_count
}';
$response = callWloGraphApi($data);

?>

<div class="grid-container home-hero">
    
    <div class="wunschbox-cta" id="wunschbox-cta">
        <img class="wunschbox-close" onclick="toggleWunschbox()" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/wunschbox-close.svg">

        <a href="https://wirlernenonline.de/wunschbox/">
            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/wunschbox-cta-2.svg">
        </a>
    </div>

    <div class="wunschbox-small" id="wunschbox-small" onclick="toggleWunschbox()">
        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/wunschbox-cta-small.svg">
    </div>

    <script>
        function toggleWunschbox() {
            jQuery('#wunschbox-cta').toggle('slow');
            jQuery('#wunschbox-small').toggle('slow');
        }
    </script>
    
    <div class="grid-x grid-margin-x">
      <div class="medium-12 cell">
        <h1><?php the_field('description') ?></h1>
      </div>
      <div class="cell medium-10 medium-offset-1 large-8 large-offset-2">

        <form action="<?php echo WLO_SEARCH; ?>de/search" method="GET" class="home-hero__form">
          <div class="search-container">
            <p><?php the_field('search_description'); ?></p>
            <div class="input-group">
              <input class="input-group-field" type="search" name="q" id="search" aria-label="Search" autocomplete="off">
              <div class="input-group-button">
                <input type="submit" class="button success" value="Suche">
              </div>
            </div>
          </div>

            <div class="home-hero__filter full-width">
                <div class="home-hero__tab">
                    <button type="button" class="home-hero__tablinks" onclick="openSchool(event, 'grundschule')">Primarstufe</button>
                    <button type="button" class="home-hero__tablinks" onclick="openSchool(event, 'sekundarstufe_1')">Sekundarstufe I</button>
                    <button type="button" class="home-hero__tablinks" onclick="openSchool(event, 'sekundarstufe_2')">Sekundarstufe II</button>
                    <button type="button" class="home-hero__tablinks" onclick="openSchool(event, 'berufliche_bildung')">Berufliche Bildung</button>
                    <button type="button" class="home-hero__tablinks" id="home-hero__close" aria-label="Close Search Filter" onclick="close_tabs()">×</button>
                </div>

                <!-- Tab content -->
                <div id="grundschule" class="tabcontent">
                    <div class="subject_grid">
                        <?php echo home_hero_fill_subjectbuttons($response, 'grundschule', get_field('list_grundschule')); ?>
                    </div>
                </div>

                <div id="sekundarstufe_1" class="tabcontent">
                    <div class="subject_grid">
                        <?php echo home_hero_fill_subjectbuttons($response, 'sekundarstufe_1', get_field('list_sek_1'));  ?>
                    </div>
                </div>

                <div id="sekundarstufe_2" class="tabcontent">
                    <div class="subject_grid">
                        <?php echo home_hero_fill_subjectbuttons($response, 'sekundarstufe_2', get_field('list_sek_1'));  ?>
                    </div>
                </div>

                <div id="berufliche_bildung" class="tabcontent">
                    <div class="subject_grid">
                        <?php echo home_hero_fill_subjectbuttons($response, 'berufliche_bildung', get_field('list_beruf')); ?>
                    </div>
                </div>
            </div>


            <script type="text/javascript">

                function wloToggleMenu(schoolType) {
                    if (document.getElementById('extra_'+schoolType).style.display == 'none'){
                        document.getElementById('extra_'+schoolType).style.display = 'grid';
                        document.getElementById('extraButton_'+schoolType).innerHTML = 'weniger Fächer';
                    }else{
                        document.getElementById('extra_'+schoolType).style.display = 'none';
                        document.getElementById('extraButton_'+schoolType).innerHTML = 'weitere Fächer';
                    }
                }

                function close_tabs(e) {
                    // Declare all variables
                    let i, tabcontent, tablinks;
                    e = e || window.event;

                    // Get all elements with class="tabcontent" and hide them
                    tabcontent = document.getElementsByClassName("tabcontent");
                    for (i = 0; i < tabcontent.length; i++) {
                        tabcontent[i].style.display = "none";
                    }

                    // Get all elements with class="tablinks" and remove the class "active"
                    tablinks = document.getElementsByClassName("home-hero__tablinks");
                    for (i = 0; i < tablinks.length; i++) {
                        tablinks[i].className = tablinks[i].className.replace(" active", "");
                    }

                    document.getElementById('home-hero__close').style.display = "none";
                }

                function openSchool(e, schulform) {
                    // Declare all variables
                    let i, tabcontent, tablinks;
                    e = e || window.event;

                    // Get all elements with class="tabcontent" and hide them
                    tabcontent = document.getElementsByClassName("tabcontent");
                    for (i = 0; i < tabcontent.length; i++) {
                        tabcontent[i].style.display = "none";
                    }

                    // Get all elements with class="tablinks" and remove the class "active"
                    tablinks = document.getElementsByClassName("home-hero__tablinks");
                    for (i = 0; i < tablinks.length; i++) {
                        tablinks[i].className = tablinks[i].className.replace(" active", "");
                    }

                    document.getElementById('home-hero__close').style.display = "block";

                    // Show the current tab, and add an "active" class to the button that opened the tab
                    document.getElementById(schulform).style.display = "block";
                    e.currentTarget.className += " active";
                    document.getElementById(schulform).scrollIntoView();
                }
            </script>
        </form action="/lernstoff" method="GET">

      </div>
    </div>
  </div>
