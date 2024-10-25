-- DROP TABLE IF EXISTS urls;
-- DROP TABLE IF EXISTS url_checks;

CREATE TABLE IF NOT EXISTS urls (
    id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY NOT NULL,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE TABLE IF NOT EXISTS url_checks (
    id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY NOT NULL,
    url_id INTEGER NOT NULL,
    status_code INTEGER,
    h1 TEXT,
    title TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);