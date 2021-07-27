<?php
class vhs_checkout
{

	static $fields = [
		'firstname', 'lastname', 'birthdate', 'company', 'department', 'address', 'zip', 'city', 'country', 'email', 'phone',
		'to_firstname', 'to_lastname', 'to_company', 'to_department', 'to_address', 'to_zip', 'to_city', 'to_country',
		'separate_delivery_address', 'payment_type', 'note', 'iban', 'bic', 'direct_debit_name', 'giropay_bic', 'info_news_ok', 'is_firma'
	];


	public function address_form()
	{

		$next_page = 'order_page';

		$userdata = self::get_user_data();
		$userdata = self::ensure_userdata_fields($userdata);
		//		dump($userdata);

		$cart = vhs_cart::get_cart();
		$kurs = $cart['kurs'];
		$kurs_json = json_decode($kurs['kurs_json'] ?? '', true);

		//		dump($kurs_json);

		$participants = $cart['participants'] ?? [];
		if (rex_request('part', 'array')) {
			$participants = rex_request('part', 'array');
		}

		$is_firma = ($userdata['is_firma'] ?? 'p') ?: 'p';


		$start_line = '<div class="uk-grid" uk-grid="margin: uk-margin-small">';
		$field_1_4 = '<div class="uk-width-1-4@s">';
		$field_3_4 = '<div class="uk-width-3-4@s">';
		$start_line_50 = '<div class="uk-child-width-1-1 uk-child-width-1-2@s uk-grid" uk-grid="margin: uk-margin-small"><div>';
		$start_line_50_abstand = '<div class="uk-child-width-1-1 uk-child-width-1-2@s uk-grid uk-margin-top" uk-grid="margin: uk-margin-small"><div>';
		$start_line_100_abstand = '<div class="uk-child-width-1-1 uk-grid uk-margin-top" uk-grid="margin: uk-margin-small"><div>';
		$start_line_100 = '<div class="uk-child-width-1-1 uk-grid" uk-grid="margin: uk-margin-small"><div>';
		$next_field = '</div><div>';
		$end_line = '</div></div>';
		$end_field = '</div>';

		$yform = new rex_yform();

		$yform->setObjectparams('form_action', rex_article::getCurrent()->getUrl());
		// $yform->setObjectparams('form_showformafterupdate', 0);
		$yform->setObjectparams('form_wrap_class', 'yform vhs-form');
		$yform->setObjectparams('debug', 0);
		$yform->setObjectparams('real_field_names', 0);
		$yform->setObjectparams('csrf_protection', false);
		$yform->setObjectparams('form_ytemplate', 'uikit,bootstrap,classic');
		$yform->setObjectparams('form_class', 'uk-form uk-form-stacked rex-yform wh_checkout');
		$yform->setObjectparams('form_anchor', 'rex-yform');
		$yform->setObjectparams('error_class', 'uk-form-danger has-error');

		$yform->setValueField('html', ['html_1', '<section class="uk-margin-large-top">']);

		$yform->setValueField('html', ['part_0', vhs_yform::add_participant_address($participants[0] ?? [], true, 0, false)]);





		if ($kurs_json['erlaubtfirmenanmeldung'] == 'W') {
			if ($kurs_json['erlaubtnormaleanmeldung'] == 'W') {
				$yform->setValueField('html', ['', $start_line_50]);
				$yform->setValueField('choice', ['is_firma', '', ['p' => 'Private Anmeldung', 'f' => 'Anmeldung als Firma/Institution'], 1, 0, $is_firma, '', '', '', ['uk-grid' => '', 'class' => 'uk-child-width-1-1', 'id' => 'firmenanmeldung_switcher'], ['class' => 'uk-width-1-1'], ['class' => 'uk-radio uk-margin-small-right'], '', '[no_db]']);
				$yform->setValueField('html', ['', $end_line]);
			} else {
				$yform->setValueField('hidden', ['is_firma', 'f']);
			}
			// ================== Firma ================================

			if ($kurs_json['erlaubtnormaleanmeldung'] == 'W') {
				$yform->setValueField('html', ['', '<div id="firmenanmeldung" hidden>']);
			}

			$yform->setValueField('html', ['', $start_line_100]);
			$yform->setValueField('html', ['', '<h2 class="uk-h2 uk-margin-large-top">Kontaktdaten Firma/Institution</h2>']);
			$yform->setValueField('html', ['', $end_line]);

			$yform->setValueField('html', ['', $start_line_50]);
			$yform->setValueField('choice', ['salutation_firma', 'Anrede', ['Herr' => 'Herr', 'Frau' => 'Frau'], 1, 0, 'Herr', '', '', '', ['uk-grid' => '', 'class' => 'uk-child-width-1-1@s'], ['class' => 'uk-width-1-2@m'], ['class' => 'uk-radio uk-margin-small-right']]);
			$yform->setValueField('html', ['', $next_field]);
			$yform->setValueField('text', ['title_firma', 'Titel', $userdata['title_firma'] ?? '', '[no_db]', []]);
			$yform->setValueField('html', ['', $end_line]);

			$yform->setValueField('html', ['', $start_line_50]);
			$yform->setValueField('text', ['firstname_firma', 'Ansprechpartner:in Vorname*', $userdata['firstname_firma'] ?? '', '[no_db]']);
			$yform->setValueField('html', ['', $next_field]);
			$yform->setValueField('text', ['lastname_firma', 'Ansprechpartner:in Nachname*', $userdata['lastname_firma'] ?? '', '[no_db]']);
			$yform->setValueField('html', ['', $end_line]);

			$yform->setValueField('html', ['', $start_line_100]);
			$yform->setValueField('text', ['firma', 'Firma/Institution*', $userdata['firma'] ?? '', '[no_db]']);
			$yform->setValueField('html', ['', $end_line]);

			$yform->setValueField('html', ['', $start_line_100]);
			$yform->setValueField('text', ['address_firma', 'Adresse*', $userdata['address_firma'] ?? '', '[no_db]']);
			$yform->setValueField('html', ['', $end_line]);

			$yform->setValueField('html', ['', $start_line]);
			$yform->setValueField('html', ['', $field_1_4]);
			$yform->setValueField('text', ['zip_firma', 'PLZ*', $userdata['zip_firma'] ?? '', '[no_db]']);
			$yform->setValueField('html', ['', $end_field]);
			$yform->setValueField('html', ['', $field_3_4]);
			$yform->setValueField('text', ['city_firma', 'Ort*', $userdata['city_firma'] ?? '', '[no_db]']);
			$yform->setValueField('html', ['', $end_line]);


			$yform->setValueField('html', ['', $start_line_50]);
			$yform->setValueField('text', ['phone_firma', 'Telefon*', $userdata['phone_firma'] ?? '', '[no_db]']);
			$yform->setValueField('html', ['', $next_field]);
			$yform->setValueField('text', ['email_firma', 'E-Mail*', $userdata['email_firma'] ?? '', '[no_db]']);
			$yform->setValueField('html', ['', $end_line]);

			if ($kurs_json['erlaubtnormaleanmeldung'] == 'W') {
				$yform->setValueField('html', ['', '</div>']);
			}

			// ================== Ende Firma ================================				



		} else {
			$yform->setValueField('hidden', ['is_firma', 'p']);
		}



		// ================== Teilnehmer ================================

		$yform->setValueField('html', ['', $start_line_100]);
		$yform->setValueField('html', ['', '<h2 class="uk-h2 uk-margin-large-top">Weitere Teilnehmer:innen</h2>']);
		$yform->setValueField('html', ['', $end_line]);

		$yform->setValueField('html', ['', '<div>']);
		foreach ($participants as $i => $participant) {
			if ($i == 0) {
				continue;
			}
			$yform->setValueField('html', ['', '<div class="participant-item uk-margin-small-top">']);

			//			$yform->add_participant_address($participant,true,$i);
			$yform->setValueField('html', ['', vhs_yform::add_participant_address($participant, true, $i)]);

			$yform->setValueField('html', ['', '<span><button type="button" uk-icon icon-trash class="uk-button uk-button-default btn-primary trash-button uk-margin-top">Teilnehmer löschen</button></span>']);
			$yform->setValueField('html', ['', '</div>']);
		}

		// ================== Ende Teilnehmer ================================


		// ================== Teilnehmer Vorlage ================================

		$yform->setValueField('html', ['', '<div id="add-participant" class="participant-item uk-margin-small-top" hidden="">']);

		//			$yform->add_participant_address([],true,'',true);
		$yform->setValueField('html', ['', vhs_yform::add_participant_address([], true, '', true)]);


		$yform->setValueField('html', ['', '<span><button type="button" uk-icon icon-trash class="uk-button uk-button-default btn-primary trash-button uk-margin-top">Teilnehmer löschen</button></span>']);

		$yform->setValueField('html', ['', '</div>']);

		// ================== Ende Teilnehmer Vorlage ================================

		$yform->setValueField('html', ['', '<button type="button" class="uk-button uk-button-default btn-primary uk-margin-large-top" id="add-participant-button">Teilnehmer hinzufügen</button>']);


		$yform->setValueField('html', ['', '</div>']);

		$yform->setValueField('html', ['', '<hr>' . $start_line_100_abstand]);
		$yform->setValueField('html', ['', '<div class="uk-align-right">Summe Kosten: <span id="price_total" data-price="' . $kurs['preis'] . '">' . number_format(floatval($kurs['preis']), 2, ',', "'") . '</span> EUR</div>']);
		$yform->setValueField('html', ['', $end_line]);

		// ================== Ende Teilnehmer ================================


		$yform->setValueField('html', ['html_2', $start_line_100_abstand]);
		$yform->setValueField('submit', ['send', 'Weiter', '', '[no_db]', '', 'uk-button-primary uk-align-right']);
		$yform->setValueField('html', ['', $end_line]);

		$yform->setValueField('html', ['', '</section>']);

		$yform->setValidateField('customfunction', ['part_0', 'vhs_checkout::validate_participants', '', '{{ Bitte füllen Sie alle markierten Felder aus. }}']);
		$yform->setValidateField('customfunction', ['html_1', 'vhs_checkout::validate_participants_email', '', '{{ Bitte tragen Sie eine gültige E-Mail Adresse ein. }}']);
		$yform->setValidateField('customfunction', ['html_2', 'vhs_checkout::validate_participants_geburtsjahr', '', '{{ Bitte geben Sie Ihr Geburtsjahr vierstellig an. }}']);

		if (!$is_fna) {
			if ($kurs_json['erlaubtfirmenanmeldung'] == 'W') {
				$yform->setValidateField('customfunction', ['firstname_firma', 'vhs_checkout::validate_sub_values', ['is_firma', 'f'], 'Bitte füllen Sie alle markierten Felder aus.']);
				$yform->setValidateField('customfunction', ['lastname_firma', 'vhs_checkout::validate_sub_values', ['is_firma', 'f'], 'Bitte füllen Sie alle markierten Felder aus.']);
				$yform->setValidateField('customfunction', ['firma', 'vhs_checkout::validate_sub_values', ['is_firma', 'f'], 'Bitte füllen Sie alle markierten Felder aus.']);
				$yform->setValidateField('customfunction', ['address_firma', 'vhs_checkout::validate_sub_values', ['is_firma', 'f'], 'Bitte füllen Sie alle markierten Felder aus.']);
				$yform->setValidateField('customfunction', ['zip_firma', 'vhs_checkout::validate_sub_values', ['is_firma', 'f'], 'Bitte füllen Sie alle markierten Felder aus.']);
				$yform->setValidateField('customfunction', ['city_firma', 'vhs_checkout::validate_sub_values', ['is_firma', 'f'], 'Bitte füllen Sie alle markierten Felder aus.']);
				$yform->setValidateField('customfunction', ['phone_firma', 'vhs_checkout::validate_sub_values', ['is_firma', 'f'], 'Bitte füllen Sie alle markierten Felder aus.']);
				$yform->setValidateField('customfunction', ['email_firma', 'vhs_checkout::validate_sub_values', ['is_firma', 'f'], 'Bitte füllen Sie alle markierten Felder aus.']);
				$yform->setValidateField('customfunction', ['email_firma', 'vhs_checkout::validate_sub_values', ['is_firma', 'f', 'email'], 'Bitte tragen Sie eine gültige E-Mail Adresse ein.']);
			}
		}



		$yform->setActionField('callback', ['vhs_cart::add_participants']);
		$yform->setActionField('callback', ['vhs_checkout::save_userdata_in_session']);
		$yform->setActionField('redirect', [rex_config::get('vhs', $next_page)]);

		return $yform;
	}


