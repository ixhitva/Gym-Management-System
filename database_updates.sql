-- Add password column to existing tables
ALTER TABLE Member ADD COLUMN password VARCHAR(255) AFTER email;
ALTER TABLE Trainer ADD COLUMN password VARCHAR(255) AFTER email;

-- Create Admin table
CREATE TABLE Admin (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100),
    password VARCHAR(255)
);

-- Create table for tracking email reminders
CREATE TABLE EmailReminders (
    reminder_id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT,
    sent_date DATETIME,
    email_type VARCHAR(50),
    status VARCHAR(20),
    FOREIGN KEY (member_id) REFERENCES Member(member_id)
);

-- Insert sample admin
INSERT INTO Admin (first_name, last_name, email, password) VALUES
('Admin', 'User', 'admin@gym.com', 'admin123');

-- Insert sample member
INSERT INTO Member (member_id, first_name, last_name, email, password, phone_no, birth_date, join_date, start_date, end_date, price) VALUES
(1, 'John', 'Doe', 'member@gym.com', 'member123', '1234567890', '1990-01-01', '2023-01-01', '2023-01-01', '2024-01-01', 99.99);

-- Insert sample trainer
INSERT INTO Trainer (trainer_id, first_name, last_name, email, password, contact_no, specialization) VALUES
(1, 'Jane', 'Smith', 'trainer@gym.com', 'trainer123', '0987654321', 'Yoga');
