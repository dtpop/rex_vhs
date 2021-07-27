<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$res = rex_sql::factory()->getArray('SELECT name FROM '.rex::getTable('yform_email_template'));
$etpl_options = array_column($res, 'name');


$form = rex_config_form::factory('vhs');
$form->addFieldset('VHS - Einstellungen');

$field = $form->addTextField('importpfad');
$field->setLabel('Pfad zur XML Datei');
$field->setNotice('Der Importpfad kann mit http(s):// beginnen, wenn die xml Datei über das Internet abrufbar ist.');

$field = $form->addTextAreaField('assocfields');
$field->setLabel('Assoziierte Feldnamen');
$field->setNotice('Datenbankfeldern zu XML Namen assoziieren <code>dbfieldname::xmlfield</code> z.B. <code>titel::titelkurz</code>.<br>Optional mit Format Option. z.B. <code>datestart::beginndat::date(Y-m-d)</code><br>Mit <code>protected</code> an der 4. Stelle kann ein Feld vor dem Überschreiben geschützt werden. Es wird dann nur beim ersten Import des Kurses befüllt. Beispiel: <code>text::titellang::::protected</code>');

$field = $form->addTextField('kufer_sync_xml_registration_path');
$field->setLabel('Dateipfad zu den Anmeldungen für Kufer Sync');
$field->setNotice('<code>rex_config::get("vhs","kufer_sync_xml_registration_path")</code>');


$form->addFieldset('Buchungseinstellungen VHS');

/*
$field = $form->addLinkmapField('cart_page');
$field->setLabel('Warenkorbseite');
$field->setNotice('<code>rex_config::get("vhs","cart_page")</code>');
*/

// =================== VHS Einstellungsseiten

$field = $form->addLinkmapField('address_page');
$field->setLabel('Adresseingabe Seite VHS');
$field->setNotice('<code>rex_config::get("vhs","address_page")</code>');

/*
$field = $form->addLinkmapField('participants_page');
$field->setLabel('Teilnehmer ergänzen Seite');
$field->setNotice('<code>rex_config::get("vhs","participants_page")</code>');
*/

$field = $form->addLinkmapField('order_page');
$field->setLabel('Bestellung Seite VHS');
$field->setNotice('<code>rex_config::get("vhs","order_page")</code>');

$field = $form->addLinkmapField('thankyou_page');
$field->setLabel('Danke Seite VHS');
$field->setNotice('<code>rex_config::get("vhs","thankyou_page")</code>');

$field = $form->addSelectField('email_template_customer');
$field->setLabel('E-Mail Template Kunde VHS');
$select = $field->getSelect();
$select->addOptions($etpl_options,true);
$field->setNotice('<code>rex_config::get("vhs","email_template_customer")</code>');

// ==== Paypal

$form->addFieldset('Paypal Einstellungen');

$field = $form->addTextField('currency');
$field->setLabel('Währung (z.B. EUR)');
$field->setNotice('<code>rex_config::get("vhs","currency")</code>');

$field = $form->addTextField('currency_symbol');
$field->setLabel('Währungssymbol (z.B. €)');
$field->setNotice('<code>rex_config::get("vhs","currency_symbol")</code>');

$field = $form->addTextField('paypal_client_id');
$field->setLabel('Paypal Client Id');

$field = $form->addTextField('paypal_secret');
$field->setLabel('Paypal Secret');

$field = $form->addCheckboxField('sandboxmode');
$field->setLabel('Paypal Sandbox ein');
$field->addOption('Paypal Sandbox ein', "1");


$field = $form->addTextField('paypal_sandbox_client_id');
$field->setLabel('Paypal Sandbox Client Id');

$field = $form->addTextField('paypal_sandbox_secret');
$field->setLabel('Paypal Sandbox Secret');

$field = $form->addTextField('paypal_getparams');
$field->setLabel('Paypal Zusätzliche Get-Parameter für Paypal');
$field->setNotice('z.B. um Maintenance bei der Entwicklung zu verwenden. Als JSON in der Form <code>{"key1":"value1","key2":"value2"}</code> angeben.');

$field = $form->addLinkmapField('paypal_page_start');
$field->setLabel('Paypal Startseite');
$field->setNotice('<code>rex_config::get("vhs","paypal_page_start")</code>');

$field = $form->addLinkmapField('paypal_page_success');
$field->setLabel('Paypal Zahlung erfolgt');
$field->setNotice('<code>rex_config::get("vhs","paypal_page_success")</code>');

$field = $form->addLinkmapField('paypal_page_error');
$field->setLabel('Paypal Fehler');
$field->setNotice('<code>rex_config::get("vhs","paypal_page_error")</code>');

$form->addFieldset('Bestätigungen / E-Mail');

$field = $form->addSelectField('email_template_seller');
$field->setLabel('E-Mail Template Bestellung');
$select = $field->getSelect();
$select->addOptions($etpl_options,true);
$field->setNotice('<code>rex_config::get("vhs","email_template_seller")</code>');

$field = $form->addTextField('order_email');
$field->setLabel('Bestellungen E-Mail Empfänger');
$field->setNotice('Mehrere E-Mail Empfänger können mit Komma getrennt werden.<br><code>rex_config::get("vhs","order_email")</code>');

/*
$field = $form->addTextAreaField('bsmimport');
$field->setLabel('Felddefinitionen für BSM Import');
$field->setNotice('Feldbezeichnung und Feldname durch zwei Doppelpunkte <code>::</code> trennen.<br>Datumsfeld mit <code>::date(Y-m-d)</code> formatierbar.');

$field = $form->addTextAreaField('viuimport');
$field->setLabel('Felddefinitionen für VIU Import');

$field = $form->addTextAreaField('objektimport');
$field->setLabel('Felddefinitionen für Adressen/Objekte');

$field = $form->addCheckboxField('debug');
$field->setLabel('Debugmodus');
$field->addOption('Debugmodus ein', "1");
$field->setNotice('Im Debugmodus werden die E-Mails an vordefinierte E-Mail Adressen geschickt.');

$field = $form->addTextField('viuemail');
$field->setLabel('VIU E-Mail Adressen');
$field->setNotice('Mehrere E-Mail-Adressen durch Komma trennen.');

$field = $form->addTextField('bsmemail');
$field->setLabel('BSM E-Mail Adressen');
$field->setNotice('Mehrere E-Mail-Adressen durch Komma trennen.');

$field = $form->addTextField('bsm_locked_steps');
$field->setLabel('Arbeitsschritte, die für den BSM gesperrt sind');
$field->setNotice('Werte durch Komma trennen.');

$field = $form->addTextField('viu_locked_steps');
$field->setLabel('Arbeitsschritte, die für den VIU gesperrt sind');
*/


$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;

