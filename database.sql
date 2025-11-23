-- MINDCARE TIPS DATABASE SCHEMA
CREATE DATABASE IF NOT EXISTS mindcare_db;
USE mindcare_db;

CREATE TABLE IF NOT EXISTS tips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    video_id VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO tips (title, content, video_id) VALUES
('Practice Mindful Breathing', 'Take five minutes right now to focus only on your breath. Inhale deeply through your nose, hold for a moment, and exhale slowly through your mouth.', 'inpok4MKVLM'),
('The Power of Gratitude', 'Practice gratitude. Write down three things you are genuinely thankful for today. This shifts your focus from what you lack to what you have.', 'WPPPFqsECz0'),
('Digital Detox for Better Sleep', 'Limit screen time, especially before bed. The blue light from devices can interfere with your sleep cycle.', 'aXItOY0sLRY'),
('Connect with Nature', 'Spend at least 15 minutes outdoors today. Studies show that exposure to nature reduces stress hormones.', 'Bf5j6daSolg');

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;