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
include 'modules/Grades/config.inc.php';

DrawBC("GradeBook >> ".ProgramTitle());

$sem = GetParentMP('SEM',UserMP());
if($sem)
    $fy = GetParentMP('FY',$sem);
else
    $fy = GetParentMP('FY',UserMP());

$pros = GetChildrenMP('PRO',UserMP());

// if the UserMP has been changed, the REQUESTed MP may not work
if(!$_REQUEST['mp'] || strpos($str="'".UserMP()."','".$sem."','".$fy."',".$pros,"'".ltrim($_REQUEST['mp'],'E')."'")===false)
	$_REQUEST['mp'] = UserMP();

$course_period_id = UserCoursePeriod();
if($course_period_id)
$course_RET = DBGet(DBQuery("SELECT cp.COURSE_ID,c.TITLE as COURSE_NAME, cp.TITLE, cp.GRADE_SCALE_ID,CREDIT(cp.COURSE_PERIOD_ID,'".$_REQUEST['mp']."') AS CREDITS  FROM COURSE_PERIODS cp, COURSES c WHERE cp.COURSE_ID = c.COURSE_ID AND cp.COURSE_PERIOD_ID='".$course_period_id."'"));
if(!$course_RET[1]['GRADE_SCALE_ID'])                                  
{
        echo ErrorMessage(array('You cannot enter grades for this course.'),'error');
        $not_graded=true;
        $_REQUEST['use_percents']=true;
}
$course_title = $course_RET[1]['TITLE'];
$grade_scale_id = $course_RET[1]['GRADE_SCALE_ID'];
$course_id = $course_RET[1]['COURSE_ID'];

if($_REQUEST['mp'])
{
	$current_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_GRADE_ID,g.GRADE_PERCENT,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_GRADES g,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=g.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='$course_period_id' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."'"),array(),array('STUDENT_ID'));
	$current_commentsA_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_COMMENTS g,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=g.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='$course_period_id' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE COURSE_ID IS NOT NULL)"),array(),array('STUDENT_ID','REPORT_CARD_COMMENT_ID'));
	$current_commentsB_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID FROM STUDENT_REPORT_CARD_COMMENTS g,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=g.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='$course_period_id' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE COURSE_ID IS NULL)"),array(),array('STUDENT_ID'));
	$max_current_commentsB = 0;
	foreach($current_commentsB_RET as $comments)
		if(count($comments)>$max_current_commentsB)
			$max_current_commentsB = count($comments);
	$current_completed = count(DBGet(DBQuery("SELECT '' FROM GRADES_COMPLETED WHERE STAFF_ID='".User('STAFF_ID')."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND PERIOD_ID='".UserPeriod()."'")));
}
//bjj need more information on grades to load into student_report_card_grades
$grades_RET = DBGet(DBQuery("SELECT rcg.ID,rcg.TITLE,rcg.GPA_VALUE AS WEIGHTED_GP, rcg.UNWEIGHTED_GP ,gs.GP_SCALE  FROM REPORT_CARD_GRADES rcg, REPORT_CARD_GRADE_SCALES gs WHERE rcg.grade_scale_id = gs.id AND rcg.SYEAR='".UserSyear()."' AND rcg.SCHOOL_ID='".UserSchool()."' AND rcg.GRADE_SCALE_ID='$grade_scale_id' ORDER BY rcg.BREAK_OFF IS NOT NULL DESC,rcg.BREAK_OFF DESC,rcg.SORT_ORDER"),array(),array('ID'));
$commentsA_RET = DBGet(DBQuery("SELECT ID,TITLE FROM REPORT_CARD_COMMENTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND (COURSE_ID='".$course_id."' OR COURSE_ID='0') ORDER BY SORT_ORDER"));
$commentsB_RET = DBGet(DBQuery("SELECT ID,TITLE,SORT_ORDER FROM REPORT_CARD_COMMENTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND COURSE_ID IS NULL ORDER BY SORT_ORDER"),array(),array('ID'));
$grades_select = array(''=>'');
foreach($grades_RET as $id=>$code)
	$grades_select += array($id=>array($code[1]['TITLE'],'<b>'.$code[1]['TITLE'].'</b>'));
$commentsB_select = array();
if(0)
foreach($commentsB_RET as $id=>$comment)
	$commentsB_select += array($id=>array($comment[1]['SORT_ORDER'],$comment[1]['TITLE']));
