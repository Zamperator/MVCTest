# MVC Framework für PHP 8.3 (Nur ein Test)

**Version:** 0.0.2

Dieses MVC-Framework wurde als Programmiertest für Bewerbungen an zwei Abenden entwickelt.
Es ist für PHP 8.3 angepasst und enthält eine Reihe von Funktionen und Strukturen, die eine solide Grundlage für 
Webanwendungen bieten. Die Komponenten umfassen in Version 0.0.2 einen Router, Controller, Middleware und eine einfache Möglichkeit, 
Routen mit Platzhaltern zu definieren.

## Hauptmerkmale

- **Router-Klasse**: Der Router unterstützt das Definieren von GET-Routen und das Hinzufügen von Middleware. Routen können Platzhalter enthalten, die zu regulären Ausdrücken konvertiert werden, um Parameter zu extrahieren und an die entsprechenden Controller-Methoden weiterzuleiten.
- **Controller**: Controller-Klassen bieten die Logik für die verschiedenen Endpunkte. Beispielsweise gibt es einen `IndexController` für die Startseite und einen `ProfileController`, der Benutzerdetails anhand von IDs anzeigt.
- **Middleware**: Es gibt Unterstützung für Middleware, um vor der Verarbeitung der Routen zusätzliche Logik auszuführen, wie z.B. Authentifizierung oder Logging.
- **Namespace-Verwendung**: Das Projekt verwendet Namespaces, um die Struktur und Organisation des Codes zu verbessern und Konflikte zu vermeiden.
- **Docker-Integration**: Das Projekt enthält Docker-Informationen, die eine einfache Einrichtung und Ausführung einer Testumgebung ermöglichen.
- **Alte Komponenten**: Bestimmte Teile des Projekts, wie Utils, PageSetup und Registry, stammen aus einem älteren Projekt und wurden für die Verwendung in diesem Framework angepasst.
  - `Utils.php` enthält diverse Helper-Methoden zur Unterstützung (wie arraySortByIndex etc.), die in verschiedenen Teilen des Projekts verwendet werden können.
  - `PageSetup.php` ist eine Klasse, die die Konfiguration der Seiten vereinfacht, wie das Setzen vom aktuellen Seiten-Titel oder integration von Scripten/CSS im Header/Footer.
    - Siehe bspw. `IndexController.php` für die Verwendung von PageSetup.
  - `Registry.php` ist eine Klasse, die als Container für verschiedene Objekte dient, die im gesamten Projekt verwendet werden können (Global Scope).

## Beispiel fürs Routing

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Lib\Router;
use App\Controllers\HomeController;
use App\Controllers\UserController;

$router = new Router();

$router->get('/', [HomeController::class, 'index']); // Root URL
$router->get('user/{id}', [UserController::class, 'show']); // /user/123 URL

$router->middleware(function () {
    // Middleware-Logik, z.B. Authentifizierung prüfen
});

$router->dispatch();
```

## Docker-Integration
Das Projekt enthält eine Docker-Konfigurationsdatei, die eine einfache Einrichtung und Ausführung einer Testumgebung ermöglicht. 
Dies stellt sicher, dass die Anwendung in einer konsistenten Umgebung läuft, unabhängig von der lokalen Entwicklungsumgebung des Benutzers.
Aktuell:
- PHP 8.3
- mariadb
- mogodb
- Redis
- Memcached
- Nginx
- Composer
- Xdebug (deaktiviert)

## Composer
- Das Projekt verwendet Composer zur Verwaltung von Abhängigkeiten und zum Autoloading von Klassen und des Projekts an sich.
Komponenten:
- PHPSniffer: Für die Code-Qualität
- PHPStan: Für die statische Code-Analyse
- PHPUnit: Für die Unit-Tests

## Anmerkung
- Die Komponenten Utils, PageSetup und Registry wurden aus einem älteren Projekt übernommen und für die Verwendung in diesem Framework angepasst.

## TODO
- Eine Menge :p
  - Javascript Framework integrierten, bspw. Vue.js oder Angular
  - Authentifizierung hinzufügen
  - Datenbank-Integration
  - Validierungen
  - Logging
  - Caching
  - Internationalisierung
  - Installationsskript/Prozess
  - Tests schreiben
  - Refactoring
  - ...
