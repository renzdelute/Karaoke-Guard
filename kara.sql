CREATE DATABASE kara;

USE kara;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

INSERT INTO users (username, password) VALUES ('admin', 'admin');

CREATE TABLE karaoke (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    picture VARCHAR(255)
);

CREATE TABLE karaoke_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    karaoke_id INT NOT NULL,
    status ENUM('available', 'rented') NOT NULL DEFAULT 'available',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (karaoke_id) REFERENCES karaoke(id) ON DELETE CASCADE
);