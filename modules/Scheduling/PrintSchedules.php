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
	if(count($_REQUEST['st_arr']))
	{
	$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
	$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";

	if($_REQUEST['day_include_active_date'] && $_REQUEST['month_include_active_date'] && $_REQUEST['year_include_active_date'])
	{
		$date = $_REQUEST['day_include_active_date'].'-'.$_REQUEST['month_include_active_date'].'-'.$_REQUEST['year_include_active_date'];
		$date_extra = 'OR (\''.$date.'\' >= sr.START_DATE AND sr.END_DATE IS NULL)';
	}
	else
	{
		$date = DBDate();
		$date_extra = 'OR sr.END_DATE IS NULL';
	}
	$columns = array('PERIOD_TITLE'=>'Period - Teacher','MARKING_PERIOD_ID'=>'Term','DAYS'=>'Days','DURATION'=>'Time','ROOM'=>'Room','COURSE_TITLE'=>'Course','COURSE_WEIGHT'=>'Weight');

	$extra['SELECT'] .= ',c.TITLE AS COURSE_TITLE,p_cp.TITLE AS PERIOD_TITLE,sr.MARKING_PERIOD_ID,p_cp.DAYS, CONCAT(sp.START_TIME, " to ", sp.END_TIME) AS DURATION,p_cp.ROOM';
	$extra['FROM'] .= ' LEFT OUTER JOIN SCHEDULE sr ON (sr.STUDENT_ID=ssm.STUDENT_ID),COURSES c,COURSE_PERIODS p_cp,SCHOOL_PERIODS sp ';
//	$extra['WHERE'] .= " AND p_cp.PERIOD_ID=sp.PERIOD_ID AND ssm.SYEAR=sr.SYEAR AND sr.COURSE_ID=c.COURSE_ID AND sr.COURSE_PERIOD_ID=p_cp.COURSE_PERIOD_ID AND p_cp.PERIOD_ID=sp.PERIOD_ID  AND ('$date' BETWEEN sr.START_DATE AND sr.END_DATE $date_extra)";
	$extra['WHERE'] .= " AND p_cp.PERIOD_ID=sp.PERIOD_ID AND ssm.SYEAR=sr.SYEAR AND sr.COURSE_ID=c.COURSE_ID AND sr.COURSE_PERIOD_ID=p_cp.COURSE_PERIOD_ID AND p_cp.PERIOD_ID=sp.PERIOD_ID";
                  if($_REQUEST['include_inactive']!='Y'){
                        $extra['WHERE'] .= " AND ('".date('Y-m-d',strtotime($date))."' BETWEEN sr.START_DATE AND sr.END_DATE OR (sr.END_DATE IS NULL AND sr.START_DATE<='".date('Y-m-d',strtotime($date))."')) ";
                  }
                  if($_REQUEST['mp_id']){
                        $extra['WHERE'] .= ' AND sr.MARKING_PERIOD_ID IN ('.GetAllMP(GetMPTable(GetMP($_REQUEST['mp_id'],'TABLE')),$_REQUEST['mp_id']).')';
                  }
	$extra['functions'] = array('MARKING_PERIOD_ID'=>'GetMP','DAYS'=>'_makeDays');
	$extra['group'] = array('STUDENT_ID');
	$extra['ORDER'] = ',sp.SORT_ORDER';
	if($_REQUEST['mailing_labels']=='Y')
		$extra['group'][] = 'ADDRESS_ID';
	Widgets('mailing_labels');

	$RET = GetStuList($extra);

	if(count($RET))
	{
		$handle = PDFStart();
			
		foreach($RET as $student_id=>$courses)
		{
		echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
			echo "<tr><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Student Schedules Report</div></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
			if($_REQUEST['mailing_labels']=='Y')
			{
				foreach($courses as $address)
				{
					echo '<BR>';
					unset($_openSIS['DrawHeader']);
					echo '<table  border=0>';
					if($address[1]['STUDENT_ID'] !='')
					{
					echo '<tr><td>Student ID:</td>';
					echo '<td>'.$address[1]['STUDENT_ID'] .'</td></tr>';
					}
					if($address[1]['FULL_NAME'] !='')
					{
					echo '<tr><td>Student Name:</td>';
					echo '<td>'.$address[1]['FULL_NAME'] .'</td></tr>';
					}
					if($address[1]['GRADE_ID'] !='')
					{
					echo '<tr><td>Student Grade:</td>';
					echo '<td>'.$address[1]['GRADE_ID'] .'</td></tr>';
					}
					if($address[1]['MAILING_LABEL'] !='')
			       {
					echo '<tr><td valign=top>Malling Details:</td>';
					echo '<td>'.$address[1]['MAILING_LABEL'].'</td></tr>';
					}
					echo '</table>';

					//echo '<BR><BR><TABLE width=100%><TR><TD width=50> &nbsp; </TD><TD>'.$address[1]['MAILING_LABEL'].'</TD></TR></TABLE><BR>';

					ListOutputPrint($address,$columns,'Course','Courses',array(),array(),array('center'=>false,'print'=>false));
					echo "<div style=\"page-break-before: always;\"></div>";
				}
			}
			else
			{
				unset($_openSIS['DrawHeader']);
				echo '<br>';
				echo '<table  border=0>';
					echo '<tr><td>Student ID:</td>';
					echo '<td>'.$courses[1]['STUDENT_ID'] .'</td></tr>';
					echo '<tr><td>Student Name:</td>';
					echo '<td>'.$courses[1]['FULL_NAME'] .'</td></tr>';
					echo '<tr><td>Student Grade:</td>';
					echo '<td>'.$courses[1]['GRADE_ID'] .'</td></tr>';
					if($courses[1]['MAILING_LABEL'] !='')
			       {
					echo '<tr><td>Malling Details:</td>';
					echo '<td>'.$courses[1]['MAILING_LABEL'].'</td></tr>';
					}
					echo '</table>';
			//echo '<div style="font-size:18px; font-weight:bold; ">'.Config('TITLE').' Student Schedule</div><div style="height:20px;"></div>';
			//echo '<div>'.$courses[1]['FULL_NAME'].' - #'.$courses[1]['STUDENT_ID']. '</div>';
			//echo '<div>'.$courses[1]['GRADE_ID'].' Grade</div>';
			//echo '<div>'.ProperDate($date),$_REQUEST['mp_id']?GetMP($_REQUEST['mp_id']):''.'</div>';
			
				ListOutputPrint($courses,$columns,'Course','Courses',array(),array(),array('center'=>false,'print'=>false));
				echo '<div style="page-break-before: always;">&nbsp;</div><!-- NEW PAGE -->';
			}
		}
		PDFStop($handle);
	}
	else
		BackPrompt('No Students were found.');
	}
	else
		BackPrompt('You must choose at least one student.');
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Scheduling >> ".ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		$mp_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,1 AS TBL FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' UNION SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,2 AS TBL FROM SCHOOL_SEMESTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' UNION SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,3 AS TBL FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY TBL,SORT_ORDER"));
		$mp_select = '<SELECT name=mp_id><OPTION value="">N/A';
		foreach($mp_RET as $mp)
			$mp_select .= '<OPTION value='.$mp['MARKING_PERIOD_ID'].'>'.$mp['TITLE'];
		$mp_select .= '</SELECT>';

		echo "<FORM name=sch id=sch action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_openSIS_PDF=true method=POST target=_blank>";
		#$extra['header_right'] = '<INPUT type=submit value=\'Create Schedules for Selected Students\'>';
		PopTable_wo_header ('header');
		$extra['extra_header_left'] = '<TABLE>';
		$extra['extra_header_left'] .= '<TR><TD align=right width=120>Marking Period</TD><TD>'.$mp_select.'</TD></TR>';
		$extra['extra_header_left'] .= '<TR><TD align=right width=120>Include only courses active as of</TD><TD>'.PrepareDate('','_include_active_date').'</TD></TR>';
		Widgets('mailing_labels',true);
		$extra['extra_header_left'] .= $extra['search'];
		$extra['search'] = '';
		$extra['extra_header_left'] .= '</TABLE>';
	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;
	//$extra['force_search'] = true;

	Widgets('request');
	Widgets('course');

	Search('student_id',$extra);

	if($_REQUEST['search_modfunc']=='list')
	{
		PopTable ('footer');
                if($_SESSION['count_stu']!=0)
		echo '<BR><CENTER><INPUT type=submit class=btn_xlarge value=\'Create Schedules for Selected Students\'></CENTER>';
		echo "</FORM>";
	}
}

function _makeDays($value,$column)
{
	foreach(array('U','M','T','W','H','F','S') as $day)
		if(strpos($value,$day)!==false)
			$return .= $day;
		else
			$return .= '-';
	return $return;
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}
?>
