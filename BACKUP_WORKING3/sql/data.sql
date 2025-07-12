CREATE DATABASE article_db;
USE article_db;

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO articles (title, content) VALUES 
('First Article', 'This is the content of the first article'),
('Second Article', 'Here is the content of the second article');