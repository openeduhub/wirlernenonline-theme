<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Quellenübersicht-Tabelle</div>';
    $tableHeaderOffset = 32;
} ?>

<div class="wlo-content-block">

    <table id="wlo_source_table" class="wlo_source_table table display">
        <thead>
            <tr>
                <th class="wlo_big_header">Vorhandene Quellen</th>
                <th class="wlo_big_header js-sort-number">Erfasste Inhalte</th>
                <th class="wlo_big_header">Fächerzuordnung</th>
                <th class="wlo_big_header">Erschließungs-Status</th>
                <th class="wlo_big_header">Qualitätskriterien-Check</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th class="wlo_big_header">Vorhandene Quellen</th>
                <th class="wlo_big_header js-sort-number">Erfasste Inhalte</th>
                <th class="wlo_big_header">Fächerzuordnung</th>
                <th class="wlo_big_header">Erschließungs-Status</th>
                <th class="wlo_big_header">Qualitätskriterien-Check</th>
            </tr>
        </tfoot>
    </table>

    <script>
        <?php
        $tableHeaderOffset = 0;
        if (is_admin_bar_showing()) {
            $tableHeaderOffset = 32;
        }
        ?>
        jQuery(document).ready( async function () {
            let table = jQuery('#wlo_source_table').DataTable({
                ajax: "<?php echo get_template_directory_uri(); ?>/functions/datatables_ajax.php",
                columns: [
                    { "data": "title" },
                    { "data": "count" },
                    { "data": "subjects" },
                    { "data": "status" },
                    { "data": "check", "orderable": false}
                ],
                deferRender: true,
                language: {
                    url: "<?php echo get_template_directory_uri(); ?>/src/assets/js/datatables/German.json",
                },
                lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Alle"]],
                fixedHeader: {
                    header: true,
                    footer: false,
                    headerOffset: <?php echo $tableHeaderOffset; ?>
                }
            });

            let completed = 25;
            let total = 100;
            const url = "<?php echo get_template_directory_uri(); ?>/functions/datatables_ajax.php";

            while (completed < total){
                const response = await fetch(url, {
                    method: 'POST',
                    body: new URLSearchParams({
                        maxItems: 500,
                        skipCount: completed
                    })
                });
                const msg = await response.json();

                total = msg.total;
                completed += 500;
                table.rows.add( msg.data ).draw();
            }

        });
    </script>
</div>




<?php if (is_admin()) {
    echo '</div>';
} ?>
