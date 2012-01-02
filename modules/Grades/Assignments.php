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
#DrawHeader('Gradebook - '.ProgramTitle());
/*
$course_id = DBGet(DBQuery("SELECT COURSE_ID,COURSE_PERIOD_ID FROM COURSE_PERIODS WHERE TEACHER_ID='".User('STAFF_ID')."' AND PERIOD_ID='".UserPeriod()."' AND MARKING_PERIOD_ID IN (".GetAllMP('QTR',UserMP()).')'));
$course_period_id = $course_id[1]['COURSE_PERIOD_ID'];
$course_id = $course_id[1]['COURSE_ID'];
*/
include('../../Redirect_modules.php');
$course_period_id = UserCoursePeriod();
$course_id = DBGet(DBQuery("SELECT COURSE_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".UserCoursePeriod()."'"));
$course_id = $course_id[1]['COURSE_ID'];

$_openSIS['allow_edit'] = true;
unset($_SESSION['_REQUEST_vars']['assignment_type_id']);
unset($_SESSION['_REQUEST_vars']['assignment_id']);

$config_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='".User('STAFF_ID')."' AND PROGRAM='Gradebook'"),array(),array('TITLE'));
if(count($config_RET))
	foreach($config_RET as $title=>$value)
		$programconfig[$title] = $value[1]['VALUE'];
else
	$programconfig = true;

//echo '<pre>';print_r($_REQUEST['day_tables']);echo'</pre>';
if(clean_param($_REQUEST['day_tables'],PARAM_NOTAGS) && ($_POST['day_tables'] || $_REQUEST['ajax']))
{
	foreach($_REQUEST['day_tables'] as $id=>$values)
	{
		if($_REQUEST['day_tables'][$id]['DUE_DATE'] && $_REQUEST['month_tables'][$id]['DUE_DATE'] && $_REQUEST['year_tables'][$id]['DUE_DATE'])
			$_REQUEST['tables'][$id]['DUE_DATE'] =date("Y-m-d",strtotime($_REQUEST['day_tables'][$id]['DUE_DATE'].'-'.$_REQUEST['month_tables'][$id]['DUE_DATE'].'-'.$_REQUEST['year_tables'][$id]['DUE_DATE']));
		if($_REQUEST['day_tables'][$id]['ASSIGNED_DATE'] && $_REQUEST['month_tables'][$id]['ASSIGNED_DATE'] && $_REQUEST['year_tables'][$id]['ASSIGNED_DATE'])
			$_REQUEST['tables'][$id]['ASSIGNED_DATE'] = date("Y-m-d",strtotime($_REQUEST['day_tables'][$id]['ASSIGNED_DATE'].'-'.$_REQUEST['month_tables'][$id]['ASSIGNED_DATE'].'-'.$_REQUEST['year_tables'][$id]['ASSIGNED_DATE']));
	}
	$_POST['tables'] = $_REQUEST['tables'];
}


