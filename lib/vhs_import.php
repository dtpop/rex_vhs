<?php
class vhs_import {

    protected $fachbereiche = []; 
    protected $kategorien = []; 
    protected $dozenten = [];
    protected $orte = [];
    protected $aussenstellen = [];
    protected $zielgruppen = [];

    protected $dbfields = [];

    static $assoc_fields = []; // dbfield => xmlfield aus Konfiguration 
    static $formatted_fields = []; // dbfield => Sonderfunktion 
    static $protected_fields = []; // dbfelder, die nur beim Import gefüllt werden. Bei einer Aktualisierung bleibt der Wert erhalten

    public function __construct() {
        $this->init_fachbereiche();
        $this->init_kategorien();
        $this->init_dozenten();
        $this->init_orte();
        $this->init_aussenstellen();
        $this->init_zielgruppen();
        $this->set_assoc_fields(); // Assoziierte Feldnamen setzen
    }



    public function run () {
        $import_path = rex_config::get('vhs', 'importpfad', '');
        $context = null;
        $context = stream_context_create(['http' => ['header' => 'Accept: application/xml']]);
        if (!file_exists($import_path)) {
            rex_logger::factory()->log('error','Importdatei nicht vorhanden',[],__FILE__,__LINE__);
            echo rex_view::error('Importdatei nicht vorhanden');
            return;
        }
		$xmlstring = file_get_contents($import_path, false, $context);
        $kurse = simplexml_load_string($xmlstring, null, LIBXML_NOCDATA);
        $this->save_all($kurse);        
        $this->update_fachbereiche(); // Die Fachbereichtabelle wird mit den gefundenen Fachbereichen upgedated
        $this->update_dozenten(); // Die Dozententabelle wird mit den gefunden Dozenten upgedated
        $this->update_orte(); // Die Orttabelle wird mit den gefunden Orten upgedated
    }


    public function get_db_fields ($table)  {
        return rex_sql::factory()->setTable(rex::getTable($table))->select()->getFieldnames();
    }

    /**
     * Sichert alle Kurse in der db
     * 
     */
    private function save_all ($kurse) {

        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('vhs_kurs'));

        // alle Status auf offline setzen
        $sql->setValue('status',0);
        $sql->update();

