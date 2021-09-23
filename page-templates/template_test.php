<?php
/*
Template Name: WirLernenOnline-TEST
Template Post Type: post, page, portal
 */

get_header();
?>
<?php
while (have_posts()) : the_post(); ?>

    <div class="wlo-page">

        <div class="wlo-header">
            <div class="wlo-header-wrapper">
                <div class="wlo-header-content">
                    <h1><?php the_title(); ?></h1>
                    <?php the_excerpt(); ?>
                </div>
                <div class="wlo-header-bottom"></div>
            </div>
        </div>

        <div class="wlo-wrapper">
            <?php
/*
            $requestDiciplines = array('http://w3id.org/openeduhub/vocabs/discipline/120', 'http://w3id.org/openeduhub/vocabs/discipline/240');
            $idDE = 'http://w3id.org/openeduhub/vocabs/discipline/120';

            $diciplines = getWloVocaps('discipline')->hasTopConcept;
            $disciplinesMapped = [];
            foreach ($requestDiciplines as $currentDicipline){
                foreach ($diciplines as $dicipline){
                    if ($dicipline->id == $currentDicipline){
                        $disciplinesMapped[] = $dicipline->prefLabel->de;
                    }
                }
            }

            var_dump($disciplinesMapped);
*/

            //only for testing
            echo 'wloUserdata:<br>';
            //var_dump(wlo_after_email_confirmation());

            um_fetch_user( 134 );
            echo 'Name: '.um_user('display_name').'<br>';
            echo 'Email: '.um_user('user_email').'<br>';
            echo 'WLO-Email: '.um_user('user_email_15').'<br>';
            echo 'wlo-mail-check: '.um_user('wlo-mail-check')[0].'<br>';

            echo '<br>';

            //$mailData = file_get_contents('http://appserver8.metaventis.com/mailapi/api.php?wlomail='.um_user('user_email_15').'&action=create&maildest='.um_user('user_email'));

            //var_dump($mailData);

            ?>
        </div>

    </div>

<?php
endwhile;
get_footer();
