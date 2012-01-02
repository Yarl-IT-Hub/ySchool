<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. It is  web-based, 
#  open source, and comes packed with features that include student 
#  demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  Copyright (C) 2007-2008, Open Solutions for Education, Inc.
#
#*************************************************************************
#  This program is free software: you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation, version 2 of the License. See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#**************************************************************************

include('../../Redirect_modules.php');
if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
{
	$start_date = $_REQUEST['day_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['year_start'];
	$st_dt = con_date($start_date);
}

if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
{
	$end_date = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['year_end'];
	$end_dt = con_date($end_date);
}

if($_REQUEST['chk_pro'])
{
	$progress = $_REQUEST['chk_pro'];
}


if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['st_arr']))
	{
	$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
	$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";

	//$extra['functions'] = array('GRADE_ID'=>'_grade_id');
	if($_REQUEST['mailing_labels']=='Y')
		Widgets('mailing_labels');

	$RET = GetStuList($extra);

	if(count($RET))
	{
		include('modules/Students/includes/functions.php');
		//------------Comment Heading -----------------------------------------------------
		//$categories_RET = DBGet(DBQuery("SELECT ID,TITLE,INCLUDE FROM STUDENT_FIELD_CATEGORIES ORDER BY SORT_ORDER,TITLE"),array(),array('ID'));

		// get the address and contacts custom fields, create the select lists and expand select and codeds options
		$address_categories_RET = DBGet(DBQuery("SELECT c.ID AS CATEGORY_ID,c.TITLE AS CATEGORY_TITLE,c.RESIDENCE,c.MAILING,c.BUS,f.ID,f.TITLE,f.TYPE,f.SELECT_OPTIONS,f.DEFAULT_SELECTION,f.REQUIRED FROM ADDRESS_FIELD_CATEGORIES c,ADDRESS_FIELDS f WHERE f.CATEGORY_ID=c.ID ORDER BY c.SORT_ORDER,c.TITLE,f.SORT_ORDER,f.TITLE"),array(),array('CATEGORY_ID'));
		$people_categories_RET = DBGet(DBQuery("SELECT c.ID AS CATEGORY_ID,c.TITLE AS CATEGORY_TITLE,c.CUSTODY,c.EMERGENCY,f.ID,f.TITLE,f.TYPE,f.SELECT_OPTIONS,f.DEFAULT_SELECTION,f.REQUIRED FROM PEOPLE_FIELD_CATEGORIES c,PEOPLE_FIELDS f WHERE f.CATEGORY_ID=c.ID ORDER BY c.SORT_ORDER,c.TITLE,f.SORT_ORDER,f.TITLE"),array(),array('CATEGORY_ID'));
		explodeCustom($address_categories_RET, $address_custom, 'a');
		explodeCustom($people_categories_RET, $people_custom, 'p');

		unset($_REQUEST['modfunc']);
		$handle = PDFStart();
				
		foreach($RET as $student)
		{
			$_SESSION['student_id'] = $student['STUDENT_ID'];

/*

echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
			echo "<tr><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">Oregon State Hospital<br><font size=2>". GetSchool(UserSchool())."</font><div style=\"font-size:12px;\">Patient Goal and Progress Report</div></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
			


echo "<table width=100% cellspacing=0 style=\"border-collapse:collapse\">";

*/

#echo '</table>';				
		
	
	/*
echo "<table cellspacing=0 style=\"border-collapse:collapse\">";
			echo "<tr><td colspan=3 style=\"height:18px\"></td></tr>";
if($StudentPicturesPath && (($file = @fopen($picture_path=$StudentPicturesPath.UserSyear().'/'.UserStudentID().'.JPG','r')) || ($file = @fopen($picture_path=$StudentPicturesPath.(UserSyear()-1).'/'.UserStudentID().'.JPG','r'))))
{
			echo '<tr><td colspan=3 width=150><IMG SRC="'.$picture_path.'?id='.rand(6,100000).'" width=150  style="padding:4px; background-color:#fff; border:1px solid #333" ></td></tr>';
} else {
echo '<tr><td colspan=3><IMG SRC="assets/noimage.jpg?id='.rand(6,100000).'" width=144  style="padding:4px; background-color:#fff; border:1px solid #333"></td></tr>';
}
	fclose($file);
	*/
//echo '</table>';	
/*
	$sql=DBGet(DBQuery("SELECT s.OSH_ID,s.CUSTOM_200000000 AS GENDER, s.CUSTOM_200000001 AS ETHNICITY, s.CUSTOM_200000002 AS COMMON_NAME,  s.CUSTOM_200000003 AS SOCIAL_SEC_NO, s.CUSTOM_200000004 AS BIRTHDAY, s.CUSTOM_200000005 AS LANGUAGE, s.CUSTOM_200000006 AS PHYSICIAN_NAME, s.CUSTOM_200000007 AS PHYSICIAN_PHONO, se.START_DATE AS START_DATE,sec.TITLE AS STATUS, se.NEXT_SCHOOL AS ROLLING  FROM STUDENTS s, STUDENT_ENROLLMENT se,STUDENT_ENROLLMENT_CODES sec WHERE s.STUDENT_ID='".$_SESSION['student_id']."' AND s.STUDENT_ID=se.STUDENT_ID AND se.SYEAR=sec.SYEAR"));

$sql = $sql[1];
*/

$sql_student = DBGet(DBQuery("SELECT gender AS GENDER, ethnicity AS ETHNICITY, common_name AS COM_NAME, social_security AS SOCIAL_SEC, language AS LANG, birthdate AS BDATE  FROM STUDENTS WHERE STUDENT_ID='".$_SESSION['student_id']."'"),array('BDATE'=>'ProperDate'));

$sql_student = $sql_student[1];

$bir_dt = $sql_student['BDATE'];
unset($_openSIS['DrawHeader']);

if(!isset($st_dt) && !isset($end_dt))
{
	$sql_goal = "SELECT GOAL_ID,GOAL_TITLE,START_DATE,END_DATE,GOAL_DESCRIPTION FROM GOAL WHERE STUDENT_ID='".$_SESSION['student_id']."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY GOAL_TITLE";
}
if(isset($st_dt) && !isset($end_dt))
{
	$sql_goal = "SELECT GOAL_ID,GOAL_TITLE,START_DATE,END_DATE,GOAL_DESCRIPTION FROM GOAL WHERE STUDENT_ID='".$_SESSION['student_id']."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND START_DATE>='".$st_dt."' ORDER BY GOAL_TITLE";
}
if(!isset($st_dt) && isset($end_dt))
{
	$sql_goal = "SELECT GOAL_ID,GOAL_TITLE,START_DATE,END_DATE,GOAL_DESCRIPTION FROM GOAL WHERE STUDENT_ID='".$_SESSION['student_id']."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND START_DATE<='".$end_dt."' ORDER BY GOAL_TITLE";
}
if(isset($st_dt) && isset($end_dt))
{
	$sql_goal = "SELECT GOAL_ID,GOAL_TITLE,START_DATE,END_DATE,GOAL_DESCRIPTION FROM GOAL WHERE STUDENT_ID='".$_SESSION['student_id']."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND START_DATE>='".$st_dt."' AND START_DATE<='".$end_dt."' ORDER BY GOAL_TITLE";
}

$res_goal = DBGet(DBQuery($sql_goal),array('START_DATE'=>'ProperDate','END_DATE'=>'ProperDate'));

	#echo "<tr><td valign=top width=300><table width=100% ><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-weight:bold;\">Personal Information</td></tr>";
	
			//----------------------------------------------

		if(count($res_goal) != 0)
		{
		
			echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
			echo "<tr><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."</font></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
			echo "<table width=100% cellspacing=0 style=\"border-collapse:collapse\">";
		
		
			echo "<tr><td width=15%>Student Name:</td>";
			echo "<td>" .$student['FULL_NAME']. "</td></tr>";
			/*
			echo "<tr><td>OSH ID:</td>";
			echo "<td>". $student['OSH_ID'] ." </td></tr>";
			*/
			echo "<tr><td>Grade:</td>";
			echo "<td>". $student['GRADE_ID'] ." </td></tr>";
			echo "<tr><td>Gender:</td>";
			echo "<td>".$sql_student['GENDER'] ."</td></tr>";
			echo "<tr><td>Ethnicity:</td>";
			echo "<td>".$sql_student['ETHNICITY'] ."</td></tr>";
			if($sql_student['COM_NAME'] !='')
			{
			echo "<tr><td>Common Name:</td>";
			echo "<td>".$sql_student['COM_NAME'] ."</td></tr>";
			}
			if($sql_student['SOCIAL_SEC'] !='')
			{
			echo "<tr><td>Social Security:</td>";
			echo "<td>".$sql_student['SOCIAL_SEC'] ."</td></tr>";
			}
			echo "<tr><td>Date of Birth:</td>";
			echo "<td>".$bir_dt."</td></tr>";
			if($sql_student['LANG'] !='')
			{
			echo "<tr><td>Language Spoken:</td>";
			echo "<td>".$sql_student['LANG'] ."</td></tr>";
			echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
			}
			
			echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
			echo '<tr><td><b><u>Goal Details</u></b></td><td>&nbsp;</td></tr>';
			echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
			foreach($res_goal as $row_goal)
			{               
				echo '<tr><td><b>Goal Title: </b></td><td>'.$row_goal['GOAL_TITLE'].'</td></tr>';
				echo '<tr><td><b>Begin Date: </b></td><td>'.$row_goal['START_DATE'].'</td></tr>';
				echo '<tr><td><b>End Date: </b></td><td>'.$row_goal['END_DATE'].'</td></tr>';
				echo '<tr><td valign=top><b>Goal Description: </b></td><td>'.$row_goal['GOAL_DESCRIPTION'].'</td></tr>';
				echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
				
				if($progress == 'Y')
				{               $goal_id=$row_goal['GOAL_ID'];
					$res_pro = DBGet(DBQuery("SELECT START_DATE,PROGRESS_NAME ,PROFICIENCY,PROGRESS_DESCRIPTION,(SELECT TITLE FROM COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=PROGRESS.COURSE_PERIOD_ID) AS CP_TITLE FROM PROGRESS WHERE STUDENT_ID='".$_SESSION['student_id']."' AND GOAL_ID='".$goal_id."' ORDER BY PROGRESS_NAME"),array('START_DATE'=>'ProperDate'));
					echo '<tr><td><b><u>Progress Details</u></b></td><td>&nbsp;</td></tr>';
					echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
					foreach($res_pro as $row_pro)
					{
						echo '<tr><td><b>Date of Entry: </b></td><td>'.$row_pro['START_DATE'].'</td></tr>';
					# ----------------------------- CP ------------------------------------------------- #	
						echo '<tr><td><b>Course Period: </b></td><td>'.$row_pro['CP_TITLE'].'</td></tr>';
					# ----------------------------- CP ------------------------------------------------- #		
						echo '<tr><td><b>Progress Period Name: </b></td><td>'.$row_pro['PROGRESS_NAME'].'</td></tr>';
						echo '<tr><td><b>Proficiency: </b></td><td>'.$row_pro['PROFICIENCY'].'</td></tr>';
						echo '<tr><td><b>Progress Assessment: </b></td><td>'.$row_pro['PROGRESS_DESCRIPTION'].'</td></tr>';
						echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
					}
				}
				
				echo "<tr><td colspan=2 style=\"height:18px; border-top:1px solid #333;\"></td></tr>";
			}
			
			
			echo '</td><td></td><td></td></tr></table></TABLE><div style="page-break-before: always;">&nbsp;</div>';
			foreach($categories_RET as $id=>$category)
			{
				if($id!='1' && $id!='3' && $id!='2' && $id!='4' && $_REQUEST['category'][$id])
				{
					$_REQUEST['category_id'] = $id;
					//DrawHeader($category[1]['TITLE']);
					$separator = '';
					if(!$category[1]['INCLUDE'])
						include('modules/Students/includes/Other_Info.inc.php');
					elseif(!strpos($category[1]['INCLUDE'],'/'))
						include('modules/Students/includes/'.$category[1]['INCLUDE'].'.inc.php');
					else
					{
						include('modules/'.$category[1]['INCLUDE'].'.inc.php');
						$separator = '<HR>';
						//include('modules/Students/includes/Other_Info.inc.php');
					}

				}
			}
			
		}
		}
		PDFStop($handle);
	}
	else
		BackPrompt('No Students were found.');
	}
	else
		BackPrompt('You must choose at least one student.');
	unset($_SESSION['student_id']);
	//echo '<pre>'; var_dump($_REQUEST['modfunc']); echo '</pre>';
	$_REQUEST['modfunc']=true;
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Students >> ".ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_search_all_schools=$_REQUEST[_search_all_schools]&_openSIS_PDF=true method=POST target=_blank>";
		
		
	
