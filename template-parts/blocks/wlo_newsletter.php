<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: Newsletter</div>';
} ?>


<form class="wlo-newsletter ml-block-form" action="https://static.mailerlite.com/webforms/submit/c6v7a9" data-code="c6v7a9" method="post" target="_blank">

    <h3>Newsletter</h3>
    <p><?php echo get_field('newsletterTex'); ?>
        Mit der Anmeldung zu unserem Newsletter erhälst du einen Bestätigungsemail von uns. Erst mit der Bestätigung
        dieser Email landet deine Mailadresse in unserem Newsletter-Verteiler. Deine Daten sind uns wichtig und werden
        wie unten Beschrieben verwendet. Wir sind nur interessiert daran dich auf dem Laufenden zu halten.
    </p>

    <h4>Datenschutzhinweise </h4>
    <p>
        Indem ich meine E-Mail-Adresse eintrage, erkläre ich mich damit einverstanden, dass edu-sharing.net und
        Wikimedia Deutschland mich aufgrund meiner Einwilligung (Art. 6 Abs. 1 lit. a) DSGVO) per E-Mail bis auf
        Widerruf, über Freie Bildung zum Mitmachen informieren und die hierzu erforderlichen Datenverarbeitungen
        vornimmt inklusive der Analyse meines Nutzungsverhaltens durch individuelle Messung, Speicherung und
        Auswertung von Öffnungs- und Klickraten in Empfängerprofilen zu Zwecken der Gestaltung künftiger Newsletter
        entsprechend den Interessen unserer Leser. Ich kann meine Einwilligung jederzeit mit Wirkung für die Zukunft
        per E-Mail an <a href="mailto:redaktion@wirlernenonline.de">redaktion@wirlernenonline.de</a> widerrufen. Nähere Informationen zur Datenverarbeitung und zu
        meinen Rechten finde ich unter <a href="https://wirlernenonline.de/datenschutz/">Datenschutz</a>.
    </p>

    <label class="field_label required" for="dsgvo">
        <input type="checkbox" name="dsgvo" value="" aria-label="Datenschutz zustimmen" required=""> Ich habe den Datenschutzhinweis gelesen *
        <span class="form-error">Bitte stimmen sie dem Datenschutzhinweis zu.</span>
    </label>

    <div class="ml-form-formContent">
        <div class="ml-form-fieldRow ml-last-item">
            <div class="ml-field-group ml-field-email ml-validate-email ml-validate-required">
                <input aria-label="email" aria-required="true" type="email" class="form-control" data-inputmask="" name="fields[email]" placeholder="Email" autocomplete="email">
            </div>
        </div>
    </div>
    <div class="ml-form-embedPermissions" style="">
        <div class="ml-form-embedPermissionsContent default privacy-policy">
            <p>Du kannst diese Anmeldung jederzeit rückgängig machen.</p>
        </div>
    </div>
    <input type="hidden" name="ml-submit" value="1">
    <div class="ml-form-embedSubmit">
        <button type="submit" class="primary">Anmelden</button>
        <button disabled="disabled" style="display:none" type="button" class="loading"> <div class="ml-form-embedSubmitLoad"></div> <span class="sr-only">Loading...</span> </button>
    </div>
    <input type="hidden" name="anticsrf" value="true">
</form>

<?php if (is_admin()) {
    echo '</div>';
} ?>