	/**
	 * p1 - Feldname
	 * p2 - ?
	 * p3 - Messagetext
	 * p4 - yform Objekt
	 */
	public static function validate_participants($p1, $p2, $p3, $p4)
	{

		$parts = rex_request('part', 'array');
		foreach ($parts as $part) {
			foreach (['firstname', 'lastname', 'address', 'zip', 'city', 'phone', 'email', 'geburtsjahr'] as $fieldname) {
				if ($part[$fieldname] == '') {
					return true;
				}
			}
		}
		return false;
	}

	public static function validate_participants_email($p1, $p2, $p3, $p4)
	{

		$parts = rex_request('part', 'array');
		foreach ($parts as $part) {
			foreach (['email'] as $fieldname) {
				if (!self::is_email_correct($part[$fieldname])) {
					return true;
				}
			}
		}
		return false;
	}

	public static function validate_participants_geburtsjahr($p1, $p2, $p3, $p4)
	{

		$parts = rex_request('part', 'array');
		foreach ($parts as $part) {
			foreach (['geburtsjahr'] as $fieldname) {
				if (!self::is_geburtsjahr_korrekt($part[$fieldname])) {
					return true;
				}
			}
		}
		return false;
	}


	public static function validate_sub_values($label, $value, $params, $yf)
	{
		$compare = '';
		if (is_array($params)) {
			list($field, $compare) = $params;
		} else {
			$field = $params;
		}
		foreach ($yf->getObjects() as $o) {
			if ($o->getName() == $field) {
				$base_val = $o->getValue();
			}
			if ($o->getName() == $label) {
				$val = $o->getValue();
			}
		}
		if ($compare) {
			if ($compare != $base_val) {
				return false;
			}
		}
		if (!$base_val) {
			return false;
		}
		if (!$val) {
			return true;
		}

		if (isset($params[2])) {
			if ($params[2] == 'email') {
				if (!self::is_email_correct($val)) {
					return true;
				}
			} elseif ($params[2] == 'iban') {
				if (!self::is_iban_correct($val)) {
					return true;
				}
			}
		}

		return false;
	}




