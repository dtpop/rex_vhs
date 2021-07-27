<?php

/**
 *
 * @package redaxo5
 */

// $addon = rex_addon::get('ohgnetz');

$importfile = rex_config::get('vhs','importpfad');

if (!$importfile) {
    echo rex_view::error('Es muss in den Einstellungen eine xml Datei für den Import angegeben werden.');
    return;
}



if (rex_request('submit','int') == 1) {
    $import = new vhs_import();
    $import->run();
}


?>


<!-- section class="rex-page-section">
    <div class="panel panel-edit">
        <div class="panel-body">
            <h3>Info</h3>
            <p>Hier kann eine csv Datei hochgeladen und in die Tabelle <code>Objekte (Adressen)</code> importiert werden. Die Tabelle wird zunächst geleert.</p>
            <p>In der ersten Zeile der CSV Datei müssen die Feldnamen angegeben sein. Die Einstellungen für die Feldnamen können auf der Einstellungs-Seite geändert werden.</p>
            <p>Die Reihenfolge der Felder ist beliebig und wird anhand der ersten Zeile korrekt erkannt.</p>
            <p>Die zu importierende Datei muss als Trennzeichen das Semikolon <code>;</code> enthalten und muss utf-8-codiert sein.</p>
        </div>    
    </div>    
</section -->
<form enctype="multipart/form-data" action="<?= rex_url::currentBackendPage() ?>" method="post">
    <section class="rex-page-section">
        <div class="panel panel-edit">
            <header class="panel-heading">
                <div class="panel-title">Import starten</div>
            </header>
            <div class="panel-body">
                <footer class="panel-footer">
                    <div class="rex-form-panel-footer">
                        <div class="btn-toolbar">
                            <button class="btn btn-send rex-form-aligned" name="submit" type="submit" value="1"><i class="rex-icon rex-icon-import"></i> importieren</button>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </section>
</form>

