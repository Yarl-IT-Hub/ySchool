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
$tables = array('SCHOOL_PERIODS'=>'School Periods','SCHOOL_YEARS'=>'Marking Periods','REPORT_CARD_GRADES'=>'Report Card Grade Codes','REPORT_CARD_COMMENTS'=>'Report Card Comment Codes','ELIGIBILITY_ACTIVITIES'=>'Eligibility Activity Codes','ATTENDANCE_CODES'=>'Attendance Codes','SCHOOL_GRADELEVELS'=>'Grade Levels');

$table_list = '<TABLE align=center cellspacing="5" cellpadding="3" border=0><tr><td ><b>New School\'s Title:&nbsp;&nbsp;</b><INPUT type=text name=title value="New School"  class="cell_medium_wide"></td></tr>';
foreach($tables as $table=>$name)
{
	$table_list .= '<TR><TD><INPUT type=checkbox value=Y name=tables['.$table.'] CHECKED> '.$name.'</TD></TR>';
}
$table_list .= '</TABLE>';
if(clean_param($_REQUEST['copy'],PARAM_ALPHAMOD)=='done'){
echo '<strong>School information has been copied successfully.</strong>';
}else{
DrawBC("School Setup > ".ProgramTitle());

if(Prompt_Copy_School('Confirm Copy School','Are you sure you want to copy the data for '.GetSchool(UserSchool()).' to a new school?',$table_list))
{
	if(count($_REQUEST['tables']))
	{
                // $id = DBGet(DBQuery("SELECT ".db_seq_nextval('SCHOOLS_SEQ')." AS ID".FROM_DUAL));
                $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'SCHOOLS'"));
                $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
		$id = $id[1]['ID'];
		$copy_syear_RET=DBGet(DBQuery("SELECT SYEAR FROM SCHOOLS WHERE ID=".UserSchool()." LIMIT 1"));
                                    DBQuery("INSERT INTO SCHOOLS (ID,SYEAR,TITLE) values('".$id."',".$copy_syear_RET[1]['SYEAR'].",'".str_replace("\'","''",paramlib_validation($col=TITLE,$_REQUEST['title']))."')");
		DBQuery("UPDATE STAFF SET SCHOOLS=CONCAT(SCHOOLS,'$id',',') WHERE STAFF_ID='".User('STAFF_ID')."' AND SCHOOLS IS NOT NULL");
		foreach($_REQUEST['tables'] as $table=>$value)
			_rollover($table);
	}
	echo '<FORM action=Modules.php?modname='.$_REQUEST['modname'].' method=POST>';
	echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
	echo "<br><br>";
	DrawHeaderHome('<IMG SRC=assets/check.gif> &nbsp;The data have been copied to a new school called "'.paramlib_validation($col=TITLE,$_REQUEST['title']).'".To finish the operation, click OK button.','<INPUT  type=submit value=OK class="btn_medium">');
	echo '<input type="hidden" name="copy" value="done"/>';
	echo '</FORM>';
	unset($_SESSION['_REQUEST_vars']['tables']);
	unset($_SESSION['_REQUEST_vars']['delete_ok']);
}
}
function _rollover($table)
{	global $id;

	switch($table)
	{
		case 'SCHOOL_PERIODS':
                                    DBQuery("INSERT INTO SCHOOL_PERIODS (SYEAR,SCHOOL_ID,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,START_TIME,END_TIME,IGNORE_SCHEDULING,ATTENDANCE) SELECT SYEAR,'$id' AS SCHOOL_ID,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,START_TIME,END_TIME,IGNORE_SCHEDULING,ATTENDANCE FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'SCHOOL_GRADELEVELS':
			$table_properties = db_properties($table);
			$columns = '';
			foreach($table_properties as $column=>$values)
			{
				if($column!='ID' && $column!='SCHOOL_ID' && $column!='NEXT_GRADE_ID')
					$columns .= ','.$column;
			}
			DBQuery("INSERT INTO $table (SCHOOL_ID".$columns.") SELECT '$id' AS SCHOOL_ID".$columns." FROM $table WHERE SCHOOL_ID='".UserSchool()."'");
                                                      DBQuery("UPDATE $table t1,$table t2 SET t1.NEXT_GRADE_ID= t1.ID+1 WHERE t1.SCHOOL_ID='".$id."' AND t1.ID+1=t2.ID");
		break;

		case 'SCHOOL_YEARS':
			DBQuery("INSERT INTO SCHOOL_YEARS (MARKING_PERIOD_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT fn_marking_period_seq()+MARKING_PERIOD_ID,SYEAR,'$id' AS SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY MARKING_PERIOD_ID");
			DBQuery("INSERT INTO SCHOOL_SEMESTERS (MARKING_PERIOD_ID,YEAR_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT fn_marking_period_seq()+MARKING_PERIOD_ID,(SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS y WHERE y.SYEAR=s.SYEAR AND y.ROLLOVER_ID=s.YEAR_ID AND y.SCHOOL_ID='$id') AS YEAR_ID,SYEAR,'$id' AS SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS s WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY MARKING_PERIOD_ID");
			DBQuery("INSERT INTO SCHOOL_QUARTERS (MARKING_PERIOD_ID,SEMESTER_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT fn_marking_period_seq()+MARKING_PERIOD_ID,(SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS s WHERE s.SYEAR=q.SYEAR AND s.ROLLOVER_ID=q.SEMESTER_ID AND s.SCHOOL_ID='$id') AS SEMESTER_ID,SYEAR,'$id' AS SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM SCHOOL_QUARTERS q WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY MARKING_PERIOD_ID");
			DBQuery("INSERT INTO SCHOOL_PROGRESS_PERIODS (MARKING_PERIOD_ID,QUARTER_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT fn_marking_period_seq()+MARKING_PERIOD_ID,(SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS q WHERE q.SYEAR=p.SYEAR AND q.ROLLOVER_ID=p.QUARTER_ID AND q.SCHOOL_ID='$id'),SYEAR,'$id' AS SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM SCHOOL_PROGRESS_PERIODS p WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY MARKING_PERIOD_ID");

                                                      DBQuery("UPDATE SCHOOL_YEARS SET ROLLOVER_ID = NULL WHERE SCHOOL_ID='$id'");
                                                      DBQuery("UPDATE SCHOOL_SEMESTERS SET ROLLOVER_ID = NULL WHERE SCHOOL_ID='$id'");
                                                      DBQuery("UPDATE SCHOOL_QUARTERS SET ROLLOVER_ID = NULL WHERE SCHOOL_ID='$id'");
                                                      DBQuery("UPDATE SCHOOL_PROGRESS_PERIODS SET ROLLOVER_ID = NULL WHERE SCHOOL_ID='$id'");
                        
		break;

		case 'REPORT_CARD_GRADES':
			DBQuery("INSERT INTO REPORT_CARD_GRADE_SCALES (SYEAR,SCHOOL_ID,TITLE,COMMENT,SORT_ORDER,ROLLOVER_ID) SELECT SYEAR,'$id',TITLE,COMMENT,SORT_ORDER,ID FROM REPORT_CARD_GRADE_SCALES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
			DBQuery("INSERT INTO REPORT_CARD_GRADES (SYEAR,SCHOOL_ID,TITLE,COMMENT,BREAK_OFF,GPA_VALUE,GRADE_SCALE_ID,SORT_ORDER) SELECT SYEAR,'$id',TITLE,COMMENT,BREAK_OFF,GPA_VALUE,(SELECT ID FROM REPORT_CARD_GRADE_SCALES WHERE ROLLOVER_ID=REPORT_CARD_GRADES.GRADE_SCALE_ID AND SCHOOL_ID=REPORT_CARD_GRADES.SCHOOL_ID),SORT_ORDER FROM REPORT_CARD_GRADES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		
                                                      DBQuery("UPDATE REPORT_CARD_GRADE_SCALES SET ROLLOVER_ID=NULL WHERE SCHOOL_ID='$id'");
		break;

		case 'REPORT_CARD_COMMENTS':
			DBQuery("INSERT INTO REPORT_CARD_COMMENTS (SYEAR,SCHOOL_ID,TITLE,SORT_ORDER,COURSE_ID) SELECT SYEAR,'$id',TITLE,SORT_ORDER,NULL FROM REPORT_CARD_COMMENTS WHERE COURSE_ID IS NULL AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;

		case 'ELIGIBILITY_ACTIVITIES':
		case 'ATTENDANCE_CODES':
			$table_properties = db_properties($table);
			$columns = '';
			foreach($table_properties as $column=>$values)
			{
				if($column!='ID' && $column!='SYEAR' && $column!='SCHOOL_ID')
					$columns .= ','.$column;
			}
			DBQuery("INSERT INTO $table (SYEAR,SCHOOL_ID".$columns.") SELECT SYEAR,'$id' AS SCHOOL_ID".$columns." FROM $table WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
		break;
	}
}
?>