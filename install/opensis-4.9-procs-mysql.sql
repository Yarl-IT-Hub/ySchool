DROP FUNCTION IF EXISTS `SET_CLASS_RANK_MP`;
DELIMITER $$
CREATE FUNCTION `SET_CLASS_RANK_MP`(
	mp_id int
) RETURNS int(11)
BEGIN

DECLARE done INT DEFAULT 0;
DECLARE marking_period_id INT;
DECLARE student_id INT;
DECLARE rank NUMERIC;

declare cur1 cursor for
select
  mp.marking_period_id,
  sgm.student_id,
 (select count(*)+1 
   from STUDENT_MP_STATS sgm3
   where sgm3.cum_weighted_factor > sgm.cum_weighted_factor
     and sgm3.marking_period_id = mp.marking_period_id 
     and sgm3.student_id in (select distinct sgm2.student_id 
                            from STUDENT_MP_STATS sgm2, STUDENT_ENROLLMENT se2
                            where sgm2.student_id = se2.student_id 
                              and sgm2.marking_period_id = mp.marking_period_id 
               		      and se2.grade_id = se.grade_id
                              and se2.syear = se.syear)
  ) as rank
  from STUDENT_ENROLLMENT se, STUDENT_MP_STATS sgm, MARKING_PERIODS mp
  where se.student_id = sgm.student_id
    and sgm.marking_period_id = mp.marking_period_id
    and mp.marking_period_id = mp_id
    and se.syear = mp.syear
    and not sgm.cum_weighted_factor is null
  order by grade_id, rank;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

open cur1;
fetch cur1 into marking_period_id,student_id,rank;

while not done DO
	update STUDENT_MP_STATS
	  set
	    cum_rank = rank
	where STUDENT_MP_STATS.marking_period_id = marking_period_id
	  and STUDENT_MP_STATS.student_id = student_id;
	fetch cur1 into marking_period_id,student_id,rank;
END WHILE;
CLOSE cur1;

RETURN 1;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS `CALC_CUM_GPA_MP`;
DELIMITER $$
CREATE FUNCTION `CALC_CUM_GPA_MP`(
mp_id int
) RETURNS int(11)
BEGIN

  CREATE TEMPORARY TABLE tmp(
    student_id int,
    sum_weighted_factors decimal(10,6),
    count_weighted_factors int,
    sum_unweighted_factors decimal(10,6),
    count_unweighted_factors int,
    grade_level_short varchar(10)
  );

  INSERT INTO tmp(student_id,sum_weighted_factors,count_weighted_factors,
    sum_unweighted_factors, count_unweighted_factors,grade_level_short)
  SELECT
    srcg.student_id,
    SUM(srcg.weighted_gp/s.reporting_gp_scale) AS sum_weighted_factors, 
    COUNT(*) AS count_weighted_factors,                        
    SUM(srcg.unweighted_gp/srcg.gp_scale) AS sum_unweighted_factors, 
    COUNT(*) AS count_unweighted_factors,                        
    eg.short_name
  FROM STUDENT_REPORT_CARD_GRADES srcg
  INNER JOIN SCHOOLS s ON s.id=srcg.school_id
  LEFT JOIN ENROLL_GRADE eg on eg.student_id=srcg.student_id AND eg.syear=srcg.syear AND eg.school_id=srcg.school_id
  WHERE srcg.marking_period_id=mp_id AND srcg.gp_scale<>0 AND srcg.marking_period_id NOT LIKE 'E%'
  GROUP BY srcg.student_id,eg.short_name;

  UPDATE STUDENT_MP_STATS sms
    INNER JOIN tmp t on t.student_id=sms.student_id
  SET
    sms.sum_weighted_factors=t.sum_weighted_factors,
    sms.count_weighted_factors=t.count_weighted_factors,
    sms.sum_unweighted_factors=t.sum_unweighted_factors,
    sms.count_unweighted_factors=t.count_unweighted_factors
  WHERE sms.marking_period_id=mp_id;

  INSERT INTO STUDENT_MP_STATS(student_id,marking_period_id,sum_weighted_factors,count_weighted_factors,
    sum_unweighted_factors,count_unweighted_factors,grade_level_short)
  SELECT
      t.student_id,
      mp_id,
      t.sum_weighted_factors, 
      t.count_weighted_factors,                        
      t.sum_unweighted_factors, 
      t.count_unweighted_factors,                        
      t.grade_level_short
    FROM tmp t
    LEFT JOIN STUDENT_MP_STATS sms ON sms.student_id=t.student_id AND sms.marking_period_id=mp_id
    WHERE sms.student_id IS NULL;
 
  UPDATE STUDENT_MP_STATS g
    INNER JOIN (
	SELECT s.student_id,
		SUM(s.weighted_gp/sc.reporting_gp_scale)/COUNT(*) AS cum_weighted_factor,
		SUM(s.unweighted_gp/s.gp_scale)/COUNT(*) AS cum_unweighted_factor
	FROM STUDENT_REPORT_CARD_GRADES s
	INNER JOIN SCHOOLS sc ON sc.id=s.school_id
	LEFT JOIN COURSE_PERIODS p ON p.course_period_id=s.course_period_id
	WHERE p.marking_period_id IS NULL OR p.marking_period_id=s.marking_period_id
	GROUP BY student_id) gg ON gg.student_id=g.student_id
    SET g.cum_unweighted_factor=gg.cum_unweighted_factor, g.cum_weighted_factor=gg.cum_weighted_factor;

  RETURN 1;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS `CALC_GPA_MP`;
