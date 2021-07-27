<?php
$cart = $this->cart;
$showcart = $this->mode == 'template' && rex_request('showcart','int') ? 1 : 0;
$rex_article_id = rex_article::getCurrentId();
?>

<?php if (!$cart) : ?>
<p>{{ Der Warenkorb ist leer }}</p>
<?php else : ?>

<table class="wh_cart_table">
<?php foreach ($cart as $k=>$item) : ?>
    <tr>
        <td class="align-left"><?= html_entity_decode($item['title']) ?></td>
        <td class="align-right"><?= rex_config::get('vhs','currency') ?> <?= number_format($item['preis'],2) ?></td>
        <td>
            <a href="/?current_article=<?= $rex_article_id ?>&showcart=<?= $showcart ?>&action=modify_cart&art_uid=<?= $k ?>&mod=del" class="circle plus white cross">{{ delete }}</a>
        </td>
    </tr>
<?php endforeach; ?>
    <tr class="bigtext"><td class="align-left">{{ Total }}</td><td></td><td></td><td class="align-right"><?= rex_config::get('warehouse','currency') ?> <?= number_format(vhs_cart::get_cart_total(),2) ?></td><td></td></tr>
</table>

<p><a href="<?= rex_getUrl(rex_config::get('vhs','address_page')) ?>" class="white_big_circle">Weiter</a></p>
<?php endif ?>