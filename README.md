IfThenElse Bundle
==============================================

Das IfThenElse Bundle stellt die Symfony ExpressionLanguage über die InsertTag Notation zur Verfügung. Damit lassen sich einfache Abfragen und davon abhängige Ausgaben realisieren.


Aufbau
------

Damit die Erweiterung den Insert-Tag als solchen erkennen kann, muss eine bestimmte Struktur benutzt werden. Diese sieht wie folgt aus:
```{{sel::Bedingung::Ausgabe}}```

### Variablen und Funktionen
* app.isoDate `// Date::parse('Y-m-d')`
* app.isoTime `// Date::parse('H:i')`
* app.minute `// Date::parse('i')`
* app.hour `// Date::parse('H')`
* app.day `// Date::parse('d')`
* app.month `// Date::parse('m')`
* app.year `// Date::parse('Y')`
* app.date `// Date::parse('Ymd')`
* app.time `// Date::parse('Hi')`
* app.tstamp `// time()`
* app.tools.dateDiff(dateA, dateB, 'days') `// days, y, m, d, h, i, s`
* member.[...]
* page.[...]

### Beispiele

Tageszeitabhängige Begrüßung auf der Website
```
{{sel::app.hour >= 22 OR (app.hour >= 0 && app.hour < 5)::Gute Nacht}}
{{sel::app.hour >=  5 && app.hour < 10::Guten Morgen}}
{{sel::app.hour >= 10 && app.hour < 14::Guten Tag}}
{{sel::app.hour >= 14 && app.hour < 16::Guten Nachmittag}}
{{sel::app.hour >= 16 && app.hour < 18::Gute Kaffeezeit}}
{{sel::app.hour >= 18 && app.hour < 22::Guten Abend}}
```

Die Ausgabe ist dann wie folgt:
* zwischen 22 Uhr und 5 Uhr: `Gute Nacht`
* zwischen 5 Uhr und 10 Uhr: `Guten Morgen`
* zwischen 10 Uhr und 14 Uhr: `Guten Tag`
* zwischen 14 Uhr und 16 Uhr: `Guten Nachmittag`
* zwischen 16 Uhr und 18 Uhr: `Gute Kaffeezeit`
* zwischen 18 Uhr und 22 Uhr: `Guten Abend`

Vergleich zwei Datum:
```
{{sel::app.tools.dateDiff('now', page.tstamp, 'days') > 14::todo}}
{{sel::app.tools.dateDiff('now', page.tstamp, 'days') < 14::OK}}
```

Ausgabe: Ist die Differenz von 'now' und dem Timestamp der Seite älter als 14 Tage wird `todo` ausgegeben.


IfThenElse Bundle
==============================================

The IfThenElse bundle provides the Symfony ExpressionLanguage via the InsertTag notation. This allows for the implementation of simple queries and dependent outputs.
```{{sel::Bedingung::Ausgabe}}```

### Variables and functions
* app.isoDate `// Date::parse('Y-m-d')`
* app.isoTime `// Date::parse('H:i')`
* app.minute `// Date::parse('i')`
* app.hour `// Date::parse('H')`
* app.day `// Date::parse('d')`
* app.month `// Date::parse('m')`
* app.year `// Date::parse('Y')`
* app.date `// Date::parse('Ymd')`
* app.time `// Date::parse('Hi')`
* app.tstamp `// time()`
* app.tools.dateDiff(dateA, dateB, 'days') `// days, y, m, d, h, i, s`
* member.[...]
* page.[...]

### Examples

Time-of-day-dependent greeting on the website
```
{{sel::app.hour >= 22 OR (app.hour >= 0 && app.hour < 5)::Good night}}
{{sel::app.hour >=  5 && app.hour < 10::Good morning}}
{{sel::app.hour >= 10 && app.hour < 14::Good afternoon}}
{{sel::app.hour >= 14 && app.hour < 16::Good afternoon}}
{{sel::app.hour >= 16 && app.hour < 18::Enjoy your coffee}}
{{sel::app.hour >= 18 && app.hour < 22::Good evening}}
```

The output is then as follows:
* between 10 p.m. and 5 a.m.: `Good night`
* between 5 a.m. and 10 a.m.: `Good morning`
* between 10 a.m. and 2 p.m.: `Good afternoon`
* between 2 p.m. and 4 p.m.: `Good afternoon`
* between 4 p.m. and 6 p.m.: `Enjoy your coffee`
* between 6 p.m. and 10 p.m.: `Good evening`

Comparing two dates:
```
{{sel::app.tools.dateDiff('now', page.tstamp, 'days') > 14::todo}}
{{sel::app.tools.dateDiff('now', page.tstamp, 'days') < 14::OK}}
```

Output: If the difference between 'now' and the page timestamp is older than 14 days, `todo` is output.


Installation
------------


Install the extension via composer: [trilobit-gmbh/contao-ifthenelse-bundle](https://packagist.org/packages/trilobit-gmbh/contao-ifthenelse-bundle).


Compatibility
-------------

- (Contao version ~4.13)
- Contao version ~5.3
- Contao version ~5.7
