<?php
/* Paypal Zahlung erfolgt */
// Paypal Zahlung bestätigen, Erfolg loggen, weiter leiten zur Danke-Seite
if (rex::isBackend()) {
    echo '<h2>PayPal Abschluss der Zahlung, Bestätigungsmail und Bestellmail verschicken.</h2>';
    return;
} else {
    $pp = new vhs_paypal();
    $pp->execute_payment();
    
    $cart = vhs_cart::get_cart();

    $fb = 'vhs';

    $next_page = 'thankyou_page';


    vhs_checkout::create_xml_registration($cart, rex_session('user_data', 'array'), rex_session('value_pool','array'), 'selbst');
    $vhs_response = new vhs_response();
    $vhs_response->send_response_mail($fb);
    $cart['kurs'] = [];
    rex_set_session('vhs_cart',$cart);
    rex_redirect(rex_config::get('vhs',$next_page));

    
}
?>