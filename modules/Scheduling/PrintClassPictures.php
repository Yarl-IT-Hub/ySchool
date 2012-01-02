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
if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['cp_arr']))
	{
		$cp_list = '\''.implode('\',\'',$_REQUEST['cp_arr']).'\'';

		$course_periods_RET = DBGet(DBQuery("SELECT cp.COURSE_PERIOD_ID,cp.TITLE,TEACHER_ID FROM COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID IN ($cp_list) ORDER BY (SELECT SORT_ORDER FROM SCHOOL_PERIODS WHERE PERIOD_ID=cp.PERIOD_ID)"));
		//echo '<pre>'; var_dump($course_periods_RET); echo '</pre>';
		if($_REQUEST['include_teacher']=='Y')
			$teachers_RET = DBGet(DBQuery("SELECT STAFF_ID,LAST_NAME,FIRST_NAME,ROLLOVER_ID FROM STAFF WHERE STAFF_ID IN (SELECT TEACHER_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID IN ($cp_list))"),array(),array('STAFF_ID'));
		//echo '<pre>'; var_dump($teachers_RET); echo '</pre>';

		$handle = PDFStart();
		if($_REQUEST['legal_size']=='Y')
			//echo '<!-- MEDIA SIZE 8.5x14in -->';
		$PCP_UserCoursePeriod = $_SESSION['UserCoursePeriod']; // save/restore for teachers

		foreach($course_periods_RET as $course_period)
		{
			$course_period_id = $course_period['COURSE_PERIOD_ID'];
			$teacher_id = $course_period['TEACHER_ID'];

			if($teacher_id)
			{
				$_openSIS['User'] = array(1=>array('STAFF_ID'=>$teacher_id,'NAME'=>'name','PROFILE'=>'teacher','SCHOOLS'=>','.UserSchool().',','SYEAR'=>UserSyear()));
				$_SESSION['UserCoursePeriod'] = $course_period_id;

				$extra = array('SELECT_ONLY'=>'s.STUDENT_ID,s.LAST_NAME,s.FIRST_NAME','ORDER_BY'=>'s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME');
				$RET = GetStuList($extra);
				//echo '<pre>'; var_dump($RET); echo '</pre>';

				if(count($RET))
				
				{
				echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
			echo "<tr><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Student Class Pictures</div></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
					echo '<TABLE border=1 style=border-collapse:collapse>';
					echo '<TR><TD colspan=5 align=center  style=font-size:15px; font-weight:bold;>'.UserSyear().'-'.(UserSyear()+1).' - '.$course_period['TITLE'].'</TD></TR>';
					$i = 0;
					if($_REQUEST['include_teacher']=='Y')
					{
						$teacher = $teachers_RET[$teacher_id][1];

						echo '<TR><TD valign=bottom><TABLE>';
						if($UserPicturesPath && (($size=getimagesize($picture_path=$UserPicturesPath.UserSyear().'/'.$teacher_id.'.JPG')) || $_REQUEST['last_year']=='Y' && $staff['ROLLOVER_ID'] && ($size=getimagesize($picture_path=$UserPicturesPath.(UserSyear()-1).'/'.$staff['ROLLOVER_ID'].'.JPG'))))
							if($size[1]/$size[0] > 172/130)
								echo '<TR><TD><IMG SRC="'.$picture_path.'" width=144></TD></TR>';
							else
								echo '<TR><TD><IMG SRC="'.$picture_path.'" width=144></TD></TR>';
						else
							echo '<TR><TD><img src="assets/noimage.jpg" width=144></TD></TR>';
						echo '<TR><TD><FONT size=-1><B>'.$teacher['LAST_NAME'].'</B><BR>'.$teacher['FIRST_NAME'].'</FONT></TD></TR>';
						echo '</TABLE></TD>';
						$i++;
					}

					foreach($RET as $student)
					{
						$student_id = $student['STUDENT_ID'];

						if($i++%5==0)
							echo '<TR>';
							
						echo '<TD valign=bottom><TABLE>';
						if($StudentPicturesPath && (($size=getimagesize($picture_path=$StudentPicturesPath.UserSyear().'/'.$student_id.'.JPG')) || $_REQUEST['last_year']=='Y' && ($size=getimagesize($picture_path=$StudentPicturesPath.(UserSyear()-1).'/'.$student_id.'.JPG'))))
							if($size[1]/$size[0] > 144/144)
								echo '<TR><TD><IMG SRC="'.$picture_path.'" width=144></TD></TR>';
							else
								echo '<TR><TD><IMG SRC="'.$picture_path.'" width=144></TD></TR>';
						else
							echo '<TR><TD><img src="assets/noimage.jpg" width=144></TD></TR>';
						echo '<TR><TD><FONT size=-1><B>'.$student['LAST_NAME'].'</B><BR>'.$student['FIRST_NAME'].'</FONT></TD></TR>';
						echo '</TABLE></TD>';

						if($i%5==0)
							echo '</TR><!-- NEED 2in -->';
					}
					if($i%5!=0)
						echo '</TR>';
					echo '</TABLE>';
					echo "<div style=\"page-break-before: always;\"></div>";
				}
			}
		}
		$_SESSION['UserCoursePeriod'] = $PCP_UserCoursePeriod;
		PDFStop($handle);
	}
	else
		BackPrompt('You must choose at least one course period.');
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Scheduling > ".ProgramTitle());

	if(User('PROFILE')!='admin')
		$_REQUEST['search_modfunc'] = 'list';

	if($_REQUEST['search_modfunc']=='list')
	{
	//	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save&_openSIS_PDF=true method=POST>";
		echo "<FORM name=inc id=inc action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&_openSIS_PDF=true method=POST target=_blank>";
		//$extra['header_right'] = '<INPUT type=submit value=\'Create Class Pictures for Selected Course Periods\'>';

		$extra['extra_header_left'] = '<TABLE>';
		$extra['extra_header_left'] .= '<TR><TD><INPUT type=checkbox name=include_teacher value=Y checked>Include Teacher</TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=legal_size value=Y>Legal Size Paper</TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=last_year value=Y>Use Last Year\'s if Missing</TD></TR>';
		if(User('PROFILE')=='admin' || User('PROFILE')=='teacher')
			$extra['extra_header_left'] .= '<TR><TD><INPUT type=checkbox name=include_inactive value=Y>Include Inactive Students</TD></TR>';
		$extra['extra_header_left'] .= '</TABLE>';
	}

	mySearch('course_period',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
	//	echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Create Class Pictures for Selected Course Periods\' onclick=\'formload_ajax("inc");\'></CENTER>';
	if($_SESSION['count_course_periods']!=0)	
            echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Create Class Pictures for Selected Course Periods\'></CENTER>';
		echo "</FORM>";
	}
}

