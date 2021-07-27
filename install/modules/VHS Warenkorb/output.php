<?php
if (rex::isBackend()) {
    echo rex_view::info('Warenkorb (Ausgabe nur im Frontend)');
    return;
}

/*
    $cart = vhs_cart::get_cart();
    $fragment = new rex_fragment();
    $fragment->setVar('cart',$cart);
    $fragment->setVar('mode','modul');
    $fragment->parse('vhs_cart.php');
    */

$yform = new vhs_participants_form();
$form = $yform->get_form();

$cart = vhs_cart::get_cart();
$kurs = current($cart);

$fragment = new rex_fragment();
$fragment->setVar('kurs_id', $kurs['id']);
$teaser = $fragment->parse('vhs_cart_kursteaser.php');

?>

<div class="ce-table uk-margin-large-top uk-margin-large-bottom">
    <div class="uk-container uk-container-small">
        <?= $teaser ?>

        <section class="ce-headline uk-margin-large-top">
            <div class="pretitle uk-margin-remove">Schritt 2 von 3</div>
            <h1 class="uk-h1 uk-margin-small-top">Teilnehmer:innen</h1>
        </section>


        <?php if ($cart) : ?>
            <?= $form->getForm(); ?>
        <?php else : ?>
            <h2>Der Warenkorb ist leer.</h2>
        <?php endif ?>
    </div>
</div>





<?php /*

<div class="ce-table uk-margin-large-top uk-margin-large-bottom">

    <div class="uk-container uk-container-large">
        <section class="ce-headline">
            <div class="pretitle uk-margin-remove">Schritt 2 von 3</div>
            <h1 class="uk-h1 uk-margin-small-top">Teilnehmer ergänzen</h1>
        </section>
    </div>

    <div class="uk-container uk-container-small">

        <?php if (rex_session('vhs_cart', 'array')) : ?>
            <?php // $fragment->parse('vhs_cart.php'); 
            ?>
            <?= $form->getForm() ?>



            <?php // dump(rex_session('vhs_cart','array')) 
            ?>
            <p><a href="<?= rex_getUrl(rex_config::get('vhs', 'address_page')) ?>">Zur Adresseingabe</a></p>
        <?php else : ?>
            <h2>Der Warenkorb ist leer.</h2>
        <?php endif ?>
    </div>
</div>
*/