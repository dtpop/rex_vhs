<?php

rex_yform_manager_table::deleteCache();

/** @var rex_addon $this */

$content = rex_file::get(rex_path::addon('vhs', 'install/tablesets/vhs_yform_tabellen.json'));
rex_yform_manager_table_api::importTablesets($content);

rex_delete_cache();
rex_yform_manager_table::deleteCache();
