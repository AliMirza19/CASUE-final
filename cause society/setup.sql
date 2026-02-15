-- CAUSE Smart Society Management System Database Setup
-- Ye script database aur tables create karega

CREATE DATABASE IF NOT EXISTS cause_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cause_db;

-- Academic Terms table banao
CREATE TABLE IF NOT EXISTS academic_terms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    term_name VARCHAR(100) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'inactive',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Users table banao
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reg_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'hod', 'patron', 'president', 'student', 'sa', 'vc', 'gd') NOT NULL,
    password_changed TINYINT(1) DEFAULT 0,
    current_term_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (current_term_id) REFERENCES academic_terms(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default active term insert karo
INSERT INTO academic_terms (term_name, status, start_date, end_date) 
VALUES ('Fall 2024', 'active', '2024-09-01', '2024-12-31');

-- Default admin user insert karo (password: 123456)
INSERT INTO users (reg_id, name, email, password, role, password_changed, current_term_id) 
VALUES ('ADMIN-001', 'System Administrator', 'admin@cause.edu.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 0, 1);

-- Budgets table banao (Epic E9 - Budget Management)
CREATE TABLE IF NOT EXISTS budgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    term_id INT NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    remaining_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    is_locked TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (term_id) REFERENCES academic_terms(id) ON DELETE CASCADE,
    UNIQUE KEY unique_term_budget (term_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Events table banao (Epic E4 - Event Requests)
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    student_id INT NOT NULL,
    term_id INT NOT NULL,
    expected_date DATE NOT NULL,
    venue VARCHAR(255) NOT NULL,
    grand_total DECIMAL(15,2) DEFAULT 0.00,
    team_member_1 VARCHAR(50),
    team_member_2 VARCHAR(50),
    team_member_3 VARCHAR(50),
    status ENUM('pending_president','pending_patron','pending_hod','pending_sa','approved','rejected','completed') DEFAULT 'pending_president',
    rejection_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (term_id) REFERENCES academic_terms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Event Items table banao (Epic E5 - Budget Control)
CREATE TABLE IF NOT EXISTS event_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_rate DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    is_approved_by_patron TINYINT(1) DEFAULT 0,
    patron_comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default users insert karo (password: 123456 for all)
INSERT IGNORE INTO users (reg_id, name, email, password, role, password_changed, current_term_id) VALUES
('HOD-001', 'Dr. Ahmed Khan', 'hod@cause.edu.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hod', 1, 1),
('STU-001', 'Ali Hassan', 'student@cause.edu.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 1, 1),
('PRES-001', 'Sarah Ahmed', 'president@cause.edu.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'president', 1, 1),
('PAT-001', 'Prof. Muhammad Khan', 'patron@cause.edu.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patron', 1, 1),
('SA-001', 'Fatima Ali', 'sa@cause.edu.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sa', 1, 1),
('VC-001', 'Dr. Ayesha Khan', 'vc@cause.edu.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vc', 1, 1),
('GD-001', 'Hassan Ali', 'gd@cause.edu.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gd', 1, 1);

-- Event Graphics table banao (Epic E7 - Graphics Design)
CREATE TABLE IF NOT EXISTS event_graphics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    gd_id INT NOT NULL,
    design_category ENUM('poster', 'banner', 'social_media') NOT NULL,
    image_path VARCHAR(500),
    image_link VARCHAR(500),
    status ENUM('pending_patron', 'approved', 'rejected') DEFAULT 'pending_patron',
    patron_feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (gd_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Event Volunteers table banao (Volunteer Coordination)
CREATE TABLE IF NOT EXISTS event_volunteers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    vc_id INT NOT NULL,
    volunteer_name VARCHAR(255) NOT NULL,
    volunteer_contact VARCHAR(50),
    role_description VARCHAR(255) NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (vc_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Candidate Profiles table banao (Epic E8 - Elections)
CREATE TABLE IF NOT EXISTS candidate_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    manifesto TEXT NOT NULL,
    photo_url VARCHAR(500),
    experience TEXT,
    vp_name VARCHAR(255),
    status ENUM('pending_patron', 'approved', 'rejected') DEFAULT 'pending_patron',
    patron_feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_candidate (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Votes table banao (Election System)
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    candidate_id INT NOT NULL,
    term_id INT NOT NULL,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidate_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (term_id) REFERENCES academic_terms(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_vote (student_id, term_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Election Settings table banao (Voting control)
CREATE TABLE IF NOT EXISTS election_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    term_id INT NOT NULL,
    voting_enabled TINYINT(1) DEFAULT 0,
    voting_start_date DATETIME,
    voting_end_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (term_id) REFERENCES academic_terms(id) ON DELETE CASCADE,
    UNIQUE KEY unique_term_election (term_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Activity Logs table banao (Master Activity Log)
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_role VARCHAR(20) NOT NULL,
    action_text TEXT NOT NULL,
    related_event_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (related_event_id) REFERENCES events(id) ON DELETE SET NULL,
    INDEX idx_user_role (user_role),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Announcements table banao (System Announcements)
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_by INT NOT NULL,
    target_roles JSON,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Note: Password hash '123456' ke liye hai
