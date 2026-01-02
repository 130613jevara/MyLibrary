-- Create database with new name
CREATE DATABASE IF NOT EXISTS website;
USE website;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Create authors table
CREATE TABLE IF NOT EXISTS authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    birth_year INT,
    death_year INT,
    nationality VARCHAR(100),
    biography TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create genres table
CREATE TABLE IF NOT EXISTS genres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create books table with foreign keys
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author_id INT,
    year INT,
    genre_id INT,
    description TEXT,
    added_by_user_id INT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE SET NULL,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE SET NULL,
    FOREIGN KEY (added_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert sample users
INSERT INTO users (username, email, password, full_name) VALUES
('emma_reader', 'emma@example.com', '$2y$10$YourHashedPasswordHere1', 'Emma Johnson'),
('lily_books', 'lily@example.com', '$2y$10$YourHashedPasswordHere2', 'Lily Smith'),
('sophie_writes', 'sophie@example.com', '$2y$10$YourHashedPasswordHere3', 'Sophie Anderson'),
('olivia_reads', 'olivia@example.com', '$2y$10$YourHashedPasswordHere4', 'Olivia Brown'),
('ava_library', 'ava@example.com', '$2y$10$YourHashedPasswordHere5', 'Ava Davis');

-- Insert sample genres
INSERT INTO genres (name, description) VALUES
('Fiction', 'Literary works based on imagination rather than fact'),
('Dystopian', 'Stories set in oppressive, controlled societies'),
('Romance', 'Stories centered on romantic relationships'),
('Fantasy', 'Stories with magical or supernatural elements'),
('Satire', 'Works using humor and irony to criticize'),
('Mystery', 'Stories involving crime solving and suspense'),
('Science Fiction', 'Futuristic or speculative fiction based on science'),
('Historical Fiction', 'Stories set in the past with historical elements');

-- Insert sample authors
INSERT INTO authors (name, birth_year, death_year, nationality, biography) VALUES
('Harper Lee', 1926, 2016, 'American', 'American novelist best known for To Kill a Mockingbird'),
('George Orwell', 1903, 1950, 'British', 'English novelist and essayist known for dystopian works'),
('F. Scott Fitzgerald', 1896, 1940, 'American', 'American novelist of the Jazz Age'),
('Jane Austen', 1775, 1817, 'British', 'English novelist known for romantic fiction'),
('J.D. Salinger', 1919, 2010, 'American', 'American writer known for The Catcher in the Rye'),
('J.R.R. Tolkien', 1892, 1973, 'British', 'English writer and philologist, creator of Middle-earth'),
('J.K. Rowling', 1965, NULL, 'British', 'British author of the Harry Potter series'),
('Aldous Huxley', 1894, 1963, 'British', 'English writer and philosopher');

-- Insert sample books with relationships
INSERT INTO books (title, author_id, year, genre_id, description, added_by_user_id) VALUES
('To Kill a Mockingbird', 1, 1960, 1, 'A classic novel about racial injustice in the American South.', 1),
('1984', 2, 1949, 2, 'A dystopian social science fiction novel and cautionary tale.', 2),
('The Great Gatsby', 3, 1925, 1, 'A novel about the American dream and the Jazz Age.', 3),
('Pride and Prejudice', 4, 1813, 3, 'A romantic novel of manners set in Georgian England.', 4),
('The Catcher in the Rye', 5, 1951, 1, 'A story about teenage rebellion and alienation.', 5),
('The Hobbit', 6, 1937, 4, 'A fantasy novel about the quest of Bilbo Baggins.', 1),
('Harry Potter and the Sorcerer''s Stone', 7, 1997, 4, 'The first book in the Harry Potter series.', 2),
('The Lord of the Rings', 6, 1954, 4, 'An epic high-fantasy novel.', 3),
('Animal Farm', 2, 1945, 5, 'An allegorical novella about Soviet totalitarianism.', 4),
('Brave New World', 8, 1932, 2, 'A dystopian novel set in a futuristic World State.', 5);
