<?php

class vhs_yform
{

    public static $is_participant = true;
    public static $use_required_fields = false;
    private static $participant = [];
    private static $which_i = '0';
    private static $is_disabled = false;
    private static $error_class = ' uk-form-danger has-error';





    public static function add_participant_address($participant = [], $is_participant = true, $which_i = '999', $is_disabled = false)
    {

        self::$is_disabled = $is_disabled;
        self::$which_i = $which_i;
        self::$participant = $participant;

        //        dump(self::$is_fna);

        if (!$participant) {
            self::$error_class = '';
        }

        //        dump(self::$participant);


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

        $required_params = [];

        $out = '';
        $out .= $start_line_50;
        //        $out .= self::get_field('choice',['salutation','Anrede*',['Herr'=>'Herr','Frau'=>'Frau'],1,0,'Herr','','','',['uk-grid'=>'','class'=>'uk-child-width-1-1@s'],['class'=>'uk-width-1-2@m'],['class'=>'uk-radio uk-margin-small-right']]);
        $out .= self::get_field('choice', ['geschlecht', 'Geschlecht*', ['m' => 'm', 'w' => 'w', 'd' => 'd'], 1, 0, 'm', '', '', '', ['uk-grid' => '', 'class' => 'uk-child-width-1-1@s'], ['class' => 'uk-width-1-3@m'], ['class' => 'uk-radio uk-margin-small-right']]);
        $out .= $next_field;
        $out .= self::get_field('text', ['title', 'Titel', $participant['title'] ?? '', '[no_db]', []]);
        $out .= $end_line;

        $out .= $start_line_50;
        $out .= self::get_field('text', ['firstname', 'Vorname*', $participant['firstname'] ?? '', '[no_db]', $required_params], true);
        $out .= $next_field;
        $out .= self::get_field('text', ['lastname', 'Nachname*', $participant['lastname'] ?? '', '[no_db]', $required_params], true);
        $out .= $end_line;
        $out .= $start_line_100;
        $out .= self::get_field('text', ['address', 'Adresse*', $participant['address'] ?? '', '[no_db]', $required_params], true);
        $out .= $end_line;

        $out .= $start_line;
        $out .= $field_1_4;
        $out .= self::get_field('text', ['zip', 'PLZ*', $participant['zip'] ?? '', '[no_db]', $required_params], true);
        $out .= $end_field;
        $out .= $field_3_4;
        $out .= self::get_field('text', ['city', 'Ort*', $participant['city'] ?? '', '[no_db]', $required_params], true);
        $out .= $end_line;


        $out .= $start_line_50;
        $out .= self::get_field('text', ['phone', 'Telefon*', $participant['phone'] ?? '', '[no_db]', $required_params], true);
        $out .= $next_field;
        $out .= self::get_field('text', ['email', 'E-Mail*', $participant['email'] ?? '', '[no_db]', $required_params], true);
        $out .= $end_line;

        $out .= $start_line_50;
        $out .= self::get_field('text', ['geburtsjahr', 'Geburtsjahr*', $participant['geburtsjahr'] ?? '', '[no_db]', $required_params], true);
        $out .= $next_field;
        //        $out .= self::get_field('choice',['geschlecht','Geschlecht*',['m'=>'m','w'=>'w','d'=>'d'],1,0,'m','','','',['uk-grid'=>'','class'=>'uk-child-width-1-1@s'],['class'=>'uk-width-1-3@m'],['class'=>'uk-radio uk-margin-small-right']]);
        $out .= $end_line;

        return $out;
    }


    private static function get_field($type, $params, $validate = false)
    {

        // Initial nicht validieren - erst nach submit
        if (!rex_request('FORM', 'array')) {
            $validate = false;
        }

        $fieldname = 'part[' . self::$which_i . '][' . $params[0] . ']';
        $out = '';
        if (self::$is_participant) {
            if ($type == 'choice') {
                return self::get_choice_field_radio($params);
            }
            if ($type == 'choice_select') {
                return self::get_choice_field_select($params);
            }
            if ($type == 'text') {
                return self::get_text_field($params, $validate);
            }
        }
        return '';
    }


    private static function get_choice_field_radio($params)
    {
        $options = '';
        $disabled = self::$is_disabled ? ' disabled="disabled"' : '';
        $val = self::$participant[$params[0]] ?? '';
        foreach ($params[2] as $label => $value) {
            $checked = '';
            if ($val && self::$participant[$params[0]]) {
                $checked = $val == $value ? ' checked="checked"' : '';
            } elseif ($params[5] == $value) {
                $checked =  ' checked="checked"';
            }
            $options .= '<div class="' . $params[10]['class'] . ' radio uk-grid-margin">
            <label>
                <input value="' . $value . '" class="' . $params[11]['class'] . '" name="part[' . self::$which_i . '][' . $params[0] . ']" type="radio"' . $checked . $disabled . '><i class="form-helper"></i>' . $label . '
            </label>
        </div>
';
        }
        return '<div class="uk-first-column">
                    <div uk-grid="" class="uk-child-width-1-1@s form-check-group uk-grid">
                        <label class="control-label uk-first-column" for="yform-formular-field-2">' . $params[1] . '</label>
                        ' . $options . '
                    </div>
                </div>';
    }

    private static function get_choice_field_select($params)
    {
        $options = '';
        $disabled = self::$is_disabled ? ' disabled="disabled"' : '';
        $val = self::$participant[$params[0]] ?? '';
        foreach ($params[2] as $label => $value) {
            $checked = '';
            if ($val && self::$participant[$params[0]]) {
                $checked = $val == $value ? ' checked="checked"' : '';
            } elseif ($params[5] == $value) {
                $checked =  ' checked="checked"';
            }
            $options .= '<option class="' . $params[10]['class'] . '" value="' . $value . '"' . $checked . $disabled . '>' . $label . '</option>';
        }
        return '<div>
                        <label class="control-label uk-first-column" for="yform-formular-field-2"><div class="uk-form-label ">' . $params[1] . '</div></label>
                        <select class="uk-select">
                        
                        ' . $options . '
                        </select>
                </div>';
    }

    private static function get_text_field($params, $validate)
    {

        $disabled = self::$is_disabled ? ' disabled="disabled"' : '';
        $part = rex_request('part', 'array');
        $val = $part[self::$which_i][$params[0]] ?? $params[2];
        $error = '';
        if ($validate) {
            if (!$val) {
                $error = self::$error_class;
            }
            if ($params[0] == 'email') {
                if (!vhs_checkout::is_email_correct($val)) {
                    $error = self::$error_class;
                }
            }
            if ($params[0] == 'geburtsjahr') {
                if (!vhs_checkout::is_geburtsjahr_korrekt($val)) {
                    $error = self::$error_class;
                }
            }
        }

        return '<div>
                    <label class="uk-form-label' . $error . '" for="">
                        <div class="uk-form-label ">' . $params[1] . '</div>
                        <input class="form-control uk-input' . $error . '" name="part[' . self::$which_i . '][' . $params[0] . ']" type="text" value="' . $val . '"' . $disabled . ' />
                    </label>
                </div>';
    }
}