if(clean_param($_REQUEST['tables'],PARAM_NOTAGS) && ($_POST['tables'] || $_REQUEST['ajax']))
{
	$table = $_REQUEST['table'];
	foreach($_REQUEST['tables'] as $id=>$columns)
	{
		if($table=='GRADEBOOK_ASSIGNMENT_TYPES' && $programconfig['WEIGHT']=='Y')
        		$columns['FINAL_GRADE_PERCENT'] = ereg_replace('[^0-9.]','',clean_param($columns['FINAL_GRADE_PERCENT'],PARAM_PERCENT)) / 100;

		if($id!='new')
		{
                    if(trim($columns['TITLE'])!="" || !isset($columns['TITLE']))
                    {
			if($columns['ASSIGNMENT_TYPE_ID'] && $columns['ASSIGNMENT_TYPE_ID']!=$_REQUEST['assignment_type_id'])
				$_REQUEST['assignment_type_id'] = $columns['ASSIGNMENT_TYPE_ID'];

			$sql = "UPDATE $table SET ";

			if(!$columns['COURSE_ID'] && $table=='GRADEBOOK_ASSIGNMENTS')
				$columns['COURSE_ID'] = 'N';

			foreach($columns as $column=>$value)
			{
				/*if($column=='DUE_DATE' || $column=='ASSIGNED_DATE')
		 		{
					if(!VerifyDate($value))
			 			BackPrompt('Not all of the dates were entered correctly.');
				}
				elseif($column=='COURSE_ID' && $value=='Y' && $table=='GRADEBOOK_ASSIGNMENTS')
				{
					$value = $course_id;
					$sql .= 'COURSE_PERIOD_ID=NULL,';
				}
				elseif($column=='COURSE_ID' && $table=='GRADEBOOK_ASSIGNMENTS')
				{
					$column = 'COURSE_PERIOD_ID';
					$value = $course_period_id;
					$sql .= 'COURSE_ID=NULL,';
				}*/

                                if($column=='COURSE_ID' && $value=='Y' && $table=='GRADEBOOK_ASSIGNMENTS')
				{
					$value = $course_id;
					$sql .= 'COURSE_PERIOD_ID=NULL,';
				}
				elseif($column=='COURSE_ID' && $table=='GRADEBOOK_ASSIGNMENTS')
				{
					$column = 'COURSE_PERIOD_ID';
					$value = $course_period_id;
					$sql .= 'COURSE_ID=NULL,';
				}

                                 $value= paramlib_validation($column,$value);
				if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux'))
                                                {
                                                 $value =  mysql_real_escape_string($value);
                                                 $value=str_replace('%u201D', "\"", $value);
        					}
				$sql .= $column."='".$value." ',";
			}
			$sql = substr($sql,0,-1) . " WHERE ".substr($table,10,-1)."_ID='$id'";
			$go = true;
		}
		else
		{
                        ShowErrPhp('Title Cannot be Blank');
                    }
		}
		else
		{
			$sql = "INSERT INTO $table ";

			if($table=='GRADEBOOK_ASSIGNMENTS')
			{
				if($columns['ASSIGNMENT_TYPE_ID'])
				{
					$_REQUEST['assignment_type_id'] = $columns['ASSIGNMENT_TYPE_ID'];
					unset($columns['ASSIGNMENT_TYPE_ID']);
				}
				//$id = DBGet(DBQuery("SELECT ".db_seq_nextval('GRADEBOOK_ASSIGNMENTS_SEQ').' AS ID '.FROM_DUAL));
				$id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'GRADEBOOK_ASSIGNMENTS'"));
                                $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
                                $id = $id[1]['ID'];
				$fields = "ASSIGNMENT_TYPE_ID,STAFF_ID,MARKING_PERIOD_ID,";
				$values = "'".$_REQUEST['assignment_type_id']."','".User('STAFF_ID')."','".UserMP()."',";
				$_REQUEST['assignment_id'] = $id;
			}
			elseif($table=='GRADEBOOK_ASSIGNMENT_TYPES')
			{
				//$id = DBGet(DBQuery("SELECT ".db_seq_nextval('GRADEBOOK_ASSIGNMENT_TYPES_SEQ').' AS ID '.FROM_DUAL));
				$id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'GRADEBOOK_ASSIGNMENT_TYPES'"));
                                $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
                                $id = $id[1]['ID'];
				$fields = "STAFF_ID,COURSE_ID,COURSE_PERIOD_ID,";
				$values = "'".User('STAFF_ID')."','$course_id','$course_period_id',";
				$_REQUEST['assignment_type_id'] = $id;
			}

			$go = false;

			if(!$columns['COURSE_ID'] && $_REQUEST['table']=='GRADEBOOK_ASSIGNMENTS')
				$columns['COURSE_ID'] = 'N';

			foreach($columns as $column=>$value)
			{
				/*if($column=='DUE_DATE' || $column=='ASSIGNED_DATE')
		 		{
					if(!VerifyDate($value))
			 			BackPrompt('Not all of the dates were entered correctly.');
				}
				elseif($column=='COURSE_ID' && $value=='Y')
					$value = $course_id;
				elseif($column=='COURSE_ID')
				{
					$column = 'COURSE_PERIOD_ID';
					$value = $course_period_id;
				}
                                */

                                if($column=='COURSE_ID' && $value=='Y')
					$value = $course_id;
				elseif($column=='COURSE_ID')
				{
					$column = 'COURSE_PERIOD_ID';
					$value = $course_period_id;
				}

				if($value!='')
				{
                                        $value= paramlib_validation($column,$value);
					$fields .= $column.',';
					#$values .= "'".str_replace("\'","''",$value)."',";
					if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux'))
                                                {
                                                 $value =  mysql_real_escape_string($value);
        					}
					$values .= '"'.$value.'",';
					$go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
		}

		if($go){
			DBQuery($sql);
                        DBQuery("UPDATE GRADEBOOK_ASSIGNMENTS SET UNGRADED=2 WHERE ASSIGNMENT_ID IN (SELECT ASSIGNMENT_ID FROM GRADEBOOK_GRADES WHERE POINTS IS NULL OR POINTS='') OR ASSIGNMENT_ID NOT IN (SELECT ASSIGNMENT_ID FROM GRADEBOOK_GRADES WHERE POINTS IS NOT NULL OR POINTS!='')");
                        }
	}
	unset($_REQUEST['tables']);
	unset($_SESSION['_REQUEST_vars']['tables']);
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete')
{
		if($_REQUEST['assignment_id'])
		{
			#$table = 'assignment';
			#$sql = "DELETE FROM GRADEBOOK_ASSIGNMENTS WHERE ASSIGNMENT_ID='$_REQUEST[assignment_id]'";
			$table = 'assignment';
			$sql = "DELETE FROM GRADEBOOK_ASSIGNMENTS WHERE assignment_id='$_REQUEST[assignment_id]'";
		}
		else
		{
			$data=DBGet(DBQuery("SELECT assignment_id FROM GRADEBOOK_ASSIGNMENTS WHERE assignment_type_id=$_REQUEST[assignment_type_id]"));
			if (count($data)<=0){
			#	$table = 'assignment type';
			#	$sql = "DELETE FROM GRADEBOOK_ASSIGNMENT_TYPES WHERE ASSIGNMENT_TYPE_ID='$_REQUEST[assignment_type_id]'";
				$table = 'assignment type';
				$sql = "DELETE FROM GRADEBOOK_ASSIGNMENT_TYPES WHERE assignment_type_id='$_REQUEST[assignment_type_id]'";		
			}
			else {
				PopTable('header','Unable to Delete');
						DrawHeaderHome('<font color=red>Assignment Type cannot be deleted.</font>');
						echo '<div align=right><a href=Modules.php?modname=Grades/Assignments.php&assignment_id='.$_REQUEST['assignment_id'].'&assignment_type_id='.$_REQUEST['assignment_type_id'].' style="text-decoration:none"><b>back to Assignment Type</b></a></div>';
						PopTable('footer');
			}
		}
		
	$data=DBGet(DBQuery("select assignment_id from GRADEBOOK_ASSIGNMENTS where assignment_type_id=$_REQUEST[assignment_type_id]"));
	
	if (count($data)<=0 && !$_REQUEST['assignment_id']) {
	
		if(DeletePromptAssignment($table))
		{
				DBQuery($sql);
		
			if(!$_REQUEST['assignment_id'] )
			{
				$assignments_RET = DBGet(DBQuery("SELECT ASSIGNMENT_ID FROM GRADEBOOK_ASSIGNMENTS WHERE ASSIGNMENT_TYPE_ID='$_REQUEST[assignment_type_id]'"));
				if(count($assignments_RET))
				{
					foreach($assignments_RET as $assignment_id)
						DBQuery("DELETE FROM GRADEBOOK_GRADES WHERE assignment_id='".$assignment_id['ASSIGNMENT_ID']."'");
				}
				DBQuery("DELETE FROM GRADEBOOK_ASSIGNMENTS WHERE assignment_type_id='$_REQUEST[assignment_type_id]'");
				unset($_REQUEST['assignment_type_id']);
			}
			/*else
			{
				DBQuery("DELETE FROM GRADEBOOK_GRADES WHERE ASSIGNMENT_ID='$_REQUEST[assignment_id]'");
				unset($_REQUEST['assignment_id']);
			}*/
			unset($_REQUEST['modfunc']);
		}
	}
	
	if ($_REQUEST['assignment_id']){
	include("classes/db/db.mysqli.class.php");
	$stmt = $mysqli->prepare("SELECT COUNT(*) AS TOTAL_ASSIGNED FROM GRADEBOOK_GRADES WHERE assignment_id=".$_REQUEST['assignment_id']);
					#$stmt->bind_param("i",$marking_period_id); //binding name as string
					#$marking_period_id = $courses[$key]['MARKING_PERIOD_ID'];
					$stmt->execute();
					$has_assigned=0;
					$stmt->bind_result($has_assigned);
					$stmt->fetch();
		if($has_assigned>0){
		UnableDeletePrompt('Gradebook Assignment cannot be deleted because it has other associations.');
		}else{
		if(DeletePromptAssignment($table, $_REQUEST['assignment_type_id']))
			{
						DBQuery($sql);
				DBQuery("DELETE FROM GRADEBOOK_GRADES WHERE assignment_id='$_REQUEST[assignment_id]'");
						unset($_REQUEST['assignment_id']);
						unset($_REQUEST['modfunc']);
			}
		}
	}
}

if(!$_REQUEST['modfunc'] && $course_id)
{
	// ASSIGNMENT TYPES
	$sql = " SELECT distinct gat.ASSIGNMENT_TYPE_ID,gat.TITLE FROM COURSE_PERIODS cp , GRADEBOOK_ASSIGNMENT_TYPES gat where cp.COURSE_PERIOD_ID=gat.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='".$course_period_id."' ORDER BY gat.TITLE";
	$QI = DBQuery($sql);
	$types_RET = DBGet($QI);

	if($_REQUEST['assignment_id']!='new' && $_REQUEST['assignment_type_id']!='new')
		$delete_button = "<INPUT type=button value=Delete onClick='javascript:window.location=\"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&assignment_type_id=$_REQUEST[assignment_type_id]&assignment_id=$_REQUEST[assignment_id]\"'>";

	// ADDING & EDITING FORM
	if($_REQUEST['assignment_id'] && $_REQUEST['assignment_id']!='new')
	{
		$sql = "SELECT ASSIGNMENT_TYPE_ID,TITLE,ASSIGNED_DATE,DUE_DATE,POINTS,COURSE_ID,DESCRIPTION,
				CASE WHEN DUE_DATE<ASSIGNED_DATE THEN 'Y' ELSE NULL END AS DATE_ERROR
				FROM GRADEBOOK_ASSIGNMENTS
				WHERE ASSIGNMENT_ID='$_REQUEST[assignment_id]'";
		$QI = DBQuery($sql);
		$RET = DBGet($QI);
		$RET = $RET[1];
		$title = $RET['TITLE'];
	}
	elseif($_REQUEST['assignment_type_id'] && $_REQUEST['assignment_type_id']!='new' && $_REQUEST['assignment_id']!='new')
	{
		$sql = "SELECT at.TITLE,at.FINAL_GRADE_PERCENT,
				(SELECT sum(FINAL_GRADE_PERCENT) FROM GRADEBOOK_ASSIGNMENT_TYPES WHERE COURSE_ID='$course_id' ) AS TOTAL_PERCENT
				FROM GRADEBOOK_ASSIGNMENT_TYPES at
				WHERE at.ASSIGNMENT_TYPE_ID='$_REQUEST[assignment_type_id]'";
		$QI = DBQuery($sql);
		$RET = DBGet($QI,array('FINAL_GRADE_PERCENT'=>'_makePercent'));
		$RET = $RET[1];
		$title = $RET['TITLE'];
	}
	elseif($_REQUEST['assignment_id']=='new')
	{
		$title = 'New Assignment';
		$new = true;
	}
	elseif($_REQUEST['assignment_type_id']=='new')
	{
		$sql = "SELECT sum(FINAL_GRADE_PERCENT) AS TOTAL_PERCENT FROM GRADEBOOK_ASSIGNMENT_TYPES WHERE COURSE_ID='$course_id' AND STAFF_ID='".User('STAFF_ID')."'";
		$QI = DBQuery($sql);
		$RET = DBGet($QI,array('FINAL_GRADE_PERCENT'=>'_makePercent'));
		$RET = $RET[1];
		$title = 'New Assignment Type';
	}

	if($_REQUEST['assignment_id'])
	{
           
		echo "<FORM name=F3 action=Modules.php?modname=$_REQUEST[modname]&assignment_type_id=$_REQUEST[assignment_type_id]";
		if($_REQUEST['assignment_id']!='new')
			echo "&assignment_id=$_REQUEST[assignment_id]";
		echo "&table=GRADEBOOK_ASSIGNMENTS method=POST>";

		DrawHeader($title,$delete_button.'<INPUT type=submit value=Save onclick="formcheck_assignments();">');
		$header .= '<TABLE cellpadding=3 bgcolor=#F0F0F1 width=100%>';
		$header .= '<TR>';

		$header .= '<TD>' . TextInput($RET['TITLE'],'tables['.$_REQUEST['assignment_id'].'][TITLE]',($RET['TITLE']?'':'<FONT color=red>').'Title'.($RET['TITLE']?'':'</FONT>'),'size=36') . '</TD>';
		$header .= '<TD>' . TextInput($RET['POINTS'],'tables['.$_REQUEST['assignment_id'].'][POINTS]',($RET['POINTS']!=''?'':'<FONT color=red>').'Points'.($RET['POINTS']?'':'</FONT>'),' size=4 maxlength=4') . '</TD>';
		$header .= '<TD>' . CheckboxInput($RET['COURSE_ID'],'tables['.$_REQUEST['assignment_id'].'][COURSE_ID]','Apply to all Periods for this Course') . '</TD>';
		foreach($types_RET as $type)
			$assignment_type_options[$type['ASSIGNMENT_TYPE_ID']] = $type['TITLE'];

		$header .= '<TD>' . SelectInput($RET['ASSIGNMENT_TYPE_ID']?$RET['ASSIGNMENT_TYPE_ID']:$_REQUEST['assignment_type_id'],'tables['.$_REQUEST['assignment_id'].'][ASSIGNMENT_TYPE_ID]','Assignment Type',$assignment_type_options,false) . '</TD>';
		$header .= '</TR><TR>';
		$header .= '<TD valign=top>' . DateInput($new && Preferences('DEFAULT_ASSIGNED','Gradebook')=='Y'?DBDate():$RET['ASSIGNED_DATE'],'tables['.$_REQUEST['assignment_id'].'][ASSIGNED_DATE]','Assigned',!$new) . '</TD>';
		$header .= '<TD valign=top>' . DateInput($new && Preferences('DEFAULT_DUE','Gradebook')=='Y'?DBDate():$RET['DUE_DATE'],'tables['.$_REQUEST['assignment_id'].'][DUE_DATE]','Due',!$new) . '</TD>';
		$header .= '<TD rowspan=2 colspan=2>' . TextareaInput($RET['DESCRIPTION'],'tables['.$_REQUEST['assignment_id'].'][DESCRIPTION]','Description') . '</TD>';
		$header .= '</TR>';
		$header .= '<TR><TD valign=top colspan=2>'.($RET['DATE_ERROR']=='Y'?'<Font color=red>Due date earlier than assigned date!</FONT>':'').'</TD></TR>';
		$header .= '</TABLE>';
	}
	elseif($_REQUEST['assignment_type_id'])
	{
            
		echo "<FORM name=F3 action=Modules.php?modname=$_REQUEST[modname]&table=GRADEBOOK_ASSIGNMENT_TYPES";
		if($_REQUEST['assignment_type_id']!='new')
			echo "&assignment_type_id=$_REQUEST[assignment_type_id]";
		echo " method=POST>";
           
		DrawHeader($title,$delete_button.'<INPUT type=submit value=Save onclick="formcheck_assignments();">');
		$header .= '<TABLE cellpadding=3 bgcolor=#F0F0F1 width=100%>';
		$header .= '<TR>';

		$header .= '<TD>' . TextInput($RET['TITLE'],'tables['.$_REQUEST['assignment_type_id'].'][TITLE]','Title','size=36') . '</TD>';
		if($programconfig['WEIGHT']=='Y')
		{
			$header .= '<TD>' . TextInput($RET['FINAL_GRADE_PERCENT'],'tables['.$_REQUEST['assignment_type_id'].'][FINAL_GRADE_PERCENT]',($RET['FINAL_GRADE_PERCENT']!=0?'':'<FONT color=red>').'Percent of Final Grade'.($RET['FINAL_GRADE_PERCENT']!=0?'':'</FONT>')) . '</TD>';
			$header .= '<TD>' . NoInput($RET['TOTAL_PERCENT']==1?'100%':'<FONT COLOR=red>'.(100*$RET['TOTAL_PERCENT']).'%</FONT>','Percent Total') . '</TD>';
		}

		$header .= '</TR>';
		$header .= '</TABLE>';
	}
	else
		$header = false;

	if($header)
	{
		DrawHeader($header);
		echo '</FORM>';
	}

	// DISPLAY THE MENU
	$LO_options = array('save'=>false,'search'=>false,'add'=>true);

	echo '<TABLE><TR>';

	if(count($types_RET))
	{
		if($_REQUEST['assignment_type_id'])
		{
			foreach($types_RET as $key=>$value)
			{
				if($value['ASSIGNMENT_TYPE_ID']==$_REQUEST['assignment_type_id'])
					$types_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
			}
		}
	}

	echo '<TD valign=top>';
	$columns = array('TITLE'=>'Assignment Type');
	$link = array();
	$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
	$link['TITLE']['variables'] = array('assignment_type_id'=>'ASSIGNMENT_TYPE_ID');
	$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&assignment_type_id=new";
	$link['add']['first'] = 50000; // number before add link moves to top

	ListOutput($types_RET,$columns,'Assignment Type','Assignment Types',$link,array(),$LO_options);
	echo '</TD>';


	// ASSIGNMENTS
	if($_REQUEST['assignment_type_id'] && $_REQUEST['assignment_type_id']!='new' && count($types_RET))
	{
		$sql = "SELECT ASSIGNMENT_ID,TITLE FROM GRADEBOOK_ASSIGNMENTS WHERE (COURSE_ID='".$course_id."' OR COURSE_PERIOD_ID='".$course_period_id."') AND ASSIGNMENT_TYPE_ID='".$_REQUEST['assignment_type_id']."' AND MARKING_PERIOD_ID='".UserMP()."' ORDER BY ".Preferences('ASSIGNMENT_SORTING','Gradebook')." DESC";
		$QI = DBQuery($sql);
		$assn_RET = DBGet($QI);

		if(count($assn_RET))
		{
			if($_REQUEST['assignment_id'] && $_REQUEST['assignment_id']!='new')
			{
				foreach($assn_RET as $key=>$value)
				{
					if($value['ASSIGNMENT_ID']==$_REQUEST['assignment_id'])
						$assn_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
				}
			}
		}

		echo '<TD valign=top>';
		$columns = array('TITLE'=>'Assignment');
		$link = array();
		$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&assignment_type_id=$_REQUEST[assignment_type_id]";
		$link['TITLE']['variables'] = array('assignment_id'=>'ASSIGNMENT_ID');
		$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&assignment_type_id=$_REQUEST[assignment_type_id]&assignment_id=new";
		$link['add']['first'] = 50000; // number before add link moves to top

		ListOutput($assn_RET,$columns,'Assignment','Assignments',$link,array(),$LO_options);

		echo '</TD>';
	}

	echo '</TR></TABLE>';
}
elseif(!$course_id)
	echo '<BR>'.ErrorMessage(array('You don\'t have a course this period.'),'error');

function _makePercent($value,$column)
{
	return Percent($value,2);
}
?>