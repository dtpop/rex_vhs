<?php

class vhs_helper {
    
    

    /**
     * Beispiel:
     *  $yf->setValidateField('customfunction',['ki_ort','wh_helper::validate_sub_values','anderer_kontoinhaber','Bitte füllen Sie alle markierten Felder aus.']);
     *  das Feld ki_ort soll nur geprüft werden, wenn im Feld "anderer_kontoinhaber" was drin steht
     * 
     * Beispiel 2:
     *  $yf->setValidateField('customfunction',['iban','wh_helper::validate_sub_values',['zahlungsweise_lastschrift','SEPA-Lastschrift'],'Bitte füllen Sie alle markierten Felder aus.']);
     *  Das Feld iban soll nur auf empty geprüft werden, wenn das Feld zahlungsweise_lastschfit = SEPA-Lastschrift enthält
     * 
     * @param type $label
     * @param type $value
     * @param type $params kann als String (nur Feldname) oder als Array (Feldname und Wert bei dem die Prüfung des label Feldes stattfindet) übergeben werden.
     * @param type $yf
     * @return boolean
     */
    
    public static function validate_sub_values ($label, $value, $params, $yf) {
        $compare = '';
        if (is_array($params)) {
            list($field,$compare) = $params;
        } else {
            $field = $params;
        }
        foreach ($yf->getObjects() as $o) {
            if ($o->getName() == $field) {
                $base_val = $o->getValue();
            }
            if ($o->getName() == $label) {
                $val = $o->getValue();
            }
        }
        if ($compare) {
            if ($compare != $base_val) {
                return false;
            }
        }
        if (!$base_val) {
            return false;
        }
        if (!$val) {
            return true;
        }
        return false;
        
    }
    
    public static function uk_format_text ($text) {
        $search = ['<ul>'];
        $replace = ['<ul class="uk-list uk-list-bullet">'];
        return str_replace($search,$replace,$text);
        
    }
    
    
}