#	DrawHeaderHome('<table><tr><td>'.PrepareDate($start_date,'_start').'</td><td> - </td><td>'.PrepareDate($end_date,'_end').'</td><td> - </td><td>'.$advanced_link.'</td><td> : <INPUT type=submit value=Go class=btn_medium></td></tr></table>');

	echo '<TABLE border=0 width=98% align=center><tr><td style=padding-top:25px;>Please select the date range :</td><TD valign=middle style=padding-top:25px;>';
	#$date=date("Y-m-d");
	$date=''; // 2009-04-08
	echo 'From : </TD><TD valign=middle>';
	#DrawHeader(PrepareDateGoal_Start($date,'_date',false,array('submit'=>true)));
	#DrawHeader(PrepareDate($date,'_date',false,array('submit'=>true)));
	DrawHeader(PrepareDate($start_date,'_start'));
	echo '</TD><TD valign=middle style=padding-top:25px;>To : </TD><TD valign=middle>';
	#DrawHeader(PrepareDateGoal_End($date,'_date',false,array('submit'=>true)));
	DrawHeader(PrepareDate($end_date,'_end'));
	echo '</TD><TD valign=middle style=padding-top:22px;><input type="checkbox" name="chk_pro" id="chk_pro" value="Y" /> With Progress';
	echo '</TD></TR></TABLE>';

