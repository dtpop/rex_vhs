<?php

$query = rex_yform_manager_table::get('rex_vhs_kategorie')->query();
$query->whereRaw('parent_id = 0 OR parent_id = ""');
$cats = $query->find();
$items = [];
foreach ($cats as $cat) {
    $items[$cat->name] = $cat->name;
}

$mform = new MForm();

$mform->addTab("Parameter");
$mform->addLinkField(1,['label'=>'Kursanzeige']);
$mform->addTextField("4",['label'=>'Alternativer Titel für Bereich (optional)']);
$mform->addSelectField("2", $items, ['label'=>'Kategorie wählen']);
$mform->addCheckboxField("3",[1=>"Ohne Auswahl der Hauptkategorie anzeigen"],['label'=>'Anzeigemodus']);
$mform->closeTab();

$mform->addTab("Texte (Fragen zum Kurs)");
$mform->addTextAreaField("5.text", array('label'=>'Einführungstext','class'=>'tinyMCE-headline'));
$mform->addTextField("5.email", array('label'=>'Mailadresse'));

echo $mform->show();
