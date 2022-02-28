<?php
/*
Template Name: WLO-Themenseite
Template Post Type: post, page, portal
 */

get_header();
?>
<?php
while (have_posts()) : the_post(); ?>

    <div class="portal">

        <div class="portal-wrapper-header">
            <?php require_once(get_template_directory().'/template-parts/blocks/fachportal_header_block.php');?>
        </div>

        <div class="portal-wrapper">
            <div class="portal-wrapper-left">
                <div class="fachportal-header-accordion">

                    <div class="wlo-accordion-wrapper" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 0.2);">
                        <button class="wlo-accordion" id="wlo-accordion-<?php echo $accordionID; ?>">
                            <h2>Die neusten geprüften Inhalte für dich!</h2>
                            <img class="wlo-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg"  alt="Inhalte ein odder ausklappen">
                        </button>

                        <div class="wlo-accordion-content" id="wlo-accordion-content-<?php echo $sliderId; ?>">

                            <?php
                            if (!empty($contentArray)){
                                foreach (array_slice($contentArray, 0, get_field('content_count')) as $content) { ?>
                                    <div class="widget-content <?php if (!empty($content['resourcetype'])){ foreach ($content['resourcetype'] as $type){ echo $type.' '; } } ?>">


                                        <?php if (!empty($content['image_url'])) { ?>
                                            <img class="main-image" src="<?php echo $content['image_url']; ?>" alt="Cover: <?php echo $content['title']; ?>">
                                        <?php } ?>
                                        <div class="content-info">
                                            <div class="content-header">
                                                <?php if ($content['source']){ ?>
                                                    <p class="content-source"><?php echo $content['source']; ?></p>
                                                <?php } ?>
                                                <img class="badge" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/badge_green.svg"  alt="Auszeichnung: geprüfter Inhalt">
                                                <?php if ($content['oer']){ ?>
                                                    <div class="badge ">OER</div>
                                                <?php } ?>
                                            </div>
                                            <div class="content-title"><?php echo $content['title']; ?></div>
                                            <p class="content-description"><?php echo $content['description'] ?></p>
                                            <div class="content-meta">
                                                <?php if (!empty($content['resourcetype'])){
                                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/img_icon.svg"  alt="Materialart">';
                                                    echo '<p>';
                                                    $i = 0;
                                                    foreach ($content['resourcetype'] as $type){
                                                        if(++$i === count($content['resourcetype'])) {
                                                            echo $type;
                                                        }else{
                                                            echo $type.', ';
                                                        }
                                                    }
                                                    echo '</p>';
                                                } ?>
                                            </div>
                                            <div class="content-meta">
                                                <?php if (!empty($content['subjects'])){
                                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/subject_icon.svg"  alt="Fächer">';
                                                    echo '<p>';
                                                    $i = 0;
                                                    foreach ($content['subjects'] as $subject) {
                                                        if(++$i === count($content['subjects'])) {
                                                            echo $subject;
                                                        }else{
                                                            echo $subject.', ';
                                                        }
                                                    }
                                                    echo '</p>';
                                                } ?>
                                            </div>
                                            <div class="content-meta">
                                                <?php if (!empty($content['educationalcontext'])){
                                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/class_icon.svg"  alt="Bildungsebene">';
                                                    echo '<p>';
                                                    $i = 0;
                                                    foreach ($content['educationalcontext'] as $subject) {
                                                        if(++$i === count($content['educationalcontext'])) {
                                                            echo $subject;
                                                        }else{
                                                            echo $subject.', ';
                                                        }
                                                    }
                                                    echo '</p>';
                                                } ?>
                                            </div>

                                            <a class="content-button" href="<?php echo $content['content_url']; ?>" target="_blank" aria-label="Zum-Inhalt: <?php echo $content['title']; ?>">Zum Inhalt</a>

                                        </div>


                                    </div>
                                <?php }
                            } ?>
                        </div>

                        <div class="wlo-accordion-bottom"></div>

                    </div>
                </div>
            <?php the_content(); ?>
            </div>
            <div class="portal-wrapper-right">
                <div class="fachportal-content-popup">
                    <script>
                        window.EDU_SHARING_API_URL = 'https://redaktion-staging.openeduhub.net/edu-sharing/rest';
                    </script>
                    <oeh-details-embedded></oeh-details-embedded>
                    <script>
                        document.getElementsByTagName('oeh-details-embedded')[0].addEventListener('closed', () => {
                            console.log('received closed event');
                            jQuery(".portal-wrapper-right").hide('slow');
                        });
                    </script>
                </div>
            </div>
        </div>

    </div>

    <script>
        function showContentPopup(nodeID){
            console.log("showContentPopup");
            document.getElementsByTagName("oeh-details-embedded")[0].setAttribute("node-id", nodeID);
            jQuery(".portal-wrapper-right").show('slow');
        }

        jQuery(".fachportal-content-popup").click(function(e) {
            // Do something
            e.stopPropagation();
        });

        jQuery(".fachportal-content-popup").click(function(){
            console.log("hideContentPopup");
            //jQuery(".portal-wrapper-right").hide();
        });
    </script>

    </div>
<?php
endwhile;
get_footer();
