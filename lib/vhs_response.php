<?php
class vhs_response {

    public function send_response_mail ($fb) {

        $etpl = rex_config::get('vhs', 'email_template_customer');

        $value_pool = rex_session('value_pool','array');
        $cart = vhs_cart::get_cart(); // kurs, participants
        $participants = $cart['participants'];
        $kurs = $cart['kurs'];
        $user_data = vhs_checkout::get_user_data(); // firma ...
        if (!$value_pool) {
            return false;
        }
        if (!$cart) {
            return false;
        }
        if (!$participants) {
            return false;
        }
        if (!$kurs) {
            return false;
        }
        if (!$user_data) {
            return false;
        }


        $yform = new rex_yform();
        $fragment = new rex_fragment();
        $fragment->setVar('cart', $cart);
//        $fragment->setVar('wh_userdata', $wh_userdata);
        
        $yform->setObjectparams('csrf_protection',false);
        $yform->setValueField('hidden', ['email', $participants[0]['email']]);
        $yform->setValueField('hidden', ['firstname', $participants[0]['firstname']]);
        $yform->setValueField('hidden', ['lastname', $participants[0]['lastname']]);
        $yform->setValueField('hidden', ['payment_type', $user_data['payment_type'] ?? '']);
        $yform->setValueField('hidden', ['kurs_title', $kurs['title']]);
        $yform->setValueField('hidden', ['kurs_zeitraum', $kurs['zeitraum']]);
        
        foreach (explode(',', rex_config::get('vhs', 'order_email')) as $email) {
//            $yform->setActionField('tpl2email', [rex_config::get('vhs', 'email_template_seller'), '', $email]);
        }
        $yform->setActionField('tpl2email', [$etpl, 'email']);
//        $yform->setActionField('callback', ['warehouse::clear_cart']);
        
        $yform->getForm();
        $yform->setObjectparams('send',1);
    
    }



}