/*
	echo '<TABLE border=0 width=100%><TR><TD valign=middle>';
	#$date=date("Y-m-d");
	$date=''; // 2009-04-08
	echo 'From: </TD><TD valign=middle>';
	#DrawHeader(PrepareDateGoal_Start($date,'_date',false,array('submit'=>true)));
	#DrawHeader(PrepareDate($date,'_date',false,array('submit'=>true)));
	DrawHeader(PrepareDateGoal_Start($date,'_date',true,array('submit'=>true)));
	echo '</TD><TD valign=middle>To: </TD><TD valign=middle>';
	#DrawHeader(PrepareDateGoal_End($date,'_date',false,array('submit'=>true)));
	DrawHeader(PrepareDateGoal_End($date,'_date',true,array('submit'=>true)));
	echo '</TD><TD valign=middle><input type="checkbox" name="chk_pro" id="chk_pro" value="Y" /> With Progress';
	echo '</TD></TR></TABLE>';
*/

		
	
		
		
		//$extra['header_right'] = '<INPUT type=submit value=\'Print Info for Selected Students\'>';

		/*$extra['extra_header_left'] = '<TABLE>';
		//Widgets('mailing_labels',true);
		$extra['extra_header_left'] .= $extra['search'];
		$extra['search'] = '';
		$extra['extra_header_left'] .= '';

		if(User('PROFILE_ID'))
			$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
		else
			$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
		$categories_RET = DBGet(DBQuery("SELECT ID,TITLE,INCLUDE FROM STUDENT_FIELD_CATEGORIES ORDER BY SORT_ORDER,TITLE"));
		$extra['extra_header_left'] .= '';
		foreach($categories_RET as $category)
			if($can_use_RET['Students/Student.php&category_id='.$category['ID']])
			{
			$extra['extra_header_left'] .= '<TR><TD align="right" style="white-space:nowrap">'.$category['TITLE'].'</td>';
				$extra['extra_header_left'] .= '<td><INPUT type=checkbox name=category['.$category['ID'].'] value=Y checked></TD></TR>';
				
			}
		$extra['extra_header_left'] .= '</TABLE>';*/
	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;

	Widgets('mailing_labels');
	Widgets('course');
	Widgets('request');
	Widgets('activity');
	Widgets('absences');
	Widgets('gpa');
	Widgets('class_rank');
	Widgets('letter_grade');
	Widgets('eligibility');

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Print Info for Selected Students\'></CENTER>';
		echo "</FORM>";
	}
}

