-- MySQL Script generated for phpMyAdmin
-- Sun Nov 17 13:43:19 2024

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8mb4;
USE `mydb`;

-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`;

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NULL,
  `email` VARCHAR(45) NULL,
  `password` VARCHAR(255) NULL,
  `role` ENUM('student', 'instructor', 'admin') NULL,
  `is_banned` TINYINT(1) NULL,
  PRIMARY KEY (`user_id`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `courses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `courses`;

CREATE TABLE IF NOT EXISTS `courses` (
  `course_id` INT NOT NULL AUTO_INCREMENT,
  `course_name` VARCHAR(100) NULL,
  PRIMARY KEY (`course_id`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `user_courses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_courses`;

CREATE TABLE IF NOT EXISTS `user_courses` (
  `user_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  PRIMARY KEY (`user_id`, `course_id`),
  INDEX `fk_users_courses_courses_idx` (`course_id` ASC),
  INDEX `fk_users_courses_users_idx` (`user_id` ASC),
  CONSTRAINT `fk_users_courses_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_users_courses_courses`
    FOREIGN KEY (`course_id`)
    REFERENCES `courses` (`course_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `announcements`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `announcements`;

CREATE TABLE IF NOT EXISTS `announcements` (
  `announcement_id` INT NOT NULL AUTO_INCREMENT,
  `course_id` INT NOT NULL,
  `instructor_id` INT NOT NULL,
  `content` VARCHAR(500) NULL,
  `title` VARCHAR(100) NULL,
  `date` DATETIME NULL,
  PRIMARY KEY (`announcement_id`),
  INDEX `fk_announcements_courses_idx` (`course_id` ASC),
  CONSTRAINT `fk_announcements_courses`
    FOREIGN KEY (`course_id`)
    REFERENCES `courses` (`course_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `assignments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `assignments`;

CREATE TABLE IF NOT EXISTS `assignments` (
  `assignment_id` INT NOT NULL AUTO_INCREMENT,
  `course_id` INT NOT NULL,
  `instructor_id` INT NOT NULL,
  `title` VARCHAR(100) NULL,
  `description` TEXT NULL,
  `deadline` DATETIME NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`assignment_id`),
  INDEX `fk_assignments_courses_idx` (`course_id` ASC),
  CONSTRAINT `fk_assignments_courses`
    FOREIGN KEY (`course_id`)
    REFERENCES `courses` (`course_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `submissions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `submissions`;

CREATE TABLE IF NOT EXISTS `submissions` (
  `student_id` INT NOT NULL,
  `assignment_id` INT NOT NULL,
  `submission_date` DATETIME NULL,
  `content` TEXT NULL,
  PRIMARY KEY (`student_id`, `assignment_id`),
  INDEX `fk_submissions_assignments_idx` (`assignment_id` ASC),
  INDEX `fk_submissions_users_idx` (`student_id` ASC),
  CONSTRAINT `fk_submissions_users`
    FOREIGN KEY (`student_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_submissions_assignments`
    FOREIGN KEY (`assignment_id`)
    REFERENCES `assignments` (`assignment_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `comments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `comments`;

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` INT NOT NULL AUTO_INCREMENT,
  `student_id` INT NOT NULL,
  `assignment_id` INT NOT NULL,
  `content` VARCHAR(500) NULL,
  `date` DATETIME NULL,
  `private` TINYINT(1) NULL,
  PRIMARY KEY (`comment_id`),
  INDEX `fk_comments_users_idx` (`student_id` ASC),
  CONSTRAINT `fk_comments_users`
    FOREIGN KEY (`student_id`)
    REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
