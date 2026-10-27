-- --------------------------------------------------------
-- Database: adsu_lms
-- Description: Complete database for ADSU Sandwich Programme Virtual Learning Platform
-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS adsu_lms;
USE adsu_lms;

-- --------------------------------------------------------
-- Table: users (base for all roles)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'lecturer', 'student') NOT NULL,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    email_verification_token VARCHAR(64) NULL,
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- Table: lecturers (extends users)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS lecturers (
    lecturer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    department VARCHAR(100),
    specialization VARCHAR(100),
    office_hours TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Table: students (extends users)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    matric_number VARCHAR(20) UNIQUE NOT NULL,
    programme VARCHAR(100),
    level INT CHECK (level BETWEEN 100 AND 500),
    current_gpa DECIMAL(3,2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Table: academic_sessions
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS academic_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    session_name VARCHAR(20) NOT NULL UNIQUE,
    is_current BOOLEAN DEFAULT FALSE
);

-- --------------------------------------------------------
-- Table: semesters
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS semesters (
    semester_id INT AUTO_INCREMENT PRIMARY KEY,
    semester_name VARCHAR(20) NOT NULL,      -- 'Harmattan' or 'Rain'
    session_id INT NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (session_id) REFERENCES academic_sessions(session_id),
    UNIQUE KEY (semester_name, session_id)
);

-- --------------------------------------------------------
-- Table: courses
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) NOT NULL UNIQUE,
    course_title VARCHAR(200) NOT NULL,
    credit_unit INT NOT NULL,
    lecturer_id INT,
    semester_id INT NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(lecturer_id),
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id)
);

-- --------------------------------------------------------
-- Table: enrollments (many-to-many students-courses)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('enrolled', 'dropped', 'completed') DEFAULT 'enrolled',
    UNIQUE KEY (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

-- --------------------------------------------------------
-- Table: course_materials
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS course_materials (
    material_id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type ENUM('pdf', 'docx', 'pptx', 'video', 'link', 'image') NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    version INT DEFAULT 1,
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

-- --------------------------------------------------------
-- Table: appointments
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    lecturer_id INT NOT NULL,
    meeting_date DATE NOT NULL,
    meeting_time TIME NOT NULL,
    duration_minutes INT DEFAULT 30,
    status ENUM('pending', 'approved', 'rejected', 'cancelled', 'completed') DEFAULT 'pending',
    meeting_link VARCHAR(500),
    student_remarks TEXT,
    lecturer_feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(lecturer_id),
    INDEX idx_appointment_date (meeting_date),
    INDEX idx_appointment_status (status)
);

-- --------------------------------------------------------
-- Table: assignments
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS assignments (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    deadline DATETIME NOT NULL,
    max_score INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

-- --------------------------------------------------------
-- Table: submissions
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS submissions (
    submission_id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    file_path VARCHAR(500),
    submission_text TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    score INT NULL,
    feedback TEXT,
    graded_at TIMESTAMP NULL,
    FOREIGN KEY (assignment_id) REFERENCES assignments(assignment_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    UNIQUE KEY (assignment_id, student_id)
);

-- --------------------------------------------------------
-- Table: results (final grades per course, per session/semester)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS results (
    result_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    ca_score INT DEFAULT 0,
    exam_score INT DEFAULT 0,
    total_score INT GENERATED ALWAYS AS (ca_score + exam_score) STORED,
    grade VARCHAR(2) GENERATED ALWAYS AS (
        CASE 
            WHEN (ca_score + exam_score) >= 70 THEN 'A'
            WHEN (ca_score + exam_score) >= 60 THEN 'B'
            WHEN (ca_score + exam_score) >= 50 THEN 'C'
            WHEN (ca_score + exam_score) >= 45 THEN 'D'
            WHEN (ca_score + exam_score) >= 40 THEN 'E'
            ELSE 'F'
        END
    ) STORED,
    session_id INT NOT NULL,
    semester_id INT NOT NULL,
    UNIQUE KEY (student_id, course_id, session_id, semester_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (course_id) REFERENCES courses(course_id),
    FOREIGN KEY (session_id) REFERENCES academic_sessions(session_id),
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id)
);

-- --------------------------------------------------------
-- Table: notifications
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_notif_user_read (user_id, is_read)
);

-- --------------------------------------------------------
-- Table: audit_logs
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100),
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_audit_user (user_id),
    INDEX idx_audit_created (created_at)
);

-- --------------------------------------------------------
-- Optional: Insert default admin user (password: Admin@123)
-- --------------------------------------------------------
-- The password hash below is for 'Admin@123' using bcrypt.
-- You can change it after first login.
INSERT INTO users (full_name, email, password_hash, role, status, email_verified_at)
VALUES ('System Administrator', 'admin@adsu.edu.ng', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', NOW())
ON DUPLICATE KEY UPDATE user_id = user_id;

UPDATE users 
SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    status = 'active',
    email_verified_at = NOW()
WHERE email = 'admin@adsu.edu.ng';
-- --------------------------------------------------------
-- Indexes for performance (if not already included)
-- --------------------------------------------------------
CREATE INDEX idx_course_semester ON courses(semester_id, status);
CREATE INDEX idx_enrollment_student ON enrollments(student_id);
CREATE INDEX idx_submission_assignment ON submissions(assignment_id);