else
foreach($commentsB_RET as $id=>$comment)
	$commentsB_select += array($id=>array($comment[1]['SORT_ORDER'].' - '.substr($comment[1]['TITLE'],0,19).(strlen($comment[1]['TITLE'])>20?'...':''),$comment[1]['TITLE']));

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='gradebook')
{
	if($_REQUEST['mp'])
	{
	$config_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='".User('STAFF_ID')."' AND PROGRAM='Gradebook'"),array(),array('TITLE'));
	if(count($config_RET))
		foreach($config_RET as $title=>$value)
			$programconfig[User('STAFF_ID')][$title] = $value[1]['VALUE'];
	else
		$programconfig[User('STAFF_ID')] = true;
	$_openSIS['_makeLetterGrade']['courses'][$course_period_id] = DBGet(DBQuery("SELECT DOES_BREAKOFF,GRADE_SCALE_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$course_period_id."'"));

	include '_makeLetterGrade.fnc.php';
	if(false && GetMP($_REQUEST['mp'],'TABLE')=='SCHOOL_SEMESTERS')
		$points_RET = DBGet(DBQuery("SELECT STUDENT_ID,MARKING_PERIOD_ID FROM STUDENT_REPORT_CARD_GRADES WHERE COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID IN (".GetAllMP('SEM',$_REQUEST['mp']).",'E".GetParentMP('SEM',UserMP())."')"),array(),array('STUDENT_ID'));

	if(GetMP($_REQUEST['mp'],'TABLE')=='SCHOOL_QUARTERS' || GetMP($_REQUEST['mp'],'TABLE')=='SCHOOL_PROGRESS_PERIODS')
	{
		// the 'populate the form' approach does not require that we get precisely the right students because nothing is modified here
		// so we don't need to filter on enrollment dates - in fact, for include_inactive we want 'em all anyway
		if($programconfig[User('STAFF_ID')]['WEIGHT']=='Y')
                {
                    
			$points_RET = DBGet(DBQuery("SELECT DISTINCT s.STUDENT_ID,gt.ASSIGNMENT_TYPE_ID,  gt.ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,
                            sum(".db_case(array('gg.POINTS',"'-1' OR (ga.due_date <  (select DISTINCT ssm.start_date  from STUDENT_ENROLLMENT ssm where ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR='2011' AND (ssm.START_DATE IS NOT NULL AND (CURRENT_DATE<=ssm.END_DATE OR CURRENT_DATE>=ssm.END_DATE OR  ssm.END_DATE IS NULL)) order by ssm.start_date desc limit 1
)  ) ","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,
                                gt.FINAL_GRADE_PERCENT FROM STUDENTS s JOIN SCHEDULE ss ON (ss.STUDENT_ID=s.STUDENT_ID AND ss.COURSE_PERIOD_ID='$course_period_id') JOIN GRADEBOOK_ASSIGNMENTS ga ON ((ga.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='".User('STAFF_ID')."') AND ga.MARKING_PERIOD_ID='".UserMP()."') LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.STUDENT_ID=s.STUDENT_ID AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID)
                                 
                                ,GRADEBOOK_ASSIGNMENT_TYPES gt WHERE gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND gt.COURSE_ID='$course_id' AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE) OR gg.POINTS IS NOT NULL) GROUP BY s.STUDENT_ID,ss.START_DATE,gt.ASSIGNMENT_TYPE_ID,gt.FINAL_GRADE_PERCENT"),array(),array('STUDENT_ID'));
                } else
                {                  
                       $points_RET = DBGet(DBQuery("SELECT DISTINCT  s.STUDENT_ID,'-1' AS ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,
                        sum(".db_case(array('gg.POINTS',"'-1' OR (ga.due_date <  (select DISTINCT ssm.start_date  from STUDENT_ENROLLMENT ssm where ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR='2011' AND (ssm.START_DATE IS NOT NULL AND (CURRENT_DATE<=ssm.END_DATE OR CURRENT_DATE>=ssm.END_DATE OR  ssm.END_DATE IS NULL)) order by ssm.start_date desc limit 1
)  ) ","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,
                               '1' AS FINAL_GRADE_PERCENT FROM STUDENTS s JOIN SCHEDULE ss ON (ss.STUDENT_ID=s.STUDENT_ID AND ss.COURSE_PERIOD_ID='$course_period_id') JOIN GRADEBOOK_ASSIGNMENTS ga ON ((ga.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='".User('STAFF_ID')."') AND ga.MARKING_PERIOD_ID='".UserMP()."') LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.STUDENT_ID=s.STUDENT_ID AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID) 
                         
                                WHERE  ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE) OR gg.POINTS IS NOT NULL) GROUP BY s.STUDENT_ID,ss.START_DATE,FINAL_GRADE_PERCENT"),array(),array('STUDENT_ID'));
                }
                    
		if(count($points_RET))
		{
			foreach($points_RET as $student_id=>$student)
			{
				$total = $total_percent = 0;
				foreach($student as $partial_points)
					if($partial_points['PARTIAL_TOTAL']!=0)
					{
						$total += $partial_points['PARTIAL_POINTS'] * $partial_points['FINAL_GRADE_PERCENT'] / $partial_points['PARTIAL_TOTAL'];
						$total_percent += $partial_points['FINAL_GRADE_PERCENT'];
					}
				if($total_percent!=0)
					$total /= $total_percent;

				$import_RET[$student_id] = array(1=>array('REPORT_CARD_GRADE_ID'=>_makeLetterGrade($total,$course_period_id,0,'ID'),'GRADE_PERCENT'=>round(100*$total,1)));
			}
		}
	}
        elseif(GetMP($_REQUEST['mp'],'TABLE')=='SCHOOL_SEMESTERS' || GetMP($_REQUEST['mp'],'TABLE')=='SCHOOL_YEARS')
	{
            if($sem || (GetMP($_REQUEST['mp'],'TABLE')=='SCHOOL_YEARS') && $fy)
            {
                                    if(GetMP($_REQUEST['mp'],'TABLE')=='SCHOOL_SEMESTERS')
		{
			$RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,'Y' AS DOES_GRADES,NULL AS DOES_EXAM FROM SCHOOL_QUARTERS WHERE SEMESTER_ID='$_REQUEST[mp]' UNION SELECT MARKING_PERIOD_ID,NULL AS DOES_GRADES,DOES_EXAM FROM SCHOOL_SEMESTERS WHERE MARKING_PERIOD_ID='$_REQUEST[mp]'"));
			$prefix = 'SEM-';
		}
		else
		{
			$RET = DBGet(DBQuery("SELECT q.marking_period_id,'Y' AS DOES_GRADES,NULL AS DOES_EXAM FROM SCHOOL_QUARTERS q,SCHOOL_SEMESTERS s WHERE q.SEMESTER_ID=s.MARKING_PERIOD_ID AND s.YEAR_ID='$_REQUEST[mp]' UNION SELECT MARKING_PERIOD_ID,DOES_GRADES,DOES_EXAM FROM SCHOOL_SEMESTERS WHERE YEAR_ID='$_REQUEST[mp]' UNION SELECT MARKING_PERIOD_ID,NULL AS DOES_GRADES,DOES_EXAM FROM SCHOOL_YEARS WHERE MARKING_PERIOD_ID='$_REQUEST[mp]'"));
			$prefix = 'FY-';
		}
		foreach($RET as $mp)
		{
			if($mp['DOES_GRADES']=='Y')
				$mps .= "'$mp[MARKING_PERIOD_ID]',";
			if($mp['DOES_EXAM']=='Y')
				$mps .= "'E$mp[MARKING_PERIOD_ID]',";
		}
		$mps = substr($mps,0,-1);

		$percents_RET = DBGet(DBQuery("SELECT STUDENT_ID,GRADE_PERCENT,MARKING_PERIOD_ID FROM STUDENT_REPORT_CARD_GRADES WHERE COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID IN ($mps)"),array(),array('STUDENT_ID'));
                
		foreach($percents_RET as $student_id=>$percents)
		{
			$total = $total_percent = 0;
			foreach($percents as $percent)
			{ 
                                                       
				$total += $percent['GRADE_PERCENT'] * $programconfig[User('STAFF_ID')][$prefix.$percent['MARKING_PERIOD_ID']];
				$total_percent += $programconfig[User('STAFF_ID')][$prefix.$percent['MARKING_PERIOD_ID']];
			}
			#echo $total /= $total_percent;
			$total /= $total_percent;
			
			$import_RET[$student_id] = array(1=>array('REPORT_CARD_GRADE_ID'=>_makeLetterGrade($total/100,$course_period_id,0,'ID'),'GRADE_PERCENT'=>round($total,1)));
		}
                        }
                        else
                        {
                                    if($programconfig[User('STAFF_ID')]['WEIGHT']=='Y')
                                        $points_RET = DBGet(DBQuery("SELECT DISTINCT s.STUDENT_ID,gt.ASSIGNMENT_TYPE_ID,  gt.ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1' OR (ga.due_date <  (select DISTINCT ssm.start_date  from STUDENT_ENROLLMENT ssm where ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR='2011' AND (ssm.START_DATE IS NOT NULL AND (CURRENT_DATE<=ssm.END_DATE OR CURRENT_DATE>=ssm.END_DATE OR  ssm.END_DATE IS NULL)) order by ssm.start_date desc limit 1
)  ) ","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,    gt.FINAL_GRADE_PERCENT FROM STUDENTS s JOIN SCHEDULE ss ON (ss.STUDENT_ID=s.STUDENT_ID AND ss.COURSE_PERIOD_ID='$course_period_id') JOIN GRADEBOOK_ASSIGNMENTS ga ON ((ga.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='".User('STAFF_ID')."') AND ga.MARKING_PERIOD_ID='".UserMP()."') LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.STUDENT_ID=s.STUDENT_ID AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID),GRADEBOOK_ASSIGNMENT_TYPES gt WHERE gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND gt.COURSE_ID='$course_id' AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE) OR gg.POINTS IS NOT NULL) GROUP BY s.STUDENT_ID,ss.START_DATE,gt.ASSIGNMENT_TYPE_ID,gt.FINAL_GRADE_PERCENT"),array(),array('STUDENT_ID'));
		else
                                        $points_RET = DBGet(DBQuery("SELECT DISTINCT  s.STUDENT_ID,'-1' AS ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1' OR (ga.due_date <  (select DISTINCT ssm.start_date  from STUDENT_ENROLLMENT ssm where ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR='2011' AND (ssm.START_DATE IS NOT NULL AND (CURRENT_DATE<=ssm.END_DATE OR CURRENT_DATE>=ssm.END_DATE OR  ssm.END_DATE IS NULL)) order by ssm.start_date desc limit 1
)  ) ","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,'1' AS FINAL_GRADE_PERCENT FROM STUDENTS s JOIN SCHEDULE ss ON (ss.STUDENT_ID=s.STUDENT_ID AND ss.COURSE_PERIOD_ID='$course_period_id') JOIN GRADEBOOK_ASSIGNMENTS ga ON ((ga.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='".User('STAFF_ID')."') AND ga.MARKING_PERIOD_ID='".UserMP()."') LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.STUDENT_ID=s.STUDENT_ID AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID)   WHERE gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE) OR gg.POINTS IS NOT NULL) GROUP BY s.STUDENT_ID,ss.START_DATE,FINAL_GRADE_PERCENT"),array(),array('STUDENT_ID'));

		if(count($points_RET))
		{
			foreach($points_RET as $student_id=>$student)
			{
				$total = $total_percent = 0;
				foreach($student as $partial_points)
					if($partial_points['PARTIAL_TOTAL']!=0)
					{
						$total += $partial_points['PARTIAL_POINTS'] * $partial_points['FINAL_GRADE_PERCENT'] / $partial_points['PARTIAL_TOTAL'];
						$total_percent += $partial_points['FINAL_GRADE_PERCENT'];
					}
				if($total_percent!=0)
					$total /= $total_percent;

				$import_RET[$student_id] = array(1=>array('REPORT_CARD_GRADE_ID'=>_makeLetterGrade($total,$course_period_id,0,'ID'),'GRADE_PERCENT'=>round(100*$total,1)));
			}
		}
                        }
	}
	}
	unset($_SESSION['_REQUEST_vars']['modfunc']);
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='grades')
{
	if($_REQUEST['prev_mp'])
	{
	include '_makePercentGrade.fnc.php';

	$import_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_GRADE_ID,g.GRADE_PERCENT FROM STUDENT_REPORT_CARD_GRADES g,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=g.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='$course_period_id' AND g.MARKING_PERIOD_ID='".$_REQUEST['prev_mp']."'"),array(),array('STUDENT_ID'));
	foreach($import_RET as $student_id=>$grade)
		$import_RET[$student_id][1]['GRADE_PERCENT'] = _makePercentGrade($grade[1]['REPORT_CARD_GRADE_ID'],$course_period_id);

	unset($_SESSION['_REQUEST_vars']['prev_mp']);
	}
	unset($_SESSION['_REQUEST_vars']['modfunc']);
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='comments')
{
	if($_REQUEST['prev_mp'])
	{
	$import_comments_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_GRADES g WHERE g.COURSE_PERIOD_ID='$course_period_id' AND g.MARKING_PERIOD_ID='".$_REQUEST['prev_mp']."'"),array(),array('STUDENT_ID'));
	$import_commentsA_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_COMMENTS g WHERE g.COURSE_PERIOD_ID='$course_period_id' AND g.MARKING_PERIOD_ID='".$_REQUEST['prev_mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE COURSE_ID IS NOT NULL)"),array(),array('STUDENT_ID','REPORT_CARD_COMMENT_ID'));
	$import_commentsB_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID FROM STUDENT_REPORT_CARD_COMMENTS g WHERE g.COURSE_PERIOD_ID='$course_period_id' AND g.MARKING_PERIOD_ID='".$_REQUEST['prev_mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE COURSE_ID IS NULL)"),array(),array('STUDENT_ID'));

	foreach($import_commentsB_RET as $comments)
		if(count($comments)>$max_current_commentsB)
			$max_current_commentsB = count($comments);

	unset($_SESSION['_REQUEST_vars']['prev_mp']);
	}
	unset($_SESSION['_REQUEST_vars']['modfunc']);
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='clearall')
{
	foreach($current_RET as $student_id=>$prev)
	{
		$current_RET[$student_id][1]['REPORT_CARD_GRADE_ID'] = '';
		$current_RET[$student_id][1]['GRADE_PERCENT'] = '';
		$current_RET[$student_id][1]['COMMENT'] = '';
	}
	foreach($current_commentsA_RET as $student_id=>$comments)
		foreach($comments as $id=>$comment)
			$current_commentsA_RET[$student_id][$id][1]['COMMENT'] = '';
	foreach($current_commentsB_RET as $student_id=>$comment)
		foreach($comment as $i=>$comment)
			$current_commentsB_RET[$student_id][$i] = '';
	unset($_SESSION['_REQUEST_vars']['modfunc']);
}