DELIMITER $$
CREATE FUNCTION `CALC_GPA_MP`(
	s_id int,
	mp_id int
) RETURNS int(11)
BEGIN
  SELECT
    SUM(srcg.weighted_gp/s.reporting_gp_scale) AS sum_weighted_factors, 
    COUNT(*) AS count_weighted_factors,                        
    SUM(srcg.unweighted_gp/srcg.gp_scale) AS sum_unweighted_factors, 
    COUNT(*) AS count_unweighted_factors,                        
    eg.short_name
  INTO
    @sum_weighted_factors,
    @count_weighted_factors,
    @sum_unweighted_factors,
    @count_unweighted_factors,
    @grade_level_short
  FROM STUDENT_REPORT_CARD_GRADES srcg
  INNER JOIN SCHOOLS s ON s.id=srcg.school_id
  LEFT JOIN ENROLL_GRADE eg on eg.student_id=srcg.student_id AND eg.syear=srcg.syear AND eg.school_id=srcg.school_id
  WHERE srcg.marking_period_id=mp_id AND srcg.student_id=s_id AND srcg.gp_scale<>0 AND srcg.marking_period_id NOT LIKE 'E%'
  GROUP BY srcg.student_id,eg.short_name;

  IF EXISTS(SELECT NULL FROM STUDENT_MP_STATS WHERE marking_period_id=mp_id AND student_id=s_id) THEN
    UPDATE STUDENT_MP_STATS
    SET
      sum_weighted_factors=@sum_weighted_factors,
      count_weighted_factors=@count_weighted_factors,
      sum_unweighted_factors=@sum_unweighted_factors,
      count_unweighted_factors=@count_unweighted_factors
    WHERE marking_period_id=mp_id AND student_id=s_id;
  ELSE
    INSERT INTO STUDENT_MP_STATS(student_id,marking_period_id,sum_weighted_factors,count_weighted_factors,
        sum_unweighted_factors,count_unweighted_factors,grade_level_short)
      VALUES(s_id,mp_id,@sum_weighted_factors,@count_weighted_factors,@sum_unweighted_factors,
        @count_unweighted_factors,@grade_level_short);
  END IF;

  UPDATE STUDENT_MP_STATS g
    INNER JOIN (
	SELECT s.student_id,
		SUM(s.weighted_gp/sc.reporting_gp_scale)/COUNT(*) AS cum_weighted_factor,
		SUM(s.unweighted_gp/s.gp_scale)/COUNT(*) AS cum_unweighted_factor
	FROM STUDENT_REPORT_CARD_GRADES s
	INNER JOIN SCHOOLS sc ON sc.id=s.school_id
	LEFT JOIN COURSE_PERIODS p ON p.course_period_id=s.course_period_id
	WHERE p.marking_period_id IS NULL OR p.marking_period_id=s.marking_period_id
	GROUP BY student_id) gg ON gg.student_id=g.student_id
    SET g.cum_unweighted_factor=gg.cum_unweighted_factor, g.cum_weighted_factor=gg.cum_weighted_factor
    WHERE g.student_id=s_id;

  RETURN 0;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS `CREDIT`;
