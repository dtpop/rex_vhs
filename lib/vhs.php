<?php

/**
 * !!! maincat = Fachbereich !!!
 */
class vhs extends rex_yform_manager_dataset
{

    static $wochentag = [
        'Sonntag',
        'Montag',
        'Dienstag',
        'Mittwoch',
        'Donnerstag',
        'Freitag',
        'Samstag',
    ];

    public static function get_query()
    {
        $query = self::query()->where('status', 1)->orderBy('datestart','ASC');
        $query->whereRaw('((internetvon = internetbis) or (internetvon <= :date and internetbis >= :date))', ['date' => date('Y-m-d')]);
        return $query;
    }


    /**
     * search_id
     * 
     * für die Filterfunktion in Übersicht
     */
    public static function search_id($id)
    {
        return 'c' . str_pad($id, 4, '0', STR_PAD_LEFT);
    }

    public function search_ids()
    {
        $ids = explode(',', $this->category_ids);
        $out = [];
        foreach ($ids as $id) {
            $out[] = self::search_id($id);
        }
        return implode(' ', $out);
    }

    public function preis($sufix = '&nbsp;€')
    {
        return number_format((float) $this->preis, 2, ',', '.') . $sufix;
    }
    public function preisreduziert($sufix = '&nbsp;€')
    {
        return number_format((float) $this->preisreduziert, 2, ',', '.') . $sufix;
    }

    public function zeitraum()
    {
        if (!$this->dateend) {
            return 'Zeitpunkt: ' . self::format_de_date($this->datestart);
        }
        if ($this->datestart == $this->dateend) {
            return 'Zeitpunkt: ' . self::format_de_date($this->datestart);
        }
        return 'Zeitraum: ' . self::format_de_date($this->datestart) . ' – ' . self::format_de_date($this->dateend);
    }

    public function hauptdozent()
    {
        $doz = $this->getRelatedCollection('hauptdozent');
        if (!$doz) {
            return false;
        }
        //        dump($doz);
        return $doz[0]->vorname . ' ' . $doz[0]->nachname;
    }


    /**
     * Weitere Dozenten _ohne_ Hauptdozent
     */
    public function weitere_dozenten()
    {

        $dozenten = rex_yform_manager_table::get('rex_vhs_dozent')->query()
        ->whereRaw('FIND_IN_SET(id, :doz_ids) AND id != :haupt_doz_id',['doz_ids'=>$this->dozenten,'haupt_doz_id'=>$this->hauptdozent])->orderBy('nachname')->find();
        return $dozenten;
        
    }

    public function hauptdozent_id()
    {
        $doz = $this->getRelatedCollection('hauptdozent');
        if (!$doz) {
            return false;
        }
        return $doz[0]->id;
    }

    public function get_url()
    {
		return rex_getUrl('', '', ['vhs_kurs_id' => $this->id]);
    }


    /**
     * Gibt die erste Kategorie zurück
     */
    public function first_cat()
    {
        if (!$this->category_ids) {
            return false;
        }
        $cat_ids = explode(',', $this->category_ids);
        return self::get_category_by_id($cat_ids[0]);
    }

    /**
     * Gibt die Hauptkategorie der ersten Kategorie zurück
     */
    public function main_cat()
    {
        if (!$this->category_ids) {
            return false;
        }
        $cat_ids = explode(',', $this->category_ids);
        foreach ($cat_ids as $cat_id) {
            $tree = self::get_category_tree($cat_id);
            if (isset($tree[1])) {
                return $tree[1];
            }
        }
        return false;
    }


    public static function get_category_tree($cat_id)
    {
        $start = self::get_category_by_id($cat_id);
        $tree = [];
        $tree[] = $start;
        $c_cat = $start;
        while ($c_cat->parent_id > 0) {
            $c_cat = self::get_category_by_id($c_cat->parent_id);
            $tree[] = $c_cat;
        }
        return $tree;
    }



