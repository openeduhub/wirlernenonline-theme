<ul class="attributes margin-bottom-2">

    <?php if( get_field( 'costs' ) ) : ?>
        <li>
            <?php
            $costs = get_field( 'costs' );
            ?>
            <?php echo esc_html($costs['label']); ?>
        </li>
    <?php endif; ?>


    <?php if( get_field( 'advertisment' ) ) {
        echo '<li>mit Werbung</li>';
    }else{
        echo '<li>werbefrei</li>';
    } ?>

    <?php if( get_field( 'nutzung' ) ) : ?>
      <li>
        <?php
        $nutzungs = get_field( 'nutzung' );
        if( $nutzungs ):
          foreach ($nutzungs as $nutzung):?>
          <span class="multi-select"><?php echo $nutzung['label']; ?></span>
        <?php endforeach;
        ?>
      <?php endif; ?>
    </li>
    <?php endif; ?>

    <?php if( get_field( 'dsgvo' ) ) : ?>
    <li>
      <?php
      $dsgvo = get_field( 'dsgvo' );
      ?>
      <?php echo esc_html($dsgvo['label']); ?>
    </li>
    <?php endif; ?>

    <?php if( get_field( 'accessibility' ) ) : ?>
    <li>
      <?php
      $accessibility = get_field( 'accessibility' );
      ?>
      <?php echo esc_html($accessibility['label']); ?>
    </li>
    <?php endif; ?>

    <?php if( get_field( 'lang' ) ) : ?>
        <li>
            Sprache: <?php
            $lang = get_field( 'lang' );
            ?>
            <?php echo esc_html($lang['label']); ?>
        </li>
    <?php endif; ?>

    <?php if( get_field( 'anbieter' ) ) : ?>
    <li>
      Anbieter: <?php the_field('anbieter') ?>
    </li>
    <?php endif; ?>

    <?php if( get_field( 'technical_req' ) ) : ?>
        <li>
            <?php
            $reqs = get_field( 'technical_req' );
            if( $reqs ):
                foreach ($reqs as $req):?>
                    <span class="multi-select"><?php echo $req['label']; ?></span>
                <?php endforeach;
                ?>
            <?php endif; ?>
        </li>
    <?php endif; ?>

    <?php if( get_field( 'api' ) ) : ?>
        <li>
            <?php
            $apis = get_field( 'api' );
            if( $apis ):
                foreach ($apis as $api):?>
                    <span class="multi-select"><?php echo $api['label']; ?></span>
                <?php endforeach;
                ?>
            <?php endif; ?>
        </li>
    <?php endif; ?>

    <?php if( get_field( 'storage' ) ) : ?>
        <li>
            <?php
            $storage = get_field( 'storage' );
            if( $storage ):
                foreach ($storage as $stor):?>
                    <span class="multi-select"><?php echo $stor['label']; ?></span>
                <?php endforeach;
                ?>
            <?php endif; ?>
        </li>
    <?php endif; ?>

</ul>
