SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `yschool` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `yschool` ;

-- -----------------------------------------------------
-- Table `yschool`.`School`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`School` (
  `idSchool` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `address` VARCHAR(45) NULL ,
  `zone` VARCHAR(45) NULL ,
  `district` VARCHAR(45) NULL ,
  `province` VARCHAR(45) NULL ,
  PRIMARY KEY (`idSchool`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Section`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Section` (
  `idSection` INT NOT NULL AUTO_INCREMENT ,
  `section_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idSection`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Classroom`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Classroom` (
  `idClass` INT NOT NULL AUTO_INCREMENT ,
  `year` INT NOT NULL ,
  `grade` INT NOT NULL ,
  `division` VARCHAR(45) NOT NULL ,
  `Section_idSection` INT NULL ,
  PRIMARY KEY (`idClass`) ,
  INDEX `fk_Classroom_Section1_idx` (`Section_idSection` ASC) ,
  CONSTRAINT `fk_Classroom_Section1`
    FOREIGN KEY (`Section_idSection` )
    REFERENCES `yschool`.`Section` (`idSection` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Subject`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Subject` (
  `idSubject` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `is_optional` TINYINT(1) NULL ,
  PRIMARY KEY (`idSubject`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Staff`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Staff` (
  `idStaff` INT NOT NULL AUTO_INCREMENT ,
  `staffID` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `full_name` VARCHAR(100) NULL ,
  `photo` BLOB NULL ,
  PRIMARY KEY (`idStaff`) ,
  UNIQUE INDEX `StaffID_UNIQUE` (`staffID` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Student`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Student` (
  `idStudent` INT NOT NULL AUTO_INCREMENT ,
  `addmision_no` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `full_name` VARCHAR(45) NULL ,
  `name_wt_initial` VARCHAR(45) NULL ,
  `dob` DATE NULL ,
  `gender` VARCHAR(10) NULL ,
  `address` VARCHAR(400) NULL ,
  `photo` LONGBLOB NULL ,
  PRIMARY KEY (`idStudent`) ,
  UNIQUE INDEX `addmision_no_UNIQUE` (`addmision_no` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Class_Subject`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Class_Subject` (
  `idClass_Subject` INT NOT NULL AUTO_INCREMENT ,
  `Class_idClass` INT NOT NULL ,
  `Subject_idSubject` INT NOT NULL ,
  PRIMARY KEY (`idClass_Subject`) ,
  INDEX `fk_Class_has_Subject_Subject1_idx` (`Subject_idSubject` ASC) ,
  INDEX `fk_Class_has_Subject_Class1_idx` (`Class_idClass` ASC) ,
  CONSTRAINT `fk_Class_has_Subject_Class1`
    FOREIGN KEY (`Class_idClass` )
    REFERENCES `yschool`.`Classroom` (`idClass` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Class_has_Subject_Subject1`
    FOREIGN KEY (`Subject_idSubject` )
    REFERENCES `yschool`.`Subject` (`idSubject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Exam_Type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Exam_Type` (
  `idExam_Type` INT NOT NULL AUTO_INCREMENT ,
  `type_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`idExam_Type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Exam`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Exam` (
  `idExam` INT NOT NULL AUTO_INCREMENT ,
  `date` DATE NOT NULL ,
  `term` INT NOT NULL ,
  `Class_Subject_idClass_Subject` INT NULL ,
  `Exam_Type_idExam_Type` INT NULL ,
  PRIMARY KEY (`idExam`) ,
  INDEX `fk_Exam_Class_Subject1_idx` (`Class_Subject_idClass_Subject` ASC) ,
  INDEX `fk_Exam_Exam_Type1_idx` (`Exam_Type_idExam_Type` ASC) ,
  CONSTRAINT `fk_Exam_Class_Subject1`
    FOREIGN KEY (`Class_Subject_idClass_Subject` )
    REFERENCES `yschool`.`Class_Subject` (`idClass_Subject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Exam_Exam_Type1`
    FOREIGN KEY (`Exam_Type_idExam_Type` )
    REFERENCES `yschool`.`Exam_Type` (`idExam_Type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Marks`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Marks` (
  `idMarks` INT NOT NULL AUTO_INCREMENT ,
  `Exam_idExam` INT NOT NULL ,
  `Student_idStudent` INT NOT NULL ,
  `marks` FLOAT NULL ,
  PRIMARY KEY (`idMarks`) ,
  INDEX `fk_Marks_Exam1_idx` (`Exam_idExam` ASC) ,
  INDEX `fk_Marks_Student1_idx` (`Student_idStudent` ASC) ,
  CONSTRAINT `fk_Marks_Exam1`
    FOREIGN KEY (`Exam_idExam` )
    REFERENCES `yschool`.`Exam` (`idExam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Marks_Student1`
    FOREIGN KEY (`Student_idStudent` )
    REFERENCES `yschool`.`Student` (`idStudent` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Class_Student`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Class_Student` (
  `idClass_Student` INT NOT NULL AUTO_INCREMENT ,
  `Class_idClass` INT NOT NULL ,
  `Student_idStudent` INT NOT NULL ,
  INDEX `fk_Class_has_Student_Student1_idx` (`Student_idStudent` ASC) ,
  INDEX `fk_Class_has_Student_Class1_idx` (`Class_idClass` ASC) ,
  PRIMARY KEY (`idClass_Student`) ,
  CONSTRAINT `fk_Class_has_Student_Class1`
    FOREIGN KEY (`Class_idClass` )
    REFERENCES `yschool`.`Classroom` (`idClass` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Class_has_Student_Student1`
    FOREIGN KEY (`Student_idStudent` )
    REFERENCES `yschool`.`Student` (`idStudent` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`User_Role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`User_Role` (
  `idUser_Role` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`idUser_Role`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`user` (
  `iduser` INT NOT NULL AUTO_INCREMENT ,
  `user_name` VARCHAR(45) NOT NULL ,
  `email` VARCHAR(45) NULL ,
  `password` VARCHAR(45) NOT NULL ,
  `User_Role_idUser_Role` INT NOT NULL ,
  PRIMARY KEY (`iduser`) ,
  INDEX `fk_user_User_Role1_idx` (`User_Role_idUser_Role` ASC) ,
  CONSTRAINT `fk_user_User_Role1`
    FOREIGN KEY (`User_Role_idUser_Role` )
    REFERENCES `yschool`.`User_Role` (`idUser_Role` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Student_Class_Subject`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Student_Class_Subject` (
  `idStudent_Class_Subject` INT NOT NULL AUTO_INCREMENT ,
  `Class_Subject_idClass_Subject` INT NOT NULL ,
  `Class_Student_idClass_Student` INT NOT NULL ,
  PRIMARY KEY (`idStudent_Class_Subject`) ,
  INDEX `fk_Student_Class_Subject_Class_Subject1_idx` (`Class_Subject_idClass_Subject` ASC) ,
  INDEX `fk_Student_Class_Subject_Class_Student1_idx` (`Class_Student_idClass_Student` ASC) ,
  CONSTRAINT `fk_Student_Class_Subject_Class_Subject1`
    FOREIGN KEY (`Class_Subject_idClass_Subject` )
    REFERENCES `yschool`.`Class_Subject` (`idClass_Subject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Student_Class_Subject_Class_Student1`
    FOREIGN KEY (`Class_Student_idClass_Student` )
    REFERENCES `yschool`.`Class_Student` (`idClass_Student` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Role` (
  `idRole` INT NOT NULL AUTO_INCREMENT ,
  `role_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idRole`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Staff_has_Role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Staff_has_Role` (
  `idStaff_has_role` INT NOT NULL AUTO_INCREMENT ,
  `Staff_idStaff` INT NOT NULL ,
  `Role_idRole` INT NOT NULL ,
  `start_date` DATE NOT NULL ,
  `end_date` DATE NULL ,
  PRIMARY KEY (`idStaff_has_role`) ,
  INDEX `fk_Staff_has_Role_Role1_idx` (`Role_idRole` ASC) ,
  INDEX `fk_Staff_has_Role_Staff1_idx` (`Staff_idStaff` ASC) ,
  CONSTRAINT `fk_Staff_has_Role_Staff1`
    FOREIGN KEY (`Staff_idStaff` )
    REFERENCES `yschool`.`Staff` (`idStaff` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Staff_has_Role_Role1`
    FOREIGN KEY (`Role_idRole` )
    REFERENCES `yschool`.`Role` (`idRole` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Class_Subject_has_Staff_has_Role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Class_Subject_has_Staff_has_Role` (
  `Class_Subject_idClass_Subject` INT NOT NULL ,
  `Staff_has_Role_idStaff_has_role` INT NOT NULL ,
  PRIMARY KEY (`Class_Subject_idClass_Subject`, `Staff_has_Role_idStaff_has_role`) ,
  INDEX `fk_Class_Subject_has_Staff_has_Role_Staff_has_Role1_idx` (`Staff_has_Role_idStaff_has_role` ASC) ,
  INDEX `fk_Class_Subject_has_Staff_has_Role_Class_Subject1_idx` (`Class_Subject_idClass_Subject` ASC) ,
  CONSTRAINT `fk_Class_Subject_has_Staff_has_Role_Class_Subject1`
    FOREIGN KEY (`Class_Subject_idClass_Subject` )
    REFERENCES `yschool`.`Class_Subject` (`idClass_Subject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Class_Subject_has_Staff_has_Role_Staff_has_Role1`
    FOREIGN KEY (`Staff_has_Role_idStaff_has_role` )
    REFERENCES `yschool`.`Staff_has_Role` (`idStaff_has_role` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Classroom_has_Staff_has_Role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Classroom_has_Staff_has_Role` (
  `Classroom_idClass` INT NOT NULL ,
  `Staff_has_Role_idStaff_has_role` INT NOT NULL ,
  PRIMARY KEY (`Classroom_idClass`, `Staff_has_Role_idStaff_has_role`) ,
  INDEX `fk_Classroom_has_Staff_has_Role_Staff_has_Role1_idx` (`Staff_has_Role_idStaff_has_role` ASC) ,
  INDEX `fk_Classroom_has_Staff_has_Role_Classroom1_idx` (`Classroom_idClass` ASC) ,
  CONSTRAINT `fk_Classroom_has_Staff_has_Role_Classroom1`
    FOREIGN KEY (`Classroom_idClass` )
    REFERENCES `yschool`.`Classroom` (`idClass` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Classroom_has_Staff_has_Role_Staff_has_Role1`
    FOREIGN KEY (`Staff_has_Role_idStaff_has_role` )
    REFERENCES `yschool`.`Staff_has_Role` (`idStaff_has_role` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`School_has_Staff_has_Role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`School_has_Staff_has_Role` (
  `School_idSchool` INT NOT NULL ,
  `Staff_has_Role_idStaff_has_role` INT NOT NULL ,
  PRIMARY KEY (`School_idSchool`, `Staff_has_Role_idStaff_has_role`) ,
  INDEX `fk_School_has_Staff_has_Role_Staff_has_Role1_idx` (`Staff_has_Role_idStaff_has_role` ASC) ,
  INDEX `fk_School_has_Staff_has_Role_School1_idx` (`School_idSchool` ASC) ,
  CONSTRAINT `fk_School_has_Staff_has_Role_School1`
    FOREIGN KEY (`School_idSchool` )
    REFERENCES `yschool`.`School` (`idSchool` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_School_has_Staff_has_Role_Staff_has_Role1`
    FOREIGN KEY (`Staff_has_Role_idStaff_has_role` )
    REFERENCES `yschool`.`Staff_has_Role` (`idStaff_has_role` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Section_has_Staff_has_Role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Section_has_Staff_has_Role` (
  `Section_idSection` INT NOT NULL ,
  `Staff_has_Role_idStaff_has_role` INT NOT NULL ,
  PRIMARY KEY (`Section_idSection`, `Staff_has_Role_idStaff_has_role`) ,
  INDEX `fk_Section_has_Staff_has_Role_Staff_has_Role1_idx` (`Staff_has_Role_idStaff_has_role` ASC) ,
  INDEX `fk_Section_has_Staff_has_Role_Section1_idx` (`Section_idSection` ASC) ,
  CONSTRAINT `fk_Section_has_Staff_has_Role_Section1`
    FOREIGN KEY (`Section_idSection` )
    REFERENCES `yschool`.`Section` (`idSection` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Section_has_Staff_has_Role_Staff_has_Role1`
    FOREIGN KEY (`Staff_has_Role_idStaff_has_role` )
    REFERENCES `yschool`.`Staff_has_Role` (`idStaff_has_role` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`Results`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`Results` (
  `idResults` INT NOT NULL ,
  `Exam_idExam` INT NOT NULL ,
  `Student_idStudent` INT NOT NULL ,
  `marksORresults` VARCHAR(5) NULL ,
  PRIMARY KEY (`idResults`) ,
  INDEX `fk_Results_Exam1_idx` (`Exam_idExam` ASC) ,
  INDEX `fk_Results_Student1_idx` (`Student_idStudent` ASC) ,
  CONSTRAINT `fk_Results_Exam1`
    FOREIGN KEY (`Exam_idExam` )
    REFERENCES `yschool`.`Exam` (`idExam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Results_Student1`
    FOREIGN KEY (`Student_idStudent` )
    REFERENCES `yschool`.`Student` (`idStudent` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `yschool` ;

CREATE USER 'yschool_user' IDENTIFIED BY 'yschool@123';

GRANT ALL ON `yschool`.* TO 'yschool_user';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `yschool`.`Exam_Type`
-- -----------------------------------------------------
START TRANSACTION;
USE `yschool`;
INSERT INTO `yschool`.`Exam_Type` (`idExam_Type`, `type_name`) VALUES (1, 'TermExam');
INSERT INTO `yschool`.`Exam_Type` (`idExam_Type`, `type_name`) VALUES (2, 'CA');

COMMIT;

-- -----------------------------------------------------
-- Data for table `yschool`.`User_Role`
-- -----------------------------------------------------
START TRANSACTION;
USE `yschool`;
INSERT INTO `yschool`.`User_Role` (`idUser_Role`, `name`) VALUES (1, 'admin');
INSERT INTO `yschool`.`User_Role` (`idUser_Role`, `name`) VALUES (2, 'manager');

COMMIT;
