<?php if(!$_SESSION['STAFF_ID'] && !$_SESSION['STUDENT_ID'] && (strpos($_SERVER['PHP_SELF'],'index.php'))===false)
	{
		header('Location: index.php');
		exit;
	}
        ?>
$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$

ALTER TABLE address ADD bus_no character varying(50);
ALTER TABLE address ADD student_relation character varying(50);
ALTER TABLE address ADD student_relation1 character varying(50);
ALTER TABLE address ADD first_name character varying(100);
ALTER TABLE address ADD last_name character varying(100);
ALTER TABLE address ADD first_name2 character varying(100);
ALTER TABLE address ADD last_name2 character varying(100);
ALTER TABLE address ADD home_phone character varying(50);
ALTER TABLE address ADD home_phone2 character varying(50);
ALTER TABLE address ADD work_phone character varying(50);
ALTER TABLE address ADD work_phone2 character varying(50);
ALTER TABLE address ADD mobile_phone character varying(50);
ALTER TABLE address ADD mobile_phone2 character varying(50);
ALTER TABLE address ADD address1 character varying(100);
ALTER TABLE address ADD address2 character varying(100);
ALTER TABLE address ADD email character varying(100);
ALTER TABLE address ADD email2 character varying(100);
ALTER TABLE address ADD bus_pickup1 character varying(1);
ALTER TABLE address ADD street1 character varying(100);
ALTER TABLE address ADD bus_dropoff1 character varying(1);
ALTER TABLE address ADD city1 character varying(100);
ALTER TABLE address ADD zipcode1 character varying(50);
ALTER TABLE address ADD street2 character varying(100);
ALTER TABLE address ADD bus_dropoff2 character varying(1);
ALTER TABLE address ADD city2 character varying(50);
ALTER TABLE address ADD zipcode2 character varying(50);
ALTER TABLE address ADD state1 character varying(50);
ALTER TABLE address ADD state2 character varying(50);
ALTER TABLE address ADD bus_no2 character varying(50);
ALTER TABLE address ADD bus_pickup2 character varying(1);

$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$


$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$

ALTER TABLE students_join_people ADD home_phone character varying(50);
ALTER TABLE students_join_people ADD work_phone character varying(50);
ALTER TABLE students_join_people ADD mobile_phone character varying(50);
ALTER TABLE students_join_people ADD email character varying(50);
ALTER TABLE students_join_people ADD address1 character varying(100);
ALTER TABLE students_join_people ADD street1 character varying(100);
ALTER TABLE students_join_people ADD city1 character varying(50);
ALTER TABLE students_join_people ADD state1 character varying(50);
ALTER TABLE students_join_people ADD zipcode1 character varying(50);
ALTER TABLE students_join_people ADD bus_pickup1 character varying(1);
ALTER TABLE students_join_people ADD bus_dropoff1 character varying(1);
ALTER TABLE students_join_people ADD bus_no character varying(40);

$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$



$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$


ALTER TABLE students_join_address ADD bus_no character varying(50);




$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$

