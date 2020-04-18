<ul class="attributes margin-bottom-2">
  <?php if( get_field( 'licence' ) ) : ?>
    <li>
      <?php
      $licences = get_field( 'licence' );
      if( $licences ):
        foreach ($licences as $licence):?>
        <span class="multi-select"><?php echo $licence['label']; ?></span>
      <?php endforeach;
      ?>
    <?php endif; ?>
  </li>
<?php endif; ?>
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
</ul>
