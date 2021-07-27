<?php
class vhs_run_import extends rex_console_command {
    protected function execute() {
        $import = new vhs_import();
        $import->run();    
        rex_logger::factory()->log('success','Import ausgeführt',[],__FILE__,__LINE__);
    }
}
?>