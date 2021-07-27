<?php
$user_data = $this->userdata;
?>

<h2>{{ Bestellübersicht }}</h2>
<table class="uk-table uk-table-striped uk-width-1-1 uk-table-small" id="table_order_summary">
    <thead>
        <tr>
            <th></th>
            <th class="uk-text-right"><?= rex_config::get('warehouse', 'currency') ?></th>
        </tr>
    </thead>
    <?php foreach ($this->cart as $item) : ?>
        <tr>
            <td>
                <p><?= trim(html_entity_decode($item['title'])) ?><br>
                <?= $item['zeitraum'] ?><br>
                Preis: <?= $item['preis'] ?> € pro Teilnehmer/in
                </p>
                <p>Teilnehmer:<br>
                <?php foreach ($item['participants'] as $participant) : ?>
                    <?= $participant['firstname'] . ' ' . $participant['lastname'] . '<br>' ?>
                <?php endforeach ?>
                </p>
                
                
                <?php /* $item['count'] ?> x à <?= number_format($item['preis'], 2)  */ ?>
            </td>
            <td class="uk-text-right"><?php // number_format($item['total'], 2) ?></td>
        </tr>
    <?php endforeach ?>
    <tr>
        <td>{{ Total }}</td>
        <td class="uk-text-right"><?= number_format(vhs_cart::get_cart_total(), 2,',','.') ?></td>
    </tr>
</table>

<p>{{ Rechnungsadresse }}:</p>
<p>
    <?= $user_data['firstname'] . ' ' . $user_data['lastname'] ?><br>
    <?= $user_data['address'] ?><br>
    <?= $user_data['zip'] . ' ' . $user_data['city'] ?><br>
    <?= $user_data['country'] ?>
</p>

<p>{{ Payment Type }}: {{ payment_<?= $user_data['payment_type'] ?> }}</p>

