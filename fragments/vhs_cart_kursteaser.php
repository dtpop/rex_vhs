<?php
$item = vhs::get_kurs_by_id($this->kurs_id);
if (!$item) {
    return;
}


$current_path = explode('|', trim(rex_article::getCurrent()->getPath(),'|'));

// dump($current_path);

$art_id = 60;
$params = ['vhs_kurs_id' => $item->id];

?>

<div class="uk-grid uk-child-width-1-1@s" data-uk-grid="">

    <?php $maincat = $item->main_cat(); ?>
    <div data-cat="<?= $item->search_ids() ?>">
        <div class="uk-position-relative">
            <a href="<?= rex_getUrl($art_id, '', $params) ?>" class="uk-padding-small uk-padding-remove-horizontal uk-card uk-card-secondary uk-card-hover card--kurs uk-link-toggle" alt="zur Detailseite">
                <?php if (isset($maincat->icon) || isset($maincat->name)) : ?>
                <div class="uk-card-header">
                    <div class="uk-grid-small" data-uk-grid data-uk-height-match="">
                        <div class="uk-width-auto uk-flex-center">
                            <svg class="svg-icon icon--kurs">
                                <title id="uniqueTitleID"><?= $maincat->icon ?? '' ?></title>
                                <use xlink:href="#icon-<?= $maincat->icon ?? '' ?>"></use>
                            </svg>
                        </div>
                        <div class="uk-width-expand uk-flex uk-flex-middle">
                            <div class="pretitle uk-margin-remove"><?= $maincat->name ?? '' ?></div>
                        </div>
                    </div>
                </div>
                <?php endif;?>
                <div class="uk-card-body">
                    <div class="uk-card-title"><?= $item->title ?></div>
                    <div class="card-text uk-grid">
                        <div><?= $item->zeitraum() ?></div>
                        <div>Kosten: <?= $item->preis() ?></div>
                    </div>
                </div>
            </a>
        </div>
    </div>

</div>