	/**
	 * Wenn die E-Mail Adresse nicht korrekt ist, wird false zurückgegeben, sonst true
	 */
	public static function is_email_correct($text_string)
	{
		if (preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/", $text_string)) {
			return true;
		}
		return false;
	}


	/**
	 * Wenn die IBAN nicht korrekt ist, wird false zurückgegeben
	 */
	public static function is_iban_correct($text_string)
	{

		$iban = str_replace(' ', '', strtolower($text_string));

		$countries = [
			'al' => 28, 'ad' => 24, 'at' => 20, 'az' => 28, 'bh' => 22, 'be' => 16, 'ba' => 20, 'br' => 29, 'bg' => 22, 'cr' => 21, 'hr' => 21, 'cy' => 28, 'cz' => 24,
			'dk' => 18, 'do' => 28, 'ee' => 20, 'fo' => 18, 'fi' => 18, 'fr' => 27, 'ge' => 22, 'de' => 22, 'gi' => 23, 'gr' => 27, 'gl' => 18, 'gt' => 28, 'hu' => 28,
			'is' => 26, 'ie' => 22, 'il' => 23, 'it' => 27, 'jo' => 30, 'kz' => 20, 'kw' => 30, 'lv' => 21, 'lb' => 28, 'li' => 21, 'lt' => 20, 'lu' => 20, 'mk' => 19,
			'mt' => 31, 'mr' => 27, 'mu' => 30, 'mc' => 27, 'md' => 24, 'me' => 22, 'nl' => 18, 'no' => 15, 'pk' => 24, 'ps' => 29, 'pl' => 28, 'pt' => 25, 'qa' => 29,
			'ro' => 24, 'sm' => 27, 'sa' => 24, 'rs' => 22, 'sk' => 24, 'si' => 19, 'es' => 24, 'se' => 24, 'ch' => 21, 'tn' => 24, 'tr' => 26, 'ae' => 23, 'gb' => 22, 'vg' => 24
		];
		$chars = [
			'a' => 10, 'b' => 11, 'c' => 12, 'd' => 13, 'e' => 14, 'f' => 15, 'g' => 16, 'h' => 17, 'i' => 18, 'j' => 19, 'k' => 20, 'l' => 21, 'm' => 22,
			'n' => 23, 'o' => 24, 'p' => 25, 'q' => 26, 'r' => 27, 's' => 28, 't' => 29, 'u' => 30, 'v' => 31, 'w' => 32, 'x' => 33, 'y' => 34, 'z' => 35
		];

		if (!isset($countries[substr($iban, 0, 2)]) || strlen($iban) != $countries[substr($iban, 0, 2)]) {
			return false;
		} else {
			$movedChar = substr($iban, 4) . substr($iban, 0, 4);
			$movedCharArray = str_split($movedChar);
			$newString = '';

			foreach ($movedCharArray as $k => $v) {
				if (!is_numeric($movedCharArray[$k])) {
					$movedCharArray[$k] = $chars[$movedCharArray[$k]];
				}
				$newString .= $movedCharArray[$k];
			}

			if (function_exists('bcmod')) {
				return (bcmod($newString, '97') == 1);
			} else {
				$x = $newString;
				$y = '97';
				$take = 5;
				$mod = '';

				do {
					$a = (int)$mod . substr($x, 0, $take);
					$x = substr($x, $take);
					$mod = $a % $y;
				} while (strlen($x));

				return ((int)$mod == 1);
			}
		}
		return true;
	}


