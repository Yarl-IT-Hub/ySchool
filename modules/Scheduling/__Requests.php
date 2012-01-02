<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that 
#  include student demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  This program is released under the terms of the GNU General Public License as  
#  published by the Free Software Foundation, version 2 of the License. 
#  See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#***************************************************************************************
include('../../Redirect_modules.php');
DrawHeader(ProgramTitle());

Widgets('request');
Search('student_id',$extra);

if(!$_REQUEST['modfunc'] && UserStudentID())
	$_REQUEST['modfunc'] = 'choose';

if(UserStudentID())
{
	echo "<FORM name=vv id=vv action=Modules.php?modname=$_REQUEST[modname]&modfunc=verify method=POST>";
	DrawHeader('','<INPUT type=submit value=Save onclick=\'formload_ajax("vv")\';>');
}

if($_REQUEST['modfunc']=='verify')
{
	unset($courses);
	$QI = DBQuery("SELECT TITLE,COURSE_ID,SUBJECT_ID FROM COURSES WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'");
	$courses_RET = DBGet($QI,array(),array('COURSE_ID'));

	$QI = DBQuery("SELECT COURSE_WEIGHT,COURSE_ID FROM COURSE_WEIGHTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'");
	$weights_RET = DBGet($QI,array(),array('COURSE_ID','COURSE_WEIGHT'));

	DBQuery("DELETE FROM SCHEDULE_REQUESTS WHERE STUDENT_ID='".UserStudentID()."' AND SYEAR='".UserSyear()."'");
	
	foreach($_REQUEST['courses'] as $subject=>$r_courses)
	{
		$courses_count = count($r_courses);
		for($i=0;$i<$courses_count;$i++)
		{
			$course = $r_courses[$i];
			$weight = $_REQUEST['course_weights'][$subject][$i];

			if(!$course)
				continue;
			if(!$weight)
			{
				$error[] = "No weight was selectd for ".$courses_RET[$course][1]['TITLE'];
				continue;
			}
			if(!$weights_RET[$course][$weight])
			{
				$error[] = $courses_RET[$course][1]['TITLE'].' does not have a weight of '.$weight;
				unset($r_courses[$i]);
				continue;
			}
			
			$sql = "INSERT INTO SCHEDULE_REQUESTS (SYEAR,SCHOOL_ID,STUDENT_ID,SUBJECT_ID,COURSE_ID,COURSE_WEIGHT,MARKING_PERIOD_ID,WITH_TEACHER_ID,NOT_TEACHER_ID,WITH_PERIOD_ID,NOT_PERIOD_ID)
						values('".UserSyear()."','".UserSchool()."','".UserStudentID()."','".$courses_RET[$course][1]['SUBJECT_ID']."','".$course."','".$weight."',NULL,'".$_REQUEST['with_teacher'][$subject][$i]."','".$_REQUEST['without_teacher'][$subject][$i]."','".$_REQUEST['with_period'][$subject][$i]."','".$_REQUEST['without_period'][$subject][$i]."')";
			DBQuery($sql);
		}
	}
	echo ErrorMessage($error,'Error');
	
	$_SCHEDULER['student_id'] = UserStudentID();
	$_SCHEDULER['dont_run'] = true;
	include('modules/Scheduling/Scheduler.php');
	$_REQUEST['modfunc'] = 'choose';
}

