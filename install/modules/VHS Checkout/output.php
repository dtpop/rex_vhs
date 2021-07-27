<?php
if (rex::isBackend()) {
    echo rex_view::info('Checkout Zusammenfassung (Ausgabe nur im Frontend)');
    return;
}
$cart = vhs_cart::get_cart();
if ($cart && isset($cart['kurs'])) {
    $kurs = $cart['kurs'];
    $userdata = vhs_checkout::get_user_data();

    // dump($userdata);

    $kurspreis = $kurs['preis'] ?? 0;

    $prev_page = 'address_page';

    $kurs_json = json_decode($kurs['kurs_json'] ?? '', true);

    // $payment_types = '{"SEPA Lastschrift":"direct_debit","Paypal":"paypal"}';
    $payment_types = '{"SEPA Lastschrift":"direct_debit"}';
    if (isset($userdata['is_firma']) && $userdata['is_firma'] == 'f' && $kurs_json['erlaubtfirmenanmeldung'] == 'W') {
//        $payment_types = '{"Rechnung":"invoice","SEPA Lastschrift":"direct_debit","Paypal":"paypal"}';
        $payment_types = '{"Rechnung":"invoice","SEPA Lastschrift":"direct_debit"}';
    }
    if ($is_fna) {
        $payment_types = '{"SEPA Lastschrift":"direct_debit"}';
        if ($userdata['ermaessigung'] ?? '0' == '1') {
            $kurspreis = $kurs['preisreduziert'] ?? 0;
        }
    }




    $start_line = '<div class="uk-grid" uk-grid="margin: uk-margin-small">';
    $field_1_4 = '<div class="uk-width-1-4@s">';
    $field_3_4 = '<div class="uk-width-3-4@s">';
    $start_line_50 = '<div class="uk-child-width-1-1 uk-child-width-1-2@s uk-grid" uk-grid="margin: uk-margin-small"><div>';
    $start_line_50_abstand = '<div class="uk-child-width-1-1 uk-child-width-1-2@s uk-grid uk-margin-top" uk-grid="margin: uk-margin-small"><div>';
    $start_line_100_abstand = '<div class="uk-child-width-1-1 uk-grid uk-margin-top" uk-grid="margin: uk-margin-small"><div>';
    $start_line_100_abstand_large = '<div class="uk-child-width-1-1 uk-grid uk-margin-large-top" uk-grid="margin: uk-margin-small"><div>';
    $start_line_100_lastschrift = '<div id="direct_debit_box" class="uk-child-width-1-1 uk-grid uk-margin-top" uk-grid="margin: uk-margin-small" hidden><div>';
    $start_line_100 = '<div class="uk-child-width-1-1 uk-grid" uk-grid="margin: uk-margin-small"><div>';
    $next_field = '</div><div>';
    $end_line = '</div></div>';
    $end_field = '</div>';


    if ($cart && $kurs) {
        $kurs = current($cart);
        $fragment = new rex_fragment();
        $fragment->setVar('kurs_id', $kurs['id']);
        $teaser = $fragment->parse('vhs_cart_kursteaser.php');
    }

    $yform = new rex_yform();
    $yform->setObjectparams('form_ytemplate', 'uikit,bootstrap,classic');
    $yform->setObjectparams('form_class', 'uk-form rex-yform wh_checkout');
    $yform->setObjectparams('form_action', rex_article::getCurrent()->getUrl());
    $yform->setObjectparams('error_class', 'uk-form-danger has-error');


    if (($userdata['is_firma'] ?? '') === 'f') {
        $yform->setValueField('html', ['', $start_line_100_abstand]);
        $yform->setValueField('html', ['', '<hr>']);
        $yform->setValueField('html', ['', '<p><strong>Firma/Institution</strong></p>']);
        $yform->setValueField('html', ['', $start_line_50]);
        $yform->setValueField('html', ['', $userdata['salutation_firma'] . ' ' . $userdata['title_firma'] . ' ' . $userdata['firstname_firma'] . ' ' . $userdata['lastname_firma'] . '<br>']);
        $yform->setValueField('html', ['', $userdata['firma'] . '<br>']);
        $yform->setValueField('html', ['', $userdata['address_firma'] . '<br>']);
        $yform->setValueField('html', ['', $userdata['zip_firma'] . ' ' . $userdata['city_firma']]);
        $yform->setValueField('html', ['', $next_field]);
        $yform->setValueField('html', ['', 'Telefon: ' . $userdata['phone_firma'] . '<br>']);
        $yform->setValueField('html', ['', 'E-Mail: ' . $userdata['email_firma'] . '<br>']);
        $yform->setValueField('html', ['', $end_line]);
        $yform->setValueField('html', ['', $end_line]);
    }


    $yform->setValueField('html', ['', $start_line_100_abstand]);
    $yform->setValueField('html', ['', '<hr>']);
    $yform->setValueField('html', ['', '<p><strong>Teilnehmer:innen</strong></p>']);
    foreach ($cart['participants'] as $part) {
        $yform->setValueField('html', ['', $start_line_50]);
        $yform->setValueField('html', ['', $part['title'] . ' ' . $part['firstname'] . ' ' . $part['lastname'] . '<br>']);
        $yform->setValueField('html', ['', $part['address'] . '<br>']);
        $yform->setValueField('html', ['', $part['zip'] . ' ' . $part['city']]);
        $yform->setValueField('html', ['', $next_field]);
        $yform->setValueField('html', ['', 'Telefon: ' . $part['phone'] . '<br>']);
        $yform->setValueField('html', ['', 'E-Mail: ' . $part['email'] . '<br>']);
        $yform->setValueField('html', ['', 'Geburtsjahr: ' . $part['geburtsjahr']]);
        $yform->setValueField('html', ['', $end_line]);
        $yform->setValueField('html', ['', '<hr>']);
    }
    $yform->setValueField('html', ['', $end_line]);

    $yform->setValueField('html', ['', $start_line_100_abstand]);

    $yform->setValueField('html', ['', '<p class="uk-align-right"><strong>Summe Kosten: ' . number_format(floatval($kurspreis) * count($cart['participants']), 2, ',', "'") . ' EUR</strong></p>']);
    if ($is_fna) {
        $yform->setValueField('html', ['', '<p class="uk-align-left uk-margin-medium-top">{{ Seminargebühr ohne Übernachtung. Für Übernachtung und Verpflegung fallen ggfs. zusätzliche Kosten an. }}</p>']);
    }

    $yform->setValueField('html', ['', $end_line]);


    // Bei der FNA gibt es nur Lastschrift - keine Auswahl, iban, bic und Kontoinhaber immer anzeigen.
    if ($kurs && $kurs['preis'] > 0) {
        if (!$is_fna) {
            $yform->setValueField('html', ['', $start_line_100_abstand]);
            $yform->setValueField('html', ['', '<h2 class="uk-h3">Zahlungsart</h2>']);
            $yform->setValueField('html', ['', $end_line]);
            $yform->setValueField('html', ['', $start_line_100_abstand]);
            $yform->setValueField('choice', ["payment_type", "", $payment_types, 1, 0, '', '', '', '', ['id' => 'payment_box', 'uk-grid' => '', 'class' => 'uk-child-width-1-1@s'], [], ['class' => 'uk-radio uk-margin-small-right']]);
            $yform->setValueField('html', ['', $end_line]);

            $yform->setValueField('html', ['', $start_line_100_lastschrift]);
        } else {
            $yform->setValueField('html', ['', $start_line_100]);
            $yform->setValueField('hidden', ['payment_type', 'direct_debit']);
        }
        $yform->setValueField('text', ['iban', 'IBAN*', $userdata['iban'] ?? '', '[no_db]']);
        $yform->setValueField('html', ['', $next_field]);
        $yform->setValueField('text', ['bic', 'BIC*', $userdata['bic'] ?? '', '[no_db]']);
        $yform->setValueField('html', ['', $next_field]);
        $yform->setValueField('text', ['direct_debit_name', 'Kontoinhaber:in*', $userdata['direct_debit_name'] ?? '', '[no_db]']);
        $yform->setValueField('html', ['', $end_line]);
    }

    if ($is_fna) {
        $yform->setValueField('html', ['', $start_line_100_abstand_large]);
        $yform->setValueField('checkbox', ['teilnahmegebuehr_ok', '{{ Ich habe die Höhe der Teilnahmegebühren gemäß Programm zur Kenntnis genommen. Zu den Kursgebühren kommen ggfs. Kosten für Übernachtung und Verpflegung hinzu. }}*']);
        $yform->setValueField('html', ['', $end_line]);
        $yform->setValidateField('empty', ['teilnahmegebuehr_ok', '{{ Sie müssen die Teilnahmegebühren akzeptieren. }}']);
    }

    $yform->setValueField('html', ['', $is_fna ? $start_line_100_abstand : $start_line_100_abstand_large]);
    $yform->setValueField('checkbox', ['agb_ok', '{{ Ich erkläre mich mit den allgemeinen Geschäftsbedingungen einvertanden. }}*']);
    $yform->setValueField('html', ['', $end_line]);
    $yform->setValueField('html', ['', $start_line_100_abstand]);
    $yform->setValueField('checkbox', ['datenschutz_ok', '{{ Ich erkläre mich damit einverstanden, dass die von mir angegebenen Daten gemäß Datenschutzerklärung weiterverarbeitet werden. }}*']);
    $yform->setValueField('html', ['', $end_line]);

    $yform->setValueField('html', ['', $start_line_100_abstand_large]);
    $yform->setValueField('html', ['', '<a href="' . rex_getUrl(rex_config::get('vhs', $prev_page)) . '" class="uk-button uk-button-secondary">zurück</a>']);
    $yform->setValueField('submit', ['send', '{{ verbindlich buchen }}', '', '[no_db]', '', 'uk-button uk-button-primary uk-align-right']);
    $yform->setValueField('html', ['', $end_line]);

    $yform->setValueField('html', ['', $start_line_100_abstand_large]);
    $yform->setValueField('html', ['', '{{ text_widerufsrecht }}']);
    $yform->setValueField('html', ['', $end_line]);


    $yform->setValidateField('empty', ['agb_ok', '{{ Sie müssen die AGBs akzeptieren. }}']);
    $yform->setValidateField('empty', ['datenschutz_ok', '{{ Sie müssen die Datenschutzerklärung akzeptieren. }}']);
    if ($kurs && $kurs['preis'] > 0) {
        if (!$is_fna) {
            $yform->setValidateField('empty', ['payment_type', '{{ Bitte wählen Sie eine Zahlungsart. }}']);
            $yform->setValidateField('customfunction', ['iban', 'vhs_helper::validate_sub_values', ['payment_type', 'direct_debit'], 'Bitte füllen Sie alle markierten Felder aus.']);
            $yform->setValidateField('customfunction', ['bic', 'vhs_helper::validate_sub_values', ['payment_type', 'direct_debit'], 'Bitte füllen Sie alle markierten Felder aus.']);
            $yform->setValidateField('customfunction', ['direct_debit_name', 'vhs_helper::validate_sub_values', ['payment_type', 'direct_debit'], 'Bitte füllen Sie alle markierten Felder aus.']);
            $yform->setValidateField('customfunction', ['iban', 'vhs_checkout::validate_sub_values', ['payment_type', 'direct_debit', 'iban'], 'Bitte tragen Sie eine gültige IBAN ein.']);
        } else {
            $yform->setValidateField('empty', ['direct_debit_name', 'Bitte füllen Sie alle markierten Felder aus.']);
            $yform->setValidateField('empty', ['bic', 'Bitte füllen Sie alle markierten Felder aus.']);
            $yform->setValidateField('type', ['iban', 'iban', 'Bitte tragen Sie eine gültige IBAN ein.']);
        }
    }





    $yform->setActionField('callback', ['vhs_checkout::redirect_payment']);
}


?>



<div class="ce-table uk-margin-large-top uk-margin-large-bottom">
    <div class="uk-container uk-container-small">
        <?php if (!$cart || !$kurs) : ?>
            <h2>Der Warenkorb ist leer.</h2>
        <?php else : ?>
            <?= $teaser ?>
            <section class="ce-headline uk-margin-large-top">
                <div class="pretitle uk-margin-remove">Schritt 2 von 2</div>
                <h1 class="uk-h1 uk-margin-small-top">Zusammenfassung</h1>
            </section>
            <?= $yform->getForm(); ?>
        <?php endif ?>
    </div>
</div>