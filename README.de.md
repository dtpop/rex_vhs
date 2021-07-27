REDAXO-AddOn: vhs
=====================

Das AddOn vhs dient dazu, xml Dateien mit Kursinformationen aus Kufer VHS in YForm Tabellen zu importieren.

Das AddOn bietet die Möglichkeit Kurse auf REDAXO Seiten anzuzeigen und zu buchen. Es wird eine XML Buchungsdatei erstellt, die von der Kufer Verwaltungssoftware importiert werden kann.

Der Autor des AddOns steht in keiner Verbindung zu Firma Kufer.

Das AddOn kommt gänzlich ohne Templates, Styles und Javascript und auch weitestgehend ohen Frontend Funktionalität. Das hat natürlich auch einen Grund. Die Installation, für die dieses AddOn programmiert wurde, ist für den speziellen Anwendungsfall programmiert und konfiguriert, mit Javascript, Frontend und allem umgesetzt und verzahnt. Diese Installation kann ich aus gutem Grund nicht zur Verfügung stellen. So habe ich mich ein paar Stunden dran gesetzt, zumindest die rudimentäre Funktion für die Allgemeinheit verfügbar zu machen. Diese Funktion ist auch rudimentär bis zur Kursanzeige getestet. Mehr aber auch nicht! Die Fragmente und der Frontend Ausgabecode ist für das Framework Ui-Kit geschrieben. Andere Entwickler benutzen andere Frameworks. In diesem Falle können die Fragmente selbst umgeschrieben werden. Mir ist dieser Hinweis sehr wichtig, denn gelegentlich kommt von unbedarften Anwendern der Kommentar "das funktioniert ja gar nicht!". In diesem Falle hilft: selbst Hand anlegen. Oder fragt einen Entwickler, der euch diese Arbeit abnimmt.


Installation
------------

Für eine erste Installation wird erstmal REDAXO mindestens mit den AddOns yform und yrewrite aufgesetzt. Dann kann schon das vhs AddOn installiert werden. Dabei werden die benötigten Tabellen bereits angelegt.

Als nächstes müssen in den Settings ein paar Einstellungen gemacht werden. Mindestens die Quelle zur xml Datei muss eingetragen werden und eine Felddefinition nach dem unten stehenden Schema. Die Liste auch einfach kopiert werden.

Dann kann schon der Import gestartet werden.

Wenn der Import durch ist, muss ein Artikel mit dem Modul VHS Kurse Übersicht nach Fachbereich angelegt werden. Dort muss ein Fachbereich ausgewählt werden.

Anschließend werden Kurse angezeigt.

Die gesamt Programmierung ist für die Verwendung des url AddOns vorbereitet. Die Kursanzeige funktioniert aber auch ohne url AddOn.


Settings
--------

Feldnamen können assoziiert werden. Somit kann die Datenbank um Felder erweitert werden.


Verknüpfte Dateien
------------------

Für Kategorien, Dozenten, Orte, Zielgruppen und Außenstellen wird versucht, die jeweiligen Daten aus der XML Datei zu extrahieren und in die entsprechenden verknüpften Tabellen zu schreiben. Bei Dozenten und Orten wird die Original Id verwendet. Bei den Kategorien wird der Schlüssel (xml-Wert fachb) verwendet. Die Verknüpfung findet dann über die Datensatz Id statt. Die Tabellen können danach editiert und erweitert werden.


Kurse
-----

Bei den Kursen werden die Teilnehmerzahlen und einige andere Daten bei einem neuen Import übernommen. Bilder bleiben erhalten. Es werden nur jene Felder überschrieben, die in der Konfiguration definiert wurden.

Beispieldaten für die Felder:

```
title::titelkurz
semester::semester
nummer::knr
maincategory::fachb
text::titellang::::protected
additinoal_categories
datestart::beginndat::date(Y-m-d)
dateend::endedat::date(Y-m-d)
uhrzeit_beginn::beginnuhr
uhrzeit_ende::endeuhr
dauer::dauer
ort_name::ort
ort_id::ortid
preis::gebnorm
preisreduziert::geberm
image
teilnehmer_angemeldet::tnanmeldungen
teilnehmer_min::tnmin
teilnehmer_max::tnmax
teilnehmer_warteliste::tnwarteliste
zielgruppe_id
raum_nr::ortraumnr
raum_name::ortraumname
kurs_json
status
quatschfeld::blabla
```

Zuerst steht der Feldname des Datenbankfeldes. Nach zwei Doppelpunkten steht das Tag des xml Feldes. Als drittes Element kann optional eine Formatinfo `date(Y-m-d)` mitgegeben werden. Dadurch wird der Feldinhalt umformatiert. Beim Import wird geprüft, ob das angegebene Datenfeld auch tatsächlich exisitiert. `quatschfeld` zum Beispiel wird nicht importiert.

Wenn als vierter Parameter `protected` angegeben wird, so wird das Feld nur bei einem ersten Import befüllt. Wenn der Kurs bereits vorhanden ist, wird der Wert in der Tabelle nicht mehr geändert. So kann beispielsweise ein Beschreibungsfeld im CMS redaktionelle gepflegt werden. Es wird nicht mehr mit den Inhalten aus der Verwaltungssoftware überschrieben.

Die gesamten Kursdaten werden als JSON in das Feld kurs_json geschrieben. Wenn für den Zugriff (z.B. für die Filterung oder Suche) ein Wert als Feld in der Datenbank zur Verfügung stehen soll, so genügt es, das neue Feld in yform anzulegen und den Feldnamen in der Konfiguration einzutragen. Nach dem nächsten Import stehen die Daten dann zur Verfügung.


Automatischer Import
--------------------

Für den automatischen Import der Kufer xml Datei kann ein Cronjob eingerichtet werden.

Es wird empfohlen die Import über einen echten Cronjob zu starten. Damit ist gewährleistet, dass der Import auch unabhängig von einem Seitenaufruf im Frontend oder im Backend läuft.

Der Aufruf muss lauten: php redaxo/bin/console vhs:import