// GetStuList by default translates the grade_id to the grade title which we don't want here.
// One way to avoid this is to provide a translation function for the grade_id so here we
// provide a passthru function just to avoid the translation.
function _grade_id($value)
{
	return $value;
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}

function explodeCustom(&$categories_RET, &$custom, $prefix)
{
	foreach($categories_RET as $id=>$category)
		foreach($category as $i=>$field)
		{
			$custom .= ','.$prefix.'.CUSTOM_'.$field['ID'];
			if($field['TYPE']=='select' || $field['TYPE']=='codeds')
			{
				$select_options = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
				$select_options = explode("\r",$select_options);
				$options = array();
				foreach($select_options as $option)
				{
					if($field['TYPE']=='codeds')
					{
						$option = explode('|',$option);
						if($option[0]!='' && $option[1]!='')
							$options[$option[0]] = $option[1];
					}
					else
						$options[$option] = $option;
				}
				$categories_RET[$id][$i]['SELECT_OPTIONS'] = $options;
			}
		}
}

function printCustom(&$categories, &$values)
{
	echo "<table width=100%><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-weight:bold;\">".$categories[1]['CATEGORY_TITLE']."</td></tr>";
	foreach($categories as $field)
	{
		echo '<TR>';
		echo '<TD>'.($field['REQUIRED']&&$values['CUSTOM_'.$field['ID']]==''?'<FONT color=red>':'').$field['TITLE'].($field['REQUIRED']&&$values['CUSTOM_'.$field['ID']]==''?'</FONT>':'').'</TD>';
		if($field['TYPE']=='select')
			echo '<TD>'.($field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]!=''?'':'<FONT color=red>').$values['CUSTOM_'.$field['ID']].($field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]!=''?'':'</FONT>').'</TD>';
		elseif($field['TYPE']=='codeds')

			echo '<TD>'.($field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]!=''?$field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]:'<FONT color=red>'.$values['CUSTOM_'.$field['ID']].'</FONT>').'</TD>';
		else
			echo '<TD>'.$values['CUSTOM_'.$field['ID']].'</TD>';
		echo '</TR>';
	}
	echo '</table>';
}
/*
function con_date($date)
{
	$mother_date = $date;
	$year = substr($mother_date, 2, 2);
	$temp_month = substr($mother_date, 5, 2);
	
	if($temp_month == '01')
		$month = 'JAN';
	elseif($temp_month == '02')
		$month = 'FEB';
	elseif($temp_month == '03')
		$month = 'MAR';
	elseif($temp_month == '04')
		$month = 'APR';
	elseif($temp_month == '05')
		$month = 'MAY';
	elseif($temp_month == '06')
		$month = 'JUN';
	elseif($temp_month == '07')
		$month = 'JUL';
	elseif($temp_month == '08')
		$month = 'AUG';
	elseif($temp_month == '09')
		$month = 'SEP';
	elseif($temp_month == '10')
		$month = 'OCT';
	elseif($temp_month == '11')
		$month = 'NOV';
	elseif($temp_month == '12')
		$month = 'DEC';
		
	$day = substr($mother_date, 8, 2);
	
	$select_date = $day.'-'.$month.'-'.$year;
	return $select_date;
}
*/
function con_date($date)
{
	$mother_date = $date;
	$year = substr($mother_date, 7);
	$temp_month = substr($mother_date, 3, 3);
	
		if($temp_month == 'JAN')
			$month = '01';
		elseif($temp_month == 'FEB')
			$month = '02';
		elseif($temp_month == 'MAR')
			$month = '03';
		elseif($temp_month == 'APR')
			$month = '04';
		elseif($temp_month == 'MAY')
			$month = '05';
		elseif($temp_month == 'JUN')
			$month = '06';
		elseif($temp_month == 'JUL')
			$month = '07';
		elseif($temp_month == 'AUG')
			$month = '08';
		elseif($temp_month == 'SEP')
			$month = '09';
		elseif($temp_month == 'OCT')
			$month = '10';
		elseif($temp_month == 'NOV')
			$month = '11';
		elseif($temp_month == 'DEC')
			$month = '12';
			
	$day = substr($mother_date, 0, 2);
	
	$select_date = $year.'-'.$month.'-'.$day;
	return $select_date;
}

?>
