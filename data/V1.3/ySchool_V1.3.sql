SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `yschool` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `yschool` ;

-- -----------------------------------------------------
-- Table `yschool`.`school`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`school` (
  `idschool` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `address` VARCHAR(45) NULL ,
  `zone` VARCHAR(45) NULL ,
  `district` VARCHAR(45) NULL ,
  `province` VARCHAR(45) NULL ,
  PRIMARY KEY (`idschool`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`section`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`section` (
  `idsection` INT NOT NULL AUTO_INCREMENT ,
  `section_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idsection`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`classroom`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`classroom` (
  `idclass` INT NOT NULL AUTO_INCREMENT ,
  `year` INT NOT NULL ,
  `grade` INT NOT NULL ,
  `division` VARCHAR(45) NOT NULL ,
  `section_idsection` INT NULL ,
  PRIMARY KEY (`idclass`) ,
  INDEX `fk_classroom_section1_idx` (`section_idsection` ASC) ,
  CONSTRAINT `fk_classroom_section1`
    FOREIGN KEY (`section_idsection` )
    REFERENCES `yschool`.`section` (`idsection` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`subject`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`subject` (
  `idsubject` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `is_optional` TINYINT(1) NULL ,
  PRIMARY KEY (`idsubject`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`staff`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`staff` (
  `idstaff` INT NOT NULL AUTO_INCREMENT ,
  `staffID` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `full_name` VARCHAR(100) NULL ,
  `photo` BLOB NULL ,
  PRIMARY KEY (`idstaff`) ,
  UNIQUE INDEX `staffID_UNIQUE` (`staffID` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`student`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`student` (
  `idstudent` INT NOT NULL AUTO_INCREMENT ,
  `addmision_no` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `full_name` VARCHAR(45) NULL ,
  `name_wt_initial` VARCHAR(45) NULL ,
  `dob` DATE NULL ,
  `gender` VARCHAR(10) NULL ,
  `address` VARCHAR(400) NULL ,
  `photo` LONGBLOB NULL ,
  PRIMARY KEY (`idstudent`) ,
  UNIQUE INDEX `addmision_no_UNIQUE` (`addmision_no` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`exam_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`exam_type` (
  `idexam_type` INT NOT NULL AUTO_INCREMENT ,
  `type_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`idexam_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`classroom_subject`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`classroom_subject` (
  `idclassroom_subject` INT NOT NULL AUTO_INCREMENT ,
  `classroom_idclass` INT NOT NULL ,
  `subject_idsubject` INT NOT NULL ,
  PRIMARY KEY (`idclassroom_subject`) ,
  INDEX `fk_classroom_has_subject_subject1_idx` (`subject_idsubject` ASC) ,
  INDEX `fk_classroom_has_subject_classroom1_idx` (`classroom_idclass` ASC) ,
  CONSTRAINT `fk_classroom_has_subject_classroom1`
    FOREIGN KEY (`classroom_idclass` )
    REFERENCES `yschool`.`classroom` (`idclass` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_classroom_has_subject_subject1`
    FOREIGN KEY (`subject_idsubject` )
    REFERENCES `yschool`.`subject` (`idsubject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`exam`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`exam` (
  `idexam` INT NOT NULL AUTO_INCREMENT ,
  `date` DATE NOT NULL ,
  `term` INT NOT NULL ,
  `exam_type_idexam_type` INT NULL ,
  `classroom_subject_idclassroom_subject` INT NULL ,
  PRIMARY KEY (`idexam`) ,
  INDEX `fk_exam_exam_type1_idx` (`exam_type_idexam_type` ASC) ,
  INDEX `fk_exam_classroom_subject1_idx` (`classroom_subject_idclassroom_subject` ASC) ,
  CONSTRAINT `fk_exam_exam_type1`
    FOREIGN KEY (`exam_type_idexam_type` )
    REFERENCES `yschool`.`exam_type` (`idexam_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exam_classroom_subject1`
    FOREIGN KEY (`classroom_subject_idclassroom_subject` )
    REFERENCES `yschool`.`classroom_subject` (`idclassroom_subject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`marks`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`marks` (
  `idmarks` INT NOT NULL AUTO_INCREMENT ,
  `exam_idexam` INT NOT NULL ,
  `student_idstudent` INT NOT NULL ,
  `marks` FLOAT NULL ,
  PRIMARY KEY (`idmarks`) ,
  INDEX `fk_marks_exam1_idx` (`exam_idexam` ASC) ,
  INDEX `fk_marks_student1_idx` (`student_idstudent` ASC) ,
  CONSTRAINT `fk_marks_exam1`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `yschool`.`exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_marks_student1`
    FOREIGN KEY (`student_idstudent` )
    REFERENCES `yschool`.`student` (`idstudent` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`user_role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`user_role` (
  `iduser_role` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`iduser_role`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`user` (
  `iduser` INT NOT NULL AUTO_INCREMENT ,
  `user_name` VARCHAR(45) NOT NULL ,
  `email` VARCHAR(45) NULL ,
  `password` VARCHAR(45) NOT NULL ,
  `user_role_iduser_role` INT NOT NULL ,
  PRIMARY KEY (`iduser`) ,
  INDEX `fk_user_user_role_idx` (`user_role_iduser_role` ASC) ,
  CONSTRAINT `fk_user_user_role`
    FOREIGN KEY (`user_role_iduser_role` )
    REFERENCES `yschool`.`user_role` (`iduser_role` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`role` (
  `idrole` INT NOT NULL AUTO_INCREMENT ,
  `role_name` VARCHAR(45) NULL ,
  PRIMARY KEY (`idrole`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`results`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`results` (
  `idresults` INT NOT NULL ,
  `exam_idexam` INT NOT NULL ,
  `student_idstudent` INT NOT NULL ,
  `results` VARCHAR(5) NULL ,
  PRIMARY KEY (`idresults`) ,
  INDEX `fk_results_exam1_idx` (`exam_idexam` ASC) ,
  INDEX `fk_results_student1_idx` (`student_idstudent` ASC) ,
  CONSTRAINT `fk_results_exam1`
    FOREIGN KEY (`exam_idexam` )
    REFERENCES `yschool`.`exam` (`idexam` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_results_student1`
    FOREIGN KEY (`student_idstudent` )
    REFERENCES `yschool`.`student` (`idstudent` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`classroom_student`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`classroom_student` (
  `idclassroom_student` INT NOT NULL AUTO_INCREMENT ,
  `classroom_idclass` INT NOT NULL ,
  `student_idstudent` INT NOT NULL ,
  PRIMARY KEY (`idclassroom_student`) ,
  INDEX `fk_classroom_has_student_student1_idx` (`student_idstudent` ASC) ,
  INDEX `fk_classroom_has_student_classroom1_idx` (`classroom_idclass` ASC) ,
  CONSTRAINT `fk_classroom_has_student_classroom1`
    FOREIGN KEY (`classroom_idclass` )
    REFERENCES `yschool`.`classroom` (`idclass` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_classroom_has_student_student1`
    FOREIGN KEY (`student_idstudent` )
    REFERENCES `yschool`.`student` (`idstudent` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`student_classroom_subject`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`student_classroom_subject` (
  `idstudent_classroom_subject` INT NOT NULL AUTO_INCREMENT ,
  `classroom_student_idclassroom_student` INT NOT NULL ,
  `classroom_subject_idclassroom_subject` INT NOT NULL ,
  PRIMARY KEY (`idstudent_classroom_subject`) ,
  INDEX `fk_classroom_student_has_classroom_subject_classroom_subjec_idx` (`classroom_subject_idclassroom_subject` ASC) ,
  INDEX `fk_classroom_student_has_classroom_subject_classroom_studen_idx` (`classroom_student_idclassroom_student` ASC) ,
  CONSTRAINT `fk_classroom_student_has_classroom_subject_classroom_student1`
    FOREIGN KEY (`classroom_student_idclassroom_student` )
    REFERENCES `yschool`.`classroom_student` (`idclassroom_student` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_classroom_student_has_classroom_subject_classroom_subject1`
    FOREIGN KEY (`classroom_subject_idclassroom_subject` )
    REFERENCES `yschool`.`classroom_subject` (`idclassroom_subject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`staff_has_role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`staff_has_role` (
  `idstaff_has_role` INT NOT NULL AUTO_INCREMENT ,
  `staff_idstaff` INT NOT NULL ,
  `role_idrole` INT NOT NULL ,
  `start_date` DATE NOT NULL ,
  `end_date` DATE NULL ,
  PRIMARY KEY (`idstaff_has_role`) ,
  INDEX `fk_staff_has_role_role1_idx` (`role_idrole` ASC) ,
  INDEX `fk_staff_has_role_staff1_idx` (`staff_idstaff` ASC) ,
  CONSTRAINT `fk_staff_has_role_staff1`
    FOREIGN KEY (`staff_idstaff` )
    REFERENCES `yschool`.`staff` (`idstaff` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_staff_has_role_role1`
    FOREIGN KEY (`role_idrole` )
    REFERENCES `yschool`.`role` (`idrole` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`school_has_staff_has_role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`school_has_staff_has_role` (
  `school_idschool` INT NOT NULL ,
  `staff_has_role_idstaff_has_role` INT NOT NULL ,
  PRIMARY KEY (`school_idschool`, `staff_has_role_idstaff_has_role`) ,
  INDEX `fk_school_has_staff_has_role_staff_has_role1_idx` (`staff_has_role_idstaff_has_role` ASC) ,
  INDEX `fk_school_has_staff_has_role_school1_idx` (`school_idschool` ASC) ,
  CONSTRAINT `fk_school_has_staff_has_role_school1`
    FOREIGN KEY (`school_idschool` )
    REFERENCES `yschool`.`school` (`idschool` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_school_has_staff_has_role_staff_has_role1`
    FOREIGN KEY (`staff_has_role_idstaff_has_role` )
    REFERENCES `yschool`.`staff_has_role` (`idstaff_has_role` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`section_has_staff_has_role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`section_has_staff_has_role` (
  `section_idsection` INT NOT NULL ,
  `staff_has_role_idstaff_has_role` INT NOT NULL ,
  PRIMARY KEY (`section_idsection`, `staff_has_role_idstaff_has_role`) ,
  INDEX `fk_section_has_staff_has_role_staff_has_role1_idx` (`staff_has_role_idstaff_has_role` ASC) ,
  INDEX `fk_section_has_staff_has_role_section1_idx` (`section_idsection` ASC) ,
  CONSTRAINT `fk_section_has_staff_has_role_section1`
    FOREIGN KEY (`section_idsection` )
    REFERENCES `yschool`.`section` (`idsection` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_section_has_staff_has_role_staff_has_role1`
    FOREIGN KEY (`staff_has_role_idstaff_has_role` )
    REFERENCES `yschool`.`staff_has_role` (`idstaff_has_role` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`classroom_has_staff_has_role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`classroom_has_staff_has_role` (
  `classroom_idclass` INT NOT NULL ,
  `staff_has_role_idstaff_has_role` INT NOT NULL ,
  PRIMARY KEY (`classroom_idclass`, `staff_has_role_idstaff_has_role`) ,
  INDEX `fk_classroom_has_staff_has_role_staff_has_role1_idx` (`staff_has_role_idstaff_has_role` ASC) ,
  INDEX `fk_classroom_has_staff_has_role_classroom1_idx` (`classroom_idclass` ASC) ,
  CONSTRAINT `fk_classroom_has_staff_has_role_classroom1`
    FOREIGN KEY (`classroom_idclass` )
    REFERENCES `yschool`.`classroom` (`idclass` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_classroom_has_staff_has_role_staff_has_role1`
    FOREIGN KEY (`staff_has_role_idstaff_has_role` )
    REFERENCES `yschool`.`staff_has_role` (`idstaff_has_role` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yschool`.`classroom_subject_has_staff_has_role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `yschool`.`classroom_subject_has_staff_has_role` (
  `classroom_subject_idclassroom_subject` INT NOT NULL ,
  `staff_has_role_idstaff_has_role` INT NOT NULL ,
  PRIMARY KEY (`classroom_subject_idclassroom_subject`, `staff_has_role_idstaff_has_role`) ,
  INDEX `fk_classroom_subject_has_staff_has_role_staff_has_role1_idx` (`staff_has_role_idstaff_has_role` ASC) ,
  INDEX `fk_classroom_subject_has_staff_has_role_classroom_subject1_idx` (`classroom_subject_idclassroom_subject` ASC) ,
  CONSTRAINT `fk_classroom_subject_has_staff_has_role_classroom_subject1`
    FOREIGN KEY (`classroom_subject_idclassroom_subject` )
    REFERENCES `yschool`.`classroom_subject` (`idclassroom_subject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_classroom_subject_has_staff_has_role_staff_has_role1`
    FOREIGN KEY (`staff_has_role_idstaff_has_role` )
    REFERENCES `yschool`.`staff_has_role` (`idstaff_has_role` )
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
-- Data for table `yschool`.`exam_type`
-- -----------------------------------------------------
START TRANSACTION;
USE `yschool`;
INSERT INTO `yschool`.`exam_type` (`idexam_type`, `type_name`) VALUES (1, 'TermExam');
INSERT INTO `yschool`.`exam_type` (`idexam_type`, `type_name`) VALUES (2, 'CA');

COMMIT;

-- -----------------------------------------------------
-- Data for table `yschool`.`user_role`
-- -----------------------------------------------------
START TRANSACTION;
USE `yschool`;
INSERT INTO `yschool`.`user_role` (`iduser_role`, `name`) VALUES (1, 'admin');
INSERT INTO `yschool`.`user_role` (`iduser_role`, `name`) VALUES (2, 'manager');

COMMIT;