	/**
	 * prüft auf 4stellige Jahreszahl max. 100 Jahre
	 */
	public static function is_geburtsjahr_korrekt($text_string)
	{
		if (strlen($text_string) != 4) {
			return false;
		}
		$maxyear = intval(date('Y'));
		$minyear = $maxyear - 100;
		$checkval = intval($text_string);
		if ($checkval > $maxyear || $checkval < $minyear) {
			return false;
		}
		return true;
	}




	public static function get_user_data()
	{
		return rex_session('user_data', 'array');
	}

	public static function ensure_userdata_fields($user_data)
	{
		foreach (self::$fields as $field) {
			if (!isset($user_data[$field])) {
				$user_data[$field] = '';
			}
		}
		if (rex_plugin::get('ycom', 'auth')->isAvailable()) {
			$ycom_user = rex_ycom_auth::getUser();
			if ($ycom_user) {
				$ycom_userdata = $ycom_user->getData();
				// Sonderfall name
				if ($user_data['lastname'] == '') {
					$user_data['lastname'] = $ycom_userdata['name'];
				}
				foreach ($user_data as $k => $v) {
					if (isset($ycom_userdata[$k]) && $v == '') {
						$user_data[$k] = $ycom_userdata[$k];
					}
				}
				$user_data['ycom_userid'] = $ycom_user->getId();
				//            dump($ycom_user->getData());
			}
		}

		return $user_data;
	}

