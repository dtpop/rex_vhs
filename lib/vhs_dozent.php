<?php
class vhs_dozent extends rex_yform_manager_dataset
{

    public static function get_query()
    {
        return self::query()->orderBy('nachname');
    }

    public static function get_all()
    {
        return self::get_query()->find();
    }

    public static function get_by_id($id)
    {
        $query = self::get_query();
        $query->where('id', $id);
        $item = $query->findOne();
        $item->url = $item->get_url();
        return $item;
    }

    public function is_vhs_dozent() {
        $aussenstelle = explode(',',$this->aussenstelle);
        return in_array(3,$aussenstelle);
    }

    public function get_url() {
        $url = [];
        $aussenstelle = explode(',',$this->aussenstelle);
        foreach ($aussenstelle as $a_id) {
            if (in_array($a_id,[1,3])) {
                $url[$get_pars[$a_id]] = rex_getUrl('','',['vhs_dozent_id'=>$this->id]);
            }
        }
        return $url;        
    }



}
