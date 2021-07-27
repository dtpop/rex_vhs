<?php
$cart = rex_session('vhs_cart','array');
$participant = $cart['participants'][0];
$kurs = $cart['kurs'];
// echo var_export($cart,true);
?>
Eingangsbestätigung


Sehr geehrte<?= $participant['geschlecht'] == 'm' ? 'r Herr' : ' Frau' ?> <?= $participant['lastname'] ?>,

vielen Dank für Ihre Anmeldung vom <?= date('d.m.Y, h:i') ?> Uhr. 

Ihre Buchung für folgende Veranstaltungen wurde empfangen:

Kursnummer: <?= $kurs['nummer'] ?> Kurstitel: <?= $kurs['title'] ?>


Anzahl der teilnehmenden Personen: <?= count($cart['participants']) ?>


Widerrufsrecht: Sie können Ihre Vertragserklärung innerhalb von 14 Tagen ohne Angabe von Gründen in Textform (z. B. Brief, Fax, EMail) widerrufen. Die Frist beginnt nach Erhalt dieser Belehrung in Textform, jedoch nicht vor Vertragsschluss und auch nicht vor Erfüllung unserer Informationspflichten gemäß Art. 246 § 2 i.V.m. § 1 Abs. 1 und 2 EGBGB sowie unserer Pflichten gemäß § 312e Abs. 1 Satz 1 BGB i.V.m. Art. 246 § 3 EGBGB. Zur Wahrung der Widerrufsfrist genügt die rechtzeitige Absendung des Widerrufs. Der Widerruf ist zu richten an: Volkshochschule Mustermann PLZ Ort Anschrift E-Mail Tel Fax. Gemäß § 312b Abs. 3 Nr. 6 BGB gilt das Widerrufsrecht nicht bei der Buchung von Reisen oder Exkursionsseminaren. Widerrufsfolgen: Im Falle eines wirksamen Widerrufs sind die beiderseits empfangenen Leistungen zurück zu gewähren und ggf. gezogene Nutzungen (z.B. Zinsen) herauszugeben. Können Sie uns die empfangene Leistung ganz oder teilweise nicht oder nur in verschlechtertem Zustand zurückgewähren, müssen Sie uns insoweit ggf. Wertersatz leisten. Dies kann dazu führen, dass Sie die vertraglichen Zahlungsverpflichtungen für den Zeitraum bis zum Widerruf gleichwohl erfüllen müssen. Verpflichtungen zur Erstattung von Zahlungen müssen innerhalb von 30 Tagen erfüllt werden. Die Frist beginnt für Sie mit der Absendung Ihrer Widerrufserklärung, für uns mit deren Empfang. Besondere Hinweise: Ihr Widerrufsrecht erlischt vorzeitig, wenn der Vertrag von beiden Seiten auf Ihren ausdrücklichen Wunsch vollständig erfüllt ist, bevor Sie Ihr Widerrufsrecht ausgeübt haben. 

Wenn Sie Verbraucher sind (also eine natürliche Person, die die Bestellung zu einem Zweck abgibt, der weder Ihrer gewerblichen oder selbständigen beruflichen Tätigkeit zugerechnet werden kann), steht Ihnen nach Maßgabe der gesetzlichen Bestimmungen ein Widerrufsrecht zu. Sie haben das Recht, binnen vierzehn Tagen ohne Angabe von Gründen einen mit dem Weiterbildungszentrum Mustermann geschlossenen Vertrag zu widerrufen. Die Widerrufsfrist beträgt vierzehn Tage ab dem Tag des Vertragsschlusses. Um Ihr Widerrufsrecht auszuüben, müssen Sie uns [Weiterbildungszentrum Mustermann, PLZ Ort Anschrift Tel E-Mail] mittels einer eindeutigen Erklärung (z. B. ein mit der Post versandter Brief, Telefax oder E-Mail) über Ihren Entschluss, diesen Vertrag zu widerrufen, informieren. Sie können dafür das beigefügte Muster-Widerrufsformular verwenden, das jedoch nicht vorgeschrieben ist. Sie können das Muster-Widerrufsformular oder eine andere eindeutige Erklärung auch auf unserer Website [www.weiterbildungszentrum-mustermann.xyz] elektronisch ausfüllen und übermitteln. Machen Sie von dieser Möglichkeit Gebrauch, so werden wir Ihnen unverzüglich (z. B. per E-Mail) eine Bestätigung über den Eingang eines solchen Widerrufs übermitteln. Zur Wahrung der Widerrufsfrist reicht es aus, dass Sie die Mitteilung über die Ausübung des Widerrufsrechts vor Ablauf der Widerrufsfrist absenden. Gemäß § 312b Absatz 3 Nr. 6 BGB gilt das Widerrufsrecht nicht bei der Buchung von Reisen oder Exkursionen.
Das Weiterbildungszentrum Mustermann


Sobald Ihre Anmeldung eingebucht ist, erhalten Sie eine Anmeldebestätigung.


Sollte ein Irrtum bei der aufgeführten Buchung vorliegen, bzw. Sie diese Kursbuchung nicht vorgenommen haben, bitten wir Sie umgehend um eine kurze Rückantwort an die E-Mail-Adresse vhs@weiterbildungszentrum-mustermann.xyz.

Freundliche Grüße

Ihr Weiterbildungszentrum

Anschrift
Plz Ort

E-Mail
Telefon
