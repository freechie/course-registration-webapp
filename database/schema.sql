CREATE DATABASE IF NOT EXISTS `course_registration`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `course_registration`;

CREATE TABLE `admins` (
  `admin_name` varchar(60) NOT NULL,
  `admin_username` varchar(60) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  PRIMARY KEY (`admin_name`),
  UNIQUE KEY `admins_username_unique` (`admin_username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `courses` (
  `cid` int NOT NULL,
  `cname` varchar(60) NOT NULL,
  `instructor` varchar(60) NOT NULL,
  `day` char(3) DEFAULT NULL,
  `time` char(2) DEFAULT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `students` (
  `sid` int NOT NULL,
  `sname` varchar(60) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `students_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `enrolled` (
  `sid` int NOT NULL,
  `cid` int NOT NULL,
  PRIMARY KEY (`sid`, `cid`),
  KEY `enrolled_course_index` (`cid`),
  CONSTRAINT `enrolled_course_fk` FOREIGN KEY (`cid`) REFERENCES `courses` (`cid`),
  CONSTRAINT `enrolled_student_fk` FOREIGN KEY (`sid`) REFERENCES `students` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fictional records only. Password values are intentionally unusable dummies.
INSERT INTO `admins` (`admin_name`, `admin_username`, `admin_password`) VALUES
  ('Casey Example', 'admin_example', 'DUMMY_NOT_A_LOGIN');

INSERT INTO `courses` (`cid`, `cname`, `instructor`, `day`, `time`) VALUES
  (101, 'Sample Computing', 'Dr. Rowan Vale', 'Mon', 'AM'),
  (102, 'Sample Design', 'Prof. Jules Hart', 'Wed', 'PM');

INSERT INTO `students` (`sid`, `sname`, `username`, `password`) VALUES
  (900001, 'Alex Example', 'student_example', 'DUMMY_NOT_A_LOGIN');

INSERT INTO `enrolled` (`sid`, `cid`) VALUES
  (900001, 101);
