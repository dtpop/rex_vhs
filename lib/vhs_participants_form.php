<?php

class vhs_participants_form
{

    public static $participants_form_values = [
        ['label' => 'Anrede',    'name' => 'salutation',    'type' => 'checkbox' ],
        ['label' => 'Titel',     'name' => 'title'],
        ['label' => 'Vorname*',  'name' => 'firstname'],
        ['label' => 'Nachname*', 'name' => 'lastname'],
        ['label' => 'Geburtsjahr', 'name' => 'birthyear', 'add_html' => '<div></div>'],
        ['label' => 'Adresse',   'name' => 'address'],
        ['label' => 'PLZ',       'name' => 'plz'],
        ['label' => 'Ort',       'name' => 'ort'],
        ['label' => 'Land',      'name' => 'country'],
        ['label' => 'E-Mail',    'name' => 'email'],
        ['label' => 'Telefon',   'name' => 'phone'],
    ];



    public function get_form() {

		$start_line = '<div class="uk-grid" uk-grid="margin: uk-margin-small">';
		$field_1_4 = '<div class="uk-width-1-4@s">';
		$field_3_4 = '<div class="uk-width-3-4@s">';
		$start_line_50 = '<div class="uk-child-width-1-1 uk-child-width-1-2@s uk-grid" uk-grid="margin: uk-margin-small"><div>';
		$start_line_50_abstand = '<div class="uk-child-width-1-1 uk-child-width-1-2@s uk-grid uk-margin-top" uk-grid="margin: uk-margin-small"><div>';
		$start_line_100_abstand = '<div class="uk-child-width-1-1 uk-grid uk-margin-top" uk-grid="margin: uk-margin-small"><div>';
		$start_line_100 = '<div class="uk-child-width-1-1 uk-grid" uk-grid="margin: uk-margin-small"><div>';
		$next_field = '</div><div>';
		$end_line = '</div></div>';
		$end_field = '</div>';

        $cart = vhs_cart::unset_participants();
        $cart = vhs_cart::get_cart();
        $kurs = current($cart);
//        dump($kurs);

        if (!$cart) {
            return false;
        }
//        dump($cart);
        $yform = new rex_yform();
        $yform->setObjectparams('form_action', rex_article::getCurrent()->getUrl());
        // $yform->setObjectparams('form_showformafterupdate', 0);
        $yform->setObjectparams('form_wrap_class', 'yform vhs-form');
        $yform->setObjectparams('form_showformafterupdate', 1);
        $yform->setObjectparams('debug', 0);
        $yform->setObjectparams('form_ytemplate', 'uikit,bootstrap,classic');
        $yform->setObjectparams('form_class', 'uk-form rex-yform wh_checkout uk-margin-large-top');
        //        $yform->setObjectparams('form_anchor', 'rex-yform');


        foreach ($cart as $kurs_id => $kurs) {

            $yform->setValueField('html',['',$start_line_50]);
            $yform->setValueField('checkbox',['selbst_teilnehmen','Selbst am Kurs teilnehmen','1,0','1','[no_db]']);
            $yform->setValueField('html',['',$next_field]);
            $yform->setValueField('html',['','{{ hinweis_selbst_teilnehmen }}']);
            $yform->setValueField('html',['',$end_line]);




            $yform->setValueField('html', ['', '<div>']);
            foreach ($kurs['participants'] as $i => $participant) {
                $yform->setValueField('html', ['', '<div uk-grid class="participant-item uk-child-width-1-2@s uk-margin-small-top">']);

                foreach (self::$participants_form_values as $field) {
                    $yform->setValueField('html', ['', '<div><label class="uk-form-label" for=""><div class="uk-form-label ">' . $field['label'] . '</div><input class="form-control uk-input" name="part[' . $kurs_id . '][' . $field['name'] . '][]" type="text" value="' . ($participant[$field['name']] ?? '') . '" /></label></div>']);
                    if (isset($field['add_html'])) {
                        $yform->setValueField('html', ['', $field['add_html']]);
                    }
                }

                $yform->setValueField('html', ['', '<span><button type="button" uk-icon icon-trash class="uk-button uk-button-default btn-primary trash-button">Teilnehmer löschen</button></span>']);
                $yform->setValueField('html', ['', '</div>']);
            }


            $yform->setValueField('html', ['', '<div id="add-participant" class="participant-item uk-margin-small-top" hidden="">']);
            $yform->setValueField('html', ['', '<div uk-grid class="uk-child-width-1-2@s">']);

            foreach (self::$participants_form_values as $field) {
                $yform->setValueField('html', ['', '<div><label class="uk-form-label" for=""><div class="uk-form-label ">' . $field['label'] . '</div><input class="form-control uk-input" name="part[' . $kurs_id . '][' . $field['name'] . '][]" type="text" value="" /></label></div>']);
                if (isset($field['add_html'])) {
                    $yform->setValueField('html', ['', $field['add_html']]);
                }
            }
            $yform->setValueField('html', ['', '<span><button type="button" uk-icon icon-trash class="uk-button uk-button-default btn-primary trash-button">Teilnehmer löschen</button></span>']);

            $yform->setValueField('html', ['', '</div>']);
            $yform->setValueField('html', ['', '</div>']);


            $yform->setValueField('html', ['', '<button type="button" class="uk-button uk-button-default btn-primary uk-margin-large-top" id="add-participant-button">Teilnehmer hinzufügen</button>']);


            $yform->setValueField('html', ['', '</div>']);
        }

        $yform->setValueField('html',['','<hr>'.$start_line_100_abstand]);
        $yform->setValueField('html',['','<div class="uk-align-right">Summe Kosten: <span id="price_total" data-price="'.$kurs['preis'].'">'.number_format(floatval($kurs['preis']),2,',',"'").'</span> EUR</div>']);
        $yform->setValueField('html',['',$end_line]);


        $yform->setValueField('html', ['', '<div class="uk-margin-small-top">']);
        $yform->setValueField('html', ['', '<a href="' . rex_getUrl(rex_config::get('vhs', 'address_page')) . '" class="uk-button uk-button-default">zurück</a>']);
        $yform->setValueField('submit',['Submit','Abschicken','1','no_db','','uk-align-right']);
        $yform->setValueField('html', ['', '</div>']);

        $yform->setActionField('callback', ['vhs_cart::add_participants']);
        $yform->setActionField('redirect', [rex_getUrl()]);

        return $yform;
    }
}