        foreach ($kurse as $kurs) {
            $sql->setTable(rex::getTable('vhs_kurs'));
            $values = [];
            $this->check_fachbereich($kurs); // es wird geprüft, ob der Fachbereich für diesen Kurs bereits existiert. Wenn nicht, wird sie angelegt
            $this->check_ort($kurs); // es wird geprüft, ob der Ort für diesen Kurs bereits existiert. Wenn nicht, wird er angelegt
            $this->check_aussenstelle($kurs); // es wird geprüft, ob die Aussenstelle für diesen Kurs bereits existiert. Wenn nicht, wird sie angelegt
            $this->check_zielgruppen($kurs); // es wird geprüft, ob die Zielgruppe für diesen Kurs bereits existiert. Wenn nicht, wird sie angelegt
            $dozenten = $this->check_dozent($kurs); // es wird geprüft, ob die Dozenten für diesen Kurs bereits existieren. Wenn nicht, werden sie angelegt
            $kategorien = $this->check_kategorie($kurs); // auf Kategorien prüfen
            foreach (self::$assoc_fields as $dbfield=>$xmlfield) {
                $values[$dbfield] = $kurs->{$xmlfield};
                if (isset(self::$formatted_fields[$dbfield])) {
                    $values[$dbfield] = $this->reformat($values[$dbfield],self::$formatted_fields[$dbfield]);
                }
            }
            // Special Values
            $values['status'] = $kurs->internetaktiv == 'W' ? 1 : 0;
            if ((string) $kurs->fachb && isset($this->fachbereiche[(string) $kurs->fachb]['id'])) {
                $values['maincategory'] = (int) $this->fachbereiche[(string) $kurs->fachb]['id'];
            }
            $values['kurs_json'] = json_encode($kurs);
            $values['dozenten'] = $dozenten;
            $values['category_ids'] = $kategorien; // Komma separierte Ids
            if ($kurs->aussenstelle && isset($this->aussenstellen[(string) $kurs->aussenstelle]['id'])) {
                $values['aussenstelle_id'] = $this->aussenstellen[(string) $kurs->aussenstelle]['id'];
            }
            if ($kurs->zielgruppe) {
                $values['zielgruppe_id'] = $this->zielgruppen[(string) $kurs->zielgruppe]['id'];
            }

            $sql->setWhere('nummer = :nummer',['nummer'=>$kurs->knr]);
            $sql->select();
            if ($sql->getRows() == 1) {
                foreach (self::$protected_fields as $protected_field) {
                    unset($values[$protected_field]);
                }
                $sql->setTable(rex::getTable('vhs_kurs'));
                $sql->setWhere('nummer = :nummer',['nummer'=>$kurs->knr]);
//                dump($values); exit;

                $sql->setValues($values);
                $sql->update();
                echo rex_view::info('Kurs '.$kurs->knr.' '.$kurs->haupttitel.' aktualisiert.');
            } else {
                $sql->setTable(rex::getTable('vhs_kurs'));
                $sql->setValues($values);
                $sql->insert();
                echo rex_view::success('Kurs '.$kurs->knr.' '.$kurs->haupttitel.' importiert.');
            }
        }

    }

    /**
     * Kategorien in Pfade auflösen.
     * Kommaseparierte Ids zurückgeben
     */
    private function check_kategorie ($kurs) {
        // Wenn es die Kategorie nicht gibt, in Parent/Child auflösen
        $ids = [];
        $json = json_encode($kurs->kategorien);
        $kat = json_decode($json,true);
        if (!$kat) {
            return '';
        }
        $kategorien = [];
        if (isset($kat['kategorie']['bezeichnung'])) {
            $kategorien[] = $kat['kategorie'];
        } elseif (isset($kat['kategorie'])) {
            $kategorien = $kat['kategorie'];
        }

        foreach ($kategorien as $kat) {
//                $cat_name = $kat['bezeichnung'];
            if (!isset($kat['bezeichnungstruktur'])) {
                continue;
            }
            $cat_path = $kat['bezeichnungstruktur'];
            $this->check_path_segments($cat_path);
            $ids[] = $this->kategorien[$cat_path]['id'];
//                dump($this->kategorien[$cat_path]['id']);
        }
        return implode(',',$ids);
    }

    /**
     * Übergeben wird: "VHS / Beruf / Schulabschlüsse und Qualifikationen"
     * geprüft und ggf. angelegt werden die Segmente
     * - VHS
     * - VHS / Beruf
     * - VHS / Beruf / Schulabschlüsse und Qualifikationen
     */
    private function check_path_segments($cat_path) {
        $segments = explode(' / ',$cat_path);
        $path = '';
        $parent_id = 0;
        foreach ($segments as $i=>$segment) {
            if ($i > 0) {
                $parent_id = $this->kategorien[$path]['id'];
                $path .= ' / ';
            }
            $path .= $segment;
            if (!isset($this->kategorien[$path])) {
                $this->kategorien[$path] = [
                    'name'=>$segment,
                    'path'=>$path,
                    'parent_id'=>$parent_id
                ];
                $this->update_kategorien();
                $this->init_kategorien();
            }


        }

    }


    /**
     * es wird geprüft, ob die Dozenten für diesen Kurs bereits existieren. Wenn nicht, werden sie angelegt
     * Gefundene Dozentenpfade:
     * /hauptdozent (id)
     * /termine/termin[0...n]/termin_doz/termin_dnr /termin_vorname /termin_name /termin_geschlecht /termin_telefon_privat /termin_email /termin_aktiv [J]
     * /dozenten/dozent/nummer /dnr /vorname /name /geschlecht /telefon_privat /email /aktiv [J] /doz_internet [F] /doz_typ [D] /beruf [A]
     */
    private function check_dozent ($kurs) {
        $json = json_encode($kurs);
        $a_kurs = json_decode($json,TRUE);
        $kursdozenten = [];
        if (isset($a_kurs['hauptdozent'])) {
            $kursdozenten[$a_kurs['hauptdozent']]['id'] = [$a_kurs['hauptdozent']];
        }
        if (isset($a_kurs['termine'][0])) {
            foreach ($a_kurs['termine'] as $termin) {
                $termin_doz = $termin['termin_doz'];
                $kursdozenten[$termin_doz['termin_dnr']]['id'] = $termin_doz['termin_dnr'];
                $kursdozenten[$termin_doz['termin_dnr']]['anrede'] = ($termin_doz['termin_geschlecht'] ?? '') == "W" ? 'Frau' : 'Herr';
                $kursdozenten[$termin_doz['termin_dnr']]['vorname'] = $termin_doz['termin_vorname'] ?? '';
                $kursdozenten[$termin_doz['termin_dnr']]['nachname'] = $termin_doz['termin_name'] ?? '';
                $kursdozenten[$termin_doz['termin_dnr']]['email'] = $termin_doz['termin_email'] ?? '';
                $kursdozenten[$termin_doz['termin_dnr']]['telefon'] = $termin_doz['termin_telefon_privat'] ?? '';
            }
        }


        if (isset($a_kurs['dozenten']['dozent'])) {
            $_doz = $a_kurs['dozenten']['dozent'];
            $doz = [];
            // auch bei einem einzelnen Dozenten ein Array draus machen.
            if (!isset($_doz[0])) {
                $doz[0] = $_doz;
            } else {
                $doz = $_doz;
            }
            foreach ($doz as $dozent) {
                $kursdozenten[$dozent['dnr']]['id'] = $dozent['dnr'];
                $kursdozenten[$dozent['dnr']]['anrede'] = ($dozent['geschlecht'] ?? '') == "W" ? 'Frau' : 'Herr';
                $kursdozenten[$dozent['dnr']]['vorname'] = $dozent['vorname'] ?? '';
                $kursdozenten[$dozent['dnr']]['nachname'] = $dozent['name'] ?? '';
                $kursdozenten[$dozent['dnr']]['email'] = $dozent['email'] ?? '';
                $kursdozenten[$dozent['dnr']]['telefon'] = $dozent['telefon_privat'] ?? '';
            }
        }

        $aussenstelle_key = (string) $kurs->aussenstelle;

        // es kann mehrere Dozenten in einem Kurs geben
        foreach ($kursdozenten as $dozid=>$dozent) {
            if (isset($this->dozenten[$dozid])) {
                // Dozent ist zwar vorhanden, aber ohne nachname
                if (!isset($this->dozenten[$dozid]['nachname']) || !$this->dozenten[$dozid]['nachname']) {
                    $this->dozenten[$dozid] = $dozent;
                }
            } else {
                $this->dozenten[$dozid] = $dozent;
            }
            if ($aussenstelle_key && isset($this->aussenstellen[$aussenstelle_key]['id'])) {
                $this->dozenten[$dozid]['aussenstelle'][$this->aussenstellen[$aussenstelle_key]['id']] = $this->aussenstellen[$aussenstelle_key]['id'];
            }            
        }
        return implode(',',array_keys($kursdozenten));
    }

    /**
     * es wird geprüft, ob die Kategorie für diesen Kurs bereits existiert. Wenn nicht, wird sie angelegt
     */
    private function check_fachbereich ($kurs) {
        $fachb = (string) $kurs->fachb;
        if (isset($this->fachbereiche[$fachb])) {
            // es kommt im xml bei einigen Kursen vor, dass der Name identisch mit dem Schlüssel ist
            // das ist der Fix ...
            if ($fachb == $this->fachbereiche[$fachb]['name'] && $fachb != $kurs->fachbtext) {
                $this->fachbereiche[$fachb]['name'] = $kurs->fachbtext;
            }
        } else {
            $this->fachbereiche[$fachb]['key'] = $fachb;
            $this->fachbereiche[$fachb]['name'] = $kurs->fachbtext;
            $this->update_fachbereiche();
            $this->init_fachbereiche();
        }
    }

    /**
     * es wird geprüft, ob der Ort für diesen Kurs bereits existiert. Wenn nicht, wird er angelegt
     */
    private function check_ort ($kurs) {
        $ort_id = (int) $kurs->ortid;
        if (!$ort_id) return;
        if (!isset($this->orte[$ort_id])) {
            $this->orte[$ort_id]['id'] = $ort_id;
            $this->orte[$ort_id]['name'] = $kurs->ort;
            $this->orte[$ort_id]['plz'] = $kurs->ortplz;
            $this->orte[$ort_id]['ort'] = $kurs->ortname;
            $this->orte[$ort_id]['anschrift'] = $kurs->ortstr;
            $this->orte[$ort_id]['raumnr'] = $kurs->ortraumnr;
            $this->orte[$ort_id]['raum_name'] = $kurs->ortraumname;
            $this->orte[$ort_id]['raum_kurz'] = $kurs->ortraumkurz;
            $this->orte[$ort_id]['gebaeude'] = $kurs->ortgebaeude;
        }
    }

    /**
     * es wird geprüft, ob die Außenstelle für diesen Kurs bereits existiert. Wenn nicht, wird sie angelegt
     * Außenstellen werden mit ihrem Namen geschlüsselt
     */
    private function check_aussenstelle ($kurs) {
        $aussenstelle_key = (string) $kurs->aussenstelle;
        if (!$aussenstelle_key) return;
        if (!isset($this->aussenstellen[$aussenstelle_key])) {
            $this->aussenstellen[$aussenstelle_key]['name'] = $aussenstelle_key;
            $this->update_aussenstellen();
            $this->init_aussenstellen();
        }
    }

    /**
     * es wird geprüft, ob die Außenstelle für diesen Kurs bereits existiert. Wenn nicht, wird sie angelegt
     * Außenstellen werden mit ihrem Namen geschlüsselt
     */
    private function check_zielgruppen ($kurs) {
        $key = (string) $kurs->zielgruppe;
        if (!$key) return;
        if (!isset($this->zielgruppen[$key])) {
            $this->zielgruppen[$key]['name'] = $key;
            $this->update_zielgruppen();
            $this->init_zielgruppen();
        }
    }

    /**
     * Formatiert Werte um. z.B. Datum 30.12.1980 => 1980-12-30
     * 
     * @params
     * $val - Wert
     * $format - z.B. date(Y-m-d)
     * 
     * 
     */
    private function reformat ($val,$format) {
        if (strpos($format,'date') === 0) {
            preg_match('/date\((.*?)\)/',$format,$matches);
            $val = date($matches[1],strtotime($val));
        }
        if (strpos($format,'float') === 0) {
            $val = str_replace('.','',$val);
            $val = str_replace(',','.',$val);
        }
        return $val;
    }

    /**
     * Setzt assoziierte Feldnamen dbfieldname=>xmlfield aus Konfiguration (Settings Seite)
     * Es wird geprüft, ob ein dbfeld auch tatsächlich vorhanden ist.
     */
    private function set_assoc_fields() {
        $this->dbfields = $this->get_db_fields('vhs_kurs');
        $assoc_fields = rex_config::get('vhs','assocfields');
        $assoc_field_lines = preg_split('/\R/',$assoc_fields);
        foreach ($assoc_field_lines as $line) {
            $vals = explode('::',$line);
            if (isset($vals[1]) && in_array(trim($vals[0]),$this->dbfields)) {
                self::$assoc_fields[trim($vals[0])] = trim($vals[1]);
            }
            if (isset($vals[2]) && in_array(trim($vals[0]),$this->dbfields)) {
                self::$formatted_fields[trim($vals[0])] = trim($vals[2]);
            }
            if (isset($vals[3]) && $vals[3] == 'protected') {
                self::$protected_fields[trim($vals[0])] = trim($vals[0]);
            }

        }

    }


    /**
     * Die Fachbereichtabelle wird aus den XML Werten aufgebaut und ergänzt.
     */
    private function update_fachbereiche () {
        $sql = rex_sql::factory();
        foreach ($this->fachbereiche as $cat) {
            if (!$cat['name']) {
                continue;
            }
            $sql->setTable(rex::getTable('vhs_fachbereich'));
            $sql->setWhere('`key` = :key',['key'=>$cat['key']]);
            $sql->select();
            if ($sql->getRows() == 1) {
                $sql->setTable(rex::getTable('vhs_fachbereich'));
                $sql->setWhere('`key` = :key',['key'=>$cat['key']]);
                $sql->setValue('name',$cat['name']);
                $sql->update();
//                echo rex_view::info('Fachbereich '.$cat['name'].' aktualisiert.');
            } else {
                $sql->setTable(rex::getTable('vhs_fachbereich'));
                $sql->setValues([
                    'key'=>$cat['key'],
                    'name'=>$cat['name']
                ]);
                $sql->insert();
                echo rex_view::success('Fachbereich '.$cat['name'].' hinzugefügt.');
            }
        }
    }

    
    /**
     * Die Kategorietabelle wird aus den XML Werten aufgebaut und ergänzt.
     */
    private function update_kategorien () {
        $sql = rex_sql::factory();
        foreach ($this->kategorien as $cat) {
            if (!$cat['name']) {
                continue;
            }
            $sql->setTable(rex::getTable('vhs_kategorie'));
            $sql->setWhere('path = :path',['path'=>$cat['path']]);
            $sql->select();
            if ($sql->getRows() == 1) {
                $sql->setTable(rex::getTable('vhs_kategorie'));
                $sql->setWhere('path = :path',['path'=>$cat['path']]);
                $sql->setValues([
                    'path'=>$cat['path'],
                    'name'=>$cat['name'],
                    'parent_id'=>$cat['parent_id']
                ]);
                $sql->update();
                echo rex_view::error('Kategorie '.$cat['name'].' aktualisiert.');
            } else {
                $sql->setTable(rex::getTable('vhs_kategorie'));
                $sql->setValues([
                    'path'=>$cat['path'],
                    'name'=>$cat['name'],
                    'parent_id'=>$cat['parent_id']
                ]);
                $sql->insert();
                echo rex_view::success('Kategorie '.$cat['path'].' hinzugefügt.');
            }
        }
    }



    /**
     * Die Dozententabelle wird aus den XML Werten aufgebaut und ergänzt.
     */
    private function update_dozenten () {
        $sql = rex_sql::factory();
        foreach ($this->dozenten as $dozid=>$dozent) {
            $dozent['aussenstelle'] = (string) implode(',',$dozent['aussenstelle']);
            $sql->setTable(rex::getTable('vhs_dozent'));
            $sql->setWhere('id = :id',['id'=>$dozid]);
            $sql->select();
            if ($sql->getRows() == 1) {
                $sql->setTable(rex::getTable('vhs_dozent'));
                $sql->setWhere('id = :id',['id'=>$dozid]);
                unset($dozent['id']);
                $sql->setValues($dozent);
                $sql->update();
            } else {
                $sql->setTable(rex::getTable('vhs_dozent'));
                $sql->setValues($dozent);
                $sql->insert();
                echo rex_view::success('Dozent '.$dozent['nachname'].' hinzugefügt.');
            }
        }
    }

    /**
     * Die Aussenstellen Tabelle wird aus den XML Werten aufgebaut und ergänzt.
     */
    private function update_aussenstellen () {
        $sql = rex_sql::factory();
        foreach ($this->aussenstellen as $key=>$aussenstelle) {            
            $sql->setTable(rex::getTable('vhs_aussenstelle'));
            $sql->setWhere('name = :name',['name'=>$key]);
            $sql->select();
            if ($sql->getRows() == 1) {
            } else {
                $sql->setTable(rex::getTable('vhs_aussenstelle'));
                $sql->setValues($aussenstelle);
                $sql->insert();
//                echo rex_view::success('Dozent '.$dozent['nachname'].' hinzugefügt.');
            }
        }
    }

    /**
     * Die Zielgruppen Tabelle wird aus den XML Werten aufgebaut und ergänzt.
     */
    private function update_zielgruppen () {
        $sql = rex_sql::factory();
        foreach ($this->zielgruppen as $key=>$zielgruppe) {            
            $sql->setTable(rex::getTable('vhs_zielgruppe'));
            $sql->setWhere('name = :name',['name'=>$key]);
            $sql->select();
            if ($sql->getRows() == 1) {
            } else {
                $sql->setTable(rex::getTable('vhs_zielgruppe'));
                $sql->setValues($zielgruppe);
                $sql->insert();
            }
        }
    }

    /**
     * Die Orttabelle wird aus den XML Werten aufgebaut und ergänzt.
     */
    private function update_orte () {
        $sql = rex_sql::factory();
        foreach ($this->orte as $ort_id=>$ort) {
            $sql->setTable(rex::getTable('vhs_ort'));
            $sql->setWhere('id = :id',['id'=>$ort_id]);
            $sql->select();
            if ($sql->getRows() == 1) {
                $sql->setTable(rex::getTable('vhs_ort'));
                $sql->setWhere('id = :id',['id'=>$ort_id]);
                unset($ort['id']);
                $sql->setValues($ort);
                $sql->update();
//                echo rex_view::info('Kategorie '.$cat['name'].' aktualisiert.');
            } else {
                $sql->setTable(rex::getTable('vhs_ort'));
                $sql->setValues($ort);
                $sql->insert();
                echo rex_view::success('Ort '.$ort['name'].' hinzugefügt.');
            }
        }
    }

    /**
     * Setzt die Orte aus der DB
     * 
     */
    private function init_orte () {
        $res = rex_sql::factory()->setTable(rex::getTable('vhs_ort'))->select()->getArray();
        foreach ($res as $item) {
            $this->orte[$item['id']] = $item;
        }
    }

    /**
     * Setzt die Dozenten aus der DB
     * 
     */
    private function init_dozenten () {
        $res = rex_sql::factory()->setTable(rex::getTable('vhs_dozent'))->select()->getArray();
        foreach ($res as $item) {
            $aussenstellen1 = explode(',', $item['aussenstelle']);
            $aussenstellen = [];
            foreach ($aussenstellen1 as $aussenst) {
                $aussenstellen[$aussenst] = $aussenst;
            }
            $item['aussenstelle'] = $aussenstellen;
            $this->dozenten[$item['id']] = $item;
        }
    }

    /**
     * Setzt die Fachbereiche aus der DB
     * 
     */
    private function init_fachbereiche () {
        $res = rex_sql::factory()->setTable(rex::getTable('vhs_fachbereich'))->select()->getArray();
        foreach ($res as $item) {
            $this->fachbereiche[$item['key']] = $item;
        }
    }

    /**
     * Setzt die Aussenstellen aus der DB
     * 
     */
    private function init_aussenstellen () {
        $res = rex_sql::factory()->setTable(rex::getTable('vhs_aussenstelle'))->select()->getArray();
        foreach ($res as $item) {
            $this->aussenstellen[$item['name']] = $item;
        }
    }

    /**
     * Setzt die Zielgruppen aus der DB
     * 
     */
    private function init_zielgruppen () {
        $res = rex_sql::factory()->setTable(rex::getTable('vhs_zielgruppe'))->select()->getArray();
        foreach ($res as $item) {
            $this->zielgruppen[$item['name']] = $item;
        }
    }

    /**
     * Setzt die Kategorien aus der DB
     * 
     */
    private function init_kategorien () {
        $res = rex_sql::factory()->setTable(rex::getTable('vhs_kategorie'))->select()->getArray();
        foreach ($res as $item) {
            $this->kategorien[$item['path']] = $item;
        }
    }

}

?>