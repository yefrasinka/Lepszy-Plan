CREATE TABLE book (
                           id INTEGER NOT NULL CONSTRAINT book_pk PRIMARY KEY AUTOINCREMENT,
                           name VARCHAR(255) NOT NULL,
                           author VARCHAR(255) NOT NULL,
                           description TEXT,
                           number_of_pages INT NOT NULL
);