	public static function save_userdata_in_session($params)
	{
		$value_pool = $params->params['value_pool']['email'];
		rex_set_session('user_data', $value_pool);
	}

	/**
	 * Wird nach der Bestätigungsseite aufgerufen.
	 * prüft, ob Paypal gewählt wurde und leitet zu Paypal um.
	 * Bei Rechnung und Lastschrift zur Danke-Seite	 * 
	 */
	public static function redirect_payment($params)
	{
		$next_page = 'thankyou_page';
		$fb = 'vhs';

		$value_pool = $params->params['value_pool']['email'];
		rex_set_session('value_pool', $value_pool); // payment_type, // iban // bic ...
		if ($value_pool['payment_type'] == 'paypal') {
			rex_redirect(rex_config::get('vhs', 'paypal_page_start'));
		} else {
			self::save_order($value_pool);
			$vhs_response = new vhs_response();
			$vhs_response->send_response_mail($fb);
			$cart = vhs_cart::get_cart();
			$cart['kurs'] = [];
			rex_set_session('vhs_cart', $cart);
			rex_redirect(rex_config::get('vhs', $next_page));
		}
	}



	/**
	 * xml erstellen und schreiben
	 * 
	 */
	public static function save_order($value_pool)
	{
		$cart = vhs_cart::get_cart();
		self::create_xml_registration($cart, rex_session('user_data', 'array'), $value_pool, 'selbst');
	}


