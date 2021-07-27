<?php
$kurse = $this->kurse;
// $kurse = [];
$subcategories = $this->subcategories ?? [];
$maincat = $this->maincategory ?? false;
$all_cat_ids = $this->all_cat_ids; // alle in dieser Liste vorkommenden Kategorie Ids
$title = $this->title ?? 'Unser Kursangebot auf einen Blick';
$with_hero = $this->with_hero ?? true;


$main_cat_title = '';
if ($maincat) {
    if ($this->category_title) {
        $main_cat_title = $this->category_title;
    } else {
        $main_cat_title = $maincat->name;
    }
}


?>

<?php if ($with_hero) : ?>
    <div class="ce-hero uk-background-scheme uk-light uk-section uk-section-small no-search">
        <div class="uk-container">
            <h1 class="uk-h1"><?= $main_cat_title ?></h1>
        </div>
    </div>
<?php endif ?>

<section class="uk-margin-top uk-margin-bottom-large">
    <div class="uk-container uk-container-large">
        <div data-uk-filter="target: .js-filter">
            <h2 class="uk-h3 uk-margin-bottom"><?= $title ?></h2>
            <div class="uk-margin-medium-bottom">
                <div class="uk-grid-small uk-flex-middle" data-uk-grid>
                    <div class="uk-width-expand">
                        <div class="uk-grid-small uk-flex" data-uk-grid>
                            <div class="uk-width-auto uk-text-right uk-text-bold uk-text-secondary uk-flex-last">
                                <div class="uk-margin-tiny-top"> <span class="count"><?= count($kurse) ?></span>&nbsp;Kurse</div>
                            </div>
                            <div class="uk-width-expand uk-width-1-2 uk-width-1-1@m uk-width-2-3@l">
                                <?php if ($subcategories) : ?>
                                    <ul class="uk-subnav uk-subnav-pill uk-visible@m" data-uk-margin>
                                        <li class="uk-active" data-uk-filter-control><a href="#">Alle</a></li>
                                        <?php foreach ($subcategories as $item) : ?>
                                            <?php if (in_array($item->id, $all_cat_ids)) : ?>
                                                <li data-uk-filter-control="[data-cat*='<?= vhs::search_id($item->id) ?>']"><a href="#"><?= $item->name ?></a></li>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                    </ul>
                                <?php endif ?>
                                <div class="uk-hidden@m">
                                    <div class="uk-inline">
                                        <button class="uk-button uk-button-default button-dropdown-filter" type="button">Filtern</button>
                                        <div data-uk-dropdown id="filter_dd">
                                            <ul class="uk-nav uk-width-medium uk-dropdown-nav">
                                                <li class="uk-active" data-uk-filter-control><a href="#filter_dd" data-uk-toggle>Alle</a></li>
                                                <?php foreach ($subcategories as $item) : ?>
                                                    <?php if (in_array($item->id, $all_cat_ids)) : ?>
                                                        <li data-uk-filter-control="[data-cat*='<?= vhs::search_id($item->id) ?>']"><a href="#filter_dd" data-uk-toggle><?= $item->name ?></a></li>
                                                    <?php endif ?>
                                                <?php endforeach ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <?php if (!count($kurse)) : ?>
                <div class="">
                    {{ <?= $this->ist_suche ? 'kursanzeige_suche_ohne_treffer ' : 'kursanzeige_leer' ?> }}
                </div>

                <div class="article-nav uk-margin-large-top ">

                    <div class="uk-container">
                        <a href="javascript:history.back();" class="backlink"><svg class="svg-icon">
                                <use xlink:href="#icon-arrow-back"></use>
                            </svg>Zurück zur vorherigen Ansicht</a>
                    </div>
                </div>
            <?php else : ?>
                <ul class="js-filter uk-child-width-1-2@s uk-child-width-1-3@l uk-child-width-1-4@xl" data-uk-grid="" data-uk-height-match=".uk-card" data-uk-scrollspy="cls: uk-animation-slide-bottom-medium; target: > li; delay: 100;">
                    <?php foreach ($kurse as $item) : ?>
                        <?php $_maincat = $maincat ?: $item->main_cat() ?>
                        <li data-cat="<?= $item->search_ids() ?>">
                            <div class="uk-position-relative">
                                <a href="<?= rex_getUrl('', '', [strtolower($this->category_key) . '_kurs_id' => $item->id]) ?>" class="uk-card uk-card-secondary uk-card-hover card--kurs uk-link-toggle">
                                    <div class="uk-card-header">
                                        <div class="uk-grid-small" data-uk-grid data-uk-height-match="">
                                            <div class="uk-width-auto uk-flex-center">
                                                <svg class="svg-icon icon--kurs">
                                                    <use xlink:href="#icon-<?= $_maincat->icon ?? '' ?>"></use>
                                                </svg>
                                            </div>
                                            <div class="uk-width-expand uk-flex uk-flex-middle">
                                                <div class="pretitle uk-margin-remove"><?= $_maincat->name ?? '' ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-card-body">
                                        <div class="uk-card-title"><?= $item->title ?></div>
                                        <div class="card-text">
                                            <div><?= $item->zeitraum() ?></div>
                                            <div>Kosten: <?= $item->preis() ?></div>
                                        </div>
                                        <?= $item->get_ampel_text() ?>

                                    </div>
                                    <div class="uk-card-footer">
                                        <div class="uk-link-text  uk-button uk-button-text">Details</div>
                                    </div>

                                </a>
                                <?php if ($item->bookable()) : ?>
                                    <a href="<?= vhs::add_to_cart_url('', $item->id) ?>" class="uk-position-bottom-right uk-button uk-button-primary no-text" data-uk-tooltip="title: Buchen; offset: 25;delay: 100">
                                        <svg class="svg-icon icon--buchen icon--color-wbz">
                                            <use xlink:href="#icon-buchen"></use>
                                        </svg>
                                    </a>
                                <?php else : ?>
                                    <a href="#" class="uk-position-bottom-right uk-button uk-button-primary no-text" disabled>
                                        <svg class="svg-icon icon--buchen icon--color-wbz">
                                            <use xlink:href="#icon-buchen"></use>
                                        </svg>
                                    </a>
                                <?php endif ?>
                            </div>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
        </div>
    </div>
</section>