<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachportal: Header</div>';
} ?>

<div class="fachportal-header-block">
    <div class="description">

    </div>
    <div class="collections">

    </div>
    <div class="content-info">
        <div class="header">
            <h3>Gerüfte Inhalte</h3>
        </div>

        <div class="diagram">
            <div class="top-left quad">
                <div class="text">
                    <img src="">
                    <p>Lerninhalte</p>
                </div>
                <div class="count" id="Lerninhalte-count">
                    <p>0 / 0</p>
                </div>
            </div>
            <div class="top-right quad">
                <div class="text">
                    <img src="">
                    <p>Methoden</p>
                </div>
                <div class="count" id="Methoden-count">
                    <p>0 / 0</p>
                </div>
            </div>
            <div class="bottom-left quad">
                <div class="text">
                    <img src="">
                    <p>Gut zu Wissen</p>
                </div>
                <div class="count" id="Gut zu Wissen-count">
                    <p>0 / 0</p>
                </div>
            </div>
            <div class="bottom-right quad">
                <div class="text">
                    <img src="">
                    <p>Tools</p>
                </div>
                <div class="count" id="Tools-count">
                    <p>0 / 0</p>
                </div>
            </div>
        </div>
    </div>
</div>


<?php if (is_admin()) {
    echo '</div>';
} ?>
