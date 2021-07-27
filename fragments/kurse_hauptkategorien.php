<?php
$categories = (array) $this->categories;
$link_id = $this->link_id ?: 0;
// dump($categories);
?>
<section class="ce-teaser-themen  uk-margin-xlarge-top uk-margin-xlarge-bottom no-search">
    <div class="uk-container">
        <div class="section-headline">
            <h2 class="uk-h2">Kursauswahl nach Themenbereich</h2>
        </div>
        <div class="uk-position-relative">
            <div class="uk-child-width-1-2@s uk-child-width-1-4@l grid-margin-small@xsmall" data-uk-grid data-uk-height-match="target: > div > .uk-card .uk-card-body">
                <?php foreach ($categories as $item) : ?>
                    <?php if (!is_object($item)) continue ?>
                    <div>
                        <div class="uk-card uk-card-primary uk-card-hover card--themen">
                            <a href="<?= rex_getUrl($link_id,'',['main_cat_id'=>$item->id]) ?>" class="uk-link-toggle" alt="siehe <?= $item->name ?>">
                                <div class="uk-grid uk-grid-collapse" data-uk-grid>
                                    <div class="uk-width-1-3 uk-width-1-1@s">
                                        <div class="uk-card-media-top uk-position-relative">
                                            <canvas width="620" height="288" title=""></canvas>
                                            <svg class="svg-icon icon--teaser icon--color-wbz" alt="<?= $item->icon ?>">
                                                <title id="uniqueTitleID"><?= $item->icon ?></title>
                                                <use xlink:href="#icon-<?= $item->icon ?>"></use>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="uk-width-2-3 uk-width-1-1@s uk-flex uk-flex-middle uk-flex-center@s">
                                        <div class="uk-card-body">
                                            <div class="uk-card-title"><?= $item->name ?></div>
                                        </div>
                                    </div>
                                </div>

                            </a>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</section>