    /**
     * bookable
     * 
     * prüft, ob ein Kurs buchbar ist. Wenn die Teilnehmerzahl überschritten ist oder das Datum vorbei ist, kann nicht mehr gebucht werden.
     */
    public function bookable()
    {        
        $this->schulanmeldung = false;
        if ($this->teilnehmer_warteliste >= $this->teilnehmer_max * 0.2) {
            return false;
        }
        if ($this->datestart < date('Y-m-d',strtotime('-2 weeks'))) {
            return false;
        }
        $kurs_json = json_decode($this->kurs_json, true);

        if ($kurs_json['keinewebanmeldung'] == "W") {
            $this->schulanmeldung = true;
            return false;
        }

        if (isset($kurs_json['anm_ende']) && $kurs_json['anm_ende']) {
            $anm_ende = date('Y-m-d', strtotime($kurs_json['anm_ende']));
            if ($anm_ende < date('Y-m-d')) {
                return false;
            }
        }

        return true;
    }

    public function get_ampel_text ($prefix = '', $elem = 'div') {
        if ($this->teilnehmer_warteliste >= $this->teilnehmer_max * 0.2) {
            return '<'.$elem.' class="ampel ampel-red">'.$prefix.'<span class="indicator"></span> {{ ausgebucht }}</'.$elem.'>';
        } elseif ($this->teilnehmer_angemeldet >= $this->teilnehmer_max) {
            return '<'.$elem.' class="ampel ampel-red">'.$prefix.'<span class="indicator"></span> {{ warteliste }}</'.$elem.'>';
        } elseif ($this->teilnehmer_angemeldet >= $this->teilnehmer_min) {
            return '<'.$elem.' class="ampel ampel-yellow">'.$prefix.'<span class="indicator"></span> {{ nur noch wenige Plätze frei }}</'.$elem.'>';
        } else {
            return '<'.$elem.' class="ampel ampel-green">'.$prefix.'<span class="indicator"></span> {{ noch Plätze frei }}</'.$elem.'>';
        }
    }


    public static function format_de_date($date)
    {
        return date('d.m.Y', strtotime($date));
    }

    public static function date_from_to($date, $to = '')
    {
        if ($to) {
            $date1 = explode('.', date('d.m.Y', $date));
            $date2 = explode('.', date('d.m.Y', $to));
            if ($date1[2] == $date2[2]) {
                unset($date1[2]);
                if ($date1[1] == $date2[1]) {
                    unset($date1[1]);
                }
                return implode('.', $date1) . '. – ' . implode('.', $date2);
            }
            return implode('.', $date1) . ' – ' . implode('.', $date2);
        } else {
            return date('d.m.Y', $date);
        }
    }


    /**
     * get_categories_by_name
     */
    public static function get_categories_by_name($cat_name = 'VHS', $filter = false)
    {
        $query = rex_yform_manager_table::get('rex_vhs_kategorie')->query();
        $query->where('name', $cat_name);
        $cat = $query->findOne();
        if (!$cat) {
            return false;
        }
        return self::get_categories_by_parent_id($cat->id, $filter);
    }

    /**
     * get_categories_by_parent_id
     */
    public static function get_categories_by_parent_id($parent_id = 0, $filter = false)
    {
        $query = rex_yform_manager_table::get('rex_vhs_kategorie')->query();
        $query->resetWhere()->resetLimit()->where('parent_id', $parent_id);
        $categories = $query->find();
        if ($filter) {
            foreach ($categories as $i=>$main_cat) {
                $sub_categories = self::get_categories_by_parent_id($main_cat->id);
                $all_ids = [];
                $all_ids[$main_cat->id] = $main_cat->id;
                foreach ($sub_categories as $cat) {
                    $all_ids[$cat->id] = $cat->id;
                }
                $kurse = self::find_kurse_for_categories($all_ids,true)->exists();
                if (!$kurse) {
                    unset($categories[$i]);
                }
            }
        }
        return $categories;
    }

    /**
     * get_category_by_id
     */
    public static function get_category_by_id($cat_id = 0)
    {
        $query = rex_yform_manager_table::get('rex_vhs_kategorie')->query();
        $query->resetWhere()->resetLimit()->where('id', $cat_id);
        return $query->findOne();
    }


