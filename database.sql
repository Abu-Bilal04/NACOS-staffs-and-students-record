CREATE DATABASE staff_student_db;
USE staff_student_db;

-- Admin Table
CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Student Table
CREATE TABLE student (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age_number VARCHAR(50) NOT NULL,
    address VARCHAR(255),
    phone_number VARCHAR(50),
    password VARCHAR(255) NOT NULL,
    approval_status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending'
);

-- Staff Table
CREATE TABLE staff (
    staff_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    staff_number VARCHAR(50) NOT NULL UNIQUE,
    address VARCHAR(255),
    phone_number VARCHAR(50),
    password VARCHAR(255) NOT NULL,
    approval_status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending'
);

-- Student Credentials
CREATE TABLE student_credentials (
    cred_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    primary_certificate VARCHAR(255),
    batch_certificate VARCHAR(255),
    olevel_certificate VARCHAR(255),
    admission_letter VARCHAR(255),
    recommendation_letter VARCHAR(255),
    school_fees_receipt VARCHAR(255),
    consultancy_fees_receipt VARCHAR(255),
    tship_payment_receipt VARCHAR(255),
    departmental_payment_receipt VARCHAR(255),
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES student(student_id)
);

-- Staff Credentials
CREATE TABLE staff_credentials (
    cred_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT,
    appointment_letter VARCHAR(255),
    promotion_letter_1 VARCHAR(255),
    promotion_letter_2 VARCHAR(255),
    promotion_letter_3 VARCHAR(255),
    promotion_letter_4 VARCHAR(255),
    promotion_letter_5 VARCHAR(255),
    first_degree VARCHAR(255),
    second_degree VARCHAR(255),
    third_degree VARCHAR(255),
    olevel_certificate VARCHAR(255),
    indigent_certificate VARCHAR(255),
    birth_certificate VARCHAR(255),
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
);
    