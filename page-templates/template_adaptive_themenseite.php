<?php
/*
Template Name: WLO-Adaptive-Themenseite
Template Post Type: post, page, portal
 */

get_header();

wp_enqueue_script('Subject', get_template_directory_uri() . '/src/assets/js/utils/Subject.js', array(), '', true);

$variablesJsonString = file_get_contents(__DIR__ . '/../src/assets/data/variables.json');
$variables = json_decode($variablesJsonString, true, flags: JSON_THROW_ON_ERROR);

?>

<div class="variable-select-bar">
    <?php foreach ($variables as &$variable) { ?>
        <div class="select-container">
            <label for="select-<?php echo $variable['key']; ?>">
                <?php echo $variable['label']; ?>
            </label>
            <select
                id="select-<?php echo $variable['key']; ?>"
                name="<?php echo $variable['key']; ?>">
                <?php foreach ($variable['options'] as &$option) { ?>
                    <option value="<?php echo $option['value']; ?>">
                        <?php echo $option['label']; ?>
                    </option>
                <?php }
                unset($option);
                ?>
            </select>
        </div>
    <?php }
    unset($variable);
    ?>
    <script>
        jQuery(document).ready(() => {
            const variables = <?php echo $variablesJsonString; ?>;

            /**
             * Sets the given parameter an navigates to the updated URL.
             */
            function updateGetParameter(key, value) {
                params = new URLSearchParams(window.location.search);
                params.set(key, value);
                window.location.search = params.toString();
            }

            /** 
             * Returns a key-value map for all variables taken from GET parameters.
             */
            function getInitialValues() {
                params = new URLSearchParams(window.location.search);
                return variables.reduce((acc, variable) => {
                    acc[variable.key] = params.get(variable.key) ?? variable.options[0].value;
                    return acc;
                }, {});
            }
            
            window.pageVariablesSubject = new Subject(getInitialValues());
            for (const variable of variables) {
                const selectElement = jQuery(`#select-${variable.key}`);
                selectElement.val(window.pageVariablesSubject.value[variable.key]);
                selectElement.on('change', function() {
                    updateGetParameter(variable.key, this.value);
                    // Dynamic update
                    //
                    // window.pageVariablesSubject.next({
                    //     ...window.pageVariablesSubject.value,
                    //     [variable.key]: this.value
                    // });
                });
            }
        })
    </script>
</div>



<?php while (have_posts()) : the_post(); ?>
    <?php the_content(); ?>
<?php
endwhile;
get_footer();
