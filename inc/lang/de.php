<?php

/* =================================================================================*\
  |* This file is part of InMaFSS                                                    *|
  |* InMaFSS - INformation MAnagement for School Systems - Keep yourself up to date! *|
  |* ############################################################################### *|
  |* Copyright (C) flx5                                                              *|
  |* E-Mail: me@flx5.com                                                             *|
  |* ############################################################################### *|
  |* InMaFSS is free software; you can redistribute it and/or modify                 *|
  |* it under the terms of the GNU Affero General Public License as published by     *|
  |* the Free Software Foundation; either version 3 of the License,                  *|
  |* or (at your option) any later version.                                          *|
  |* ############################################################################### *|
  |* InMaFSS is distributed in the hope that it will be useful,                      *|
  |* but WITHOUT ANY WARRANTY; without even the implied warranty of                  *|
  |* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                            *|
  |* See the GNU Affero General Public License for more details.                     *|
  |* ############################################################################### *|
  |* You should have received a copy of the GNU Affero General Public License        *|
  |* along with InMaFSS; if not, see http://www.gnu.org/licenses/.                   *|
  \*================================================================================= */


$info = Array();
$info['language'] = 'Deutsch';
$info['lang.short'] = 'de';
$info['date.format'] = 'd.m.Y';

$loc = Array();
switch ($key) {

    case 'install':
        $loc['title'] = 'Installer';
        $loc['next'] = 'Weiter';
        $loc['back'] = 'Zur&uumlck';
        $loc['dbtype'] = 'Datenbanktyp';
        $loc['dbhost'] = 'Host';
        $loc['database'] = 'Datenbank';
        $loc['username'] = 'Nutername';
        $loc['password'] = 'Passwort';
        $loc['config.saved'] = 'Konfigurationsdatei gespeichert';
        $loc['config.rights'] = 'Die Konfigurationsdatei konnte nicht gespeichert werden.<br>Bitte kopieren sie den folgenden Text in die Datei ./inc/config.php';
        $loc['db.settings'] = 'Datenbank Einstellungen';
        $loc['name.of.school'] = 'Name der Schule';
        $loc['db.connect.err'] = 'Datenbankserver nicht erreichbar';
        $loc['db.select.err'] = 'Konnte Datenbank nicht ausw&auml;hlen';
        $loc['db.set.up'] = 'Datenbank eingerichtet';
        $loc['welcome'] = 'Herzlich Willkommen bei MyVertretungsplan';
        $loc['firstpage'] = 'Dies ist ihre erste Seite. Sie k&ouml;nnen nun weitere Seiten anlegen, den Vertetungsplan importieren oder Ticker schreiben. <br><br> Sie k&ouml;nnen auch die API nutzen um den Vertretungsplan automatisch importieren zu lassen. <br><br><a href="./manage/">Zum Adminbereich</a>';
        $loc['create.admin'] = 'Es ist Zeit den Administrator Account zu erstellen.';
        $loc['acc.created'] = 'Das Account wurde erstellt.';
        $loc['finished'] = 'Die Installation ist abgeschlossen.';
        $loc['finish'] = 'Abschlie&szlig;en';
        $loc['license'] = 'Lizenz';
        $loc['accept'] = 'Akzeptieren';
        break;

    case 'home':
        $loc['title'] = 'Home';
        $loc['empty'] = 'Derzeit steht kein Inhalt zur Verf&uuml;gung!';
        $loc['page'] = 'Seite';
        $loc['last.update'] = 'Stand: %update% Uhr';
        $loc['teacher.short'] = 'Lkr.';
        $loc['lesson.short'] = 'Std.';
        $loc['room'] = 'Raum';
        $loc['grade'] = 'Klasse';
        $loc['replaced.by'] = 'vertreten durch';
        $loc['comment'] = 'Bemerkung';
        $loc['info'] = 'Info';
        $loc['absent.t'] = 'Abwesende Lehrkr&auml;fte';
        $loc['absent.g'] = 'Abwesende Klassen';
        $loc['supervision'] = 'Aufsichten';
        $loc['entire.school'] = 'Gesamte Schule';
        $loc['subs'] = 'Vertretungen';
        $loc['na.rooms'] = 'Nicht verf&uuml;gbare R&auml;ume';
        $loc['cache.warning'] = 'ACHTUNG: DIES IST EINE ZWISCHENGESPEICHERTE ANSICHT';
        break;

    case 'index':
        $loc['title'] = "Willkommen";
        break;

    case 'errors':
        $loc['too.small'] = 'FEHLER: Ihr Bildschirm ist zu klein!';
        $loc['no.js'] = 'FEHLER: Ihr Browser unterst&uuml;tzt kein JavaScript.';
        $loc['no.js.desc'] = 'Bitte aktivieren sie JavaScript, um diese Seite nutzen zu k&ouml;nnen.';
        $loc['no.cookies'] = 'FEHLER: Ihr Browser unterst&uuml;tzt keine Cookies.';
        $loc['no.cookies.desc'] = 'Bitte aktivieren sie Cookies, um diese Seite nutzen zu k&ouml;nnen.';
        break;

    case 'admin':
        $loc['title'] = 'Administrator';
        $loc['login'] = 'Login';
        $loc['username'] = 'Nutzername';
        $loc['password'] = 'Passwort';
        $loc['wrong'] = 'Der Nutzername/das Passwort stimmt nicht.';
        $loc['no.fuse'] = 'Du hast keine Berechtigung f&uuml;r diesen Bereich.';
        $loc['welcome'] = 'Willkommen, %username%';
        $loc['back.to.home'] = 'Zur&uuml;ck zur Hauptseite';
        $loc['logout'] = 'Abmelden';
        break;

    case 'database':
        $loc['error'] = 'Database Error';
        $loc['connection.error'] = 'Konnte nicht zur Datenbank verbinden!';
        $loc['database.not.found'] = 'Datenbank nicht gefunden!';
        break;

    case 'ticker':
        $loc['title'] = 'Ticker';
        $loc['no.ticker'] = 'Derzeit sind keine weiteren Informationen vorhanden';
        $loc['save'] = 'Speichern';
        $loc['add'] = 'Hinzuf&uuml;gen';
        $loc['delete'] = 'L&ouml;schen';
        $loc['id'] = 'ID';
        $loc['text'] = 'Text';
        $loc['from'] = 'Von';
        $loc['until'] = 'Bis';
        $loc['options'] = 'Optionen';
        $loc['content.empty'] = 'Inhalt darf nicht leer sein!';
        $loc['err.startdate'] = 'Das Startdatum ist nicht korrekt!';
        $loc['err.enddate'] = 'Das Enddatum ist nicht korrekt!';
        $loc['err.end.before.start'] = 'Anfangsdatum darf nicht sp&auml;ter/gleich sein als das Enddatum';
        $loc['del.rly'] = 'Den Ticker der ID %id% wirklich l&ouml;schen?';
        $loc['abort'] = 'Abbrechen';
        $loc['deleted'] = 'Der Ticker mit der ID %id% wurde gel&ouml;scht.';
        $loc['ordernum'] = 'Ordnung';
        $loc['order.string'] = 'Ordnungsnummer muss numerisch sein.';
        break;

    case 'updates':
        $loc['title'] = 'Updates';
        $loc['no.updates'] = 'Keine Updates';
        $loc['update.to.version'] = 'Updaten auf Version';
        $loc['success'] = "Update erfolgreich";
        $loc['failure'] = "Update fehlgeschlagen";
        break;

    case 'menu':
        $loc['home'] = 'Home';
        $loc['ticker'] = 'Ticker';
        $loc['pages'] = 'Seiten';
        $loc['users'] = 'Nutzer';
        $loc['import'] = 'Importieren';
        $loc['settings'] = 'Einstellungen';
        $loc['oauth'] = 'OAuth';
        $loc['ip_protection'] = 'Zugangsrechte';
        break;

    case 'info':
        $loc['no.plan'] = 'Kein Vertretungsplan';
        $loc['replacements'] = 'Vertretung(en)';
        $loc['today'] = 'Heute';
        $loc['next.day'] = 'N&auml;chster Schultag';
        $loc['no.page'] = 'Keine Seite';
        $loc['pages'] = 'Seite(n)';
        break;

    case 'import':
        $loc['title'] = 'Importieren';
        $loc['file.upload'] = 'W&auml;hlen sie die Datei zum Import';
        $loc['success'] = 'Import erfolgreich abgeschlossen!';
        $loc['parse.fail'] = 'Die Datei hatte das falsche Format.';
        $loc['upload'] = 'Hochladen';
        $loc['mensa'] = 'Mensa';
        $loc['plan'] = 'Vertretungsplan';
        $loc['appointments'] = 'Termine';
        break;

    case 'pages':
        $loc['title'] = 'Seiten';
        $loc['page'] = 'Seite';
        $loc['caption'] = '&Uuml;berschrift';
        $loc['shown'] = 'Angezeigt';
        $loc['from'] = 'Von';
        $loc['until'] = 'Bis';
        $loc['id'] = 'ID';
        $loc['del.rly'] = 'Die Seite mit der ID %id% wirklich l&ouml;schen?';
        $loc['delete'] = 'L&ouml;schen';
        $loc['abort'] = 'Abbrechen';
        $loc['deleted'] = 'Die Seite mit der ID %id% wurde gel&ouml;scht.';
        $loc['edit'] = 'Bearbeiten';
        $loc['content'] = 'Inhalt';
        $loc['caption.empty'] = '&Uuml;berschrift darf nicht leer sein!';
        $loc['content.empty'] = 'Inhalt darf nicht leer sein!';
        $loc['err.startdate'] = 'Das Startdatum ist nicht korrekt!';
        $loc['err.enddate'] = 'Das Enddatum ist nicht korrekt!';
        $loc['err.end.before.start'] = 'Anfangsdatum darf nicht sp&auml;ter/gleich sein als das Enddatum';
        $loc['save'] = 'Speichern';
        $loc['show.at'] = 'Anzeige bei';
        $loc['pupils'] = 'Sch&uuml;ler';
        $loc['teachers'] = 'Lehrer';
        break;

    case 'users':
        $loc['id'] = 'ID';
        $loc['title'] = 'Nutzer';
        $loc['name'] = 'Name';
        $loc['edit'] = 'Bearbeiten';
        $loc['delete'] = 'L&ouml;schen';
        $loc['save'] = 'Speichern';
        $loc['saved'] = 'Gespeichert';
        $loc['new.password'] = 'Neues Passwort';
        $loc['name.too.short'] = 'Der Nutzername muss mindestens 3 Zeichen haben.';
        $loc['pw.too.short'] = 'Das Passwort muss mindestens 5 Zeichen haben.';
        $loc['del.self'] = 'Der eigene Nutzer kann nicht gel&ouml;scht werden!';
        break;

    case 'settings':
        $loc['title'] = 'Einstellungen';
        $loc['settings.name'] = 'Einstellungsname';
        $loc['value'] = 'Wert';
        $loc['save'] = 'Speichern';
        $loc['saved'] = 'Speichern erfolgreich';
        $loc['no.settings.found'] = 'Etwas lief schief: Es konnten keine Einstellungen gefunden werden! Bitte installieren sie das System neu!';

        $loc['schoolname'] = 'Name der Schule';
        $loc['system'] = 'Vertretungsplansystem';
        $loc['lang'] = 'Sprache';
        $loc['auto_addition'] = 'Markiere &Auml;nderungen automatisch farbig';
        $loc['time_for_next_page'] = 'Zeit bis zur n&auml;chsten Seite';
        $loc['teacher_time_for_next_page'] = 'Zeit bis zur n&auml;chsten Seite bei Lehrern';
        $loc['use_ftp'] = 'Nutze FTP?';
        $loc['ftp_server'] = 'FTP Server (normalerweise localhost)';
        $loc['ftp_user'] = 'FTP Nutzer';
        $loc['ftp_password'] = 'FTP Passwort';
        $loc['ftp_path'] = 'Pfad zu InMaFSS auf dem FTP Server';
        break;
    
    case 'ip_protection':
        $loc['title'] = 'Zugangsrechte';
        $loc['range'] = 'Bereich';
        $loc['edit'] = 'Bearbeiten';
        $loc['delete'] = 'L&ouml;schen';
        $loc['id'] = 'ID';
        $loc['save'] = 'Speichern';
        $loc['del.rly'] = 'Den Bereich mit der ID %id% wirklich l&ouml;schen?';
        $loc['abort'] = 'Abbrechen';
        $loc['deleted'] = 'Der Bereich mit der ID %id% wurde gel&ouml;scht.';
        $loc['add'] = 'Hinzuf&uuml;gen';
        $loc['empty.range'] = 'Bereich darf nicht leer sein!';
        $loc['invalid.range'] = 'Range ung&uumlltig';
        $loc['desc'] = 'Nur Clients mit einer IP, die innerhalb eines der unteren Bereiche liegt, wird automatisch den Plan einsehen k&ouml;nnen.<br> Alle anderen Clients m&uuml;ssen sich zuerst mit einem Managementaccount einloggen.';
        $loc['formats'] = 'Netzwerkbereiche k&ouml;nnen wie folgt definiert werden:';
        $loc['wildcard'] = 'Wildcard format';
        $loc['cidr'] = 'CIDR format';
        $loc['start.end.format'] = 'Start-End IP format';
        break;

    case 'date':
        $loc['january'] = 'Januar';
        $loc['february'] = 'Februar';
        $loc['march'] = 'M&auml;rz';
        $loc['april'] = 'April';
        $loc['may'] = 'Mai';
        $loc['june'] = 'Juni';
        $loc['july'] = 'Juli';
        $loc['august'] = 'August';
        $loc['september'] = 'September';
        $loc['october'] = 'Oktober';
        $loc['november'] = 'November';
        $loc['december'] = 'Dezember';

        $loc['monday'] = 'Montag';
        $loc['tuesday'] = 'Dienstag';
        $loc['wednesday'] = 'Mittwoch';
        $loc['thursday'] = 'Donnerstag';
        $loc['friday'] = 'Freitag';
        $loc['saturday'] = 'Samstag';
        $loc['sunday'] = 'Sonntag';

        $loc['mo'] = 'Mo';
        $loc['tu'] = 'Di';
        $loc['we'] = 'Mi';
        $loc['th'] = 'Do';
        $loc['fr'] = 'Fr';
        $loc['sa'] = 'Sa';
        $loc['su'] = 'So';

        $loc['prev.month'] = 'Vorheriger Monat';
        $loc['next.month'] = 'N&auml;chster Monat';

        $loc['prev.year'] = 'Vorheriges Jahr';
        $loc['next.year'] = 'N&auml;chstes Jahr';
        break;
    
    case 'oauth':
        $loc['title'] = "OAuth";
        $loc['id'] = "ID";
        $loc['application_uri'] = "Anwendungs-URI";
        $loc['consumer_key'] = "Anwendungsschl&uuml;ssel";
        $loc['consumer_secret'] = "Anwendungs-Geheimcode";
        $loc['callback_uri'] = "Callback URI";
        $loc['application_title'] = "Anwendungsname";
        $loc['options'] = "Optionen";
        $loc['new'] = "Neu";
        $loc['login.rights'] = "Loginmethoden";
        $loc['save'] = "Speichern";
        $loc['edit'] = "Bearbeiten";
        $loc['delete'] = "L&ouml;schen";
        $loc['deleted'] = "L&ouml;schen erfolgreich";
        $loc['deletion.failure'] = "L&ouml;schen fehlgeschlagen";
        $loc['name.too.short'] = 'Der Anwendungsname muss mindestens 3 Zeichen haben.';
        $loc['consumer.exists'] = "Eine Anwendung mit diesem Namen ist bereits registeriert";
        
        $loc['grant_authorization_code'] = "Nutzer gibt Zugriff &uuml;ber InMaFSS Homepage (empfohlen)";
        $loc['grant_password'] = "Passwort basiert (nur bei eigenen Anwendungen!)";
        $loc['grant_client_credentials'] = "Anwendung darf ohne Nutzer auf die API zugreifen";
        $loc['grant_refresh_token'] = "Anwendung darf Zugriffsschl&uuml;ssel erneuern (empfohlen)";
        $loc['grant_implicit'] = "Nutzer gibt Zugriff &uuml;ber InMaFSS Homepage (f&uuml;r Mobile Applikationen oder Javascript Anwendungen)";
       
        $loc['further.information'] = "Weitere Informationen";
        
        $loc['scopes'] = "Zugriffsrechte";
        
        break;

    case 'user':
        $loc['title'] = 'Nutzerbereich';
        $loc['login'] = 'Login';
        $loc['username'] = 'Nutzername';
        $loc['password'] = 'Passwort';
        $loc['wrong'] = 'Der Nutzername/das Passwort stimmt nicht.';
        $loc['welcome'] = 'Willkommen, %displayname%';
        $loc['logout'] = 'Abmelden';
        $loc['home'] = 'Home';
        $loc['settings'] = 'Einstellungen';
        $loc['information'] = "Informationen";
        $loc['mensa'] = "Informationen";
        $loc['menu'] = "Men&uuml;";
        $loc['delete'] = "L&ouml;schen"; 
        $loc['no.consumer'] = "Du hast noch keine Anwendung zugelassen.";
        break;
    
    case 'scopes':
        $loc['authorize_title'] = "OAuth";
        $loc['scope_basic'] = "Zugriff auf Basisdaten";
        $loc['scope_substitutions'] = "Zugriff auf deinen pers&ouml;nlichen Vertretungsplan";
        $loc['scope_all_substitutions'] = "Zugriff auf den gesamten Vertretungsplan (Sch&uuml;ler)";
        $loc['scope_teacher_plan_full'] = "Zugriff auf den gesamten Vertretungsplan (Lehrer)";
        $loc['scope_update_substitutions'] = "Vertretungsplan aktualisieren";
        $loc['scope_ticker'] = "Zugriff auf Ticker";
        $loc['scope_other'] = "Zugriff auf abwesende Lehrkr&auml;fte, Aufsichten, belegte R&auml;ume, abwesende Klassen";
        $loc['scope_update_mensa'] = "Mensa aktualisieren";
        $loc['scope_update_events'] = "Termine aktualisieren";
        $loc['scope_events'] = "Zugriff auf Termine";
        $loc['scope_mensa'] = "Zugriff auf Mensaspeiseplan";
        break;
}
?>