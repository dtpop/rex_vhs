<?php
if (rex::isBackend()) {
    echo rex_view::info('VHS Kursanzeige - Ausgabe nur im Frontend');
    return;
}

$link_id = "REX_LINK[1]";

$category_key = "REX_VALUE[2]"; // VHS

$text = rex_var::toArray("REX_VALUE[5]");
/*
$query = vhs::get_query();
$items = $query->find();

dump($items);
dump($items[0]->getData());
*/

$main_cat_id = rex_get('main_cat_id', 'int');
// $kurs_id = rex_get('vhs_kurs_id', 'int');



$kurs_id = 0;

if (rex_addon::get('url') && rex_addon::get('url')->isAvailable()) {
    $manager = Url\Url::resolveCurrent();
    if ($manager) {
        $profile_id = $manager->getProfileId();
        $profile = Url\Profile::get($profile_id);
        if ($profile->getTableName() == 'rex_vhs_kategorie') {
            $main_cat_id = $manager->getDatasetId();
        } else {
            $kurs_id = $manager->getDatasetId();
        }
    }
} else {
    $main_cat_id = rex_get('main_cat_id','int');
    $kurs_id = rex_get('vhs_kurs_id','int',0);
}





if ($kurs_id == 0 && "REX_VALUE[3]" == 1) {
    $cat = rex_yform_manager_table::get('rex_vhs_kategorie')->query()->where('name',$category_key)->findOne();
    $main_cat_id = $cat->id;
}

if ($kurs_id) {
    // Kurs (Detailseite) anzeigen
    $kurs = vhs::get_kurs_by_id($kurs_id);
    $fragment = new rex_fragment();
    $fragment->setVar('kurs', $kurs);
    $fragment->setVar('category_key', $category_key); // VHS
    $fragment->setVar('contact_text', $text['text']);
    $fragment->setVar('contact_email', $text['email']);
    echo $fragment->parse('kursdetail.php');
} elseif ($main_cat_id) {
    // Kategorien anzeigen
    $sub_categories = vhs::get_categories_by_parent_id($main_cat_id);
    $main_cat = vhs::get_category_by_id($main_cat_id);
    $all_ids = [];
    $all_ids[$main_cat->id] = $main_cat->id;
    foreach ($sub_categories as $cat) {
        $all_ids[$cat->id] = $cat->id;
    }
    $kurse = vhs::find_kurse_for_categories($all_ids);
    // Alle Kategorie Ids der gefundenen Kurse
    $all_categories_found = vhs::find_categories_of_list($kurse);

    $fragment = new rex_fragment();
    $fragment->setVar('subcategories', $sub_categories);
    $fragment->setVar('kurse', $kurse);
    $fragment->setVar('maincategory', $main_cat);
    $fragment->setVar('category_title', "REX_VALUE[4]");
    $fragment->setVar('category_key', $category_key); // VHS
    $fragment->setVar('all_cat_ids',$all_categories_found);
    $fragment->setVar('ist_suche', false);
    echo $fragment->parse('kurse_unterkategorien.php');
} else {
    // Hauptkategorien anzeigen
    $main_categories = vhs::get_categories_by_name($category_key, true);
//    dump($main_categories);
    $fragment = new rex_fragment();
    $fragment->setVar('categories', $main_categories);
    $fragment->setVar('category_key', $category_key); // VHS
    $fragment->setVar('link_id', $link_id);
    echo $fragment->parse('kurse_hauptkategorien.php');
}
