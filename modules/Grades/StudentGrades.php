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

require_once('functions/_makeLetterGrade.fnc.php');
$_openSIS['allow_edit'] = false;
if($_REQUEST['_openSIS_PDF'])
	$do_stats = false;

#DrawHeader(ProgramTitle());
#Search('student_id','','true');
Search('student_id');
$MP_TYPE_RET=DBGet(DBQuery("SELECT MP_TYPE FROM MARKING_PERIODS WHERE MARKING_PERIOD_ID=".UserMP()." LIMIT 1"));
$MP_TYPE=$MP_TYPE_RET[1]['MP_TYPE'];
if($MP_TYPE=='year'){
$MP_TYPE='FY';
}else if($MP_TYPE=='semester'){$MP_TYPE='SEM';
}else if($MP_TYPE=='quarter'){$MP_TYPE='QTR';
}else{$MP_TYPE='';
}

####################

if(isset($_REQUEST['student_id']) )
{
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX,SCHOOL_ID FROM STUDENTS,STUDENT_ENROLLMENT WHERE STUDENTS.STUDENT_ID='".$_REQUEST['student_id']."' AND STUDENT_ENROLLMENT.STUDENT_ID = STUDENTS.STUDENT_ID "));
	//$_SESSION['UserSchool'] = $RET[1]['SCHOOL_ID'];
        $count_student_RET=DBGet(DBQuery("SELECT COUNT(*) AS NUM FROM STUDENTS"));
        if($count_student_RET[1]['NUM']>1){
	DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname=Students/Student.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
	//DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname=Scheduling/Schedule.php&search_modfunc=list&next_modname=Scheduling/Schedule.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');



//DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname='.$_REQUEST['modname'].'&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
        }else if($count_student_RET[1]['NUM']==1){
        DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) ');
        }

	//echo '<div align="left" style="padding-left:16px"><b>Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'</b></div>';
}
####################
if(UserStudentID() && !$_REQUEST['modfunc'])
{
 
if(!$_REQUEST['id'])
{
	DrawHeader('Totals',"<A HREF=Modules.php?modname=$_REQUEST[modname]&id=all>Expand All</A>");
	$courses_RET = DBGet(DBQuery("SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.COURSE_ID,cp.TEACHER_ID AS STAFF_ID FROM SCHEDULE s,COURSE_PERIODS cp,COURSES c WHERE s.SYEAR='".UserSyear()."' AND cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.MARKING_PERIOD_ID IN (".GetAllMP($MP_TYPE,UserMP()).") AND ('".date('Y-m-d',strtotime(DBDate()))."' BETWEEN s.START_DATE AND s.END_DATE OR '".date('Y-m-d',strtotime(DBDate()))."'>=s.START_DATE AND s.END_DATE IS NULL) AND s.STUDENT_ID='".UserStudentID()."' AND cp.GRADE_SCALE_ID IS NOT NULL".(User('PROFILE')=='teacher'?' AND (cp.TEACHER_ID=\''.User('STAFF_ID').'\' OR cp.SECONDARY_TEACHER_ID=\''.User('STAFF_ID').'\')':'')." AND c.COURSE_ID=cp.COURSE_ID ORDER BY cp.COURSE_ID"),array(),array('COURSE_PERIOD_ID'));
        $LO_columns = array('TITLE'=>'Course Title','TEACHER'=>'Teacher','PERCENT'=>'Percent','GRADE'=>'Letter','UNGRADED'=>'Ungraded')+($do_stats?array('BAR1'=>'Grade Range(%)','BAR2'=>'Class Rank'):array());

	if(count($courses_RET))
	{
		$LO_ret = array(0=>array());

		foreach($courses_RET as $course)
		{
			
                    $mp = GetAllMP('QTR',UserMP());

                    if(!isset($mp))
                      $mp = GetAllMP('SEM',UserMP());

                    if(!isset($mp))
                      $mp = GetAllMP('FY',UserMP());
                    
                    
                        $course = $course[1];
			$staff_id = $course['STAFF_ID'];
			$course_id = $course['COURSE_ID'];
			$course_period_id = $course['COURSE_PERIOD_ID'];
			$course_title = $course['TITLE'];
                       //echo $staff_id.'+'.$course_id.'+'.$course_period_id.'+'.$course_title.'|';
			$assignments_RET = DBGet(DBQuery("SELECT ASSIGNMENT_ID,TITLE,POINTS FROM GRADEBOOK_ASSIGNMENTS WHERE STAFF_ID='$staff_id' AND (COURSE_ID='$course_id' OR COURSE_PERIOD_ID='$course_period_id') AND MARKING_PERIOD_ID IN (".$mp.") ORDER BY DUE_DATE DESC,ASSIGNMENT_ID"));
			//echo '<pre>'; var_dump($assignments_RET); echo '</pre>';

			if(!$programconfig[$staff_id])
			{
				$config_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='$staff_id' AND PROGRAM='Gradebook'"),array(),array('TITLE'));
				if(count($config_RET))
					foreach($config_RET as $title=>$value)
						$programconfig[$staff_id][$title] = $value[1]['VALUE'];
				else
					$programconfig[$staff_id] = true;
			}

			if($programconfig[$staff_id]['WEIGHT']=='Y')
			{

                               	$mp = GetAllMP('QTR',UserMP());

                                if(!isset($mp))
                                  $mp = GetAllMP('SEM',UserMP());

                                if(!isset($mp))
                                  $mp = GetAllMP('FY',UserMP());
                            
                            
                                $points_RET1 = DBGet(DBQuery("SELECT DISTINCT s.STUDENT_ID, gt.ASSIGNMENT_TYPE_ID, sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL, gt.FINAL_GRADE_PERCENT FROM STUDENTS s JOIN SCHEDULE ss ON (ss.STUDENT_ID=s.STUDENT_ID AND ss.COURSE_PERIOD_ID='$course_period_id') JOIN GRADEBOOK_ASSIGNMENTS ga ON ((ga.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='".User('STAFF_ID')."') AND ga.MARKING_PERIOD_ID IN (".$mp.")) LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.STUDENT_ID=s.STUDENT_ID AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID),GRADEBOOK_ASSIGNMENT_TYPES gt WHERE gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND gt.COURSE_ID='$course_id' AND ((ga.ASSIGNED_DATE IS NOT NULL )  OR gg.POINTS IS NOT NULL) GROUP BY s.STUDENT_ID,ss.START_DATE,gt.ASSIGNMENT_TYPE_ID,gt.FINAL_GRADE_PERCENT"),array(),array('STUDENT_ID'));
                                $points_RET = DBGet(DBQuery("SELECT      gt.ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,    gt.FINAL_GRADE_PERCENT,sum(".db_case(array('gg.POINTS',"''","1","0")).") AS UNGRADED FROM GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.COURSE_PERIOD_ID='$course_period_id' AND gg.STUDENT_ID='".UserStudentID()."' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID),GRADEBOOK_ASSIGNMENT_TYPES gt WHERE (ga.COURSE_PERIOD_ID='$course_period_id' OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID IN (".$mp.") AND gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND gt.COURSE_ID='$course_id' AND ((ga.ASSIGNED_DATE IS NOT NULL )  and gg.POINTS IS NOT NULL) GROUP BY gt.ASSIGNMENT_TYPE_ID,gt.FINAL_GRADE_PERCENT"));
                                $points_RET_all1 = DBGet(DBQuery("SELECT      gt.ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,    gt.FINAL_GRADE_PERCENT,sum(".db_case(array('gg.POINTS',"''","1","0")).") AS UNGRADED FROM GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.COURSE_PERIOD_ID='$course_period_id' AND gg.STUDENT_ID='".UserStudentID()."' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID),GRADEBOOK_ASSIGNMENT_TYPES gt WHERE (ga.COURSE_PERIOD_ID='$course_period_id' OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID IN (".$mp.") AND gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND gt.COURSE_ID='$course_id' AND ((ga.ASSIGNED_DATE IS NOT NULL )  or gg.POINTS IS NOT NULL) GROUP BY gt.ASSIGNMENT_TYPE_ID,gt.FINAL_GRADE_PERCENT"));
                              if($do_stats)
					$all_RET = DBGet(DBQuery("SELECT gg.STUDENT_ID, gt.ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,    gt.FINAL_GRADE_PERCENT FROM GRADEBOOK_GRADES gg,GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES g ON (g.COURSE_PERIOD_ID='$course_period_id' AND g.STUDENT_ID='".UserStudentID()."' AND g.ASSIGNMENT_ID=ga.ASSIGNMENT_ID),GRADEBOOK_ASSIGNMENT_TYPES gt WHERE gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND ga.ASSIGNMENT_ID=gg.ASSIGNMENT_ID AND ga.MARKING_PERIOD_ID IN (".$mp.") AND gg.COURSE_PERIOD_ID='$course_period_id' AND (ga.COURSE_PERIOD_ID='$course_period_id' OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='$staff_id') AND gt.COURSE_ID='$course_id' AND (ga.ASSIGNED_DATE IS NOT NULL   OR gg.POINTS IS NOT NULL) GROUP BY gg.STUDENT_ID,gt.ASSIGNMENT_TYPE_ID,gt.FINAL_GRADE_PERCENT"),array(),array('STUDENT_ID'));
			}
			else
			{   
			      
                            $mp = GetAllMP('QTR',UserMP());

                                if(!isset($mp))
                                  $mp = GetAllMP('SEM',UserMP());

                                if(!isset($mp))
                                  $mp = GetAllMP('FY',UserMP());
                            
                            
                            $points_RET = DBGet(DBQuery("SELECT '-1' AS ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,'1' AS FINAL_GRADE_PERCENT,sum(".db_case(array('gg.POINTS',"''","1","0")).") AS UNGRADED FROM GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.COURSE_PERIOD_ID='$course_period_id' AND gg.STUDENT_ID='".UserStudentID()."' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID) WHERE (ga.COURSE_PERIOD_ID='$course_period_id' OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID IN (".$mp.") AND (ga.ASSIGNED_DATE IS NOT NULL   AND gg.POINTS IS NOT NULL) GROUP BY  FINAL_GRADE_PERCENT"));
			       $points_RET_all1 = DBGet(DBQuery("SELECT      gt.ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,    gt.FINAL_GRADE_PERCENT,sum(".db_case(array('gg.POINTS',"''","1","0")).") AS UNGRADED FROM GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.COURSE_PERIOD_ID='$course_period_id' AND gg.STUDENT_ID='".UserStudentID()."' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID),GRADEBOOK_ASSIGNMENT_TYPES gt WHERE (ga.COURSE_PERIOD_ID='$course_period_id' OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID IN (".$mp.") AND gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND gt.COURSE_ID='$course_id' AND ((ga.ASSIGNED_DATE IS NOT NULL )  or gg.POINTS IS NOT NULL) GROUP BY gt.ASSIGNMENT_TYPE_ID,gt.FINAL_GRADE_PERCENT"));
                             if($do_stats)

                              $all_RET = DBGet(DBQuery("SELECT gg.STUDENT_ID,'-1' AS ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,'1' AS FINAL_GRADE_PERCENT FROM GRADEBOOK_GRADES gg,GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES g ON (g.COURSE_PERIOD_ID='$course_period_id' AND g.STUDENT_ID='".UserStudentID()."' AND g.ASSIGNMENT_ID=ga.ASSIGNMENT_ID)
                                                        WHERE  ga.ASSIGNMENT_ID=gg.ASSIGNMENT_ID AND ga.MARKING_PERIOD_ID IN (".$mp.") AND gg.COURSE_PERIOD_ID='$course_period_id' AND (ga.COURSE_PERIOD_ID='$course_period_id' OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='$staff_id') AND (ga.ASSIGNED_DATE IS NOT NULL OR gg.POINTS IS NOT NULL) GROUP BY gg.STUDENT_ID, FINAL_GRADE_PERCENT"),array(),array('STUDENT_ID'));
			}
			//echo '<pre>'; var_dump($points_RET); echo '</pre>';
			//echo '<pre>'; var_dump($all_RET); echo '</pre>';
                        
                                $Class_Rank = DBGet(DBQuery("SELECT  COUNT(ga.STUDENT_ID) AS TOTAL_STUDENT FROM GRADEBOOK_GRADES ga WHERE ga.COURSE_PERIOD_ID='$course_period_id'   GROUP BY ga.STUDENT_ID"));
                                $Class_Rank = DBGet(DBQuery(" SELECT FOUND_ROWS() as TOTAL_STUDENT")) ;
			if(count($points_RET))
			{
				$total = $total_percent = 0;
				$ungraded = 0;
				foreach($points_RET as $partial_points)
				{
					if($partial_points['PARTIAL_TOTAL']!=0)
					{
						$total += $partial_points['PARTIAL_POINTS'] * $partial_points['FINAL_GRADE_PERCENT'] / $partial_points['PARTIAL_TOTAL'];
						$total_percent += $partial_points['FINAL_GRADE_PERCENT'];
					}
					#$ungraded += $partial_points['UNGRADED'];
				}
                                foreach($points_RET_all1 as $partial_points1)
				{
				  $ungraded += $partial_points1['UNGRADED'];
				}
				if($total_percent!=0)
					$total /= $total_percent;
				 $percent = $total;
                               
                               
				if($do_stats)
				{
					unset($bargraph1);
                                        unset($bargraph2);
                                        $min_percent = $max_percent = $percent;
					$avg_percent = 0;
					$lower = $higher = 0;
					foreach($all_RET as $xstudent_id=>$student)
					{
						if($student['STUDENT_ID'])
                                                $count++;
                                                $total = $total_percent = 0;
						foreach($student as $partial_points)
							if($partial_points['PARTIAL_TOTAL']!=0)
							{
								$total += $partial_points['PARTIAL_POINTS'] * $partial_points['FINAL_GRADE_PERCENT'] / $partial_points['PARTIAL_TOTAL'];
								$total_percent += $partial_points['FINAL_GRADE_PERCENT'];
                                                              
							}
                                                        
						if($total_percent!=0)
							 $total /= $total_percent;
                                                $Rank_Pos[] = number_format(100*$total,1) ;
                                                 
                                                     }
						if($total<$min_percent)
							$min_percent = $total;
						if($total>$max_percent)
							$max_percent = $total;
						$avg_percent += $total;
						if($xstudent_id!==UserStudentID())
							if($total>$percent)
								$higher++;
							else
								$lower++;
					}
                                       
                                       
					$avg_percent /= count($all_RET);

					$scale = $max_percent>1?$max_percent:1;
					$w1 = round(100*$min_percent/$scale);
					if($percent<$avg_percent)
					{
						$w2 = round(100*($percent-$min_percent)/$scale); $c2 = '#ff0000';
						$w4 = round(100*($max_percent-$avg_percent)/$scale); $c4 = '#00ff00';
					}
					else
					{
						$w2 = round(100*($avg_percent-$min_percent)/$scale); $c2 = '#00ff00';
						$w4 = round(100*($max_percent-$percent)/$scale); $c4 = '#ff0000';
					}
					 $w5 = round(100*(1.0-$max_percent/$scale));
                                       
					$w3 = 100-$w1-$w2-$w4-$w5;
					#$bargraph1 = '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0>'.($w1>0?"<TD width=$w1%></TD>":'').($w2>0?"<TD width=$w2% bgcolor=#00a000></TD>":'')."<TD width=0% bgcolor=$c2>&nbsp;</TD>".($w3>0?"<TD width=$w3% bgcolor=#00a000></TD>":'')."<TD width=0% bgcolor=$c4>&nbsp;</TD>".($w4>0?"<TD width=$w4% bgcolor=#00a000></TD>":'').($w5>0?"<TD width=$w5%></TD>":'').'</TR></TABLE>';
                                    
                                       # $bargraph1 =($min_percent*100)." - ".($max_percent*100);
                                       
                                        rsort($Rank_Pos);
                                       foreach ($Rank_Pos as $key => $val) {
                                        {      #  echo "Rank[" . $key . "] = " . $val . "\n";
                                           if (number_format(100*$percent,1)==$val)
                                          $rank = $key+1;
                                         
                                        }

                                        $highrange = max($Rank_Pos);
                                        $lowrange = min($Rank_Pos);
                                        $bargraph1 =$lowrange." - ".$highrange;
                                      
					$scale = $lower+$higher+1;
					$w1 = round(100*$lower/$scale);
					 $w3 = round(100*$higher/$scale);
					$w2 = 100-$w1-$w3;
					#$bargraph2 = '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0>'.($w1>0||$lower>0?"<TD width=$w1%></TD>":'')."<TD width=$w2% bgcolor=#ff0000>&nbsp;</TD>".($w3>0||$higher>0?"<TD width=$w3%></TD>":'').'</TR></TABLE>';
				      if ($rank)
                                      $bargraph2 = $rank . " out of " .$Class_Rank[1]['TOTAL_STUDENT'];
                                         
                                }

				$LO_ret[] = array('ID'=>$course_period_id,'TITLE'=>$course['COURSE_TITLE'],'TEACHER'=>substr($course_title,strrpos(str_replace(' - ',' ^ ',$course_title),'^')+2),'PERCENT'=>number_format(100*$percent,2).'%','GRADE' =>'<b>'._makeLetterGrade($percent,$course_period_id,$staff_id).'</b>','UNGRADED'=>$ungraded)+($do_stats?array('BAR1'=>$bargraph1,'BAR2'=>$bargraph2):array());
			  unset($Rank_Pos);

                        }
			//else
			//$LO_ret[] = array('ID'=>$course_period_id,'TITLE'=>$course['COURSE_TITLE'],'TEACHER'=>substr($course_title,strrpos(str_replace(' - ',' ^ ',$course_title),'^')+2));
		}
		unset($LO_ret[0]);
		$link = array('TITLE'=>array('link'=>"Modules.php?modname=$_REQUEST[modname]",'variables'=>array('id'=>'ID')));
		ListOutput($LO_ret,$LO_columns,'Course','Courses',$link,array(),array('center'=>false,'save'=>false,'search'=>false));
	}
	else
		DrawHeader('There are no grades available for this student.');
}

else
{
 if($_REQUEST['modfun']=='assgn_detail')
    {
     $assignments_RET = DBGet(DBQuery("SELECT ga.TITLE,ga.DESCRIPTION,ga.ASSIGNED_DATE,ga.DUE_DATE,ga.POINTS ,gt.title as assignment_type
                                                   FROM GRADEBOOK_ASSIGNMENTS ga, GRADEBOOK_ASSIGNMENT_TYPES gt
                                      where assignment_id ='".$_REQUEST['assignment_id']."' and gt.assignment_type_id=ga.assignment_type_id"));
    # $val = PopTable('header','Assignment Description ' ,'width=30%');
      $val1 = '<div>';
    # $val1 .=  '<table width="100%" cellpadding="2" cellspacing="2" border="0" align="center"><tr><td><fieldset style="border:1px solid;">';
     $val1 .= '<center > <strong>Assignment Details</center><br><br>';
    $val1 .= '<table width="95%" cellpadding="2" cellspacing="2" border="0" align="center">';
     $val1 .= '<tr><td valign=top > <strong>Title</strong></td><td valign=top>:</td><td valign=top >'.$assignments_RET[1]['TITLE'].'</td>';
     $val1 .= '<td valign=top > <strong>Description</strong></td><td valign=top >:</td><td valign=top width=55%>'.$assignments_RET[1]['DESCRIPTION'].'</td></tr>';
     $val1 .= '<tr><td valign=top > <strong>Assignement Type</strong></td><td valign=top >:</td><td valign=top>'.$assignments_RET[1]['ASSIGNMENT_TYPE'].'</td>';
     $val1 .= '<td valign=top> <strong>Points</strong></td><td valign=top>:</td><td valign=top>'.$assignments_RET[1]['POINTS'].'</td></tr>';
     $val1 .= '<tr><td valign=top>';
     $val1 .= '<strong>Assigned Date</strong>';
     $val1 .= '</td><td valign=top>:</td><td valign=top>'.$assignments_RET[1]['ASSIGNED_DATE'].'</td>';
     $val1 .= '<td valign=top> <strong>Due Date</strong></td><td valign=top>:</td><td valign=top>'.$assignments_RET[1]['DUE_DATE'].'</td></tr>';
    # $val1 .= '</table></fieldset></tr></td></table>';
     $val1 .= '</table>';
     $val1 .= '</div>';
     #$val1 .= PopTable('footer');
   }
   
if($_REQUEST['id']=='all')
	{
		
            $mp = GetAllMP('QTR',UserMP());

                                if(!isset($mp))
                                  $mp = GetAllMP('SEM',UserMP());

                                if(!isset($mp))
                                  $mp = GetAllMP('FY',UserMP());
    
                $courses_RET = DBGet(DBQuery("SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.COURSE_ID,cp.TEACHER_ID AS STAFF_ID FROM SCHEDULE s,COURSE_PERIODS cp,COURSES c WHERE s.SYEAR='".UserSyear()."' AND cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.MARKING_PERIOD_ID IN (".$mp.") AND ('".DBDate()."' BETWEEN s.START_DATE AND s.END_DATE OR '".DBDate()."'>=s.START_DATE AND s.END_DATE IS NULL) AND s.STUDENT_ID='".UserStudentID()."' AND cp.GRADE_SCALE_ID IS NOT NULL".(User('PROFILE')=='teacher'?' AND cp.TEACHER_ID=\''.User('STAFF_ID').'\'':'')." AND c.COURSE_ID=cp.COURSE_ID ORDER BY cp.COURSE_ID"));
		DrawHeader('All Courses','');
                 DrawHeader($val1);
               
        }
	else
	{
		$courses_RET = DBGet(DBQuery("SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.COURSE_ID,cp.TEACHER_ID AS STAFF_ID FROM COURSE_PERIODS cp,COURSES c WHERE cp.COURSE_PERIOD_ID='".clean_param($_REQUEST[id],PARAM_INT)."' AND c.COURSE_ID=cp.COURSE_ID"));
		DrawHeaderhome($val1);
                DrawHeader('<br><B>'.$courses_RET[1]['COURSE_TITLE'].'</B> - '.substr($courses_RET[1]['TITLE'],strrpos(str_replace(' - ',' ^ ',$courses_RET[1]['TITLE']),'^')+2),"<A HREF=Modules.php?modname=$_REQUEST[modname]>Back to Totals</A>");
	}
	//echo '<pre>'; var_dump($courses_RET); echo '</pre>';

	foreach($courses_RET as $course)
	{
	
   	$staff_id = $course['STAFF_ID'];
		if(!$programconfig[$staff_id])
		{
			$config_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='$staff_id' AND PROGRAM='Gradebook'"),array(),array('TITLE'));
			if(count($config_RET))
				foreach($config_RET as $title=>$value)
					$programconfig[$staff_id][$title] = $value[1]['VALUE'];
			else
				$programconfig[$staff_id] = true;
		}
              
           $assignments_RET = DBGet(DBQuery( "SELECT ga.ASSIGNMENT_ID,gg.POINTS,ga.ASSIGNED_DATE,ga.DUE_DATE,ga.DUE_DATE AS DUE ,gg.COMMENT,ga.TITLE,ga.DESCRIPTION,ga.ASSIGNED_DATE,ga.DUE_DATE,ga.POINTS AS POINTS_POSSIBLE,at.TITLE AS CATEGORY
                                                   FROM GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES gg
                                                  ON (gg.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.STUDENT_ID='".UserStudentID()."'),GRADEBOOK_ASSIGNMENT_TYPES at
                                                  WHERE (ga.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' OR ga.COURSE_ID='$course[COURSE_ID]' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."'
                                                   AND at.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND ((ga.ASSIGNED_DATE IS NOT NULL )
                                                  or gg.POINTS IS NOT NULL) AND (ga.POINTS!='0' OR gg.POINTS IS NOT NULL AND gg.POINTS!='-1') ORDER BY ga.ASSIGNMENT_ID DESC"),array('TITLE'=>'_makeTipTitle','ASSIGNED_DATE'=>'ProperDate','DUE_DATE'=>'ProperDate'));
       
		/*$assignments_RET = DBGet(DBQuery("SELECT ga.ASSIGNMENT_ID,gg.POINTS,gg.COMMENT,ga.TITLE,ga.DESCRIPTION,ga.ASSIGNED_DATE,ga.DUE_DATE,ga.POINTS AS POINTS_POSSIBLE,at.TITLE AS CATEGORY
                                                   FROM GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES gg
                                                  ON (gg.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.STUDENT_ID='".UserStudentID()."'),GRADEBOOK_ASSIGNMENT_TYPES at
                                                  WHERE (ga.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' OR ga.COURSE_ID='$course[COURSE_ID]' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."'
                                                   AND at.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE+".round($programconfig[$staff_id]['LATENCY']).")
                                                  OR gg.POINTS IS NOT NULL) AND (ga.POINTS!='0' OR gg.POINTS IS NOT NULL AND gg.POINTS!='-1') ORDER BY ga.ASSIGNMENT_ID DESC"),array('TITLE'=>'_makeTipTitle'));
		*/
                 //echo '<pre>'; var_dump($assignments_RET); echo '</pre>';
//           ECHO "SELECT * FROM STUDENT_ENROLLMENT ssm WHERE STUDENT_ID='".UserStudentID()."'  AND ssm.SYEAR='".UserSyear()."' AND ((ssm.START_DATE IS NOT NULL AND '".date('Y-m-d')."'>=ssm.START_DATE) AND ('".date('Y-m-d')."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) ";
           $stu_enroll_date=DBGet(DBQuery( "SELECT * FROM STUDENT_ENROLLMENT ssm WHERE STUDENT_ID='".UserStudentID()."'  AND ssm.SYEAR='".UserSyear()."' AND ((ssm.START_DATE IS NOT NULL AND '".date('Y-m-d')."'>=ssm.START_DATE) AND ('".date('Y-m-d')."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) "));
           $stu_enroll_date=$stu_enroll_date[1][START_DATE];
               if(count($assignments_RET))
		{
		      if($_REQUEST['id']=='all')
                        {
                          # echo '<BR>';
                          DrawHeader('<br><B>'.$course['COURSE_TITLE'].'</B> - '.substr($course['TITLE'],strrpos(str_replace(' - ',' ^ ',$course['TITLE']),'^')+2),"<A HREF=Modules.php?modname=$_REQUEST[modname]>Back to Totals</A>");
                        }
                       if($do_stats)
                        
			$all_RET = DBGet(DBQuery("SELECT ga.ASSIGNMENT_ID,gg.POINTS,min(".db_case(array('gg.POINTS',"'-1'",'ga.POINTS','gg.POINTS')).") AS MIN,max(".db_case(array('gg.POINTS',"'-1'",'0','gg.POINTS')).") AS MAX,".db_case(array("sum(".db_case(array('gg.POINTS',"'-1'",'0','1')).")","'0'","'0'","sum(".db_case(array('gg.POINTS',"'-1'",'0','gg.POINTS')).") / sum(".db_case(array('gg.POINTS',"'-1'",'0','1')).")"))." AS AVG,sum(CASE WHEN gg.POINTS<=g.POINTS AND gg.STUDENT_ID!=g.STUDENT_ID THEN 1 ELSE 0 END) AS LOWER,sum(CASE WHEN gg.POINTS>g.POINTS THEN 1 ELSE 0 END) AS HIGHER FROM GRADEBOOK_GRADES gg,GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES g ON (g.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' AND g.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND g.STUDENT_ID='".UserStudentID()."'),GRADEBOOK_ASSIGNMENT_TYPES at WHERE (ga.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' OR ga.COURSE_ID='$course[COURSE_ID]' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND at.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND ((ga.ASSIGNED_DATE IS NOT NULL )  OR g.POINTS IS NOT NULL) AND ga.POINTS!='0' GROUP BY ga.ASSIGNMENT_ID"),array(),array('ASSIGNMENT_ID'));
			//echo '<pre>'; var_dump($all_RET); echo '</pre>';
                     
			$LO_columns = array('TITLE'=>'Title','CATEGORY'=>'Category','POINTS'=>'Points / Possible','PERCENT'=>'Percent','LETTER'=>'Letter','ASSIGNED_DATE'=>'Assigned Date','DUE_DATE'=>'Due Date')+($do_stats?array('BAR1'=>'Grade Range','BAR2'=>'Class Rank'):array());

			$LO_ret = array(0=>array());

			foreach($assignments_RET as $assignment)
			{
                            
			  $days_left= floor((strtotime($assignment[DUE],0)-strtotime($stu_enroll_date,0))/86400);
                            if($days_left>=1)
                            {
                           if($do_stats)
				{
					unset($bargraph1);
                                        unset($bargraph2);
                                        if($all_RET[$assignment['ASSIGNMENT_ID']])
					{
					     $all = $all_RET[$assignment['ASSIGNMENT_ID']][1];
                                                $all_RET1 = DBGet(DBQuery("SELECT g.ASSIGNMENT_ID,g.POINTS  FROM GRADEBOOK_GRADES g where g.COURSE_PERIOD_ID='".$course[COURSE_PERIOD_ID]."' "));
                                            $count_tot =0;
                                            foreach($all_RET1 as $all1)
                                            {
                                               if($assignment['ASSIGNMENT_ID']==$all1['ASSIGNMENT_ID'])
                                               { 
                                                $assg_tot[]= $all1['POINTS'];
                                                $count_tot++;
                                                 }
                                            }
                                               rsort($assg_tot);
                                               unset($ranknew);
                                               unset($prev_val);
                                               $k=0;
                                               foreach ($assg_tot as $key => $val)
                                                {if($prev_val!=$val) $k++;
                                                   "RankNew[" . $key . "] = " . $val . "\n";
                                                   if ($assignment['POINTS']==$val)
                                                   if($prev_val!=$val) $ranknew = $k; ;
                                                   $prev_val = $val;}
                                               #}
                                              unset($assg_tot);
                                              
						$scale = $all['MAX']>$assignment['POINTS_POSSIBLE']?$all['MAX']:$assignment['POINTS_POSSIBLE'];
                                                if ($ranknew && $assignment['POINTS']>0 )
                                               $bargraph2 =$ranknew ." out of ". $count_tot;
						if($assignment['POINTS']!='-1' && $assignment['POINTS']!='')
						{
							
                                                        $w1 = round(100*$all['MIN']/$scale);
							if($assignment['POINTS']<$all['AVG'])
							{
								$w2 = round(100*($assignment['POINTS']-$all['MIN'])/$scale); $c2 = '#ff0000';
								$w4 = round(100*($all['MAX']-$all['AVG'])/$scale); $c4 = '#00ff00';
							}
							else
							{
								$w2 = round(100*($all['AVG']-$all['MIN'])/$scale); $c2 = '#00ff00';
								$w4 = round(100*($all['MAX']-$assignment['POINTS'])/$scale); $c4 = '#ff0000';
							}
							$w5 = round(100*(1.0-$all['MAX']/$scale));
							$w3 = 100-$w1-$w2-$w4-$w5;
							#$bargraph1 = '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0>'.($w1>0?"<TD width=$w1%></TD>":'').($w2>0?"<TD width=$w2% bgcolor=#00a000></TD>":'')."<TD width=0% bgcolor=$c2>&nbsp;</TD>".($w3>0?"<TD width=$w3% bgcolor=#00a000></TD>":'')."<TD width=0% bgcolor=$c4>&nbsp;</TD>".($w4>0?"<TD width=$w4% bgcolor=#00a000></TD>":'').($w5>0?"<TD width=$w5%></TD>":'').'</TR></TABLE>';
                                                        $bargraph1 = $all['MIN'] ." - ". $all['MAX'];
							$scale = $all['LOWER']+$all['HIGHER']+1;
							$w1 = round(100*$all['LOWER']/$scale);
							$w3 = round(100*$all['HIGHER']/$scale);
							$w2 = 100-$w1-$w3;
							#$bargraph2 = '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0>'.($w1>0||$lower>0?"<TD width=$w1%></TD>":'')."<TD width=$w2% bgcolor=#ff0000>&nbsp;</TD>".($w3>0||$higher>0?"<TD width=$w3%></TD>":'').'</TR></TABLE>';
						}
                                                                                            
					}
					#else
					#	$bargraph1 = $bargraph2 = '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0><TD width=100%>&nbsp;</TD></TR></TABLE>';
				}
                        
				#$LO_ret[] = array('TITLE'=>$assignment['TITLE'],'CATEGORY'=>$assignment['CATEGORY'],'POINTS'=>($assignment['POINTS']=='-1'?'*':($assignment['POINTS']==''?'<FONT color=red>0</FONT>':rtrim(rtrim(number_format($assignment['POINTS'],1),'0'),'.'))).' / '.$assignment['POINTS_POSSIBLE'],'PERCENT'=>($assignment['POINTS_POSSIBLE']=='0'?'':($assignment['POINTS']=='-1'?'*':number_format(100*$assignment['POINTS']/$assignment['POINTS_POSSIBLE'],1).'%')),'LETTER'=>($assignment['POINTS_POSSIBLE']=='0'?'e/c':($assignment['POINTS']=='-1'?'n/a':'<b>'._makeLetterGrade($assignment['POINTS']/$assignment['POINTS_POSSIBLE'],$course['COURSE_PERIOD_ID'],$staff_id))).'</b>','ASSIGNED_DATE'=>$assignment['ASSIGNED_DATE'].($assignment['POINTS']==''?($assignment['ASSIGNED_DATE']?'<BR>':'').'<FONT color=red>Not Graded</FONT>':''))+($do_stats?array('BAR1'=>$bargraph1,'BAR2'=>$bargraph2):array());
			       $LO_ret[] = array('TITLE'=>$assignment['TITLE'],'CATEGORY'=>$assignment['CATEGORY'],'POINTS'=>($assignment['POINTS']=='-1'?'*':($assignment['POINTS']==''?'<FONT color=red>0</FONT>':rtrim(rtrim(number_format($assignment['POINTS'],1),'0'),'.'))).' / '.$assignment['POINTS_POSSIBLE'],'PERCENT'=>($assignment['POINTS_POSSIBLE']=='0'?'':($assignment['POINTS']=='-1'?'*':number_format(100*$assignment['POINTS']/$assignment['POINTS_POSSIBLE'],2).'%')),'LETTER'=>($assignment['POINTS_POSSIBLE']=='0'?'e/c':($assignment['POINTS']=='-1'?'n/a':'<b>'._makeLetterGrade($assignment['POINTS']/$assignment['POINTS_POSSIBLE'],$course['COURSE_PERIOD_ID'],$staff_id))).'</b>','ASSIGNED_DATE'=>$assignment['ASSIGNED_DATE'],'DUE_DATE'=>$assignment['DUE_DATE'])+($do_stats?array('BAR1'=>$bargraph1,'BAR2'=>$bargraph2):array());
                            }
			}
                       
			unset($LO_ret[0]);
			ListOutput($LO_ret,$LO_columns,'Assignment','Assignments',array(),array(),array('center'=>false,'save'=>$_REQUEST['id']!='all','search'=>false));
		}
		else
			if($_REQUEST['id']!='all')
				DrawHeader('There are no grades available for this student.');
	}
}
}

function _makeTipTitle($value,$column)
{	global $THIS_RET;

	if(($THIS_RET['DESCRIPTION'] || $THIS_RET['ASSIGNED_DATE'] || $THIS_RET['DUE_DATE']) && !$_REQUEST['_openSIS_PDF'])
	{
		
		#$tip_title = '<A HREF=# onMouseOver=\'htm(["Details","'.$tip_title.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'>'.$value.'</A>';
	$tip_title = '<A HREF="Modules.php?modname=Grades/StudentGrades.php&id='.$_REQUEST['id'].'&modfun=assgn_detail&assignment_id='.$THIS_RET['ASSIGNMENT_ID'].'">'.$value.'</A>';



        }
	else
		$tip_title = $value;

	return $tip_title;
}


?>
