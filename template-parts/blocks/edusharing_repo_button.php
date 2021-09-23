<?php
$id = get_field('id');

if (function_exists('get_repo_ticket')){
    $ticket = get_repo_ticket();
    $repo_url = get_option('es_repo_url');
    $link_text = get_field('text');
}else{
    $ticket = '';
    $repo_url = '#';
    $link_text = 'no ticket';
}

if (strpos($id, '?') !== false){
    $url = $id.'&ticket='.$ticket;
}else{
    $url = $id.'?ticket='.$ticket;
}

?>

<div class="grid-container">
<!--    <a class="wlo_repo_button button" href="--><?php //echo $repo_url.'components/collections?id='.$id.'&ticket='.$ticket; ?><!--" target="_blank">--><?php //echo $link_text; ?><!--</a>-->
    <a class="wlo_repo_button button" href="<?php echo $url; ?>" target="_blank"><?php echo $link_text; ?></a>
</div>
