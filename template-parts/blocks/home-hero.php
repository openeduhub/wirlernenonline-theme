
<?php
// get the school subjects via graphQL
$data = '{
  grundschule: facets(size: 10000, filters: [{field: "valuespaces.educationalContext.key.keyword", terms: ["https://w3id.org/openeduhub/vocabs/educationalContext/grundschule"]}]) {
    disciplines {
      buckets {
        doc_count
        key
      }
    }
  }
  sek1: facets(size: 10000, filters: [{field: "valuespaces.educationalContext.key.keyword", terms: ["https://w3id.org/openeduhub/vocabs/educationalContext/sekundarstufe_1"]}]) {
    disciplines {
      buckets {
        doc_count
        key
      }
    }
  }
  sek2: facets(size: 10000, filters: [{field: "valuespaces.educationalContext.key.keyword", terms: ["https://w3id.org/openeduhub/vocabs/educationalContext/sekundarstufe_2"]}]) {
    disciplines {
      buckets {
        doc_count
        key
      }
    }
  }
  beruf: facets(size: 10000, filters: [{field: "valuespaces.educationalContext.key.keyword", terms: ["https://w3id.org/openeduhub/vocabs/educationalContext/berufliche_bildung"]}]) {
    disciplines {
      buckets {
        doc_count
        key
      }
    }
  }
}
';

$curl_post_data = array("query" => $data);
$data_string =  json_encode($curl_post_data);
//$url = 'https://suche.wirlernenonline.de/relay/graphql';
$url = 'https://staging.wirlernenonline.de/relay/graphql';

try {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/json; charset=utf-8'
        )
    );
    $response = curl_exec($curl);
    if($response === false) {
        echo 'curl error';
        trigger_error(curl_error($curl), E_USER_WARNING);
        return false;
    }
} catch (Exception $e) {
    echo 'curl error: '.$e->getMessage();
    trigger_error($e->getMessage(), E_USER_WARNING);
    return false;
}
curl_close($curl);
$response = json_decode($response);
//var_dump($response->data->grundschule->facets[1]->buckets);

?>

<div class="grid-container home-hero">
    <div class="grid-x grid-margin-x">
      <div class="medium-12 cell">
        <h1><?php the_field('description') ?></h1>
      </div>
      <div class="cell medium-10 medium-offset-1 large-8 large-offset-2">

        <form action="https://suche.wirlernenonline.de/de/search" method="GET" class="home-hero__form">
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
                    <button type="button" class="home-hero__tablinks" onclick="openSchool(event, 'grundschule')">Grundschule</button>
                    <button type="button" class="home-hero__tablinks" onclick="openSchool(event, 'sekundarstufe_1')">Sekundarstufe I</button>
                    <button type="button" class="home-hero__tablinks" onclick="openSchool(event, 'sekundarstufe_2')">Sekundarstufe II</button>
                    <button type="button" class="home-hero__tablinks" onclick="openSchool(event, 'berufliche_bildung')">Berufliche Bildung</button>
                    <button type="button" class="home-hero__tablinks" id="home-hero__close" aria-label="Close Search Filter" onclick="close_tabs()">×</button>
                </div>

                <!-- Tab content -->
                <div id="grundschule" class="tabcontent">
                    <div class="subjects">
                        <?php
                        $list_grundschule = get_field('list_grundschule');
                        echo home_hero_fill_subjectbuttons($response, 'grundschule', 'Grundschule', $list_grundschule);
                        ?>
                </div>

                <div id="sekundarstufe_1" class="tabcontent">
                    <div class="subjects">
                    <?php
                    $list_sek1 = get_field('list_sek_1');
                    echo home_hero_fill_subjectbuttons($response, 'sek1', 'Sekundarstufe I', $list_sek1);
                    ?>
                </div>

                <div id="sekundarstufe_2" class="tabcontent">
                    <div class="subjects">
                        <?php echo home_hero_fill_subjectbuttons($response, 'sek2', 'Sekundarstufe II', $list_sek1); ?>
                </div>

                <div id="berufliche_bildung" class="tabcontent">
                    <div class="subjects">
                        <?php
                        $list_beruf = get_field('list_beruf');
                        echo home_hero_fill_subjectbuttons($response, 'beruf', 'Berufliche Bildung', $list_beruf);
                        ?>
                </div>
            </div>


            <script type="text/javascript">

                function wloSearch(fach, schoolType) {
                    const searchTerm = document.getElementById("search").value;
                    //window.open('https://suche.wirlernenonline.de/de/search?filters={"disciplines":["' + fach + '"],"educationalContexts":["' + schoolType + '"]}&q=' + searchTerm, '_self');
                    window.open('https://staging.wirlernenonline.de/de/subjects-portal/' + schoolType + '/' + fach, '_self');
                }

                function wloToggleMenu(schoolType) {
                    if (document.getElementById('extra_'+schoolType).style.display == 'none'){
                        document.getElementById('extra_'+schoolType).style.display = 'flex';
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
                }
            </script>
        </form action="/lernstoff" method="GET">

      </div>
    </div>
  </div>
