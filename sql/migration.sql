CREATE DATABASE IF NOT EXISTS todolist_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE todolist_db;

CREATE TABLE IF NOT EXISTS todos (
  id CHAR(36) PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  is_done TINYINT(1) DEFAULT 0,
  created_at DATETIME NOT NULL
);

