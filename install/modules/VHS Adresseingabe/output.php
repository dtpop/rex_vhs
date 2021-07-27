<?php
if (rex::isBackend()) {
    echo rex_view::info('Adresseingabe Formular (Ausgabe nur im Frontend)');
    return;
}
$checkout = new vhs_checkout();

$cart = vhs_cart::get_cart();
// dump($cart);

if ($cart) {
    $kurs = current($cart);
    $yform = $checkout->address_form();
    $fragment = new rex_fragment();
    $fragment->setVar('kurs_id', $kurs['id']);
    $teaser = $fragment->parse('vhs_cart_kursteaser.php');
}

?>

<div class="ce-table uk-margin-large-top uk-margin-large-bottom">
    <div class="uk-container uk-container-small">
        <?php if (!$cart) : ?>
            <h2>Der Warenkorb ist leer.</h2>
        <?php else : ?>
            <?= $teaser ?>
            <section class="ce-headline uk-margin-large-top">
                <div class="pretitle uk-margin-remove">Schritt 1 von 2</div>
                <h1 class="uk-h1 uk-margin-small-top">Kontaktdaten Teilnehmer:in</h1>
                <p>mit * gekennzeichnete Felder müssen ausgefüllt sein.</p>
            </section>
            <?= $yform->getForm(); ?>
        <?php endif ?>
    </div>
</div>