DELIMITER $$
CREATE FUNCTION `CREDIT`(
 	cp_id int,
 	mp_id int
 ) RETURNS decimal(10,3)
BEGIN
  SELECT credits,marking_period_id,mp INTO @credits,@marking_period_id,@mp FROM COURSE_PERIODS WHERE course_period_id=cp_id;
  SELECT mp_type INTO @mp_type FROM MARKING_PERIODS WHERE marking_period_id=mp_id;
 
  IF @marking_period_id=mp_id THEN
    RETURN @credits;
   ELSEIF @mp='FY' AND @mp_type='semester' THEN
     SELECT COUNT(*) INTO @val FROM MARKING_PERIODS WHERE parent_id=@marking_period_id GROUP BY parent_id;
   ELSEIF @mp = 'FY' AND @mp_type = 'quarter' THEN
     SELECT count(*) into @val FROM MARKING_PERIODS WHERE grandparent_id=@marking_period_id GROUP BY grandparent_id;
   ELSEIF @mp = 'SEM' AND @mp_type = 'quarter' THEN
     SELECT count(*) into @val FROM MARKING_PERIODS WHERE parent_id=@marking_period_id GROUP BY parent_id;
   ELSE
     RETURN 0;
   END IF;
   IF @val > 0 THEN
     RETURN @credits/@val;
   END IF;
   RETURN 0;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS `STUDENT_DISABLE`;
DELIMITER $$
CREATE FUNCTION `STUDENT_DISABLE`(
stu_id int
) RETURNS int(1)
BEGIN
UPDATE STUDENTS set is_disable ='Y' where (select end_date from STUDENT_ENROLLMENT where  student_id=stu_id ORDER BY id DESC LIMIT 1) IS NOT NULL AND (select end_date from STUDENT_ENROLLMENT where  student_id=stu_id ORDER BY id DESC LIMIT 1)< CURDATE() AND  student_id=stu_id;
RETURN 1;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `SEAT_COUNT`;
DELIMITER $$
CREATE PROCEDURE `SEAT_COUNT`() 
BEGIN
UPDATE COURSE_PERIODS SET filled_seats=filled_seats-1 WHERE COURSE_PERIOD_ID IN (SELECT COURSE_PERIOD_ID FROM SCHEDULE WHERE end_date IS NOT NULL AND end_date < CURDATE() AND dropped='N');
UPDATE SCHEDULE SET dropped='Y' WHERE end_date IS NOT NULL AND end_date < CURDATE() AND dropped='N';
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `SEAT_FILL`;
DELIMITER $$
CREATE PROCEDURE `SEAT_FILL`() 
BEGIN
UPDATE COURSE_PERIODS SET filled_seats=filled_seats+1 WHERE COURSE_PERIOD_ID IN (SELECT COURSE_PERIOD_ID FROM SCHEDULE WHERE dropped='Y' AND ( end_date IS NULL OR end_date >= CURDATE()));
UPDATE SCHEDULE SET dropped='N' WHERE dropped='Y' AND ( end_date IS NULL OR end_date >= CURDATE()) ;
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `td_student_report_card_grades`;
DELIMITER $$
CREATE TRIGGER `td_student_report_card_grades`
    AFTER DELETE ON STUDENT_REPORT_CARD_GRADES
    FOR EACH ROW
	SELECT CALC_GPA_MP(OLD.student_id, OLD.marking_period_id) INTO @return$$
DELIMITER ;

DROP TRIGGER IF EXISTS `ti_student_report_card_grades`;
DELIMITER $$
CREATE TRIGGER `ti_student_report_card_grades`
    AFTER INSERT ON STUDENT_REPORT_CARD_GRADES
    FOR EACH ROW
	SELECT CALC_GPA_MP(NEW.student_id, NEW.marking_period_id) INTO @return$$
DELIMITER ;

DROP TRIGGER IF EXISTS `tu_student_report_card_grades`;
DELIMITER $$
CREATE TRIGGER `tu_student_report_card_grades`
    AFTER UPDATE ON STUDENT_REPORT_CARD_GRADES
    FOR EACH ROW
	SELECT CALC_GPA_MP(NEW.student_id, NEW.marking_period_id) INTO @return$$
DELIMITER ;