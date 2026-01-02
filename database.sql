-- Create database with new name
CREATE DATABASE IF NOT EXISTS website;
USE website;

-- Create books table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    year INT,
    genre VARCHAR(100),
    description TEXT,
    added_by VARCHAR(100),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample books
INSERT INTO books (title, author, year, genre, description, added_by) VALUES
('To Kill a Mockingbird', 'Harper Lee', 1960, 'Fiction', 'A classic novel about racial injustice in the American South.', 'Emma'),
('1984', 'George Orwell', 1949, 'Dystopian', 'A dystopian social science fiction novel and cautionary tale.', 'Lily'),
('The Great Gatsby', 'F. Scott Fitzgerald', 1925, 'Fiction', 'A novel about the American dream and the Jazz Age.', 'Sophie'),
('Pride and Prejudice', 'Jane Austen', 1813, 'Romance', 'A romantic novel of manners set in Georgian England.', 'Olivia'),
('The Catcher in the Rye', 'J.D. Salinger', 1951, 'Fiction', 'A story about teenage rebellion and alienation.', 'Ava'),
('The Hobbit', 'J.R.R. Tolkien', 1937, 'Fantasy', 'A fantasy novel about the quest of Bilbo Baggins.', 'Mia'),
('Harry Potter and the Sorcerer''s Stone', 'J.K. Rowling', 1997, 'Fantasy', 'The first book in the Harry Potter series.', 'Isabella'),
('The Lord of the Rings', 'J.R.R. Tolkien', 1954, 'Fantasy', 'An epic high-fantasy novel.', 'Charlotte'),
('Animal Farm', 'George Orwell', 1945, 'Satire', 'An allegorical novella about Soviet totalitarianism.', 'Amelia'),
('Brave New World', 'Aldous Huxley', 1932, 'Dystopian', 'A dystopian novel set in a futuristic World State.', 'Harper');