	/**
	 * Create XML registration for KuferSQL
	 * Mit Code von Tobias Krais
	 */
	public static function create_xml_registration($cart, $user_data, $value_pool, $registration_type, $get_code = false)
	{

		$payment_types = [
			'direct_debit' => 'L',
			'invoice' => 'R',
			'paypal' => 'P',
		];

		$firma = '';
		if ($user_data['is_firma'] == 'f') {
			$firma = ' (' . $user_data['firma'] . ')';
		}
		$is_fna = $user_data['is_fna'] ?? false;


		//		dump($cart);
		//		dump($user_data);
		//		dump($value_pool);

		$invoice_address = $cart['participants'][0];
		$participants = $cart['participants'];

		// <?xml version="1.0" encoding="UTF-8">
		$xml = new \DOMDocument("1.0", "UTF-8");
		$xml->formatOutput = true;

		// <ANMELDUNGEN>
		$registrations = $xml->createElement("ANMELDUNGEN");
		$xml->appendChild($registrations);

		// <ANMELDUNG>
		$registration = $xml->createElement("ANMELDUNG");
		$registrations->appendChild($registration);

		// <STAMMDATEN>
		$stammdaten = $xml->createElement("STAMMDATEN");
		$registration->appendChild($stammdaten);

		if ($user_data['is_firma'] == 'f') {

			// <NAME>Last name</NAME>
			$name = $xml->createElement("NAME");
			$name->appendChild($xml->createTextNode($user_data['lastname_firma']));
			$stammdaten->appendChild($name);
			// <VORNAME>First name</VORNAME>
			$firstname = $xml->createElement("VORNAME");
			$firstname->appendChild($xml->createTextNode($user_data['firstname_firma'] . $firma));
			$stammdaten->appendChild($firstname);
			// <STRASSE>Street and number</STRASSE>
			$strasse = $xml->createElement("STRASSE");
			$strasse->appendChild($xml->createTextNode($user_data['address_firma']));
			$stammdaten->appendChild($strasse);
			// <ORT>City</ORT>
			$city = $xml->createElement("ORT");
			$city->appendChild($xml->createTextNode($user_data['zip_firma'] . ' ' . $user_data['city_firma']));
			$stammdaten->appendChild($city);

			// <KOMMUNIKATION>
			$kommunikation = $xml->createElement("KOMMUNIKATION");
			$stammdaten->appendChild($kommunikation);

			// <KOMMUNIKATIONSEINTRAG>
			$kommunikationseintrag_phone = $xml->createElement("KOMMUNIKATIONSEINTRAG");
			$kommunikation->appendChild($kommunikationseintrag_phone);
			// <KOMMART>T</KOMMART>
			$kommart_phone = $xml->createElement("KOMMART");
			$kommart_phone->appendChild($xml->createTextNode("T"));
			$kommunikationseintrag_phone->appendChild($kommart_phone);
			// <KOMMBEZ>Telefon</KOMMBEZ>
			$kommbez_phone = $xml->createElement("KOMMBEZ");
			$kommbez_phone->appendChild($xml->createTextNode("Telefon"));
			$kommunikationseintrag_phone->appendChild($kommbez_phone);
			// <KOMMWERT>Phone number</KOMMWERT>
			$kommwert_phone = $xml->createElement("KOMMWERT");
			$kommwert_phone->appendChild($xml->createTextNode($user_data['phone_firma']));
			$kommunikationseintrag_phone->appendChild($kommwert_phone);

			// <KOMMUNIKATIONSEINTRAG>
			$kommunikationseintrag_email = $xml->createElement("KOMMUNIKATIONSEINTRAG");
			$kommunikation->appendChild($kommunikationseintrag_email);
			// <KOMMART>T</KOMMART>
			$kommart_email = $xml->createElement("KOMMART");
			$kommart_email->appendChild($xml->createTextNode("E"));
			$kommunikationseintrag_email->appendChild($kommart_email);
			// <KOMMBEZ>eMail</KOMMBEZ>
			$kommbez_email = $xml->createElement("KOMMBEZ");
			$kommbez_email->appendChild($xml->createTextNode("eMail"));
			$kommunikationseintrag_email->appendChild($kommbez_email);
			// <KOMMWERT>E-Mail address</KOMMWERT>
			$kommwert_email = $xml->createElement("KOMMWERT");
			$kommwert_email->appendChild($xml->createTextNode($user_data['email_firma']));
			$kommunikationseintrag_email->appendChild($kommwert_email);

			// <SESSIONTIME>Unix Timestamp</SESSIONTIME>
			$session_time = $xml->createElement("SESSIONTIME");
			$session_time->appendChild($xml->createTextNode(time()));
			$stammdaten->appendChild($session_time);
		} else {
			// <NAME_TITEL>M = Herr, W = Frau, F = Herr</NAME_TITEL>

			$name_titel = $xml->createElement("NAME_TITEL");
			$name_titel->appendChild($xml->createTextNode($invoice_address['title']));
			$stammdaten->appendChild($name_titel);

			// <NAME>Last name</NAME>
			$name = $xml->createElement("NAME");
			$name->appendChild($xml->createTextNode($invoice_address['lastname']));
			$stammdaten->appendChild($name);
			// <VORNAME>First name</VORNAME>
			$firstname = $xml->createElement("VORNAME");
			$firstname->appendChild($xml->createTextNode($invoice_address['firstname'] . $firma));
			$stammdaten->appendChild($firstname);
			// <STRASSE>Street and number</STRASSE>
			$strasse = $xml->createElement("STRASSE");
			$strasse->appendChild($xml->createTextNode($invoice_address['address']));
			$stammdaten->appendChild($strasse);
			// <ORT>City</ORT>
			$city = $xml->createElement("ORT");
			$city->appendChild($xml->createTextNode($invoice_address['zip'] . ' ' . $invoice_address['city']));
			$stammdaten->appendChild($city);

			// <GESCHLECHT>M = male, W = female, F = company</GESCHLECHT>
			$gender = $xml->createElement("GESCHLECHT");
			$gender->appendChild($xml->createTextNode(strtoupper($invoice_address['geschlecht'])));
			$stammdaten->appendChild($gender);

			// <GEBJAHR>
			$elem = $xml->createElement("GEBJAHR");
			$elem->appendChild($xml->createTextNode($invoice_address['geburtsjahr']));
			$stammdaten->appendChild($elem);

			if (isset($user_data['bemerkung']) && $user_data['bemerkung']) {
				$elem = $xml->createElement("BEMERKUNG");
				$elem->appendChild($xml->createTextNode($user_data['bemerkung']));
				$stammdaten->appendChild($elem);
			}


			// <KOMMUNIKATION>
			$kommunikation = $xml->createElement("KOMMUNIKATION");
			$stammdaten->appendChild($kommunikation);

			// <KOMMUNIKATIONSEINTRAG>
			$kommunikationseintrag_phone = $xml->createElement("KOMMUNIKATIONSEINTRAG");
			$kommunikation->appendChild($kommunikationseintrag_phone);
			// <KOMMART>T</KOMMART>
			$kommart_phone = $xml->createElement("KOMMART");
			$kommart_phone->appendChild($xml->createTextNode("T"));
			$kommunikationseintrag_phone->appendChild($kommart_phone);
			// <KOMMBEZ>Telefon</KOMMBEZ>
			$kommbez_phone = $xml->createElement("KOMMBEZ");
			$kommbez_phone->appendChild($xml->createTextNode("Telefon"));
			$kommunikationseintrag_phone->appendChild($kommbez_phone);
			// <KOMMWERT>Phone number</KOMMWERT>
			$kommwert_phone = $xml->createElement("KOMMWERT");
			$kommwert_phone->appendChild($xml->createTextNode($invoice_address['phone']));
			$kommunikationseintrag_phone->appendChild($kommwert_phone);

			// <KOMMUNIKATIONSEINTRAG>
			$kommunikationseintrag_email = $xml->createElement("KOMMUNIKATIONSEINTRAG");
			$kommunikation->appendChild($kommunikationseintrag_email);
			// <KOMMART>T</KOMMART>
			$kommart_email = $xml->createElement("KOMMART");
			$kommart_email->appendChild($xml->createTextNode("E"));
			$kommunikationseintrag_email->appendChild($kommart_email);
			// <KOMMBEZ>eMail</KOMMBEZ>
			$kommbez_email = $xml->createElement("KOMMBEZ");
			$kommbez_email->appendChild($xml->createTextNode("eMail"));
			$kommunikationseintrag_email->appendChild($kommbez_email);
			// <KOMMWERT>E-Mail address</KOMMWERT>
			$kommwert_email = $xml->createElement("KOMMWERT");
			$kommwert_email->appendChild($xml->createTextNode($invoice_address['email']));
			$kommunikationseintrag_email->appendChild($kommwert_email);

			// <SESSIONTIME>Unix Timestamp</SESSIONTIME>
			$session_time = $xml->createElement("SESSIONTIME");
			$session_time->appendChild($xml->createTextNode(time()));
			$stammdaten->appendChild($session_time);
		}

		if (isset($value_pool['payment_type']) && $value_pool['payment_type'] == "direct_debit") {

			// <KONTOINH>Account owner</KONTOINH>
			$account_owner = $xml->createElement("KONTOINH");
			$account_owner->appendChild($xml->createTextNode($value_pool['direct_debit_name']));
			$stammdaten->appendChild($account_owner);
			// <BIC>BIC</BIC>
			$bic = $xml->createElement("BIC");
			$bic->appendChild($xml->createTextNode($value_pool['bic']));
			$stammdaten->appendChild($bic);
			// <IBAN>IBAN</IBAN>
			$iban = $xml->createElement("IBAN");
			$iban->appendChild($xml->createTextNode($value_pool['iban']));
			$stammdaten->appendChild($iban);
		}

		// <KURS>
		$kurse_xml = $xml->createElement("KURSE");
		$registration->appendChild($kurse_xml);

		$kurs = $cart['kurs'];

		$course_id = $kurs['nummer'];
		//			if(is_array($participant)) {
		//				$course = new Course($course_id);
		//	$course = json_decode($kurs['kurs_json']);

		// <KURS>
		$kurs_xml = $xml->createElement("KURS");
		$kurse_xml->appendChild($kurs_xml);

		// <KURSNUMMER>Course number</KURSNUMMER>
		$kursnummer = $xml->createElement("KURSNUMMER");
		$kursnummer->appendChild($xml->createTextNode($course_id));
		$kurs_xml->appendChild($kursnummer);

		// <STATUS>A</STATUS>
		$status = $xml->createElement("STATUS");
		$status->appendChild($xml->createTextNode("A"));
		$kurs_xml->appendChild($status);

		// <ZAHLART>A</ZAHLART>
		if (isset($value_pool['payment_type'])) {
			$zahlart = $xml->createElement("ZAHLART");
			$zahlart->appendChild($xml->createTextNode($payment_types[$value_pool['payment_type']]));
			$kurs_xml->appendChild($zahlart);
		}

		$kurspreis = $kurs['preis'];
		if ($user_data['ermaessigung']) {
			$kurspreis = $kurs['preisreduziert'];
		}

		// <KURSGEBUEHR>35,00</KURSGEBUEHR>
		$kursgebuehr = $xml->createElement("KURSGEBUEHR");
		$kursgebuehr->appendChild($xml->createTextNode(number_format($kurspreis * count($participants), 2)));
		$kurs_xml->appendChild($kursgebuehr);

		// <ANZAHL>1</ANZAHL>
		$anzahl = $xml->createElement("ANZAHL");
		$anzahl->appendChild($xml->createTextNode(count($participants)));
		$kurs_xml->appendChild($anzahl);

		$weitereanm = $xml->createElement("WEITEREANM");

		$is_weitere_anm = false;

		//		if (count($participants) > 1) {
		// <WEITEREANM>
		foreach ($participants as $i => $participant) {
			// Bei Firmenanmeldung ersten Teilnehmer aufnehmen.
			if ($i == 0 && $user_data['is_firma'] != 'f') {
				continue;
			}

			// <WEITERANM>
			$weiteranm = $xml->createElement("WEITERANM");
			$weitereanm->appendChild($weiteranm);


			$typ_value = 'E';
			if ($user_data['is_firma'] == 'f') {
				$typ_value = 'F';
			} elseif (count($participants) > 1) {
				$typ_value = 'M';
			}

			// <TYP>K / F / M</TYP> - (Mutter)Kind / Firma / Mehrfach
			$typ = $xml->createElement("TYP");
			$typ->appendChild($xml->createTextNode($typ_value));
			$weiteranm->appendChild($typ);

			// <KURSGEBUEHR>35,00</KURSGEBUEHR>
			$kursgebuehr = $xml->createElement("KURSGEBUEHR");
			$kursgebuehr->appendChild($xml->createTextNode($kurs['preis']));
			$weiteranm->appendChild($kursgebuehr);

			// <WEITERSTAMM>
			$weiterstamm = $xml->createElement("WEITERSTAMM");
			$weiteranm->appendChild($weiterstamm);


			$name_titel = $xml->createElement("NAME_TITEL");
			$name_titel->appendChild($xml->createTextNode($participant['title']));
			$weiterstamm->appendChild($name_titel);

			// <NAME>Last name</NAME>
			$name = $xml->createElement("NAME");
			$name->appendChild($xml->createTextNode($participant['lastname']));
			$weiterstamm->appendChild($name);
			// <VORNAME>First name</VORNAME>
			$firstname = $xml->createElement("VORNAME");
			$firstname->appendChild($xml->createTextNode($participant['firstname'] . $firma));
			$weiterstamm->appendChild($firstname);
			// <STRASSE>Street and number</STRASSE>
			$strasse = $xml->createElement("STRASSE");
			$strasse->appendChild($xml->createTextNode($participant['address']));
			$weiterstamm->appendChild($strasse);
			// <ORT>City</ORT>
			$city = $xml->createElement("ORT");
			$city->appendChild($xml->createTextNode($participant['zip'] . ' ' . $participant['city']));
			$weiterstamm->appendChild($city);

			// <GESCHLECHT>M = male, W = female, F = company</GESCHLECHT>
			$gender = $xml->createElement("GESCHLECHT");
			$gender->appendChild($xml->createTextNode(strtoupper($participant['geschlecht'])));
			$weiterstamm->appendChild($gender);

			// <GEBJAHR>
			$elem = $xml->createElement("GEBJAHR");
			$elem->appendChild($xml->createTextNode($participant['geburtsjahr']));
			$weiterstamm->appendChild($elem);

			// <KOMMUNIKATION>
			$kommunikation = $xml->createElement("KOMMUNIKATION");
			$weiterstamm->appendChild($kommunikation);

			// <KOMMUNIKATIONSEINTRAG>
			$kommunikationseintrag_phone = $xml->createElement("KOMMUNIKATIONSEINTRAG");
			$kommunikation->appendChild($kommunikationseintrag_phone);
			// <KOMMART>T</KOMMART>
			$kommart_phone = $xml->createElement("KOMMART");
			$kommart_phone->appendChild($xml->createTextNode("T"));
			$kommunikationseintrag_phone->appendChild($kommart_phone);
			// <KOMMBEZ>Telefon</KOMMBEZ>
			$kommbez_phone = $xml->createElement("KOMMBEZ");
			$kommbez_phone->appendChild($xml->createTextNode("Telefon"));
			$kommunikationseintrag_phone->appendChild($kommbez_phone);
			// <KOMMWERT>Phone number</KOMMWERT>
			$kommwert_phone = $xml->createElement("KOMMWERT");
			$kommwert_phone->appendChild($xml->createTextNode($participant['phone']));
			$kommunikationseintrag_phone->appendChild($kommwert_phone);

			// <KOMMUNIKATIONSEINTRAG>
			$kommunikationseintrag_email = $xml->createElement("KOMMUNIKATIONSEINTRAG");
			$kommunikation->appendChild($kommunikationseintrag_email);
			// <KOMMART>T</KOMMART>
			$kommart_email = $xml->createElement("KOMMART");
			$kommart_email->appendChild($xml->createTextNode("E"));
			$kommunikationseintrag_email->appendChild($kommart_email);
			// <KOMMBEZ>eMail</KOMMBEZ>
			$kommbez_email = $xml->createElement("KOMMBEZ");
			$kommbez_email->appendChild($xml->createTextNode("eMail"));
			$kommunikationseintrag_email->appendChild($kommbez_email);
			// <KOMMWERT>E-Mail address</KOMMWERT>
			$kommwert_email = $xml->createElement("KOMMWERT");
			$kommwert_email->appendChild($xml->createTextNode($participant['email']));
			$kommunikationseintrag_email->appendChild($kommwert_email);

			$is_weitere_anm = true;
		}
		//		}


		if ($is_weitere_anm) {
			$kurs_xml->appendChild($weitereanm);
		}


		// XML in Datei schreiben
		try {
			$dir = trim(rex_config::get('vhs', 'kufer_sync_xml_registration_path'), "/");
			if (!file_exists($dir)) {
				mkdir($dir, "0777", TRUE);
			}
			if (!file_exists($dir . "/.htaccess")) {
				$handle = fopen($dir . "/.htaccess", 'a');
				if ($handle !== FALSE) {
					fwrite($handle, "order deny,allow" . PHP_EOL . "deny from all");
				}
			}
			if ($xml->save($dir . "/" . time() . '-' . rand() . '.xml')) {
				return TRUE;
			}
		} catch (Exception $e) {
			print "Error: " . $e;
			return FALSE;
		}
	}
}
