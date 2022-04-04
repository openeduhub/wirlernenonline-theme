<?php /* Template Name: ernst-test */

$all_users = get_users();
echo '<ol>';
foreach ($all_users as $user) {
    $mailData = json_decode(file_get_contents('http://appserver8.metaventis.com/mailapi/api.php?action=getWloMail&mail='.esc_html($user->user_email)));

    ?>
    <li>
        <span><?php echo esc_html($user->user_email) . ' : ' . esc_html($user->wloEmail); ?></span><br>
        <?php foreach ($mailData as $mail){
            if ( empty($user->wloEmail && !empty($mailData[0]))  ) {
                echo 'update...<br>';
                update_user_meta( $user->id, 'wloEmail',  $mailData[0] );
            }
            ?>
            <span>Wlo-mail: <?php echo $mailData[0]; ?></span><br>
        <?php } ?>
        <span>-------------</span>
    </li>
<?php }
echo '</ol>';
?>