if($_REQUEST['modfunc']=='choose')
{
	$QI = DBQuery("SELECT SUBJECT_ID,COURSE_ID,COURSE_WEIGHT,WITH_PERIOD_ID,NOT_PERIOD_ID,WITH_TEACHER_ID,NOT_TEACHER_ID FROM SCHEDULE_REQUESTS WHERE SYEAR='".UserSyear()."' AND STUDENT_ID='".UserStudentID()."'");
	$requests_RET = DBGet($QI,array(),array('SUBJECT_ID'));
	
	$QI = DBQuery("SELECT SUBJECT_ID,TITLE FROM COURSE_SUBJECTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY TITLE");
	$subjects_RET = DBGet($QI,array(),array('SUBJECT_ID'));
	
	$QI = DBQuery("SELECT DISTINCT COURSE_ID,TITLE,SUBJECT_ID FROM COURSES WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'");
	$courses_RET = DBGet($QI,array(),array('SUBJECT_ID','COURSE_ID'));

	$QI = DBQuery("SELECT DISTINCT c.SUBJECT_ID,cw.COURSE_WEIGHT FROM COURSE_WEIGHTS cw,COURSES c WHERE c.COURSE_ID=cw.COURSE_ID AND c.SCHOOL_ID='".UserSchool()."' AND c.SYEAR='".UserSyear()."'");
	$weights_RET = DBGet($QI,array(),array('SUBJECT_ID'));

	$QI = DBQuery("SELECT COURSE_WEIGHT,COURSE_ID FROM COURSE_WEIGHTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'");
	$course_weights_RET = DBGet($QI,array(),array('COURSE_ID'));

	$QI = DBQuery("SELECT COURSE_WEIGHT,COURSE_ID,TEACHER_ID,PERIOD_ID FROM COURSE_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'");
	$periods_RET = DBGet($QI,array(),array('COURSE_ID','COURSE_WEIGHT'));

	$__DBINC_NO_SQLSHOW = true;

	echo "<script language=javascript>\n";

	foreach($subjects_RET as $key=>$value)
	{
		$html[$key] = "<TABLE><TR><TD width=10></TD><TD><SELECT name=courses[$key][]><OPTION value=''>Not Specified</OPTION>";
		
		if(count($courses_RET[$key]))
		{
			foreach($courses_RET[$key] as $crs_num=>$course)
				$html[$key] .= "<OPTION value='$crs_num'>".$course[1][TITLE]."</OPTION>";
		}
		$html[$key] .= "</SELECT></TD>";
		$html[$key] .= "<TD><SELECT name=course_weights[$key][]><OPTION value=''>Not Specified</OPTION>";
		if(count($weights_RET[$key]))
		{
			foreach($weights_RET[$key] as $weight)
				$html[$key] .= "<OPTION value='".$weight['COURSE_WEIGHT']."'>".$weight['COURSE_WEIGHT']."</OPTION>";
		}
		$html[$key] .= "</SELECT></TD>";
		$html[$key] .= "</TR></TABLE>";

		echo "var html_$key=\"$html[$key]\";\n";
	}
	echo "</script>";
	
	if(count($requests_RET))
	{
		foreach($requests_RET as $key=>$requests)
		{
			foreach($requests as $value)
			{
				$select_html[$key] .= "<TABLE><TR><TD width=10></TD><TD><SELECT name=courses[$key][]><OPTION value=''>Not Specified</OPTION>";
				
				if(count($courses_RET[$key]))
				{
					foreach($courses_RET[$key] as $crs_num=>$course)
						$select_html[$key] .= "<OPTION value='$crs_num'".(($value['COURSE_ID']==$crs_num)?' SELECTED':'').">".$course[1][TITLE]."</OPTION>";
				}
				$select_html[$key] .= "</SELECT></TD>";
				$select_html[$key] .= "<TD><SELECT name=course_weights[$key][]><OPTION value=''>Not Specified</OPTION>";
				if(count($course_weights_RET[$value['COURSE_ID']]))
				{
					foreach($course_weights_RET[$value['COURSE_ID']] as $weight)
						$select_html[$key] .= "<OPTION value='".$weight['COURSE_WEIGHT']."'".(($value['COURSE_WEIGHT']==$weight['COURSE_WEIGHT'])?' SELECTED':'').">".$weight['COURSE_WEIGHT']."</OPTION>";
				}
				$select_html[$key] .= "</SELECT></TD>";
				$with_teachers = $with_periods = $without_teachers = $without_periods = '';
				$teachers_done = $periods_done = array();
				foreach($periods_RET[$value['COURSE_ID']][$value['COURSE_WEIGHT']] as $period)
				{
					if(!$teachers_done[$period['TEACHER_ID']])
					{
						$with_teachers .= "<OPTION value=".$period['TEACHER_ID']." ".(($value['WITH_TEACHER_ID']==$period['TEACHER_ID'])?' SELECTED':'').">".GetTeacher($period['TEACHER_ID'])."</OPTION>";
						$without_teachers .= "<OPTION value=".$period['TEACHER_ID']." ".(($value['NOT_TEACHER_ID']==$period['TEACHER_ID'])?' SELECTED':'').">".GetTeacher($period['TEACHER_ID'])."</OPTION>";
					}
					if(!$periods_done[$period['PERIOD_ID']])
					{
						$with_periods .= "<OPTION value=".$period['PERIOD_ID']." ".(($value['WITH_PERIOD_ID']==$period['PERIOD_ID'])?' SELECTED':'').">".GetPeriod($period['PERIOD_ID']).'</OPTION>';
						$without_periods .= "<OPTION value=".$period['PERIOD_ID']." ".(($value['NOT_PERIOD_ID']==$period['PERIOD_ID'])?' SELECTED':'').">".GetPeriod($period['PERIOD_ID']).'</OPTION>';
					}
					
					$periods_done[$period['PERIOD_ID']] = true;
					$teachers_done[$period['TEACHER_ID']] = true;
				}
				
				$select_html[$key] .= "<TD><TABLE><TR><TD>With</TD><TD><SELECT name=with_teacher[$key][]><OPTION value=''>Not Specified</OPTION>".$with_teachers."</SELECT></TD><TD><SELECT name=with_period[$key][]><OPTION value=''>Not Specified</OPTION>".$with_periods."</TD></TR><TR><TR><TD>Without</TD><TD><SELECT name=without_teacher[$key][]><OPTION value=''>Not Specified</OPTION>".$without_teachers."</SELECT></TD><TD><SELECT name=without_period[$key][]><OPTION value=''>Not Specified</OPTION>".$without_periods."</TD></TR></TABLE></TD>";
				$select_html[$key] .= "</TR></TABLE>";
			}
		}
	}	

	echo "<BR><TABLE>";
	if(count($subjects_RET))
	{
		foreach($subjects_RET as $key=>$value)
		{
			echo "<TR><TD>".button('add','',"# onclick='javascript:addHTML(html_$key,$key); return false;'")."<TD><b>".$value[1][TITLE]."</b></TD></TR>";
			echo "<TR><TD></TD><TD>";
			if($select_html[$key])
				echo $select_html[$key];
			echo "<div id=$key>$html[$key]</div></TD></TR>";
		}
	}
	echo "</TABLE>";
	echo '</FORM>';
}
?>
