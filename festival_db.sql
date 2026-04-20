
CREATE DATABASE IF NOT EXISTS festival_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE festival_db;

CREATE TABLE IF NOT EXISTS tickets (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticketnummer  VARCHAR(20)  NOT NULL UNIQUE,
    name          VARCHAR(100) NOT NULL,
    credits       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    erstellt_am   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS produkte (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    kategorie   ENUM('pizza','asia','getraenke') NOT NULL,
    preis       DECIMAL(10,2) NOT NULL,
    verfuegbar  TINYINT(1)    NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS staende (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100) NOT NULL,
    kategorie    ENUM('pizza','asia','getraenke') NOT NULL,
    wartezeit    INT          NOT NULL DEFAULT 0  COMMENT 'Wartezeit in Minuten',
    aktiv        TINYINT(1)   NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS bestellungen (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id     INT UNSIGNED NOT NULL,
    stand_id      INT UNSIGNED NOT NULL,
    status        ENUM('offen','in_bearbeitung','abgeschlossen','storniert') NOT NULL DEFAULT 'offen',
    gesamt_credits DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    bestellt_am   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id),
    FOREIGN KEY (stand_id)  REFERENCES staende(id)
);

CREATE TABLE IF NOT EXISTS bestellpositionen (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bestellung_id INT UNSIGNED NOT NULL,
    produkt_id    INT UNSIGNED NOT NULL,
    menge         INT UNSIGNED NOT NULL DEFAULT 1,
    einzelpreis   DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (bestellung_id) REFERENCES bestellungen(id) ON DELETE CASCADE,
    FOREIGN KEY (produkt_id)    REFERENCES produkte(id)
);

CREATE TABLE IF NOT EXISTS credit_aufladungen (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id   INT UNSIGNED NOT NULL,
    betrag_euro DECIMAL(10,2) NOT NULL,
    credits     DECIMAL(10,2) NOT NULL,
    aufgeladen_am DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id)
);



INSERT INTO tickets (ticketnummer, name, credits) VALUES
    ('T-0001', 'Max Mustermann',   50.00),
    ('T-0002', 'Erika Musterfrau', 30.00),
    ('T-0003', 'Jonas Beispiel',   75.00);

INSERT INTO staende (name, kategorie, wartezeit) VALUES
    ('Pizza-Stand A',    'pizza',     18),
    ('Pizza-Stand B',    'pizza',      5),
    ('Asia-Stand',       'asia',       8),
    ('Getränke-Stand',   'getraenke',  2);

INSERT INTO produkte (name, kategorie, preis) VALUES
    -- Pizza
    ('Margherita',  'pizza',     5.00),
    ('Pepperoni',   'pizza',     8.00),
    ('Spezial',     'pizza',    12.00),
    -- Asia
    ('Ramen',       'asia',      8.00),
    ('Sushi',       'asia',     10.00),
    ('Dumplings',   'asia',      6.00),
    -- Getränke
    ('Wasser',      'getraenke', 5.00),
    ('Bier',        'getraenke', 8.00),
    ('Cocktail',    'getraenke',12.00);

