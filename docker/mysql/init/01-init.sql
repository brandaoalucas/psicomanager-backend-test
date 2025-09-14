-- Create database if not exists
CREATE DATABASE IF NOT EXISTS tasks_db;

-- Create user for the application
CREATE USER IF NOT EXISTS 'task_user'@'%' IDENTIFIED BY 'task_password';
GRANT ALL PRIVILEGES ON tasks_db.* TO 'task_user'@'%';
FLUSH PRIVILEGES;

-- Use the database
USE tasks_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(255) UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(100),
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

-- Create tasks table
CREATE TABLE IF NOT EXISTS tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'in_progress', 'completed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create indexes
CREATE INDEX idx_tasks_user_id ON tasks(user_id);
CREATE INDEX idx_tasks_status ON tasks(status);
CREATE INDEX idx_tasks_created_at ON tasks(created_at);
