<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: Redaktions-Menü</div>';
} ?>

<?php
$menuValues = array(
        "37163" => "Fachredaktion",
        "41022" => "Community-Redaktion",
        "42813" => "Inhalteredaktion",
);
?>

<div class="wlo-redaktion-menu">
    <select onchange="location = this.value;">
        <?php
        foreach ($menuValues as $key => $val){
            if (get_field('pageType')['label'] == $val){
                echo '<option value="'.get_page_link($key).'" selected >'.$val.'</option>';
            }else{
                echo '<option value="'.get_page_link($key).'" >'.$val.'</option>';
            }
        }
        ?>
    </select>
</div>


<?php if (is_admin()) {
    echo '</div>';
} ?>