if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && ($_POST['values'] || $_REQUEST['ajax']) && $_REQUEST['submit']['save'])
{
	include '_makeLetterGrade.fnc.php';
	include '_makePercentGrade.fnc.php';
	$completed = true;
	foreach($_REQUEST['values'] as $student_id=>$columns)
	{
		$sql = $sep = '';
		if($current_RET[$student_id])
		{
			if(isset($columns['grade']) && $columns['grade']!=$current_RET[$student_id][1]['REPORT_CARD_GRADE_ID'])
			{
				if(substr($columns['grade'],-1)=='%')
				{
					$percent = substr($columns['grade'],0,-1);
					$sql .= "REPORT_CARD_GRADE_ID='"._makeLetterGrade($percent/100,$course_period_id,0,'ID')."'";
					$sql .= ",GRADE_PERCENT='".$percent."'";
					$sep = ',';
				}
				elseif($columns['grade']!=$current_RET[$student_id][1]['REPORT_CARD_GRADE_ID'])
				{
					$sql .= "REPORT_CARD_GRADE_ID='".$columns['grade']."'";
					$sql .= ",GRADE_PERCENT='".($columns['grade']==''?'':_makePercentGrade($columns['grade'],$course_period_id))."'";

					$sep = ',';
				}
                //bjj can we use $percent all the time?  TODO: rework this so updates to credits occur when grade is changed
				$grade_title_RET=DBGet(DBQuery("SELECT TITLE FROM REPORT_CARD_GRADES WHERE ID='".str_replace("\'","''",$columns['grade'])."'"));
                $sql .= ",GRADE_LETTER='".$grade_title_RET[1]['TITLE']."'";
				// edited
                $sql .= ",WEIGHTED_GP='".$grades_RET[$columns['grade']][1]['WEIGHTED_GP']."'";
                $sql .= ",UNWEIGHTED_GP='".$grades_RET[$columns['grade']][1]['UNWEIGHTED_GP']."'";
                $sql .= ",COURSE_TITLE='".$course_RET[1]['COURSE_NAME']."'";
                $sql .= ",GP_SCALE='".$grades_RET[$columns['grade']][1]['GP_SCALE']."'";
                $sql .= ",CREDIT_ATTEMPTED='".$course_RET[1]['CREDITS']."'";
                $sql .= ",CREDIT_EARNED='".($grades_RET[$columns['grade']][1]['WEIGHTED_GP']>0?$course_RET[1]['CREDITS']:0)."'";
			}
			elseif(isset($columns['percent']) && $columns['percent']!=$current_RET[$student_id][1]['GRADE_PERCENT'])
            {           
				if($columns['percent']=='')
				{
					$sql .= "REPORT_CARD_GRADE_ID=''";
					$gp_id = "";
				}
				else
				{
					$percent = rtrim($columns['percent'],'%')+0;
					if($percent>999.9)
						$percent = 999.9;
					elseif($percent<0)
						$percent = 0;
					$gp_id = _makeLetterGrade($percent/100,$course_period_id,0,'ID');
					$sql .= "REPORT_CARD_GRADE_ID='$gp_id'";
                                                                                  if($gp_id)
					$grade_title_RET=DBGet(DBQuery("SELECT TITLE FROM REPORT_CARD_GRADES WHERE ID=$gp_id"));
                	                $sql .= ",GRADE_LETTER='".$grade_title_RET[1]['TITLE']."'";
	                $sql .= ",WEIGHTED_GP='".$grades_RET[$gp_id][1]['WEIGHTED_GP']."'";
	                $sql .= ",UNWEIGHTED_GP='".$grades_RET[$gp_id][1]['UNWEIGHTED_GP']."'";
	                $sql .= ",COURSE_TITLE='".$course_RET[1]['COURSE_NAME']."'";
	                $sql .= ",GP_SCALE='".$grades_RET[$gp_id][1]['GP_SCALE']."'";
	                $sql .= ",CREDIT_ATTEMPTED='".$course_RET[1]['CREDITS']."'";
	                $sql .= ",CREDIT_EARNED='".($grades_RET[$gp_id][1]['WEIGHTED_GP']>0?$course_RET[1]['CREDITS']:0)."'";
					}
				$sql .= ",GRADE_PERCENT='$percent'";
				$sep = ',';
			}

                        if(isset($columns['comment']))
			{
                            $columns['comment']= clean_param($columns['comment'],PARAM_NOTAGS);
                            $sql .= $sep;
                            if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux'))
                            {
                             $columns['comment']=  mysql_real_escape_string($columns['comment']);
                             
                            }
                            $sql .= "COMMENT='". $columns['comment']." ' ";
                       }
                        #if(isset($columns['comment']))
			#	$sql .= $sep."COMMENT='".str_replace("\'","''",$columns['comment'])."'";

                        if($sql)
				$sql = "UPDATE STUDENT_REPORT_CARD_GRADES SET ".$sql." WHERE STUDENT_ID='".$student_id."' AND COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."'";
		}
		elseif($columns['grade'] || $columns['percent']!='' || $columns['comment'])
		{
			$grade = $percent = '';
			if($columns['grade'])
				if(substr($columns['grade'],-1)=='%')
				{
					$percent = substr($columns['grade'],0,-1);
					$grade = _makeLetterGrade($percent/100,$course_period_id,0,'ID');
				}
				else
				{
					$grade = $columns['grade'];
					$percent = _makePercentGrade($grade,$course_period_id);
				}
			elseif($columns['percent']!='')
			{
				$percent = rtrim($columns['percent'],'%')+0;
				if($percent>999.9)
					$percent = 999.9;
				elseif($percent<0)
					$percent = 0;
				$grade = _makeLetterGrade($percent/100,$course_period_id,0,'ID');
			}
 			//$grade_id = DBGet(DBQuery('SELECT '.db_seq_nextval('STUDENT_REPORT_CARD_GRADES_SEQ').' AS ID '.FROM_DUAL));
                        $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'STUDENT_REPORT_CARD_GRADES'"));
                        $grade_id[1]['ID']= $id[1]['AUTO_INCREMENT'];
                        $ngrade_id = $grade_id[1]['ID'];

                if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux'))
                {
                  $columns['comment']=  mysql_real_escape_string($columns['comment']);
                }


               $sql = "INSERT INTO STUDENT_REPORT_CARD_GRADES (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_PERIOD_ID,MARKING_PERIOD_ID,REPORT_CARD_GRADE_ID,GRADE_PERCENT,
                    COMMENT,GRADE_LETTER,WEIGHTED_GP,UNWEIGHTED_GP,COURSE_TITLE,GP_SCALE,CREDIT_ATTEMPTED,CREDIT_EARNED)
					values('".UserSyear()."','".UserSchool()."','".$student_id."','".$course_period_id."','".$_REQUEST['mp']."','".$grade."','".$percent."',
                    '".clean_param($columns['comment'],PARAM_NOTAGS)." ','".$grades_RET[$grade][1]['TITLE']."','".$grades_RET[$grade][1]['WEIGHTED_GP']."','".$grades_RET[$grade][1]['UNWEIGHTED_GP']."','".$course_RET[1]['COURSE_NAME']."','".$grades_RET[$grade][1]['GP_SCALE']."','".$course_RET[1]['CREDITS']."','".($grades_RET[$grade][1]['WEIGHTED_GP']>0?$course_RET[1]['CREDITS']:0)."')";
		}

		if($sql)
		{
			DBQuery($sql);
		}

		if(isset($columns['grade']))
		{
			if(!$columns['grade'])
				$completed = false;
		}
		elseif(!$current_RET[$student_id][1]['REPORT_CARD_GRADE_ID'])
			$completed = false;

		foreach($columns['commentsA'] as $id=>$comment)
			if($current_commentsA_RET[$student_id][$id])
				if($comment)
					DBQuery("UPDATE STUDENT_REPORT_CARD_COMMENTS SET COMMENT='".str_replace("\'","''",clean_param($comment,PARAM_NOTAGS))."' WHERE STUDENT_ID='".$student_id."' AND COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND REPORT_CARD_COMMENT_ID='".$id."'");
				else
					DBQuery("DELETE FROM STUDENT_REPORT_CARD_COMMENTS WHERE STUDENT_ID='".$student_id."' AND COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND REPORT_CARD_COMMENT_ID='".$id."'");
			elseif($comment)
					DBQuery("INSERT INTO STUDENT_REPORT_CARD_COMMENTS (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_PERIOD_ID,MARKING_PERIOD_ID,REPORT_CARD_COMMENT_ID,COMMENT)
						values('".UserSyear()."','".UserSchool()."','".$student_id."','".$course_period_id."','".$_REQUEST['mp']."','".$id."','".clean_param($comment,PARAM_NOTAGS)."')");

		// create mapping for current
		$old = array();
		foreach($current_commentsB_RET[$student_id] as $i=>$comment)
			$old[$comment['REPORT_CARD_COMMENT_ID']] = $i;
		// create change list
		$change = array();
		foreach($columns['commentsB'] as $i=>$comment)
			$change[$i] = array('REPORT_CARD_COMMENT_ID'=>0);
		// prune changes already in current set and reserve if in change list
		foreach($columns['commentsB'] as $i=>$comment)
			if($comment)
				if($old[$comment])
				{
					if($change[$old[$comment]])
						$change[$old[$comment]]['REPORT_CARD_COMMENT_ID'] = $comment;
					$columns['commentsB'][$i] = false;
				}
		// assign changes at their index if possible
		$new = array();
		foreach($columns['commentsB'] as $i=>$comment)
			if($comment)
				if(!$new[$comment])
				{
					if(!$change[$i]['REPORT_CARD_COMMENT_ID'])
					{
						$change[$i]['REPORT_CARD_COMMENT_ID'] = $comment;
						$new[$comment] = $i;
						$columns['commentsB'][$i] = false;
					}
				}
				else
					$columns['commentsB'][$i] = false;
		// assign remaining changes to first available
		reset($change);
		foreach($columns['commentsB'] as $i=>$comment)
			if($comment)
			{
				if(!$new[$comment])
				{
					while($change[key($change)]['REPORT_CARD_COMMENT_ID'])
						next($change);
					$change[key($change)]['REPORT_CARD_COMMENT_ID'] = $comment;
					$new[$comment] = key($change);
				}
				$columns['commentsB'][$i] = false;
			}

		// update the db
		foreach($change as $i=>$comment)
			if($current_commentsB_RET[$student_id][$i])
				if($comment['REPORT_CARD_COMMENT_ID'])
				{
					if($comment['REPORT_CARD_COMMENT_ID']!=$current_commentsB_RET[$student_id][$i]['REPORT_CARD_COMMENT_ID'])
						DBQuery("UPDATE STUDENT_REPORT_CARD_COMMENTS SET REPORT_CARD_COMMENT_ID='".$comment['REPORT_CARD_COMMENT_ID']."' WHERE STUDENT_ID='".$student_id."' AND COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND REPORT_CARD_COMMENT_ID='".$current_commentsB_RET[$student_id][$i]['REPORT_CARD_COMMENT_ID']."'");
				}
				else
					DBQuery("DELETE FROM STUDENT_REPORT_CARD_COMMENTS WHERE STUDENT_ID='".$student_id."' AND COURSE_PERIOD_ID='".$course_period_id."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND REPORT_CARD_COMMENT_ID='".$current_commentsB_RET[$student_id][$i]['REPORT_CARD_COMMENT_ID']."'");
			else
				if($comment['REPORT_CARD_COMMENT_ID'])
					DBQuery("INSERT INTO STUDENT_REPORT_CARD_COMMENTS (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_PERIOD_ID,MARKING_PERIOD_ID,REPORT_CARD_COMMENT_ID)
						values('".UserSyear()."','".UserSchool()."','".$student_id."','".$course_period_id."','".$_REQUEST['mp']."','".$comment['REPORT_CARD_COMMENT_ID']."')");
	}

	if($completed)
	{
		if(!$current_completed)
			DBQuery("INSERT INTO GRADES_COMPLETED (STAFF_ID,MARKING_PERIOD_ID,PERIOD_ID) values('".User('STAFF_ID')."','".$_REQUEST['mp']."','".UserPeriod()."')");
	}
	else
		if($current_completed)
			DBQuery("DELETE FROM GRADES_COMPLETED WHERE STAFF_ID='".User('STAFF_ID')."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND PERIOD_ID='".UserPeriod()."'");

	$current_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_GRADE_ID,g.GRADE_PERCENT,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_GRADES g WHERE g.COURSE_PERIOD_ID='$course_period_id' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."'"),array(),array('STUDENT_ID'));
	$current_commentsA_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_COMMENTS g WHERE g.COURSE_PERIOD_ID='$course_period_id' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE COURSE_ID IS NOT NULL)"),array(),array('STUDENT_ID','REPORT_CARD_COMMENT_ID'));
	$current_commentsB_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_COMMENT_ID FROM STUDENT_REPORT_CARD_COMMENTS g WHERE g.COURSE_PERIOD_ID='$course_period_id' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND g.REPORT_CARD_COMMENT_ID IN (SELECT ID FROM REPORT_CARD_COMMENTS WHERE COURSE_ID IS NULL)"),array(),array('STUDENT_ID'));
	$max_current_commentsB = 0;
	foreach($current_commentsB_RET as $comments)
		if(count($comments)>$max_current_commentsB)
			$max_current_commentsB = count($comments);
	$current_completed = count(DBGet(DBQuery("SELECT '' FROM GRADES_COMPLETED WHERE STAFF_ID='".User('STAFF_ID')."' AND MARKING_PERIOD_ID='".$_REQUEST['mp']."' AND PERIOD_ID='".UserPeriod()."'")));
	unset($_SESSION['_REQUEST_vars']['values']);
}

