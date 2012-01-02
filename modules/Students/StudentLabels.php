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
$max_cols = 3;
$max_rows = 10;

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='save')
{
	if(count($_REQUEST['st_arr']))
	{
		$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
		$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";

		$extra['SELECT'] .= ",coalesce(s.COMMON_NAME,s.FIRST_NAME) AS NICK_NAME";
		if(User('PROFILE')=='admin')
		{
			if($_REQUEST['w_course_period_id_which']=='course_period' && $_REQUEST['w_course_period_id'])
			{
				if($_REQUEST['teacher'])
					$extra['SELECT'] .= ",(SELECT CONCAT(st.FIRST_NAME,' ',st.LAST_NAME) FROM STAFF st,COURSE_PERIODS cp WHERE st.STAFF_ID=cp.TEACHER_ID AND cp.COURSE_PERIOD_ID='$_REQUEST[w_course_period_id]') AS TEACHER";
				if($_REQUEST['room'])
					$extra['SELECT'] .= ",(SELECT cp.ROOM FROM COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID='$_REQUEST[w_course_period_id]') AS ROOM";
			}
			else
			{
				if($_REQUEST['teacher'])
					$extra['SELECT'] .= ",(SELECT CONCAT(st.FIRST_NAME,' ',st.LAST_NAME) FROM STAFF st,COURSE_PERIODS cp,SCHOOL_PERIODS p,SCHEDULE ss WHERE st.STAFF_ID=cp.TEACHER_ID AND cp.PERIOD_id=p.PERIOD_ID AND p.ATTENDANCE='Y' AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.STUDENT_ID=s.STUDENT_ID AND ss.SYEAR='".UserSyear()."' AND ss.MARKING_PERIOD_ID IN(".GetAllMP('QTR',GetCurrentMP('QTR',DBDate(),false)).") AND (ss.START_DATE<='".DBDate()."' AND (ss.END_DATE>='".DBDate()."' OR ss.END_DATE IS NULL)) ORDER BY p.SORT_ORDER LIMIT 1) AS TEACHER";
				if($_REQUEST['room'])
					$extra['SELECT'] .= ",(SELECT cp.ROOM FROM COURSE_PERIODS cp,SCHOOL_PERIODS p,SCHEDULE ss WHERE cp.PERIOD_id=p.PERIOD_ID AND p.ATTENDANCE='Y' AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.STUDENT_ID=s.STUDENT_ID AND ss.SYEAR='".UserSyear()."' AND ss.MARKING_PERIOD_ID IN(".GetAllMP('QTR',GetCurrentMP('QTR',DBDate(),false)).") AND (ss.START_DATE<='".DBDate()."' AND (ss.END_DATE>='".DBDate()."' OR ss.END_DATE IS NULL)) ORDER BY p.SORT_ORDER LIMIT 1) AS ROOM";
			}
		}
		else
		{
			if($_REQUEST['teacher'])
				$extra['SELECT'] .= ",(SELECT CONCAT(st.FIRST_NAME,' ',st.LAST_NAME) FROM STAFF st,COURSE_PERIODS cp WHERE st.STAFF_ID=cp.TEACHER_ID AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."') AS TEACHER";
			if($_REQUEST['room'])
				$extra['SELECT'] .= ",(SELECT cp.ROOM FROM COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID='".UserCoursePeriod()."') AS ROOM";
		}
		$RET = GetStuList($extra);

		if(count($RET))
		{
			$skipRET = array();
			for($i=($_REQUEST['start_row']-1)*$max_cols+$_REQUEST['start_col']; $i>1; $i--)
				$skipRET[-$i] = array('LAST_NAME'=>' ');

			$handle = PDFstart();
			

			$cols = 0;
			$rows = 0;
			
			echo "<table width=100%  border=0 style=\" font-family:Arial; font-size:12px;\" >";
			echo "<tr><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Student Labels</div></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br \>Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
			echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" style=font-family:Arial; font-size:12px;>';
			foreach($skipRET+$RET as $i=>$student)
			{
				if($cols < 1)
					echo '<tr>';
				echo '<td width="33.3%" height="86" align="center" valign="middle">';
				echo '<table border=0 align=center>';
				echo '<tr>';
				echo '<td align=center>'.$student['NICK_NAME'].' '.$student['LAST_NAME'].'</td></tr>';
				if($_REQUEST['teacher']){
				echo '<tr><td align=center>Teacher :';
				echo ''.$student['TEACHER'].'</td></tr>';
				}
				if($_REQUEST['room']){
				echo '<tr><td align=center>Room No :';
				echo ''.$student['ROOM'].'</td></tr>';
				}
				echo '</table>';

				$cols++;

				if($cols == $max_cols)
				{
					echo '</tr>';
					$rows++;
					$cols=0;
				}

				if($rows == $max_rows)
				{
					echo '</table><div style="page-break-before: always;">&nbsp;</div>';
					echo '<table width="100%"  border="0" cellspacing="0" cellpadding="0">';
					$rows=0;
				}
			}

			if ($cols == 0 && $rows == 0)
			{}
			else
			{
				while ($cols !=0 && $cols < $max_cols)
				{
					echo '<td width="33.3%" height="86" align="center" valign="middle">&nbsp;</td>';
					$cols++;
				}
				if ($cols == $max_cols)
					echo '</tr>';
				echo '</table>';
			}

			PDFstop($handle);
		}
		else
			BackPrompt('No Students were found.');
	}
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Students >> ".ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_search_all_schools=$_REQUEST[_search_all_schools]".(User('PROFILE')=='admin'?"&w_course_period_id_which=$_REQUEST[w_course_period_id_which]&w_course_period_id=$_REQUEST[w_course_period_id]":'')."&_openSIS_PDF=true method=POST target=_blank>";
		//$extra['header_right'] = '<INPUT type=submit value=\'Create Labels for Selected Students\'>';

		$extra['extra_header_left'] = '<TABLE style="margin-top:-30px;">';

		$extra['extra_header_left'] .= '<TR><TD><b>Include on Labels:</b></TD></TR>';
		if(User('PROFILE')=='admin')
		{
			if($_REQUEST['w_course_period_id_which']=='course_period' && $_REQUEST['w_course_period_id'])
			{
				$course_RET = DBGet(DBQuery("SELECT CONCAT(s.FIRST_NAME,' ',s.LAST_NAME) AS TEACHER,cp.ROOM FROM STAFF s,COURSE_PERIODS cp WHERE s.STAFF_ID=cp.TEACHER_ID AND cp.COURSE_PERIOD_ID='$_REQUEST[w_course_period_id]'"));
				$extra['extra_header_left'] .= '<TR><TD><INPUT type=checkbox name=teacher value=Y>Teacher ('.$course_RET[1]['TEACHER'].')</TD></TR>';
				$extra['extra_header_left'] .= '<TR><TD><INPUT type=checkbox name=room value=Y>Room ('.$course_RET[1]['ROOM'].')</TD></TR>';
			}
			else
			{
				$extra['extra_header_left'] .= '<TR><TD><INPUT type=checkbox name=teacher value=Y>Attendance Teacher</TD></TR>';
				$extra['extra_header_left'] .= '<TR><TD><INPUT type=checkbox name=room value=Y>Attendance Room</TD></TR>';
			}
		}
		else
		{
			$extra['extra_header_left'] .= '<TR><TD><INPUT type=checkbox name=teacher value=Y>Teacher</TD></TR>';
			$extra['extra_header_left'] .= '<TR><TD><INPUT type=checkbox name=room value=Y>Room</TD></TR>';
		}

		$extra['extra_header_left'] .= '</TABLE>';
		$extra['extra_header_right'] = '<TABLE>';

		$extra['extra_header_right'] .= '<TR><TD align=right>Starting row</TD><TD><SELECT name=start_row style="width:40px;">';
		for($row=1; $row<=$max_rows; $row++)
			$extra['extra_header_right'] .=  '<OPTION value="'.$row.'">'.$row;
		$extra['extra_header_right'] .=  '</SELECT></TD></TR>';
		$extra['extra_header_right'] .= '<TR><TD align=right>Starting column</TD><TD><SELECT name=start_col style="width:40px;">';
		for($col=1; $col<=$max_cols; $col++)
			$extra['extra_header_right'] .=  '<OPTION value="'.$col.'">'.$col;
		$extra['extra_header_right'] .= '</SELECT></TD></TR>';

		$extra['extra_header_right'] .= '</TABLE>';
	}

	Widgets('course');
	//Widgets('request');
	//Widgets('activity');
	//Widgets('absences');
	//Widgets('gpa');
	//Widgets('class_rank');
	//Widgets('letter_grade');
	//Widgets('eligibility');
	//$extra['force_search'] = true;

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Create Labels for Selected Students\'></CENTER>';
		echo "</FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}
?>