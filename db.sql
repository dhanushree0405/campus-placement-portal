-- Enhanced DB for Campus Placement Portal v2
CREATE DATABASE IF NOT EXISTS campus_placement_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE campus_placement_v2;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150), email VARCHAR(200) UNIQUE, password VARCHAR(255), created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL, email VARCHAR(255) UNIQUE, password VARCHAR(255), created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  resume VARCHAR(255) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS jobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  company_id INT DEFAULT NULL,
  location VARCHAR(255),
  description TEXT,
  posted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  job_id INT NOT NULL,
  status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
  applied_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_type VARCHAR(50),
  user_id INT,
  action VARCHAR(255),
  meta TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- sample admin (password: admin123) - replace hash after import if desired
INSERT INTO admins (name,email,password) VALUES ('Admin','admin@example.com','""');

-- sample company
INSERT INTO companies (name,email,password) VALUES ('Acme Corp','acme@example.com','""');

-- sample jobs
INSERT INTO jobs (title,company_id,location,description) VALUES ('Software Intern',1,'Remote','Work on core product'), ('Data Analyst',1,'Bangalore','Analyze datasets');