    /**
     * find_kurse_for_categories
     */

    public static function find_kurse_for_categories($all_ids,$only_query = false)
    {
        //        dump($all_ids);
        $query = self::get_query();
        $where_raw = [];
        $params = [];

        foreach ($all_ids as $id) {
            $where_raw[] = 'FIND_IN_SET(:mc' . $id . ',`category_ids`)';
            $params['mc' . $id] = $id;
        }
        $query->whereRaw('(' . implode(' OR ', $where_raw) . ')', $params);
        if ($only_query) {
            return $query;
        }
        return $query->find();
    }

    /**
     * find_kurse_for_dozent
     */

    public static function find_kurse_for_dozent($doz_id)
    {
        $query = self::get_query();
        $query->whereRaw('FIND_IN_SET(:doz_id,dozenten)', ['doz_id' => $doz_id]);
        return $query->find();
    }


    public static function get_date_identical($termine, $glue = '<br>')
    {
        if (!is_array($termine)) {
            return false;
        }
        // Nur 1 Termin
        if (isset($termine['tag'])) {
            return substr(self::$wochentag[date('w', strtotime($termine['tag']))], 0, 2) . '., ' . $termine['tag'] . ', ' . $termine['zeitvon'] . ' – ' . $termine['zeitbis'] . ' Uhr';
        }
        if (!isset($termine[0]['tag'])) {
            return false;
        }
        $identical = true;
        $first_day = date('w', strtotime($termine[0]['tag']));
        $zeitvon = $termine[0]['zeitvon'] ?? '';
        $zeitbis = $termine[0]['zeitbis'] ?? '';
        foreach ($termine as $termin) {
            if (date('w', strtotime($termin['tag'])) != $first_day) {
                $identical = false;
                break;
            }
            if ($termin['zeitvon'] != $zeitvon || $termin['zeitbis'] != $zeitbis) {
                $identical = false;
                break;
            }
        }

        // Alle Termine am selben Wochentag und zur selben Uhrzeit
        if ($identical) {
            return self::$wochentag[$first_day] . 's, ' . $zeitvon . ' – ' . $zeitbis . ' Uhr';
        }
        $out = [];
        foreach ($termine as $termin) {
            $t_string = substr(self::$wochentag[date('w', strtotime($termin['tag']))], 0, 2) . '., ' . $termin['tag'] . ', ' . $termin['zeitvon'] . ' – ' . $termin['zeitbis'] . ' Uhr';
            $out[] = $t_string;
        }
        $out = array_unique($out);
        return implode($glue, $out);
    }

    public static function find_categories_of_list($kurse)
    {
        $cat_ids = [];
        foreach ($kurse as $kurs) {
            $cats_of_kurs = explode(',', $kurs->category_ids);
            foreach ($cats_of_kurs as $cat_id) {
                $cat_ids[$cat_id] = $cat_id;
            }
        }
        return $cat_ids;
    }

    public static function get_kurs_by_id($id)
    {
        $kurs = self::get_query()->where('id', $id)->findOne();
        if (!$kurs) {
            return false;
        }
        $kurs->zeitraum = $kurs->zeitraum();
        $kurs->url = $kurs->get_url();
        return $kurs;
    }

    public static function add_to_cart_url($article_id = 0, $kurs_id = '')
    {
        if (!$kurs_id) {
            if (rex_addon::get('url') && rex_addon::get('url')->isAvailable()) {
                $manager = Url\Url::resolveCurrent();
                if ($manager) {
                    $kurs_id = $manager->getDatasetId();
                }
            } else {
                $kurs_id = rex_get('vhs_kurs_id','int');
            }
        }

        return rex_getUrl($article_id, '', ['kurs_id' => $kurs_id, 'action' => 'add_to_cart']);
    }

    /**
     * Bereinigt den Text, entfernt überflüssige CRs (max 2)
     */
    public static function clean_text($text)
    {
        $text = preg_replace('/ +\R/', "\n", $text);
        $text = nl2br(preg_replace('/\R\R+/', "\n\n", $text));
        return $text;
    }
}
