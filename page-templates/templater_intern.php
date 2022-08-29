<?php /* Template Name: ernst-test */

//$all_users = get_users();
echo '<ol>';

//$mailcow = callMailcowAPI('v1/get/mailbox/ernst%40wirlernenonline.de');
$mailcow =  callMailcowAPI('v1/get/alias/all');

$email = 'wlo-test-3@wirlernenonline.de';

if (strpos($mailcow, '"address": "'.$email.'"') ){
    echo 'mail da!';
}else{
    echo 'ok :)';
    $data = '{
              "address": "'.$email.'",
              "goto": "test@ernsthaftes.de",
              "active": "1"
            }';

    $mailcow2 = json_decode(callMailcowAPI('v1/add/alias', 'POST', $data));

    if ($mailcow2[0]->type == 'success'){
        echo 'yay: ';
        echo $mailcow2[0]->msg[0];
    }
    else{
        echo 'nope: ';
        echo $mailcow2[0]->msg[0];
    }
    var_dump($mailcow2);
}

//var_dump($mailcow);

foreach ($mailcow as $mail) {

    ?>
    <li>
        <?php //print_r($mail->address); ?>
    </li>
<?php }
echo '</ol>';
?>
