LQFB-Autoablehnen

Autoablehnen-Mod fuer Liquid Feedback 2.0

http://autoablehnen.flamefestival.de/



Pr�ambel

Die neue Version von Liquid Feedback ist da. Alles ist toller, besser, schneller, neuer, bunter. Aber der hohe Rat der 27 Liquid-Weisen hat in der neuesten Ausgabe des Kaffeesatzes gelesen, und entschieden: Auto-Ablehnen ist unmoralisch und wider die Natur!

K�nnte nat�rlich auch damit zu tun haben, dass viele Leute Auto-Ablehnen ganz legitim nutzen, weil sie der Meinung sind: Wer meine Stimme will, der muss sich auch um meine Aufmerksamkeit bem�hen. Diese reservierte, ja gerade konservative Ansicht aber st�rt den geheiligten Ablauf der fl�ssigen Demokratie [1] und geh�rt daher verboten!

Doof nur, dass zwischenzeitlich jemand das Internet erfunden hat. Und in dem gibt es Hilfsmittel f�r so ziemlich alles. Und Leute, die sich von Herstellern ungerne vorschreiben lassen, wie ihre Hard-/Software zu nutzen ist. Und daher gibt es jetzt auch das erste, einzigartige After-Market-Mod-Pack f�r Liquid Feedback 2.0.


Was macht es?

Na das, was draufsteht. Sobald Themen abstimmreif werden - �ber alle Themenbereiche, egal ob du teilnimmst oder nicht - werden sie erstmal abgelehnt. Ausgenommen sind Themen, die Themenspezifisch delegiert wurden.  Wenn du selbst bereits abgestimmt hast, oder die Autoablehnen-Abstimmung �nderst, wird sie nicht erneut �berschrieben.


Warum so kompliziert?

Die offizielle Liquid-Feedback-API ist leider noch nicht einsatzf�hig, weil die Entwickler irgendwie Authentifizierung als API-Bestandteil �bersehen haben. Selbst wenn die API aber voll einsatzbereit w�re, ist sie nicht zwangsl�ufig �berall auch uneingeschr�nkt benutzbar. Besonderes Ziel dieser Software ist die Liquid Feedback Bundesinstanz der Piratenpartei Deutschland, und da wurde zumindest angedeutet dass ein voller API-Zugriff nicht automatisch angeboten werden wird. Datenschutz, wissenschon.


Was brauch ich daf�r?

PHP in einer einigerma�en aktuellen Version. CURL als externes Programm. Optional einen installierten TOR-Client. Ausserdem nat�rlich eine Ziel-Liquid-Feedback-Instanz mit Zugangsdaten.
Konfiguration in der Datei autoablehnen.php, starten mit
  php autoablehnen.php


Ich habe kein PHP, kein Curl, keine Ahnung, keine Lust?

Eventuell baue ich einen Autoablehn-Service auf. Falls das passiert, und wenn es soweit ist, gibt es weitere Infos dazu auf http://autoablehnen.flamefestival.de/
Dadurch dass die LQFB-Entwickler sich keine sinnvolle (genauer: keine) Authentifizierung ausgedacht haben, bedeutet ein Autoablehn-Service dass man seine Zugangsdaten aus der Hand geben muss. Das kann eventuell Nutzungsbedingungen widersprechen. Deshalb w�rde man nach aussen nat�rlich immer behaupten man betreibe die Software selbst bei sich daheim. Wenn jemand so einen Dienst aufsetzen w�rde, w�rde er nat�rlich daf�r Sorge tragen, dass alle Zugriffe h�bsch �ber nen TOR-Proxy laufen, etc.. etc..


Warum in PHP?

Weil ich kein LUA kann, und Brainfuck keine regul�ren Ausdr�cke hat. Und weil PHP f�r manche Liquid-Evangelisten der Antichrist unter den Programmiersprachen zu sein scheint.


Warum nicht in TolleSpracheX?

�bersetz es halt. Oder machs besser. Daf�r steht das Ding unter der GPL.
Die Teile von 



[1] http://streetdogg.wordpress.com/2011/04/22/the-tale-of-liquid-feedback/#AutoAblehnen

