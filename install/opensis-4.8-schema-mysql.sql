--
--
--

--SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `opensis`
--

CREATE TABLE ADDRESS (
    address_id int(8) not null auto_increment primary key,
    house_no numeric(5,0),
    fraction character varying(3),
    letter character varying(2),
    direction character varying(2),
    street character varying(30),
    apt character varying(5),
    zipcode character varying(50),
    plus4 character varying(4),
    city character varying(60),
    state character varying(50),
    mail_street character varying(30),
    mail_city character varying(60),
    mail_state character varying(50),
    mail_zipcode character varying(50),
    address character varying(255),
    mail_address character varying(255),
    phone character varying(30),
    student_id numeric(10,0),
    bus_no character varying(20),
    bus_pickup character varying(2),
    bus_dropoff character varying(2),
    prim_student_relation character varying(100),
    pri_first_name character varying(100),
    pri_last_name character varying(100),
    home_phone character varying(100),
    work_phone character varying(100),
    mobile_phone character varying(100),
    email character varying(100),
    prim_custody character varying(2),
    prim_address character varying(100),
    prim_street character varying(100),
    prim_city character varying(100),
    prim_state character varying(100),
    prim_zipcode character varying(20),
    sec_student_relation character varying(100),
    sec_first_name character varying(100),
    sec_last_name character varying(100),
    sec_home_phone character varying(100),
    sec_work_phone character varying(100),
    sec_mobile_phone character varying(100),
    sec_email character varying(100),
    sec_custody character varying(2),
    sec_address character varying(100),
    sec_street character varying(100),
    sec_city character varying(60),
    sec_state character varying(100),
    sec_zipcode character varying(100)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE ADDRESS AUTO_INCREMENT=1;


CREATE TABLE ADDRESS_FIELD_CATEGORIES (
    id int(8) not null auto_increment primary key,
    title character varying(100),
    sort_order numeric,
    residence character(1),
    mailing character(1),
    bus character(1)
)ENGINE=MyISAM;


ALTER TABLE ADDRESS_FIELD_CATEGORIES AUTO_INCREMENT=1;


CREATE TABLE ADDRESS_FIELDS (
    id int(8) not null auto_increment primary key,
    type character varying(10),
    search character varying(1),
    title character varying(30),
    sort_order numeric,
    select_options character varying(10000),
    category_id numeric,
    system_field character(1),
    required character varying(1),
    default_selection character varying(255)
)ENGINE=MyISAM;

ALTER TABLE ADDRESS_FIELDS AUTO_INCREMENT=1;


CREATE TABLE APP (
    name character varying(100) NOT NULL,
    value character varying(100) NOT NULL
)ENGINE=MyISAM;;


CREATE TABLE ATTENDANCE_CALENDAR (
    syear numeric(4,0) NOT NULL,
    school_id numeric NOT NULL,
    school_date date NOT NULL,
    minutes numeric,
    block character varying(10),
    calendar_id numeric NOT NULL
)ENGINE=MyISAM;


CREATE TABLE ATTENDANCE_CALENDARS (
    school_id numeric,
    title character varying(100),
    syear numeric(4,0),
    calendar_id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    default_calendar character varying(1),
    rollover_id numeric
)ENGINE=MyISAM;


CREATE TABLE ATTENDANCE_CODE_CATEGORIES (
    id  INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    syear numeric(4,0),
    school_id numeric,
    title character varying(255)
)ENGINE=MyISAM;


ALTER TABLE ATTENDANCE_CODE_CATEGORIES AUTO_INCREMENT=1;


CREATE TABLE ATTENDANCE_CODES (
    id int(8) not null auto_increment primary key,
    syear numeric(4,0),
    school_id numeric,
    title character varying(100),
    short_name character varying(10),
    type character varying(10),
    state_code character varying(1),
    default_code character varying(1),
    table_name numeric,
    sort_order numeric
)ENGINE=MyISAM;


ALTER TABLE ATTENDANCE_CODES AUTO_INCREMENT=1;


CREATE TABLE ATTENDANCE_COMPLETED (
    staff_id numeric NOT NULL,
    school_date date NOT NULL,
    period_id numeric NOT NULL
)ENGINE=MyISAM;


CREATE TABLE ATTENDANCE_DAY (
    student_id numeric NOT NULL,
    school_date date NOT NULL,
    minutes_present numeric,
    state_value numeric(2,1),
    syear numeric(4,0),
    marking_period_id integer,
    comment character varying(255)
)ENGINE=MyISAM;


CREATE TABLE ATTENDANCE_PERIOD (
    student_id numeric NOT NULL,
    school_date date NOT NULL,
    period_id numeric NOT NULL,
    attendance_code numeric,
    attendance_teacher_code numeric,
    attendance_reason character varying(100),
    admin character varying(1),
    course_period_id numeric,
    marking_period_id integer,
    comment character varying(100)
)ENGINE=MyISAM;


CREATE TABLE CALENDAR_EVENTS (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    syear numeric(4,0),
    school_id numeric,
    school_date date,
    title character varying(50),
    description text
)ENGINE=MyISAM;


ALTER TABLE CALENDAR_EVENTS AUTO_INCREMENT=1;


CREATE TABLE CONFIG (
    title character varying(100),
    syear numeric(4,0),
    login character varying(3)
)ENGINE=MyISAM;


CREATE TABLE COURSE_PERIODS (
    syear numeric(4,0) NOT NULL,
    school_id numeric NOT NULL,
    course_period_id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    course_id numeric NOT NULL,
    course_weight character varying(10),
    title character varying(100),
    short_name text,
    period_id numeric,
    mp character varying(3),
    marking_period_id integer,
    teacher_id numeric,
    secondary_teacher_id numeric,
    room character varying(10),
    total_seats numeric,
    filled_seats numeric default 0,
    does_attendance character varying(1),
    does_honor_roll character varying(1),
    does_class_rank character varying(1),
    gender_restriction character varying(1),
    house_restriction character varying(1),
    availability numeric,
    parent_id numeric,
    days character varying(7),
    calendar_id numeric,
    half_day character varying(1),
    does_breakoff character varying(1),
    rollover_id numeric,
    grade_scale_id numeric,
    credits decimal(10,3) null default null
)ENGINE=MyISAM;


CREATE TABLE COURSES (
    syear numeric(4,0) NOT NULL,
    course_id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    subject_id numeric NOT NULL,
    school_id numeric NOT NULL,
    grade_level numeric,
    title character varying(100),
    short_name character varying(25),
    rollover_id numeric
)ENGINE=MyISAM;


ALTER TABLE COURSES AUTO_INCREMENT=1;


CREATE TABLE COURSE_SUBJECTS (
    syear numeric(4,0),
    school_id numeric,
    subject_id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title text,
    short_name text,
    rollover_id numeric
)ENGINE=MyISAM;



ALTER TABLE COURSE_SUBJECTS AUTO_INCREMENT=1;

CREATE TABLE CUSTOM_FIELDS (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    type character varying(10),
    search character varying(1),
    title character varying(30),
    sort_order numeric,
    select_options character varying(10000),
    category_id numeric,
    system_field character(1),
    required character varying(1),
    default_selection character varying(255),
	hide varchar(1)
)ENGINE=MyISAM;
ALTER TABLE CUSTOM_FIELDS AUTO_INCREMENT=1;


CREATE TABLE ELIGIBILITY (
    student_id numeric,
    syear numeric(4,0),
    school_date date,
    period_id numeric,
    eligibility_code character varying(20),
    course_period_id numeric
)ENGINE=MyISAM;


CREATE TABLE ELIGIBILITY_ACTIVITIES (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    syear numeric(4,0),
    school_id numeric,
    title character varying(100),
    start_date date,
    end_date date
)ENGINE=MyISAM;



ALTER TABLE ELIGIBILITY_ACTIVITIES AUTO_INCREMENT=1;


CREATE TABLE ELIGIBILITY_COMPLETED (
    staff_id numeric NOT NULL,
    school_date date NOT NULL,
    period_id numeric NOT NULL
)ENGINE=MyISAM;


CREATE TABLE SCHOOL_GRADELEVELS (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    school_id numeric,
    short_name character varying(5),
    title character varying(50),
    next_grade_id numeric,
    sort_order numeric
)ENGINE=MyISAM;


CREATE TABLE STUDENT_ENROLLMENT (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    syear numeric(4,0),
    school_id numeric,
    student_id numeric,
    grade_id numeric,
    start_date date,
    end_date date,
    enrollment_code numeric,
    drop_code numeric,
    next_school numeric,
    calendar_id numeric,
    last_school numeric
)ENGINE=MyISAM;

ALTER TABLE STUDENT_ENROLLMENT AUTO_INCREMENT=1;


CREATE TABLE GRADEBOOK_ASSIGNMENT_TYPES (
    assignment_type_id  INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    staff_id numeric,
    course_id numeric,
    title character varying(100),
    final_grade_percent numeric(6,5),
    course_period_id numeric
)ENGINE=MyISAM;


ALTER TABLE GRADEBOOK_ASSIGNMENT_TYPES AUTO_INCREMENT=1;


CREATE TABLE GRADEBOOK_ASSIGNMENTS (
    assignment_id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    staff_id numeric,
    marking_period_id integer,
    course_period_id numeric,
    course_id numeric,
    assignment_type_id numeric,
    title character varying(100),
    assigned_date date,
    due_date date,
    points numeric,
    description longtext,
	ungraded int(8) NOT NULL DEFAULT 1
)ENGINE=MyISAM;


ALTER TABLE GRADEBOOK_ASSIGNMENTS AUTO_INCREMENT=1;


CREATE TABLE GRADEBOOK_GRADES (
    student_id numeric NOT NULL,
    period_id numeric,
    course_period_id numeric NOT NULL,
    assignment_id numeric NOT NULL,
    points numeric(6,2),
    comment longtext
)ENGINE=MyISAM;


CREATE TABLE GRADES_COMPLETED (
    staff_id numeric NOT NULL,
    marking_period_id integer NOT NULL,
    period_id numeric NOT NULL
)ENGINE=MyISAM;


CREATE TABLE HISTORY_MARKING_PERIODS (
    parent_id integer,
    mp_type character(20),
    name character(30),
    post_end_date date,
    school_id integer,
    syear integer,
    marking_period_id integer
)ENGINE=MyISAM;


CREATE TABLE LUNCH_PERIOD (
    student_id numeric,
    school_date date,
    period_id numeric,
    attendance_code numeric,
    attendance_teacher_code numeric,
    attendance_reason character varying(100),
    admin character varying(1),
    course_period_id numeric,
    marking_period_id integer,
    lunch_period character varying(100),
    table_name numeric,
    comment varchar(100) default NULL
)ENGINE=MyISAM;


CREATE TABLE SCHOOL_QUARTERS (
    marking_period_id integer NOT NULL,
    syear numeric(4,0),
    school_id numeric,
    semester_id numeric,
    title character varying(50),
    short_name character varying(10),
    sort_order numeric,
    start_date date,
    end_date date,
    post_start_date date,
    post_end_date date,
    does_grades character varying(1),
    does_exam character varying(1),
    does_comments character varying(1),
    rollover_id numeric
)ENGINE=MyISAM;


CREATE TABLE SCHOOL_SEMESTERS (
    marking_period_id integer NOT NULL,
    syear numeric(4,0),
    school_id numeric,
    year_id numeric,
    title character varying(50),
    short_name character varying(10),
    sort_order numeric,
    start_date date,
    end_date date,
    post_start_date date,
    post_end_date date,
    does_grades character varying(1),
    does_exam character varying(1),
    does_comments character varying(1),
    rollover_id numeric
)ENGINE=MyISAM;


CREATE TABLE SCHOOL_YEARS (
    marking_period_id integer NOT NULL,
    syear numeric(4,0),
    school_id numeric,
    title character varying(50),
    short_name character varying(10),
    sort_order numeric,
    start_date date,
    end_date date,
    post_start_date date,
    post_end_date date,
    does_grades character varying(1),
    does_exam character varying(1),
    does_comments character varying(1),
    rollover_id numeric
)ENGINE=MyISAM;


CREATE VIEW MARKING_PERIODS AS
    SELECT q.marking_period_id, 'openSIS' AS mp_source, q.syear,
	q.school_id, 'quarter' AS mp_type, q.title, q.short_name,
	q.sort_order, q.semester_id AS parent_id,
	s.year_id AS grandparent_id, q.start_date,
	q.end_date, q.post_start_date,
	q.post_end_date, q.does_grades,
	q.does_exam, q.does_comments
    FROM SCHOOL_QUARTERS q
    JOIN SCHOOL_SEMESTERS s ON q.semester_id = s.marking_period_id
UNION
    SELECT marking_period_id, 'openSIS' AS mp_source, syear,
	school_id, 'semester' AS mp_type, title, short_name,
	sort_order, year_id AS parent_id,
	-1 AS grandparent_id, start_date,
	end_date, post_start_date,
	post_end_date, does_grades,
	does_exam, does_comments
    FROM SCHOOL_SEMESTERS
UNION
    SELECT marking_period_id, 'openSIS' AS mp_source, syear,
	school_id, 'year' AS mp_type, title, short_name,
	sort_order, -1 AS parent_id,
	-1 AS grandparent_id, start_date,
	end_date, post_start_date,
	post_end_date, does_grades,
	does_exam, does_comments
    FROM SCHOOL_YEARS
UNION
    SELECT marking_period_id, 'History' AS mp_source, syear,
	school_id, mp_type, name AS title, NULL AS short_name,
	NULL AS sort_order, parent_id,
	-1 AS grandparent_id, NULL AS start_date,
	post_end_date AS end_date, NULL AS post_start_date,
	post_end_date, 'Y' AS does_grades,
	NULL AS does_exam, NULL AS does_comments
    FROM HISTORY_MARKING_PERIODS;




CREATE TABLE MARKING_PERIOD_ID_GENERATOR (
    id INTEGER NOT NULL AUTO_INCREMENT KEY
);
DROP FUNCTION IF EXISTS `fn_marking_period_seq`;
DELIMITER $$
CREATE FUNCTION fn_marking_period_seq () RETURNS INT
BEGIN
  INSERT INTO MARKING_PERIOD_ID_GENERATOR VALUES(NULL);
 -- DELETE FROM MARKING_PERIOD_ID_GENERATOR;
RETURN LAST_INSERT_ID();
END$$
DELIMITER ;
ALTER TABLE MARKING_PERIOD_ID_GENERATOR AUTO_INCREMENT=12;

-- ALTER TABLE `MARKING_PERIODS` ADD PRIMARY KEY(`marking_period_id`);
 --ALTER TABLE `MARKING_PERIODS` CHANGE `marking_period_id` `marking_period_id` INT(8) NOT NULL AUTO_INCREMENT ;


CREATE TABLE OLD_COURSE_WEIGHTS (
    syear numeric(4,0),
    school_id numeric,
    course_id numeric,
    course_weight character varying(10),
    gpa_multiplier numeric,
    year_fraction numeric,
    rollover_id numeric
)ENGINE=MyISAM;


CREATE TABLE PEOPLE (
    person_id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    last_name character varying(25) NOT NULL,
    first_name character varying(25) NOT NULL,
    middle_name character varying(25)
)ENGINE=MyISAM;


CREATE TABLE PEOPLE_FIELD_CATEGORIES (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title character varying(100),
    sort_order numeric,
    custody character(1),
    emergency character(1)
)ENGINE=MyISAM;


ALTER TABLE PEOPLE_FIELD_CATEGORIES AUTO_INCREMENT=1;


CREATE TABLE PEOPLE_FIELDS (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    type character varying(10),
    search character varying(1),
    title character varying(30),
    sort_order numeric,
    select_options character varying(10000),
    category_id numeric,
    system_field character(1),
    required character varying(1),
    default_selection character varying(255)
)ENGINE=MyISAM;




ALTER TABLE PEOPLE_FIELDS AUTO_INCREMENT=1;


CREATE TABLE PEOPLE_JOIN_CONTACTS (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    person_id numeric,
    title character varying(100),
    value character varying(100)
)ENGINE=MyISAM;


ALTER TABLE PEOPLE_JOIN_CONTACTS AUTO_INCREMENT=1;




ALTER TABLE PEOPLE AUTO_INCREMENT=1;


CREATE TABLE PORTAL_NOTES (
    id int(8) not null auto_increment primary key,
    school_id numeric,
    syear numeric(4,0),
    title character varying(255),
    content longtext,
    sort_order numeric,
    published_user numeric,
    published_date timestamp ,
    start_date date,
    end_date date,
    published_profiles character varying(255)
)ENGINE=MyISAM;

ALTER TABLE PORTAL_NOTES AUTO_INCREMENT=1;


CREATE TABLE PROFILE_EXCEPTIONS (
    profile_id numeric,
    modname character varying(255),
    can_use character varying(1),
    can_edit character varying(1)
)ENGINE=MyISAM;


CREATE TABLE PROGRAM_CONFIG (
    syear numeric(4,0),
    school_id numeric,
    program character varying(255),
    title character varying(100),
    value character varying(100)
)ENGINE=MyISAM;


CREATE TABLE PROGRAM_USER_CONFIG (
    user_id numeric NOT NULL,
    program character varying(255),
    title character varying(100),
    value character varying(100)
)ENGINE=MyISAM;


CREATE TABLE REPORT_CARD_COMMENTS (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    syear numeric(4,0),
    school_id numeric,
    course_id numeric,
    sort_order numeric,
    title text
)ENGINE=MyISAM;


ALTER TABLE REPORT_CARD_COMMENTS AUTO_INCREMENT=1;


CREATE TABLE REPORT_CARD_GRADE_SCALES (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    syear numeric(4,0),
    school_id numeric NOT NULL,
    title character varying(25),
    comment character varying(100),
    sort_order numeric,
    rollover_id numeric,
    gp_scale numeric(10,3)
)ENGINE=MyISAM;




ALTER TABLE REPORT_CARD_GRADE_SCALES AUTO_INCREMENT=1;


CREATE TABLE REPORT_CARD_GRADES (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    syear numeric(4,0),
    school_id numeric,
    title character varying(15),
    sort_order numeric,
    gpa_value numeric(4,2),
    break_off numeric,
    comment longtext,
    grade_scale_id numeric,
    unweighted_gp numeric(4,2)
   )ENGINE=MyISAM;


ALTER TABLE REPORT_CARD_GRADES AUTO_INCREMENT=1;


CREATE TABLE SCHEDULE (
    syear numeric(4,0) NOT NULL,
    school_id numeric,
    student_id numeric NOT NULL,
    start_date date NOT NULL,
    end_date date,
    modified_date date,
    modified_by character varying(255),
    course_id numeric NOT NULL,
    course_weight character varying(10),
    course_period_id numeric NOT NULL,
    mp character varying(3),
    marking_period_id integer,
    scheduler_lock character varying(1),
	dropped character varying(1) DEFAULT 'N',
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY
)ENGINE=MyISAM;


CREATE TABLE SCHEDULE_REQUESTS (
    syear numeric(4,0),
    school_id numeric,
    request_id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id numeric,
    subject_id numeric,
    course_id numeric,
    course_weight character varying(10),
    marking_period_id integer,
    priority numeric,
    with_teacher_id numeric,
    not_teacher_id numeric,
    with_period_id numeric,
    not_period_id numeric
)ENGINE=MyISAM;


ALTER TABLE SCHEDULE_REQUESTS AUTO_INCREMENT=1;


ALTER TABLE SCHEDULE AUTO_INCREMENT=1;


ALTER TABLE SCHOOL_GRADELEVELS AUTO_INCREMENT=1;


CREATE TABLE SCHOOL_PERIODS (
    period_id int(8) not null auto_increment primary key,
    syear numeric(4,0),
    school_id numeric,
    sort_order numeric,
    title character varying(100),
    short_name character varying(10),
    length numeric,
    block character varying(10),
    ignore_scheduling character varying(10), 
    attendance character varying(1),
    rollover_id numeric,
    start_time character varying(15),
    end_time character varying(15)
)ENGINE=MyISAM;


ALTER TABLE SCHOOL_PERIODS AUTO_INCREMENT=1;


CREATE TABLE SCHOOL_PROGRESS_PERIODS (
    marking_period_id integer NOT NULL,
    syear numeric(4,0),
    school_id numeric,
    quarter_id numeric,
    title character varying(50),
    short_name character varying(10),
    sort_order numeric,
    start_date date,
    end_date date,
    post_start_date date,
    post_end_date date,
    does_grades character varying(1),
    does_exam character varying(1),
    does_comments character varying(1),
    rollover_id numeric
)ENGINE=MyISAM;


CREATE TABLE SCHOOLS (
    id int(8) not null auto_increment primary key,
	syear numeric(4,0),
    title character varying(100),
    address character varying(100),
    city character varying(100),
    state character varying(10),
    zipcode character varying(10),
    area_code numeric(3,0),
    phone character varying(30),
    principal character varying(100),
    www_address character varying(100),
    e_mail character varying(100),
    ceeb character varying(100),
    reporting_gp_scale numeric(10,3)
)ENGINE=MyISAM;


CREATE TABLE SYSTEM_PREFERENCE (
id INT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
school_id INT( 8 ) NOT NULL,
full_day_minute INT( 8 ),
half_day_minute INT( 8 )
)ENGINE=MyISAM;


CREATE TABLE STAFF (
    staff_id int(8) not null auto_increment primary key,
    syear numeric(4,0),
    current_school_id numeric,
    title character varying(5),
    first_name character varying(100),
    last_name character varying(100),
    middle_name character varying(100),
    username character varying(100),
    password character varying(100),
    phone character varying(100),
    email character varying(100),
    profile character varying(30),
    homeroom character varying(5),
    schools character varying(255),
    last_login date,
    failed_login numeric,
    profile_id numeric,
    rollover_id numeric,
    is_disable varchar(10) default NULL
)ENGINE=MyISAM;



CREATE TABLE STAFF_EXCEPTIONS (
    user_id numeric NOT NULL,
    modname character varying(255),
    can_use character varying(1),
    can_edit character varying(1)
)ENGINE=MyISAM;


CREATE TABLE STAFF_FIELD_CATEGORIES (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title character varying(100),
    sort_order numeric,
    include character varying(100),
    admin character(1),
    teacher character(1),
    parent character(1),
    none character(1)
)ENGINE=MyISAM;


ALTER TABLE STAFF_FIELD_CATEGORIES AUTO_INCREMENT=1;


CREATE TABLE STAFF_FIELDS (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    type character varying(10),
    search character varying(1),
    title character varying(30),
    sort_order numeric,
    select_options character varying(10000),
    category_id numeric,
    system_field character(1),
    required character varying(1),
    default_selection character varying(255)
)ENGINE=MyISAM;



ALTER TABLE STAFF_FIELDS AUTO_INCREMENT=1;


CREATE TABLE STUDENT_ELIGIBILITY_ACTIVITIES (
    syear numeric(4,0),
    student_id numeric,
    activity_id numeric
)ENGINE=MyISAM;


CREATE TABLE STUDENT_ENROLLMENT_CODES (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    syear numeric(4,0),
    title character varying(100),
    short_name character varying(10),
    type character varying(4)
)ENGINE=MyISAM;


ALTER TABLE STUDENT_ENROLLMENT_CODES AUTO_INCREMENT=1;


CREATE TABLE STUDENT_FIELD_CATEGORIES (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title character varying(100),
    sort_order numeric,
    include character varying(100)
)ENGINE=MyISAM;


ALTER TABLE STUDENT_FIELD_CATEGORIES AUTO_INCREMENT=1;


CREATE TABLE STUDENT_GPA_CALCULATED (
    student_id numeric,
    marking_period_id integer,
    mp character varying(4),
    gpa numeric,
    weighted_gpa numeric,
    unweighted_gpa numeric,
    class_rank numeric
)ENGINE=MyISAM;


CREATE TABLE STUDENT_GPA_RUNNING (
    student_id numeric,
    marking_period_id integer,
    gpa_points numeric,
    gpa_points_weighted numeric,
    divisor numeric
)ENGINE=MyISAM;


CREATE TABLE STUDENT_MEDICAL (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id numeric,
    type character varying(25),
    medical_date date,
    comments longtext
)ENGINE=MyISAM;


CREATE TABLE STUDENT_MEDICAL_ALERTS (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id numeric,
    title text,
    alert_date date
)ENGINE=MyISAM;

ALTER TABLE STUDENT_MEDICAL_ALERTS AUTO_INCREMENT=1;


ALTER TABLE STUDENT_MEDICAL AUTO_INCREMENT=1;


CREATE TABLE STUDENT_MEDICAL_VISITS (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id numeric,
    school_date date,
    time_in character varying(20),
    time_out character varying(20),
    reason text,
    result text,
    comments longtext
)ENGINE=MyISAM;

ALTER TABLE STUDENT_MEDICAL_VISITS AUTO_INCREMENT=1;

CREATE TABLE STUDENT_MEDICAL_NOTES (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id numeric NOT NULL,
    doctors_note_date date,
    doctors_note_comments longtext
)ENGINE=MyISAM;

CREATE TABLE STUDENT_MP_COMMENTS (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id numeric NOT NULL,
    syear numeric(4,0) NOT NULL,
    marking_period_id integer NOT NULL,
    staff_id integer,
    comment longtext,
    comment_date date
)ENGINE=MyISAM;


CREATE TABLE STUDENT_MP_STATS (
    student_id integer NOT NULL,
    marking_period_id integer NOT NULL,
    cum_weighted_factor decimal(10,6),
    cum_unweighted_factor decimal(10,6),
    cum_rank integer,
    mp_rank integer,
    sum_weighted_factors decimal(10,6),
    sum_unweighted_factors decimal(10,6),
    count_weighted_factors integer,
    count_unweighted_factors integer,
    grade_level_short character varying(3),
    class_size integer
)ENGINE=MyISAM;


CREATE TABLE STUDENT_REPORT_CARD_COMMENTS (
    syear numeric(4,0) NOT NULL,
    school_id numeric,
    student_id numeric NOT NULL,
    course_period_id numeric NOT NULL,
    report_card_comment_id numeric NOT NULL,
    comment character varying(1),
    marking_period_id integer NOT NULL
)ENGINE=MyISAM;

CREATE TABLE STUDENT_REPORT_CARD_GRADES (
    syear numeric(4,0),
    school_id numeric,
    student_id numeric NOT NULL,
    course_period_id numeric,
    report_card_grade_id numeric,
    report_card_comment_id numeric,
    comment longtext,
    grade_percent numeric(4,1),
    marking_period_id varchar(10) NOT NULL,
    grade_letter character varying(5),
    weighted_gp decimal(10,3),
    unweighted_gp decimal(10,3),
    gp_scale decimal(10,3),
    credit_attempted decimal(10,3),
    credit_earned decimal(10,3),
    credit_category character varying(10),
    course_title text,
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY
)ENGINE=MyISAM;

ALTER TABLE STUDENT_REPORT_CARD_GRADES AUTO_INCREMENT=1;

CREATE TABLE STUDENTS (
    student_id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    last_name character varying(50) NOT NULL,
    first_name character varying(50) NOT NULL,
    middle_name character varying(50),
    name_suffix character varying(3),
    username character varying(100),
    password character varying(100),
    last_login date,
    failed_login numeric,
    gender character varying(255),
    ethnicity character varying(255),
    common_name character varying(255),
    social_security character varying(255),
    birthdate character varying(255),
    language character varying(255),
    physician character varying(255),
    physician_phone character varying(255),
    preferred_hospital character varying(255),
    estimated_grad_date character varying(255),
    alt_id character varying(50),
    email character varying(50),
    phone character varying(30),
    is_disable varchar(10) default NULL
)ENGINE=MyISAM;

ALTER TABLE STUDENTS AUTO_INCREMENT=1;


CREATE TABLE STUDENTS_JOIN_ADDRESS (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id numeric NOT NULL,
    address_id numeric(10,0) NOT NULL,
    contact_seq numeric(10,0),
    gets_mail character varying(1),
    primary_residence character varying(1),
    legal_residence character varying(1),
    am_bus character varying(1),
    pm_bus character varying(1),
    mailing character varying(1),
    residence character varying(1),
    bus character varying(1),
    bus_pickup character varying(1),
    bus_dropoff character varying(1),
    bus_no character varying(50)
)ENGINE=MyISAM;

ALTER TABLE STUDENTS_JOIN_ADDRESS AUTO_INCREMENT=1;


CREATE TABLE STUDENTS_JOIN_PEOPLE (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id numeric NOT NULL,
    person_id numeric(10,0) NOT NULL,
    address_id numeric,
    custody character varying(1),
    emergency character varying(1),
    student_relation character varying(100),
    addn_bus_pickup character varying(2),
    addn_bus_dropoff character varying(2),
    addn_busno character varying(50),
    addn_home_phone character varying(100),
    addn_work_phone character varying(100),
    addn_mobile_phone character varying(100),
    addn_email character varying(100),
    addn_address character varying(100),
    addn_street character varying(100),
    addn_city character varying(100),
    addn_state character varying(100),
    addn_zipcode character varying(100)
)ENGINE=MyISAM;

ALTER TABLE STUDENTS_JOIN_PEOPLE AUTO_INCREMENT=1;


CREATE TABLE STUDENTS_JOIN_USERS (
    student_id numeric NOT NULL,
    staff_id numeric NOT NULL
)ENGINE=MyISAM;


CREATE TABLE USER_PROFILES (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    profile character varying(30),
    title character varying(100)
)ENGINE=MyISAM;

ALTER TABLE USER_PROFILES AUTO_INCREMENT=1;


CREATE TABLE HACKING_LOG (
    host_name character varying(20),
    ip_address character varying(20),
    login_date date,
    version character varying(20),
    php_self character varying(20),
    document_root character varying(100),
    script_name character varying(100),
    modname character varying(100),
    username character varying(20)
)ENGINE=MyISAM;

--
--
--

ALTER TABLE ATTENDANCE_CALENDARS AUTO_INCREMENT=1;



ALTER TABLE COURSE_PERIODS AUTO_INCREMENT=1;


-- ALTER TABLE MARKING_PERIODS AUTO_INCREMENT=1;

--
--
--






ALTER TABLE ATTENDANCE_CALENDAR
    ADD CONSTRAINT attendance_calendar_pkey PRIMARY KEY (syear, school_id, school_date, calendar_id);


ALTER TABLE ATTENDANCE_COMPLETED
    ADD CONSTRAINT attendance_completed_pkey PRIMARY KEY (staff_id, school_date, period_id);


ALTER TABLE ATTENDANCE_DAY
    ADD CONSTRAINT attendance_day_pkey PRIMARY KEY (student_id, school_date);


ALTER TABLE ATTENDANCE_PERIOD
    ADD CONSTRAINT attendance_period_pkey PRIMARY KEY (student_id, school_date, period_id);


-- ALTER TABLE CALENDAR_EVENTS
  --  ADD CONSTRAINT calendar_events_pkey PRIMARY KEY (id);


-- ALTER TABLE COURSE_PERIODS
 --   ADD CONSTRAINT course_periods_pkey PRIMARY KEY (course_period_id);


-- ALTER TABLE COURSE_SUBJECTS
 --   ADD CONSTRAINT course_subjects_pkey PRIMARY KEY (subject_id);


-- ALTER TABLE COURSES
   --  ADD CONSTRAINT courses_pkey PRIMARY KEY (course_id);


-- ALTER TABLE CUSTOM_FIELDS
 --   ADD CONSTRAINT custom_fields_pkey PRIMARY KEY (id);

-- ALTER TABLE ELIGIBILITY_ACTIVITIES
 --   ADD CONSTRAINT eligibility_activities_pkey PRIMARY KEY (id);


ALTER TABLE ELIGIBILITY_COMPLETED
    ADD CONSTRAINT eligibility_completed_pkey PRIMARY KEY (staff_id, school_date, period_id);


-- ALTER TABLE GRADEBOOK_ASSIGNMENT_TYPES
 --   ADD CONSTRAINT gradebook_assignment_types_pkey PRIMARY KEY (assignment_type_id);


-- ALTER TABLE GRADEBOOK_ASSIGNMENTS
--    ADD CONSTRAINT gradebook_assignments_pkey PRIMARY KEY (assignment_id);


ALTER TABLE GRADEBOOK_GRADES
    ADD CONSTRAINT gradebook_grades_pkey PRIMARY KEY (student_id, assignment_id, course_period_id);


ALTER TABLE GRADES_COMPLETED
    ADD CONSTRAINT grades_completed_pkey PRIMARY KEY (staff_id, marking_period_id, period_id);


ALTER TABLE HISTORY_MARKING_PERIODS
    ADD CONSTRAINT history_marking_periods_pkey PRIMARY KEY (marking_period_id);


-- ALTER TABLE PEOPLE_FIELD_CATEGORIES
 --   ADD CONSTRAINT people_field_categories_pkey PRIMARY KEY (id);


-- ALTER TABLE PEOPLE_FIELDS
--    ADD CONSTRAINT people_fields_pkey PRIMARY KEY (id);


-- ALTER TABLE PEOPLE_JOIN_CONTACTS
 --   ADD CONSTRAINT people_join_contacts_pkey PRIMARY KEY (id);


-- ALTER TABLE PEOPLE
  --  ADD CONSTRAINT people_pkey PRIMARY KEY (person_id);



-- ALTER TABLE REPORT_CARD_COMMENTS
 --   ADD CONSTRAINT report_card_comments_pkey PRIMARY KEY (id);


-- ALTER TABLE REPORT_CARD_GRADE_SCALES
  --  ADD CONSTRAINT report_card_grade_scales_pkey PRIMARY KEY (id);


-- ALTER TABLE REPORT_CARD_GRADES
 --   ADD CONSTRAINT report_card_grades_pkey PRIMARY KEY (id);


-- ALTER TABLE SCHEDULE
 --   ADD CONSTRAINT schedule_pkey PRIMARY KEY (syear, student_id, course_id, course_period_id, start_date);


-- ALTER TABLE SCHEDULE_REQUESTS
 --   ADD CONSTRAINT schedule_requests_pkey PRIMARY KEY (request_id);


-- ALTER TABLE SCHOOL_GRADELEVELS
  --  ADD CONSTRAINT school_gradelevels_pkey PRIMARY KEY (id);



ALTER TABLE SCHOOL_PROGRESS_PERIODS
    ADD CONSTRAINT school_progress_periods_pkey PRIMARY KEY (marking_period_id);

--ALTER TABLE `SCHOOL_PROGRESS_PERIODS` CHANGE `marking_period_id` `marking_period_id` INT( 8 ) NOT NULL AUTO_INCREMENT ;

--ALTER TABLE SCHOOL_PROGRESS_PERIODS AUTO_INCREMENT=1;

ALTER TABLE SCHOOL_QUARTERS
    ADD CONSTRAINT school_quarters_pkey PRIMARY KEY (marking_period_id);

--ALTER TABLE `SCHOOL_QUARTERS` CHANGE `marking_period_id` `marking_period_id` INT( 8 ) NOT NULL AUTO_INCREMENT ;

--ALTER TABLE SCHOOL_QUARTERS AUTO_INCREMENT=1;

ALTER TABLE SCHOOL_SEMESTERS
    ADD CONSTRAINT school_semesters_pkey PRIMARY KEY (marking_period_id);

--ALTER TABLE `SCHOOL_SEMESTERS` CHANGE `marking_period_id` `marking_period_id` INT( 8 ) NOT NULL AUTO_INCREMENT ;

--ALTER TABLE SCHOOL_SEMESTERS AUTO_INCREMENT=1;

ALTER TABLE SCHOOL_YEARS
    ADD CONSTRAINT school_years_pkey PRIMARY KEY (marking_period_id);

--ALTER TABLE `SCHOOL_YEARS` CHANGE `marking_period_id` `marking_period_id` INT( 8 ) NOT NULL AUTO_INCREMENT ;

--ALTER TABLE SCHOOL_YEARS AUTO_INCREMENT=1;



-- ALTER TABLE STAFF_FIELD_CATEGORIES
 --   ADD CONSTRAINT staff_field_categories_pkey PRIMARY KEY (id);


--ALTER TABLE STAFF_FIELDS
 --   ADD CONSTRAINT staff_fields_pkey PRIMARY KEY (id);



-- ALTER TABLE STUDENT_ENROLLMENT
 --   ADD CONSTRAINT student_enrollment_pkey PRIMARY KEY (id);


-- ALTER TABLE STUDENT_FIELD_CATEGORIES
 --   ADD CONSTRAINT student_field_categories_pkey PRIMARY KEY (id);


-- ALTER TABLE STUDENT_MEDICAL_ALERTS
 --   ADD CONSTRAINT student_medical_alerts_pkey PRIMARY KEY (id);


-- ALTER TABLE STUDENT_MEDICAL
 --  ADD CONSTRAINT student_medical_pkey PRIMARY KEY (id);


--ALTER TABLE STUDENT_MEDICAL_VISITS
 --   ADD CONSTRAINT student_medical_visits_pkey PRIMARY KEY (id);


--ALTER TABLE STUDENT_MP_COMMENTS
 --   ADD CONSTRAINT student_mp_comments_pkey PRIMARY KEY (student_id, syear, marking_period_id);


ALTER TABLE STUDENT_MP_STATS
    ADD CONSTRAINT student_mp_stats_pkey PRIMARY KEY (student_id, marking_period_id);


ALTER TABLE STUDENT_REPORT_CARD_COMMENTS
    ADD CONSTRAINT student_report_card_comments_pkey PRIMARY KEY (syear, student_id, course_period_id, marking_period_id, report_card_comment_id);


-- ALTER TABLE STUDENT_REPORT_CARD_GRADES
 --   ADD CONSTRAINT student_report_card_grades_id_key UNIQUE (id);


-- ALTER TABLE STUDENT_REPORT_CARD_GRADES
 --   ADD CONSTRAINT student_report_card_grades_pkey PRIMARY KEY (id);


-- ALTER TABLE STUDENTS_JOIN_ADDRESS
 --   ADD CONSTRAINT students_join_address_pkey PRIMARY KEY (id);


-- ALTER TABLE STUDENTS_JOIN_PEOPLE
 --   ADD CONSTRAINT students_join_people_pkey PRIMARY KEY (id);


ALTER TABLE STUDENTS_JOIN_USERS
    ADD CONSTRAINT students_join_users_pkey PRIMARY KEY (student_id, staff_id);


-- ALTER TABLE STUDENTS
 --   ADD CONSTRAINT students_pkey PRIMARY KEY (student_id);

--
--
--

CREATE INDEX address_3  USING btree ON ADDRESS(zipcode, plus4);


CREATE INDEX address_4  USING btree ON ADDRESS(street);


CREATE INDEX address_desc_ind  USING btree ON ADDRESS_FIELDS(id);


CREATE INDEX address_desc_ind2  USING btree ON CUSTOM_FIELDS(type);


CREATE INDEX address_fields_ind3  USING btree ON CUSTOM_FIELDS(category_id);


CREATE INDEX attendance_code_categories_ind1  USING btree ON ATTENDANCE_CODE_CATEGORIES(id);


CREATE INDEX attendance_code_categories_ind2  USING btree ON ATTENDANCE_CODE_CATEGORIES(syear, school_id);


CREATE INDEX attendance_codes_ind2  USING btree ON ATTENDANCE_CODES(syear, school_id);


CREATE INDEX attendance_codes_ind3  USING btree ON ATTENDANCE_CODES(short_name);


CREATE INDEX attendance_period_ind1  USING btree ON ATTENDANCE_PERIOD(student_id);


CREATE INDEX attendance_period_ind2  USING btree ON ATTENDANCE_PERIOD(period_id);


CREATE INDEX attendance_period_ind3  USING btree ON ATTENDANCE_PERIOD(attendance_code);


CREATE INDEX attendance_period_ind4  USING btree ON ATTENDANCE_PERIOD(school_date);


CREATE INDEX attendance_period_ind5  USING btree ON ATTENDANCE_PERIOD(attendance_code);


CREATE INDEX course_periods_ind1  USING btree ON COURSE_PERIODS(syear);


CREATE INDEX course_periods_ind2  USING btree ON COURSE_PERIODS(course_id, course_weight, syear, school_id);


CREATE INDEX course_periods_ind3  USING btree ON COURSE_PERIODS(course_period_id);


CREATE INDEX course_periods_ind4  USING btree ON COURSE_PERIODS(period_id);


CREATE INDEX course_periods_ind5  USING btree ON COURSE_PERIODS(parent_id);


CREATE INDEX course_subjects_ind1  USING btree ON COURSE_SUBJECTS(syear, school_id, subject_id);


CREATE INDEX courses_ind1  USING btree ON COURSES(course_id, syear);


CREATE INDEX courses_ind2  USING btree ON COURSES(subject_id);


CREATE INDEX custom_desc_ind  USING btree ON CUSTOM_FIELDS(id);


CREATE INDEX custom_desc_ind2  USING btree ON CUSTOM_FIELDS(type);


CREATE INDEX custom_fields_ind3  USING btree ON CUSTOM_FIELDS(category_id);


CREATE INDEX eligibility_activities_ind1  USING btree ON ELIGIBILITY_ACTIVITIES(school_id, syear);


CREATE INDEX eligibility_ind1  USING btree ON ELIGIBILITY(student_id, course_period_id, school_date);


CREATE INDEX gradebook_assignment_types_ind1  USING btree ON GRADEBOOK_ASSIGNMENTS(staff_id, course_id);


CREATE INDEX gradebook_assignments_ind1  USING btree ON GRADEBOOK_ASSIGNMENTS(staff_id, marking_period_id);


CREATE INDEX gradebook_assignments_ind2  USING btree ON GRADEBOOK_ASSIGNMENTS(course_id, course_period_id);


CREATE INDEX gradebook_assignments_ind3  USING btree ON GRADEBOOK_ASSIGNMENTS(assignment_type_id);


CREATE INDEX gradebook_grades_ind1  USING btree ON GRADEBOOK_GRADES(assignment_id);


CREATE INDEX history_marking_period_ind1  USING btree ON HISTORY_MARKING_PERIODS(school_id);


CREATE INDEX history_marking_period_ind2  USING btree ON HISTORY_MARKING_PERIODS(syear);


CREATE INDEX history_marking_period_ind3  USING btree ON HISTORY_MARKING_PERIODS(mp_type);


CREATE INDEX name  USING btree ON STUDENTS(last_name, first_name, middle_name);


CREATE INDEX people_1  USING btree ON PEOPLE(last_name, first_name);


CREATE INDEX people_3  USING btree ON PEOPLE(person_id, last_name, first_name, middle_name);


CREATE INDEX people_desc_ind  USING btree ON PEOPLE_FIELDS(id);


CREATE INDEX people_desc_ind2  USING btree ON CUSTOM_FIELDS(type);


CREATE INDEX people_fields_ind3  USING btree ON CUSTOM_FIELDS(category_id);


CREATE INDEX people_join_contacts_ind1  USING btree ON PEOPLE_JOIN_CONTACTS(person_id);


CREATE INDEX program_config_ind1  USING btree ON PROGRAM_CONFIG(program, school_id, syear);


CREATE INDEX program_user_config_ind1  USING btree ON PROGRAM_USER_CONFIG(user_id, program);


CREATE INDEX relations_meets_2  USING btree ON STUDENTS_JOIN_PEOPLE(person_id);


CREATE INDEX relations_meets_5  USING btree ON STUDENTS_JOIN_PEOPLE(id);


CREATE INDEX relations_meets_6  USING btree ON STUDENTS_JOIN_PEOPLE(custody, emergency);


CREATE INDEX report_card_comments_ind1  USING btree ON REPORT_CARD_COMMENTS(syear, school_id);


CREATE INDEX report_card_grades_ind1  USING btree ON REPORT_CARD_GRADES(syear, school_id);


CREATE INDEX schedule_ind1  USING btree ON SCHEDULE(course_id, course_weight);


CREATE INDEX schedule_ind2  USING btree ON SCHEDULE(course_period_id);


CREATE INDEX schedule_ind3  USING btree ON SCHEDULE(student_id, marking_period_id, start_date, end_date);


CREATE INDEX schedule_ind4  USING btree ON SCHEDULE(syear, school_id);


CREATE INDEX schedule_requests_ind1  USING btree ON SCHEDULE_REQUESTS(student_id, course_id, course_weight, syear, school_id);


CREATE INDEX schedule_requests_ind2  USING btree ON SCHEDULE_REQUESTS(syear, school_id);


CREATE INDEX schedule_requests_ind3  USING btree ON SCHEDULE_REQUESTS(course_id, course_weight, syear, school_id);


CREATE INDEX schedule_requests_ind4  USING btree ON SCHEDULE_REQUESTS(with_teacher_id);


CREATE INDEX schedule_requests_ind5  USING btree ON SCHEDULE_REQUESTS(not_teacher_id);


CREATE INDEX schedule_requests_ind6  USING btree ON SCHEDULE_REQUESTS(with_period_id);


CREATE INDEX schedule_requests_ind7  USING btree ON SCHEDULE_REQUESTS(not_period_id);


CREATE INDEX schedule_requests_ind8  USING btree ON SCHEDULE_REQUESTS(request_id);


CREATE INDEX school_gradelevels_ind1  USING btree ON SCHOOL_GRADELEVELS(school_id);


CREATE INDEX school_periods_ind1  USING btree ON SCHOOL_PERIODS(period_id, syear);


CREATE INDEX school_progress_periods_ind1  USING btree ON SCHOOL_PROGRESS_PERIODS(quarter_id);


CREATE INDEX school_progress_periods_ind2  USING btree ON SCHOOL_PROGRESS_PERIODS(syear, school_id, start_date, end_date);


CREATE INDEX school_quarters_ind1  USING btree ON SCHOOL_QUARTERS(semester_id);


CREATE INDEX school_quarters_ind2  USING btree ON SCHOOL_QUARTERS(syear, school_id, start_date, end_date);


CREATE INDEX school_semesters_ind1  USING btree ON SCHOOL_SEMESTERS(year_id);


CREATE INDEX school_semesters_ind2  USING btree ON SCHOOL_SEMESTERS(syear, school_id, start_date, end_date);


CREATE INDEX school_years_ind2  USING btree ON SCHOOL_YEARS(syear, school_id, start_date, end_date);


CREATE INDEX schools_ind1  USING btree ON SCHOOLS(syear);


CREATE INDEX staff_desc_ind1  USING btree ON STAFF_FIELDS(id);


CREATE INDEX staff_desc_ind2  USING btree ON STAFF_FIELDS(type);


CREATE INDEX staff_fields_ind3  USING btree ON STAFF_FIELDS(category_id);


CREATE INDEX staff_ind1  USING btree ON STAFF(staff_id, syear);


CREATE INDEX staff_ind2  USING btree ON STAFF(last_name, first_name);


CREATE INDEX staff_ind3  USING btree ON STAFF(schools);


CREATE UNIQUE INDEX staff_ind4  USING btree ON STAFF (username, syear);


CREATE INDEX stu_addr_meets_2  USING btree ON STUDENTS_JOIN_ADDRESS(address_id);


CREATE INDEX stu_addr_meets_3  USING btree ON STUDENTS_JOIN_ADDRESS(primary_residence);


CREATE INDEX stu_addr_meets_4  USING btree ON STUDENTS_JOIN_ADDRESS(legal_residence);


CREATE INDEX student_eligibility_activities_ind1  USING btree ON STUDENT_ELIGIBILITY_ACTIVITIES(student_id);


CREATE INDEX student_enrollment_1  USING btree ON STUDENT_ENROLLMENT(student_id, enrollment_code);


CREATE INDEX student_enrollment_2  USING btree ON STUDENT_ENROLLMENT(grade_id);


CREATE INDEX student_enrollment_3  USING btree ON STUDENT_ENROLLMENT(syear, student_id, school_id, grade_id);


CREATE INDEX student_enrollment_6  USING btree ON STUDENT_ENROLLMENT(start_date, end_date);


CREATE INDEX student_enrollment_7  USING btree ON STUDENT_ENROLLMENT(school_id);


CREATE INDEX student_gpa_calculated_ind1  USING btree ON STUDENT_GPA_CALCULATED(marking_period_id, student_id);


CREATE INDEX student_gpa_running_ind1  USING btree ON STUDENT_GPA_RUNNING(marking_period_id, student_id);


CREATE INDEX student_medical_alerts_ind1  USING btree ON STUDENT_MEDICAL_ALERTS(student_id);


CREATE INDEX student_medical_ind1  USING btree ON STUDENT_MEDICAL(student_id);


CREATE INDEX student_medical_visits_ind1  USING btree ON STUDENT_MEDICAL_VISITS(student_id);


CREATE INDEX student_report_card_comments_ind1  USING btree ON STUDENT_REPORT_CARD_COMMENTS(school_id);


CREATE INDEX student_report_card_grades_ind1  USING btree ON STUDENT_REPORT_CARD_GRADES(school_id);


CREATE INDEX student_report_card_grades_ind2  USING btree ON STUDENT_REPORT_CARD_GRADES(student_id);


CREATE INDEX student_report_card_grades_ind3  USING btree ON STUDENT_REPORT_CARD_GRADES(course_period_id);


CREATE INDEX student_report_card_grades_ind4  USING btree ON STUDENT_REPORT_CARD_GRADES(marking_period_id);


CREATE INDEX students_join_address_ind1  USING btree ON STUDENTS_JOIN_ADDRESS(student_id);


CREATE INDEX students_join_people_ind1  USING btree ON STUDENTS_JOIN_PEOPLE(student_id);


CREATE INDEX sys_c007322  USING btree ON STUDENTS_JOIN_ADDRESS(id, student_id, address_id);

--
--
--

CREATE VIEW COURSE_DETAILS AS
  SELECT cp.school_id, cp.syear, cp.marking_period_id, cp.period_id, c.subject_id,
	  cp.course_id, cp.course_period_id, cp.teacher_id,cp. secondary_teacher_id, c.title AS course_title,
	  cp.title AS cp_title, cp.grade_scale_id, cp.mp, cp.credits
  FROM COURSE_PERIODS cp, COURSES c WHERE (cp.course_id = c.course_id);

CREATE VIEW ENROLL_GRADE AS
  SELECT e.id, e.syear, e.school_id, e.student_id, e.start_date, e.end_date, sg.short_name, sg.title
  FROM STUDENT_ENROLLMENT e, SCHOOL_GRADELEVELS sg WHERE (e.grade_id = sg.id);

CREATE VIEW TRANSCRIPT_GRADES AS
    SELECT s.id AS school_id, mp.mp_source, mp.marking_period_id AS mp_id,
	mp.title AS mp_name, mp.syear, mp.end_date AS posted, rcg.student_id,
	sms.grade_level_short AS gradelevel, rcg.grade_letter, rcg.weighted_gp AS gp_value,
	rcg.unweighted_gp AS weighting, rcg.gp_scale, rcg.credit_attempted, rcg.credit_earned,
	rcg.credit_category, rcg.course_title AS course_name,
	sms.cum_weighted_factor AS cum_gp_factor,
	(sms.cum_weighted_factor * s.reporting_gp_scale) AS cum_gpa,
	((sms.sum_weighted_factors / sms.count_weighted_factors) * s.reporting_gp_scale) AS gpa,
	sms.cum_rank,mp.sort_order
    FROM STUDENT_REPORT_CARD_GRADES rcg
    INNER JOIN MARKING_PERIODS mp ON mp.marking_period_id = rcg.marking_period_id AND mp.mp_type IN ('year','semester','quarter')
    INNER JOIN STUDENT_MP_STATS sms ON sms.student_id = rcg.student_id AND sms.marking_period_id = rcg.marking_period_id
    INNER JOIN SCHOOLS s ON s.id = mp.school_id;




-- ****************** For storing all log details ***************************
CREATE TABLE LOGIN_RECORDS
(
  syear numeric(5),
  first_name character varying(100),
  last_name character varying(100),
  profile character varying(50),
  user_name character varying(100),
  login_time timestamp,
  faillog_count numeric(4),
  staff_id numeric(10),
  id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  faillog_time timestamp,
  ip_address character varying(20),
  status character varying(50),
  school_id DECIMAL( 10 ) NULL 
)ENGINE=MyISAM;


-- ******************** For Creating Login Seq **************************

ALTER TABLE LOGIN_RECORDS AUTO_INCREMENT=1;
-- ********************* Log Maintain Table *****************************
CREATE TABLE LOG_MAINTAIN
(
  id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  value numeric(30),
  session_id character varying(50)
)ENGINE=MyISAM;


-- *********************** Log Maintain Sequence ************************


ALTER TABLE LOG_MAINTAIN AUTO_INCREMENT=1;

--
-- Table structure for table  SYSTEM_PREFERENCE_MISC
--

CREATE TABLE SYSTEM_PREFERENCE_MISC (
  fail_count decimal(5,0) NOT NULL default '3',
  activity_days decimal(5,0) NOT NULL default '30',
  system_maintenance_switch char(1) character set utf8 collate utf8_bin default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE GOAL
(
  goal_id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  student_id numeric NOT NULL,
  goal_title character varying(100),
  start_date date,
  end_date date,
  goal_description text,
  school_id numeric(10),
  syear numeric(10)
)ENGINE=MyISAM;


CREATE TABLE PROGRESS
(
  progress_id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  goal_id numeric NOT NULL,
  student_id numeric NOT NULL,
  start_date date,
  progress_name text NOT NULL,
  proficiency character varying(100) NOT NULL,
  progress_description text NOT NULL,
  course_period_id numeric(10)
)ENGINE=MyISAM;




ALTER TABLE GOAL AUTO_INCREMENT=1;





ALTER TABLE PROGRESS AUTO_INCREMENT=1;

--
-- TABLE STRUCTURE FOR TABLE LOGIN_MESSAGE
--

CREATE TABLE LOGIN_MESSAGE (
  id INT(8) NOT NULL AUTO_INCREMENT,
  message longtext DEFAULT NULL,
display char(1) character set utf8 collate utf8_bin default NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

