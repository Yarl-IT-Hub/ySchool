SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `yschool_lite` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `yschool_lite` ;

-- -----------------------------------------------------
-- Table `yschool_lite`.`Staff`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool_lite`.`Staff` (
  `idStaff` INT NOT NULL ,
  `name` VARCHAR(45) NULL ,
  `type` INT NULL ,
  PRIMARY KEY (`idStaff`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool_lite`.`School`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool_lite`.`School` (
  `idSchool` INT NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `principal` INT NULL ,
  `address` VARCHAR(45) NULL ,
  `zone` VARCHAR(45) NULL ,
  `district` VARCHAR(45) NULL ,
  `province` VARCHAR(45) NULL ,
  PRIMARY KEY (`idSchool`) ,
  INDEX `fk_principalf_idx` USING BTREE (`principal` ASC) ,
  CONSTRAINT `fk_principal`
    FOREIGN KEY (`principal` )
    REFERENCES `yschool_lite`.`Staff` (`idStaff` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool_lite`.`Classroom`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool_lite`.`Classroom` (
  `idClass` INT NOT NULL ,
  `year` YEAR NOT NULL ,
  `grade` INT NOT NULL ,
  `division` CHAR NOT NULL ,
  `class_teacher` INT NOT NULL ,
  PRIMARY KEY (`idClass`) ,
  INDEX `fk_Class_Staff1_idx` (`class_teacher` ASC) ,
  CONSTRAINT `fk_Class_Staff1`
    FOREIGN KEY (`class_teacher` )
    REFERENCES `yschool_lite`.`Staff` (`idStaff` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool_lite`.`Subject`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool_lite`.`Subject` (
  `idSubject` INT NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `isOptional` BINARY NULL ,
  PRIMARY KEY (`idSubject`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool_lite`.`Student`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool_lite`.`Student` (
  `idStudent` INT NOT NULL ,
  `addmision_no` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `full_name` VARCHAR(45) NULL ,
  `name_wt_initial` VARCHAR(45) NULL ,
  `dob` DATE NULL ,
  `gender` CHAR NULL ,
  `address` VARCHAR(400) NULL ,
  `photo` LONGBLOB NULL ,
  PRIMARY KEY (`idStudent`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool_lite`.`Class_Subject`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool_lite`.`Class_Subject` (
  `idClass_Subject` INT NOT NULL ,
  `Class_idClass` INT NOT NULL ,
  `Subject_idSubject` INT NOT NULL ,
  PRIMARY KEY (`idClass_Subject`) ,
  INDEX `fk_Class_has_Subject_Subject1_idx` (`Subject_idSubject` ASC) ,
  INDEX `fk_Class_has_Subject_Class1_idx` (`Class_idClass` ASC) ,
  CONSTRAINT `fk_Class_has_Subject_Class1`
    FOREIGN KEY (`Class_idClass` )
    REFERENCES `yschool_lite`.`Classroom` (`idClass` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Class_has_Subject_Subject1`
    FOREIGN KEY (`Subject_idSubject` )
    REFERENCES `yschool_lite`.`Subject` (`idSubject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool_lite`.`Exam`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool_lite`.`Exam` (
  `idExam` INT NOT NULL ,
  `type` INT NOT NULL ,
  `date` DATE NOT NULL ,
  `term` INT NOT NULL ,
  `year` YEAR NOT NULL ,
  `Class_Subject_idClass_Subject` INT NOT NULL ,
  PRIMARY KEY (`idExam`) ,
  INDEX `fk_Exam_Class_Subject1_idx` (`Class_Subject_idClass_Subject` ASC) ,
  CONSTRAINT `fk_Exam_Class_Subject1`
    FOREIGN KEY (`Class_Subject_idClass_Subject` )
    REFERENCES `yschool_lite`.`Class_Subject` (`idClass_Subject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool_lite`.`Marks`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool_lite`.`Marks` (
  `idMarks` INT NOT NULL ,
  `Exam_idExam` INT NOT NULL ,
  `Student_idStudent` INT NOT NULL ,
  `marks` FLOAT NULL ,
  PRIMARY KEY (`idMarks`) ,
  INDEX `fk_Marks_Exam1_idx` (`Exam_idExam` ASC) ,
  INDEX `fk_Marks_Student1_idx` (`Student_idStudent` ASC) ,
  CONSTRAINT `fk_Marks_Exam1`
    FOREIGN KEY (`Exam_idExam` )
    REFERENCES `yschool_lite`.`Exam` (`idExam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Marks_Student1`
    FOREIGN KEY (`Student_idStudent` )
    REFERENCES `yschool_lite`.`Student` (`idStudent` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool_lite`.`Class_Student`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool_lite`.`Class_Student` (
  `Class_idClass` INT NOT NULL ,
  `Student_idStudent` INT NOT NULL ,
  PRIMARY KEY (`Class_idClass`, `Student_idStudent`) ,
  INDEX `fk_Class_has_Student_Student1_idx` (`Student_idStudent` ASC) ,
  INDEX `fk_Class_has_Student_Class1_idx` (`Class_idClass` ASC) ,
  CONSTRAINT `fk_Class_has_Student_Class1`
    FOREIGN KEY (`Class_idClass` )
    REFERENCES `yschool_lite`.`Classroom` (`idClass` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Class_has_Student_Student1`
    FOREIGN KEY (`Student_idStudent` )
    REFERENCES `yschool_lite`.`Student` (`idStudent` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool_lite`.`Student_has_OptionalSubject`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool_lite`.`Student_has_OptionalSubject` (
  `Student_idStudent` INT NOT NULL ,
  `Subject_idSubject` INT NOT NULL ,
  PRIMARY KEY (`Student_idStudent`, `Subject_idSubject`) ,
  INDEX `fk_Student_has_Subject_Subject1_idx` (`Subject_idSubject` ASC) ,
  INDEX `fk_Student_has_Subject_Student1_idx` (`Student_idStudent` ASC) ,
  CONSTRAINT `fk_Student_has_Subject_Student1`
    FOREIGN KEY (`Student_idStudent` )
    REFERENCES `yschool_lite`.`Student` (`idStudent` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Student_has_Subject_Subject1`
    FOREIGN KEY (`Subject_idSubject` )
    REFERENCES `yschool_lite`.`Subject` (`idSubject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool_lite`.`Class_Staff`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool_lite`.`Class_Staff` (
  `Class_idClass` INT NOT NULL ,
  `Staff_idStaff` INT NOT NULL ,
  `Subject_idSubject` INT NOT NULL ,
  PRIMARY KEY (`Class_idClass`, `Staff_idStaff`, `Subject_idSubject`) ,
  INDEX `fk_Class_has_Staff_Staff1_idx` (`Staff_idStaff` ASC) ,
  INDEX `fk_Class_has_Staff_Class1_idx` (`Class_idClass` ASC) ,
  INDEX `fk_Class_has_Staff_Subject1_idx` (`Subject_idSubject` ASC) ,
  CONSTRAINT `fk_Class_has_Staff_Class1`
    FOREIGN KEY (`Class_idClass` )
    REFERENCES `yschool_lite`.`Classroom` (`idClass` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Class_has_Staff_Staff1`
    FOREIGN KEY (`Staff_idStaff` )
    REFERENCES `yschool_lite`.`Staff` (`idStaff` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Class_has_Staff_Subject1`
    FOREIGN KEY (`Subject_idSubject` )
    REFERENCES `yschool_lite`.`Subject` (`idSubject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool_lite`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool_lite`.`user` (
  `iduser` INT NOT NULL ,
  `user_name` VARCHAR(45) NOT NULL ,
  `user_role` TINYINT NOT NULL ,
  `email` VARCHAR(45) NULL ,
  `password` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`iduser`) )
ENGINE = InnoDB;

USE `yschool_lite` ;

CREATE USER 'yschool_user' IDENTIFIED BY 'yschool@123';

GRANT ALL ON `yschool_lite`.* TO 'yschool_user';
GRANT SELECT ON TABLE `yschool_lite`.* TO 'yschool_user';
GRANT SELECT, INSERT, TRIGGER ON TABLE `yschool_lite`.* TO 'yschool_user';
GRANT SELECT, INSERT, TRIGGER, UPDATE, DELETE ON TABLE `yschool_lite`.* TO 'yschool_user';
GRANT EXECUTE ON ROUTINE `yschool_lite`.* TO 'yschool_user';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
