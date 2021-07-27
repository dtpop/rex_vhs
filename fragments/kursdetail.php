<?php
$kurs = $this->kurs;

// dump($kurs);
// dump($kurs->weitere_dozenten());
if (!$kurs) {
    return false;
}

$category_key = $this->category_key; // vhs
$kurs_json = json_decode($kurs->kurs_json, true);
$freie_plaetze = $kurs->teilnehmer_max - $kurs->teilnehmer_angemeldet;

$main_cat =  $kurs->main_cat();


?>
<section class="ce-headline uk-margin-large-top uk-margin-large-bottom">
    <div class="uk-container">
        <div class="uk-position-relative">
            <div class="uk-width-2-3@s">
                <div class="uk-grid" data-uk-grid data-uk-height-match="">
                    <div class="uk-width-auto uk-flex-center">
                        <svg class="svg-icon icon--kurs icon--large">
                            <use xlink:href="#icon-<?= $main_cat->icon ?? '' ?>"></use>
                        </svg>
                    </div>
                    <div class="uk-width-expand uk-flex uk-flex-middle">
                        <div class="pretitle uk-margin-remove"><?= $main_cat->name ?? '' ?></div>
                    </div>
                </div>
                <h1 class="uk-h1 uk-margin-small-top"><?= $kurs->title ?></h1>
                <p class="uk-margin-top uk-text-lead"><?= $kurs->zeitraum() ?><br>
                    <?= $kurs->get_ampel_text('Status: ','i') ?></p>
                    
                    
                    <?php /*
                    <?= $freie_plaetze ?> <?= $freie_plaetze == 1 ? 'Platz' :  'Plätze' ?> frei</p>
                    */ ?>
            </div>
            <?php if ($kurs->bookable()) : ?>
                <a href="<?= vhs::add_to_cart_url() ?>" class="cart-button-top uk-button uk-button-primary uk-button-large">
                    <svg class="svg-icon icon--buchen icon--color-wbz">
                        <use xlink:href="#icon-buchen"></use>
                    </svg><span>Kurs buchen</span>
                </a>
            <?php endif ?>
        </div>
    </div>
</section>


<div class="ce-table uk-margin-large-top uk-margin-large-bottom">
    <div class="uk-container uk-container-small">
        <table class="uk-table uk-table-divider uk-table-justify uk-table-responsive">
            <tr>
                <th class="uk-width-1-4@s">Kurszeit/Termine</th>
                <?php if (isset($kurs_json['termine']['termin']) && $termin = vhs::get_date_identical($kurs_json['termine']['termin'])) : ?>
                    <td><?= $termin ?></td>
                <?php else : ?>
                    <td>siehe Beschreibung</td>
                <?php endif ?>
            </tr>
            <tr>
                <th>Dauer</th>
                <td><?= $kurs->dauer ?> Termin<?= $kurs->dauer > 1 ? 'e' : '' ?></td>
            </tr>
            <tr>
                <th>Ort</th>
                <td><?= $kurs->ort_name ?></td>
            </tr>
            <tr>
                <th>Kosten</th>
                <td><?= $kurs->preis() ?><?= isset($kurs_json['nachspann']) && $kurs_json['nachspann'] ? '*' : '' ?></td>
            </tr>
            <?php if ($kurs->preisreduziert != $kurs->preis) : ?>
                <tr>
                    <th>Reduziert</th>
                    <td><?= $kurs->preisreduziert() ?></td>
                </tr>
            <?php endif ?>
            <tr>
                <th>Teilnehmer</th>
                <td>
                    <?php if ($kurs->teilnehmer_min == $kurs->teilnehmer_max) : ?>
                        <?= $kurs->teilnehmer_min ?>
                    <?php else : ?>
                        <?= $kurs->teilnehmer_min ?> – <?= $kurs->teilnehmer_max ?>
                    <?php endif ?>
                </td>
            </tr>
            <?php if ($kurs->nummer) : ?>
            <tr>
                <th>Kursnummer</th>
                <td>
                        <?= $kurs->nummer ?>
                </td>
            </tr>
            <?php endif;?>
            <?php if ($kurs->hauptdozent) : ?>
                <tr>
                    <th>Dozent*in</th>
                    <td>
                        <a href="<?= rex_getUrl('','',[strtolower($category_key).'_dozent_id'=>$kurs->hauptdozent_id()]) ?>" class="uk-button uk-button-text"><?= $kurs->hauptdozent() ?></a>
                            <?php foreach ($kurs->weitere_dozenten() as $doz) : ?>
                                <br>
                                <a href="<?= rex_getUrl('','',[strtolower($category_key).'_dozent_id'=>$doz->id]) ?>" class="uk-button uk-button-text"><?= trim($doz->titel . ' ' . $doz->vorname.' '.$doz->nachname) ?></a>
                            <?php endforeach ?>
                    </td>
                </tr>
            <?php endif ?>
        </table>
    </div>
</div>

<div class="ce-text uk-margin-large-top uk-margin-large-bottom">
    <div class="uk-container uk-container-small">
        <div class="copytext">
            <h2 class="uk-h3">
                Kursbeschreibung
            </h2>
            <?php if (isset($kurs_json['vorspann']) && $kurs_json['vorspann']) : ?>
                <p><strong><?= nl2br($kurs_json['vorspann']) ?></strong></p>
            <?php endif ?>
            <p><?php // preg_replace('/\R/', '<br>', $kurs->text) ?></p>
            <p><?= nl2br($kurs_json['web_info'] ?? $kurs->text) ?></p>

            <?php if (isset($kurs_json['material']) && $kurs_json['material']) : ?>
                <h3 class="uk-h4">{{ Mitzubringen/Materialien }}</h3>
                <p class="uk-margin-large-bottom">* <?= nl2br($kurs_json['material']) ?></p>
            <?php endif ?>
            <?php if (isset($kurs_json['nachspann']) && $kurs_json['nachspann']) : ?>
                <p>* <?= nl2br($kurs_json['nachspann']) ?></p>
            <?php endif ?>

        </div>
        <?php if ($kurs->bookable()) : ?>
            <div class="uk-margin-top">
                <a href="<?= vhs::add_to_cart_url() ?>" class="uk-button uk-button-primary uk-button-large">
                    <svg class="svg-icon icon--buchen icon--color-wbz">
                        <use xlink:href="#icon-buchen"></use>
                    </svg>
                    <span>Kurs buchen</span>
                </a>
            </div>
        <?php endif ?>
    </div>
</div>

<div class="ce-text uk-margin-large-top uk-margin-large-bottom">
    <div class="uk-container uk-container-small">
        <div class="copytext">
            <h2 class="uk-h3">
                Fragen zum Kurs?
            </h2>
            <p><?= $this->contact_text;?></p>
        </div>
        <div class="uk-margin-top">
            <a href="mailto:<?= $this->contact_email;?>" class="uk-button uk-button-default">
                <svg class="svg-icon icon--mailto icon--color-wbz">
                    <use xlink:href="#icon-mailto"></use>
                </svg>
                <span><?= $this->contact_email;?></span></a>
        </div>
    </div>
</div>

<?php
// dump(rex_session('vhs_cart'));
// dump($kurs->getData());
//    dump($kurs->getData());
//    dump($kurs_json);

?>