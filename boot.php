<?php
rex_yform_manager_dataset::setModelClass('rex_vhs_kurs', vhs::class);
rex_yform_manager_dataset::setModelClass('rex_vhs_jms_dozenten', jms_dozent::class);
rex_yform_manager_dataset::setModelClass('rex_vhs_dozent', vhs_dozent::class);
rex_yform_manager_dataset::setModelClass('rex_wbz_ansprechpartner', wbz_ansprechpartner::class);

if (rex::isFrontend()) {

    /*
    $curDir = __DIR__;
    require_once $curDir . '/functions/helper.php';
    */
    rex_login::startSession();

    rex_extension::register('PACKAGES_INCLUDED', function () {

        if (rex_article::getCurrentId() == rex_config::get('warehouse', 'thankyou_page')) {
            // Bei Dankeseite Paypal bestätigen
            if (rex_get('paymentId')) {
//                warehouse::set_cart_from_payment_id(rex_get('paymentId'));
                vhs_paypal::execute_payment();
                // Führt den E-Mail Versand im Hintergrund aus
//                $yf = warehouse::summary_form(true);
//                warehouse::clear_cart();
            }
        }
    });
}


// $addon = rex_addon::get('project');

// register a custom yrewrite scheme
// rex_yrewrite::setScheme(new rex_project_rewrite_scheme());

// register yform template path
// rex_yform::addTemplatePath($addon->getPath('yform-templates'));

// Example of mediapool Whitelist
/*
rex_addon::get('mediapool')->setProperty('allowed_mime_types', [
    'gif'   => ['image/gif'],
    'jpg'   => ['image/jpeg', 'image/pjpeg'],
    'jpeg'  => ['image/jpeg', 'image/pjpeg'],
    'png'   => ['image/png'],
    'eps'   => ['application/postscript'],
    'tif'   => ['image/tiff'],
    'tiff'  => ['image/tiff'],
    'svg'   => ['image/svg+xml'],
    'pdf'   => ['application/pdf'],
    'xls'   => ['application/vnd.ms-excel'],
    'xlsx'  => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
    'xlsm'  => ['application/vnd.ms-excel.sheet.macroEnabled.12'],
    'doc'   => ['application/msword'],
    'docx'  => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    'docm'  => ['application/vnd.ms-word.document.macroEnabled.12'],
    'dot'   => ['application/msword'],
    'dotx'  => ['application/vnd.openxmlformats-officedocument.wordprocessingml.template'],
    'dotm'  => ['application/vnd.ms-word.template.macroEnabled.12'],
    'ppt'   => ['application/vnd.ms-powerpoint'],
    'pptx'  => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
    'pptm'  => ['application/vnd.ms-powerpoint.presentation.macroEnabled.12'],
    'pot'   => ['application/vnd.ms-powerpoint'],
    'potx'  => ['application/vnd.openxmlformats-officedocument.presentationml.template'],
    'potm'  => ['application/vnd.ms-powerpoint.template.macroEnabled.12'],
    'pps'   => ['application/vnd.ms-powerpoint'],
    'ppsx'  => ['application/vnd.openxmlformats-officedocument.presentationml.slideshow'],
    'ppsm'  => ['application/vnd.ms-powerpoint.slideshow.macroEnabled.12'],
    'rtf'   => ['application/rtf'],
    'txt'   => ['text/plain', 'application/octet-stream'],
    'csv'   => ['text/plain', 'application/octet-stream'],
    'zip'   => ['application/x-zip-compressed','application/zip'],
    'gz'    => ['application/x-gzip'],
    'tar'   => ['application/x-tar'],
    'mov'   => ['video/quicktime'],
    'movie' => ['video/quicktime'],
    'mp3'   => ['audio/mpeg'],
    'mpe'   => ['video/mpeg'],
    'mpeg'  => ['video/mpeg'],
    'mpg'   => ['video/mpeg'],
]);
*/
