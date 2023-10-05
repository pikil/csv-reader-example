-- Creating a new database if it doesn't exist
CREATE DATABASE IF NOT EXISTS sample_db;

USE sample_db;

-- Creating tables to store employees information
CREATE TABLE `employees` (
  `record_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` VARCHAR(15) NOT NULL DEFAULT '',
  `company_id` VARCHAR(75) NOT NULL DEFAULT '',
  `employee_name` VARCHAR(75) NOT NULL DEFAULT '',
  `email` VARCHAR(75) NOT NULL DEFAULT '',
  `salary` INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`record_id`));

CREATE TABLE `companies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(75) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`));

ALTER TABLE `sample_db`.`companies` 
ADD UNIQUE INDEX `name` (`name` ASC) VISIBLE;
;