function mySearch($type,$extra='')
{	global $extra;

	if(($_REQUEST['search_modfunc']=='search_fnc' || !$_REQUEST['search_modfunc']))
	{
		echo '<BR>';
		PopTable('header','Search');
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&search_modfunc=list&next_modname=$_REQUEST[next_modname] method=POST>";
		echo '<TABLE border=0>';

		$RET = DBGet(DBQuery("SELECT STAFF_ID,CONCAT(LAST_NAME,LAST_NAME,', ',FIRST_NAME) AS FULL_NAME FROM STAFF WHERE PROFILE='teacher' AND position(',".UserSchool().",' IN SCHOOLS)>0 AND SYEAR='".UserSyear()."' ORDER BY FULL_NAME"));
		echo '<TR><TD align=right width=120>Teacher</TD><TD>';
		echo "<SELECT name=teacher_id style='max-width:250;'><OPTION value=''>N/A</OPTION>";
		foreach($RET as $teacher)
			echo "<OPTION value=$teacher[STAFF_ID]>$teacher[FULL_NAME]</OPTION>";
		echo '</SELECT>';
		echo '</TD></TR>';

		$RET = DBGet(DBQuery("SELECT SUBJECT_ID,TITLE FROM COURSE_SUBJECTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY TITLE"));
		echo '<TR><TD align=right width=120>Subject</TD><TD>';
		echo "<SELECT name=subject_id style='max-width:250;'><OPTION value=''>N/A</OPTION>";
		foreach($RET as $subject)
			echo "<OPTION value=$subject[SUBJECT_ID]>$subject[TITLE]</OPTION>";
		echo '</SELECT>';

		$RET = DBGet(DBQuery("SELECT PERIOD_ID,TITLE FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
		echo '<TR><TD align=right width=120>Period</TD><TD>';
		echo "<SELECT name=period_id style='max-width:250;'><OPTION value=''>N/A</OPTION>";
		foreach($RET as $period)
			echo "<OPTION value=$period[PERIOD_ID]>$period[TITLE]</OPTION>";
		echo '</SELECT>';
		echo '</TD></TR>';

		Widgets('course');
		echo $extra['search'];

		echo '<TR><TD colspan=2 align=center>';
		echo '<BR>';
		echo Buttons('Submit','Reset');
		echo '</TD></TR>';
		echo '</TABLE>';
		echo '</FORM>';
		PopTable('footer');
	}
	else
	{
		DrawHeader('',$extra['header_right']);
		DrawHeader($extra['extra_header_left'],$extra['extra_header_right']);

		if(User('PROFILE')=='admin')
		{
			if($_REQUEST['teacher_id'])
				$where .= " AND cp.TEACHER_ID='$_REQUEST[teacher_id]'";
			if($_REQUEST['first'])
				$where .= " AND UPPER(s.FIRST_NAME) LIKE '".strtoupper($_REQUEST['first'])."%'";
			if($_REQUEST['w_course_period_id'])
				if($_REQUEST['w_course_period_id_which']=='course')
					$where .= " AND cp.COURSE_ID=(SELECT COURSE_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_REQUEST['w_course_period_id']."')";
				else
					$where .= " AND cp.COURSE_PERIOD_ID='".$_REQUEST['w_course_period_id']."'";
			if($_REQUEST['subject_id'])
			{
				$from .= ",COURSES c";
				$where .= " AND c.COURSE_ID=cp.COURSE_ID AND c.SUBJECT_ID='".$_REQUEST['subject_id']."'";
			}
			if($_REQUEST['period_id'])
				$where .= " AND cp.PERIOD_ID='".$_REQUEST['period_id']."'";

			$sql = "SELECT cp.COURSE_PERIOD_ID,cp.TITLE,sp.ATTENDANCE FROM COURSE_PERIODS cp,SCHOOL_PERIODS sp$from WHERE cp.SCHOOL_ID='".UserSchool()."' AND cp.SYEAR='".UserSyear()."' AND sp.PERIOD_ID=cp.PERIOD_ID$where";
		}
		elseif(User('PROFILE')=='teacher')
		{
			$sql = "SELECT cp.COURSE_PERIOD_ID,cp.TITLE,sp.ATTENDANCE FROM COURSE_PERIODS cp,SCHOOL_PERIODS sp WHERE cp.SCHOOL_ID='".UserSchool()."' AND cp.SYEAR='".UserSyear()."' AND cp.TEACHER_ID='".User('STAFF_ID')."' AND sp.PERIOD_ID=cp.PERIOD_ID";
		}
		else
		{
			$sql = "SELECT cp.COURSE_PERIOD_ID,cp.TITLE,sp.ATTENDANCE FROM COURSE_PERIODS cp,SCHOOL_PERIODS sp,SCHEDULE ss WHERE cp.SCHOOL_ID='".UserSchool()."' AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.SYEAR='".UserSyear()."' AND ss.STUDENT_ID='".UserStudentID()."' AND (CURRENT_DATE>=ss.START_DATE AND (ss.END_DATE IS NULL OR CURRENT_DATE<=ss.END_DATE)) AND sp.PERIOD_ID=cp.PERIOD_ID";
		}
		$sql .= ' ORDER BY sp.PERIOD_ID';

		$course_periods_RET = DBGet(DBQuery($sql),array('COURSE_PERIOD_ID'=>'_makeChooseCheckbox'));
	$_SESSION['count_course_periods'] = count($course_periods_RET);	
                $LO_columns = array('COURSE_PERIOD_ID'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'cp_arr\');"><A>','TITLE'=>'Course Period');
		ListOutput($course_periods_RET,$LO_columns,'Course Period','Course Periods');
	}
}

function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

	return "<INPUT type=checkbox name=cp_arr[] value=$value".($THIS_RET['ATTENDANCE']=='Y'?' checked':'').">";
}
?>