if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && ($_POST['values'] || $_REQUEST['ajax']) && $_REQUEST['submit']['cancel'])
{
	unset($_SESSION['_REQUEST_vars']['values']);
}

$time = strtotime(DBDate('postgres'));

$mps_select = "<SELECT name=mp onChange='this.form.submit();'>";
if($pros!='')
	foreach(explode(',',str_replace("'",'',$pros)) as $pro)
	{
		if($_REQUEST['mp']==$pro && GetMP($pro,'POST_START_DATE') && ($time>=strtotime(GetMP($pro,'POST_START_DATE')) && $time<=strtotime(GetMP($pro,'POST_END_DATE'))))
			$allow_edit = true;
		if(GetMP($pro,'DOES_GRADES')=='Y')
			$mps_select .= "<OPTION value=".$pro.(($pro==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($pro)."</OPTION>";
	}

if($_REQUEST['mp']==UserMP() && GetMP(UserMP(),'POST_START_DATE') && ($time>=strtotime(GetMP(UserMP(),'POST_START_DATE')) && $time<=strtotime(GetMP(UserMP(),'POST_END_DATE'))))
	$allow_edit = true;
$mps_select .= "<OPTION value=".UserMP().((UserMP()==$_REQUEST['mp'])?' SELECTED':'').">".GetMP(UserMP())."</OPTION>";

if(($_REQUEST['mp']==$sem || $_REQUEST['mp']=='E'.$sem) && GetMP($sem,'POST_START_DATE') && ($time>=strtotime(GetMP($sem,'POST_START_DATE')) && $time<=strtotime(GetMP($sem,'POST_END_DATE'))))
	$allow_edit = true;
if(GetMP($sem,'DOES_GRADES')=='Y')
	$mps_select .= "<OPTION value=$sem".(($sem==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($sem)."</OPTION>";
if(GetMP($sem,'DOES_EXAM')=='Y')
	$mps_select .= "<OPTION value=E$sem".(('E'.$sem==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($sem)." Exam</OPTION>";

if(($_REQUEST['mp']==$fy || $_REQUEST['mp']=='E'.$fy) && GetMP($fy,'POST_START_DATE') && ($time>=strtotime(GetMP($fy,'POST_START_DATE')) && $time<=strtotime(GetMP($fy,'POST_END_DATE'))))
	$allow_edit = true;
if(GetMP($fy,'DOES_GRADES')=='Y')
	$mps_select .= "<OPTION value=".$fy.(($fy==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($fy)."</OPTION>";
if(GetMP($fy,'DOES_EXAM')=='Y')
	$mps_select .= "<OPTION value=E".$fy.(('E'.$fy==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($fy)." Exam</OPTION>";

$mps_select .= '</SELECT>';

// if running as a teacher program then openSIS[allow_edit] will already be set according to admin permissions
if(!isset($_openSIS['allow_edit']))
	$_openSIS['allow_edit'] = $allow_edit;

if($_REQUEST['use_percents']!='true')
{
	$extra['SELECT'] = ",'' AS GRADE_PERCENT,'' AS REPORT_CARD_GRADE";
	$extra['functions'] = array('GRADE_PERCENT'=>'_makeGrade','REPORT_CARD_GRADE'=>'_makeGrade');
}
elseif($not_graded)
{
	$extra['SELECT'] = ",'' AS GRADE_PERCENT";
	$extra['functions'] = array('GRADE_PERCENT'=>'_makePercent');
}
else
{
	$extra['SELECT'] = ",'' AS REPORT_CARD_GRADE,'' AS GRADE_PERCENT";
	$extra['functions'] = array('REPORT_CARD_GRADE'=>'_makePercent','GRADE_PERCENT'=>'_makePercent');
}

if(substr($_REQUEST['mp'],0,1)!='E' && GetMP($_REQUEST['mp'],'DOES_COMMENTS')=='Y')
{
	foreach($commentsA_RET as $value)
	{
		$extra['SELECT'] .= ',\''.$value['ID'].'\' AS CA'.$value['ID'];
		$extra['functions'] += array('CA'.$value['ID']=>'_makeCommentsA');
	}
	for($i=1; $i<=$max_current_commentsB; $i++)
	{
		$extra['SELECT'] .= ',\''.$i.'\' AS CB'.$i;
		$extra['functions'] += array('CB'.$i=>'_makeCommentsB');
	}
	if(count($commentsB_select) && AllowEdit())
	{
		$extra['SELECT'] .= ',\''.$i.'\' AS CB'.$i;
		$extra['functions'] += array('CB'.$i=>'_makeCommentsB');
	}
}
#$extra['SELECT'] .= ",'' AS COMMENTS,'' AS COMMENT";
#$extra['functions'] += array('COMMENT'=>'_makeComment');

$extra['SELECT'] .= ",'' AS COMMENTS,'' AS COMMENT";
/*
$mp_does_comment = $_REQUEST['mp'];

if(substr($mp_does_comment,0,1) == 'E')
	$mp_does_comment = substr($mp_does_comment,1);
else
	$mp_does_comment = $mp_does_comment;
	
	#echo $mp_does_comment;

$allow_comment =  DBGet(DBQuery("SELECT does_comments FROM MARKING_PERIODS WHERE marking_period_id=".$mp_does_comment));
$allow_comment = $allow_comment[1]['DOES_COMMENTS'];

if($allow_comment == 'Y')
*/

$extra['functions'] += array('COMMENT'=>'_makeComment');
		
$stu_RET = GetStuList($extra);

echo "<FORM action=Modules.php?modname=$_REQUEST[modname] method=POST>";

if(!$_REQUEST['_openSIS_PDF'])
{
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['include_inactive']);
        $grade_start_date=DBGet(DBQuery('SELECT `POST_START_DATE` FROM `MARKING_PERIODS` WHERE `marking_period_id`='.UserMP()));
		$grade_end_date=DBGet(DBQuery('SELECT `POST_END_DATE` FROM `MARKING_PERIODS` WHERE `marking_period_id`='.UserMP()));
		$grade_start_time=strtotime( $grade_start_date[1]['POST_START_DATE']);
		$grade_end_time=strtotime( $grade_end_date[1]['POST_END_DATE']);
		$current_time= strtotime(date("Y-m-d"));
        if($current_time >=$grade_start_time && $current_time<=$grade_end_time){
		$grade_status='open';
		}else if($current_time >= $grade_end_time && $grade_end_time!=''){
		$grade_status='closed';
		}else if($current_time <= $grade_start_time){
		$grade_status='not open yet';
		}else{
                    $grade_status='not set yet';
                }
        if(count($stu_RET)!=0)
	DrawHeaderHome($mps_select,SubmitButton('Save','submit[save]','class=btn_medium'),'<INPUT type=checkbox name=include_inactive value=Y'.($_REQUEST['include_inactive']=='Y'?" CHECKED onclick='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST)."&include_inactive=\";'":" onclick='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST)."&include_inactive=Y\";'").'>Include Inactive Students');
	if(AllowEdit()){
	DrawHeader(($current_completed?'<FONT COLOR=green>These grades are complete</FONT>':'<FONT COLOR=green>Grade reporting is open for this marking period</FONT>').(AllowEdit()?' | <FONT COLOR=green>You can edit these grades</FONT>':' | <FONT COLOR=red>Grade reporting begins on : '.date("M d, Y ",strtotime( $grade_start_date[1]['POST_START_DATE'])).'</FONT>'));
	}else if($grade_status =='not open yet'){
			DrawHeader(($current_completed?'<FONT COLOR=green>These grades are complete</FONT>':'<FONT COLOR=red>Grade reporting is not open for this marking period</FONT>').(AllowEdit()?' | <FONT COLOR=green>You can edit these grades</FONT>':' | <FONT COLOR=red>Grade reporting starts on: '.date("M d, Y ",strtotime( $grade_start_date[1]['POST_START_DATE'])).' and ends on : '.date("M d, Y ",strtotime( $grade_end_date[1]['POST_END_DATE'])).'</FONT>'));
		}else if($grade_status =='closed'){
			DrawHeader(($current_completed?'<FONT COLOR=green>These grades are complete</FONT>':'<FONT COLOR=red>These grades are complete</FONT>').(AllowEdit()?' | <FONT COLOR=green>You can edit these grades</FONT>':' | <FONT COLOR=red>Grade reporting ended for this marking period on : '.date("M d, Y ",strtotime( $grade_end_date[1]['POST_END_DATE'])).'</FONT>'));
		}else if($grade_status=='not set yet'){
                    	DrawHeader('<FONT COLOR=red>Grade reporting date has not set for this marking period</FONT>');
                }
if(AllowEdit())
	{
		

	if($_REQUEST['use_percents']!='true')
		$gb_header = "<A HREF=Modules.php?modname=$_REQUEST[modname]&include_inactive=$_REQUEST[include_inactive]&mp=$_REQUEST[mp]&use_percents=true>Assign Percents</A>";
	elseif($not_graded!=true)
		$gb_header = "<A HREF=Modules.php?modname=$_REQUEST[modname]&include_inactive=$_REQUEST[include_inactive]&mp=$_REQUEST[mp]&use_percents=false>Assign Letters</A>";
	
		if(substr($_REQUEST['mp'],0,1)!='E')
		{
			#echo $_REQUEST[modname].'<br>'.$_REQUEST[include_inactive];
			$gb_header .= ($not_graded? '':' | ')."<A HREF=Modules.php?modname=$_REQUEST[modname]&include_inactive=$_REQUEST[include_inactive]&modfunc=gradebook&mp=$_REQUEST[mp]&use_percents=true>Get Gradebook Grades</A>";
			$prev_mp = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,START_DATE FROM ".GetMP($_REQUEST['mp'],'TABLE')." WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND START_DATE<'".GetMP($_REQUEST['mp'],'START_DATE')."' ORDER BY START_DATE DESC LIMIT 1"));
			$prev_mp = $prev_mp[1];
			if($prev_mp)
			{
				$gb_header .= " | <A HREF=Modules.php?modname=$_REQUEST[modname]&include_inactive=$_REQUEST[include_inactive]&modfunc=grades&mp=$_REQUEST[mp]&prev_mp=$prev_mp[MARKING_PERIOD_ID]&use_percents=false>Get $prev_mp[TITLE] Grades</A>";
				$gb_header .= " | <A HREF=Modules.php?modname=$_REQUEST[modname]&include_inactive=$_REQUEST[include_inactive]&modfunc=comments&mp=$_REQUEST[mp]&prev_mp=$prev_mp[MARKING_PERIOD_ID]&use_percents=false>Get $prev_mp[TITLE] Comments</A>";
			}
		}
                                    $bar=' | ';
                                    if(substr($_REQUEST['mp'],0,1)=='E' && $not_graded==true)
                                        $bar='';
                                            $gb_header .= "$bar<A HREF=Modules.php?modname=$_REQUEST[modname]&include_inactive=$_REQUEST[include_inactive]&modfunc=clearall&mp=$_REQUEST[mp]&use_percents=$_REQUEST[use_percents]>Clear All</A>";
	}
	DrawHeader($gb_header,$tipmessage);
}
else
{
	DrawHeader($course_title);
	DrawHeader(GetMP(UserMP()));
}

$columns = array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID');
if($_REQUEST['include_inactive']=='Y')
	$columns += array('ACTIVE'=>'School Status','ACTIVE_SCHEDULE'=>'Course Status');
if($_REQUEST['use_percents']!='true')
	$columns += array('GRADE_PERCENT'=>'Percent','REPORT_CARD_GRADE'=>'Assign Grade');
elseif($not_graded)
                  $columns += array('GRADE_PERCENT'=>'Assign Percent');
else
	$columns += array('REPORT_CARD_GRADE'=>'Grade','GRADE_PERCENT'=>'Assign Percent');

if(substr($_REQUEST['mp'],0,1)!='E'&& GetMP($_REQUEST['mp'],'DOES_COMMENTS')=='Y')
{
	foreach($commentsA_RET as $value)
		$columns += array('CA'.$value['ID']=>$value['TITLE']);
	for($i=1; $i<=$max_current_commentsB; $i++)
		$columns += array('CB'.$i=>'Comment '.$i);
	if(count($commentsB_select) && AllowEdit() && !isset($_REQUEST['_openSIS_PDF']))
		$columns += array('CB'.$i=>'Add Comment');
	$columns += array('COMMENT'=>'Comment');
}

echo "<center>";
PopTable_wo_header ('header');
echo '<div style="width:800px; overflow-x:scroll;">';
ListOutput($stu_RET,$columns,'Student','Students',false,false,array('yscroll'=>true));
echo '</div>';
PopTable ('footer');
echo "</center>";
 if(count($stu_RET)!=0)
echo '<CENTER>'.SubmitButton('Save','submit[save]','class=btn_medium').'</CENTER>';
echo "</FORM>";

function _makeGrade($value,$column)
{	global $THIS_RET,$current_RET,$import_RET,$grades_RET,$grades_select,$student_count,$tabindex;

	if($column=='REPORT_CARD_GRADE')
	{
		if(!isset($_REQUEST['_openSIS_PDF']))
		{
			$student_count++;
			$tabindex = $student_count;

			if($import_RET[$THIS_RET['STUDENT_ID']])
			{
				$select = $import_RET[$THIS_RET['STUDENT_ID']][1]['REPORT_CARD_GRADE_ID'];
				$extra_select = array($select=>$grades_select[$import_RET[$THIS_RET['STUDENT_ID']][1]['REPORT_CARD_GRADE_ID']]);
				$div = false;
			}
			else
			{
				$select = $current_RET[$THIS_RET['STUDENT_ID']][1]['REPORT_CARD_GRADE_ID'];
				$extra_select = array();
				$div = true;
			}

			$return = SelectInput($select,'values['.$THIS_RET['STUDENT_ID'].'][grade]','',$extra_select+$grades_select,false,'tabindex='.$tabindex,$div);
		}
		else
		{
			if($import_RET[$THIS_RET['STUDENT_ID']])
				$select = $import_RET[$THIS_RET['STUDENT_ID']][1]['REPORT_CARD_GRADE_ID'];
			else
				$select = $current_RET[$THIS_RET['STUDENT_ID']][1]['REPORT_CARD_GRADE_ID'];
			$return = '<b>'.$grades_RET[$select][1]['TITLE'].'</b>';
		}
	}
	elseif($column=='GRADE_PERCENT')
	{
		if($import_RET[$THIS_RET['STUDENT_ID']])
			$select = $import_RET[$THIS_RET['STUDENT_ID']][1]['GRADE_PERCENT'];
		else
			$select = $current_RET[$THIS_RET['STUDENT_ID']][1]['GRADE_PERCENT'];
		$return = $select==''?'':($select+0).'%';
	}

	return $return;
}

function _makePercent($value,$column)
{	global $THIS_RET,$current_RET,$grades_RET,$student_count,$tabindex,$import_RET;

	if($column=='GRADE_PERCENT')
	{
		if(!isset($_REQUEST['_openSIS_PDF']))
		{
			$student_count++;
			$tabindex = $student_count;
			if($import_RET[$THIS_RET['STUDENT_ID']])
				$return = TextInput($import_RET[$THIS_RET['STUDENT_ID']][1]['GRADE_PERCENT']==''?'':($import_RET[$THIS_RET['STUDENT_ID']][1]['GRADE_PERCENT']+0).'%',"values[$THIS_RET[STUDENT_ID]][percent]",'',(0?'readonly ':'').'size=5 maxlength=5 tabindex='.$tabindex,false);
			else
				$return = TextInput($current_RET[$THIS_RET['STUDENT_ID']][1]['GRADE_PERCENT']==''?'':($current_RET[$THIS_RET['STUDENT_ID']][1]['GRADE_PERCENT']+0).'%',"values[$THIS_RET[STUDENT_ID]][percent]",'',(0?'readonly ':'').'size=5 maxlength=5 tabindex='.$tabindex,!$current_RET[$THIS_RET['STUDENT_ID']][1]['DIV']);
		}
		else
			$return = $current_RET[$THIS_RET['STUDENT_ID']][1]['GRADE_PERCENT']==''?'':($current_RET[$THIS_RET['STUDENT_ID']][1]['GRADE_PERCENT']+0).'%';
	}
	elseif($column=='REPORT_CARD_GRADE')
		$return = '<b>'.$grades_RET[$current_RET[$THIS_RET['STUDENT_ID']][1]['REPORT_CARD_GRADE_ID']][1]['TITLE'].'</b>';

	return $return;
}

$table = 'STUDENT_REPORT_CARD_COMMENTS';

function _makeComment($value,$column)
{	global $THIS_RET,$current_RET,$import_comments_RET,$tabindex;
	$table = 'STUDENT_REPORT_CARD_COMMENTS';
	
	if($import_comments_RET[$THIS_RET['STUDENT_ID']])
	{
		$select = $import_comments_RET[$THIS_RET['STUDENT_ID']][1]['COMMENT'];
		$div = false;
	}
	else
	{
		$select = $current_RET[$THIS_RET['STUDENT_ID']][1]['COMMENT'];
		$div = true;
	}

	if(!isset($_REQUEST['_openSIS_PDF']))
	{
		#$return = TextInput(str_replace('"','\"',$select),"values[$THIS_RET[STUDENT_ID]][comment]",'','maxlength=255 tabindex='.($tabindex+=100),$div);
		$return = TextAreaInputInputFinalGrade(str_replace('"','\"',$select),"values[$THIS_RET[STUDENT_ID]][comment]",'','maxlength=255 tabindex='.($tabindex+=100),$div);
		
	}
	else
	{
		$return = '<small>'.$select.'</small>';
	}

	return $return;
}


function _makeCommentsA($value,$column)
{	global $THIS_RET,$current_commentsA_RET,$import_commentsA_RET,$commentsA_select,$tabindex;

	if($import_commentsA_RET[$THIS_RET['STUDENT_ID']][$value])
	{
		$select = $import_commentsA_RET[$THIS_RET['STUDENT_ID']][$value][1]['COMMENT'];
		$div = false;
	}
	else
	{
		if(!$current_commentsA_RET[$THIS_RET['STUDENT_ID']] && !$import_commentsA_RET[$THIS_RET['STUDENT_ID']] && AllowEdit())
		{
			$select = Preferences('COMMENT_A','Gradebook');
			$div = false;
		}
		else
		{
			$select = $current_commentsA_RET[$THIS_RET['STUDENT_ID']][$value][1]['COMMENT'];
			$div = true;
		}
	}

	if(!isset($_REQUEST['_openSIS_PDF']))
		$return = SelectInput($select,'values['.$THIS_RET['STUDENT_ID'].'][commentsA]['.$value.']','',$commentsA_select,'N/A','tabindex='.($tabindex+=100),$div);
	else
		$return = $select!=' ' ? $select : 'o';

	return $return;
}
function _makeCommentsB($value,$column)
{	global $THIS_RET,$current_commentsB_RET,$import_commentsB_RET,$commentsB_RET,$max_current_commentsB,$commentsB_select,$tabindex;

	if($import_commentsB_RET[$THIS_RET['STUDENT_ID']][$value])
	{
		$select = $import_commentsB_RET[$THIS_RET['STUDENT_ID']][$value]['REPORT_CARD_COMMENT_ID'];
		$div = false;
	}
	else
	{
		$select = $current_commentsB_RET[$THIS_RET['STUDENT_ID']][$value]['REPORT_CARD_COMMENT_ID'];
		$div = true;
	}

	if(!isset($_REQUEST['_openSIS_PDF']))
		if($value>$max_current_commentsB)
			$return = SelectInput('','values['.$THIS_RET['STUDENT_ID'].'][commentsB]['.$value.']','',$commentsB_select,'N/A','tabindex='.($tabindex+=100));
		elseif($import_commentsB_RET[$THIS_RET['STUDENT_ID']][$value] || $current_commentsB_RET[$THIS_RET['STUDENT_ID']][$value])
			$return = SelectInput($select,'values['.$THIS_RET['STUDENT_ID'].'][commentsB]['.$value.']','',$commentsB_select,'N/A','tabindex='.($tabindex+=100),$div);
		else
			$return = '';
	else
		$return = '<small>'.$commentsB_RET[$select][1]['TITLE'].'</small>';

	return $return;
}
?>
