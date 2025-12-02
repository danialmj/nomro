-- ---------------------------------------------------
--     ساخت دیتابیس و انتخاب آن
-- ---------------------------------------------------
CREATE DATABASE IF NOT EXISTS school
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_persian_ci;

USE school;

-- ---------------------------------------------------
--     جدول دانش‌آموزان (students)
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    national_code CHAR(10) NOT NULL UNIQUE,
    father_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------
--     جدول دروس (subjects)
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(200) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------
--     جدول نمرات (grades)
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    grade_student DECIMAL(5,2) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_grade_student FOREIGN KEY (student_id)
        REFERENCES students(id) ON DELETE CASCADE,

    CONSTRAINT fk_grade_subject FOREIGN KEY (subject_id)
        REFERENCES subjects(id) ON DELETE CASCADE,

    UNIQUE (student_id, subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
