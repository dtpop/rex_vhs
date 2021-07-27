<?php

class vhs_cart
{

    static $fields = [
//        'participants' => []
    ];

    public static function get_cart()
    {
        $cart = rex_session('vhs_cart', 'array');
        foreach ($cart as $key => $item) {
            foreach (self::$fields as $k => $v) {
                if (!isset($item[$k])) {
                    $cart[$key][$k] = $v;
                }
            }
        }
        return $cart;
    }


    public static function get_paypal_client_id()
    {
        if (rex_config::get('vhs', 'sandboxmode')) {
            return rex_config::get('vhs', 'paypal_sandbox_client_id');
        }
        return rex_config::get('vhs', 'paypal_client_id');
    }

    public static function get_paypal_secret()
    {
        if (rex_config::get('vhs', 'sandboxmode')) {
            return rex_config::get('vhs', 'paypal_sandbox_secret');
        }
        return rex_config::get('vhs', 'paypal_secret');
    }


    public static function add_to_cart()
    {
        $next_page = 'address_page';

        $kurs_id = (rex_request('kurs_id'));
        $kurs = vhs::get_kurs_by_id($kurs_id);
        if ($kurs) {
            $cart = rex_session('vhs_cart', 'array', []);        
            $kurs_data = $kurs->getData();
            $kurs_data['zeitraum'] = $kurs->zeitraum();
            $cart['kurs'] = $kurs_data;

            rex_set_session('vhs_cart', $cart);
            rex_redirect(rex_config::get('vhs', $next_page));
        }
    }

    /**
     * Wird aus Custom Function aus dem yform Formular aufgerufen
     * 
     * 
     *
     array:1 [▼
    "AV15202" => array:2 [▼
        "firstname" => array:2 [▼
            0 => "asfasfsd1"
            1 => "safasdf2"
        ]
        "lastname" => array:2 [▼
            0 => "sadafd1"
            1 => "fdsdgsdfg2"
        ]
    ]
] 
     * 
     * 
     */
    public static function add_participants($params)
    {
        $cart = vhs_cart::get_cart();
        $participants = rex_request('part','array');
        $cart['participants'] = $participants;

        rex_set_session('vhs_cart', $cart);
    }

    /**
     * Total (Warenkorb mit Shipping)
     * @return type
     */
    public static function get_cart_total()
    {
        $cart = self::get_cart();
        $price_total = 0;
        foreach ($cart as $item) {
            $price_total += $item['preis'] * count($item['participants']);
        }
        return $price_total;
    }


    public static function clear_sessions()
    {
        rex_set_session('vhs_cart', []);
        rex_set_session('user_data', []);
    }
}
