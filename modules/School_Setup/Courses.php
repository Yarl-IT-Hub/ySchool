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
unset($_SESSION['_REQUEST_vars']['subject_id']);
unset($_SESSION['_REQUEST_vars']['course_id']);
unset($_SESSION['_REQUEST_vars']['course_period_id']);


// if only one subject, select it automatically -- works for Course Setup and Choose a Course

#echo $_REQUEST['w_course_period_id'];
if($_REQUEST['course_period_id'] != 'new')
{
	if($_REQUEST['w_course_period_id'])
	{
		$sql_parent = "UPDATE COURSE_PERIODS SET PARENT_ID=".$_REQUEST['w_course_period_id']." WHERE COURSE_PERIOD_ID=".$_REQUEST['course_period_id'];
		DBQuery($sql_parent);
	} 
}

if($_REQUEST['modfunc']!='delete' && !$_REQUEST['subject_id'])
{
	$subjects_RET = DBGet(DBQuery("SELECT SUBJECT_ID,TITLE FROM COURSE_SUBJECTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));
	if(count($subjects_RET)==1)
		$_REQUEST['subject_id'] = $subjects_RET[1]['SUBJECT_ID'];
}

if(clean_param($_REQUEST['course_modfunc'],PARAM_ALPHAMOD)=='search')
{
	PopTable('header','Search');
	echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&course_modfunc=search method=POST>";
	echo '<TABLE><TR><TD><INPUT type=text class=cell_floating name=search_term value="'.$_REQUEST['search_term'].'"></TD><TD><INPUT type=submit class=btn_medium value=Search onclick=\'formload_ajax("F1")\';></TD></TR></TABLE>';
	echo '</FORM>';
	PopTable('footer');

	if($_REQUEST['search_term'])
	{
		$subjects_RET = DBGet(DBQuery("SELECT SUBJECT_ID,TITLE FROM COURSE_SUBJECTS WHERE (UPPER(TITLE) LIKE '%".strtoupper($_REQUEST['search_term'])."%' OR UPPER(SHORT_NAME) = '".strtoupper($_REQUEST['search_term'])."') AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
		$courses_RET = DBGet(DBQuery("SELECT SUBJECT_ID,COURSE_ID,TITLE FROM COURSES WHERE (UPPER(TITLE) LIKE '%".strtoupper($_REQUEST['search_term'])."%' OR UPPER(SHORT_NAME) = '".strtoupper($_REQUEST['search_term'])."') AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
		$periods_RET = DBGet(DBQuery("SELECT c.SUBJECT_ID,cp.COURSE_ID,cp.COURSE_PERIOD_ID,cp.TITLE FROM COURSE_PERIODS cp,COURSES c WHERE cp.COURSE_ID=c.COURSE_ID AND (UPPER(cp.TITLE) LIKE '%".strtoupper($_REQUEST['search_term'])."%' OR UPPER(cp.SHORT_NAME) = '".strtoupper($_REQUEST['search_term'])."') AND cp.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID='".UserSchool()."'"));

		echo '<TABLE><TR><TD valign=top>';
		$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
		$link['TITLE']['variables'] = array('subject_id'=>'SUBJECT_ID');
		ListOutput($subjects_RET,array('TITLE'=>'Subject'),'Subject','Subjects',$link,array(),array('search'=>false,'save'=>false));
		echo '</TD><TD valign=top>';
		$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
		$link['TITLE']['variables'] = array('subject_id'=>'SUBJECT_ID','course_id'=>'COURSE_ID');
		ListOutput($courses_RET,array('TITLE'=>'Course'),'Course','Courses',$link,array(),array('search'=>false,'save'=>false));
		echo '</TD><TD valign=top>';
		$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
		$link['TITLE']['variables'] = array('subject_id'=>'SUBJECT_ID','course_id'=>'COURSE_ID','course_period_id'=>'COURSE_PERIOD_ID');
		ListOutput($periods_RET,array('TITLE'=>'Course Period'),'Course Period','Course Periods',$link,array(),array('search'=>false,'save'=>false));
		echo '</TD></TR></TABLE>';
	}
}

// UPDATING
if(clean_param($_REQUEST['tables'],PARAM_NOTAGS) && ($_POST['tables'] || $_REQUEST['ajax']) && AllowEdit())
{
	$where = array('COURSE_SUBJECTS'=>'SUBJECT_ID',
					'COURSES'=>'COURSE_ID',
					'COURSE_PERIODS'=>'COURSE_PERIOD_ID');

	if($_REQUEST['tables']['parent_id'])
		$_REQUEST['tables']['COURSE_PERIODS'][$_REQUEST['course_period_id']]['PARENT_ID'] = $_REQUEST['tables']['parent_id'];

	foreach($_REQUEST['tables'] as $table_name=>$tables)
	{
		foreach($tables as $id=>$columns)
		{
			if($columns['TOTAL_SEATS'] && !is_numeric($columns['TOTAL_SEATS']))
				$columns['TOTAL_SEATS'] = ereg_replace('[^0-9]+','',$columns['TOTAL_SEATS']);
			if($columns['DAYS'])
			{
                                                            foreach($columns['DAYS'] as $day=>$y)
                                                            {
                                                                    if($y=='Y')
                                                                            $days .= $day;
                                                            }
                                                            $columns['DAYS'] = $days;
			}

			if($id!='new')
			{
				if($table_name=='COURSES' && $columns['SUBJECT_ID'] && $columns['SUBJECT_ID']!=$_REQUEST['subject_id'])
					$_REQUEST['subject_id'] = $columns['SUBJECT_ID'];

				$sql = "UPDATE $table_name SET ";

				if($table_name=='COURSE_PERIODS')
				{
                                      if(scheduleAssociation($id))
                                      {
                                          $scheduleAssociation=true;
                                      }
                                      if(gradeAssociation($id))
                                      {
                                          $gradeAssociation=true;
                                      }
					$current = DBGet(DBQuery("SELECT TEACHER_ID,PERIOD_ID,MARKING_PERIOD_ID,DAYS,SHORT_NAME,TOTAL_SEATS FROM COURSE_PERIODS WHERE ".$where[$table_name]."='$id'"));
                                                                                          if($scheduleAssociation)
                                                                                                $cur_total_seat=$current[1]['TOTAL_SEATS'];
					
                                                                                          if($columns['TEACHER_ID'])
						$staff_id = $columns['TEACHER_ID'];
					else
						$staff_id = $current[1]['TEACHER_ID'];
					if($columns['PERIOD_ID'])
						$period_id = $columns['PERIOD_ID'];
					else
						$period_id = $current[1]['PERIOD_ID'];
					if(isset($columns['MARKING_PERIOD_ID']))
						$marking_period_id = $columns['MARKING_PERIOD_ID'];
					else
						$marking_period_id = $current[1]['MARKING_PERIOD_ID'];
					if($columns['DAYS'])
						$days = $columns['DAYS'];
					else
						$days = $current[1]['DAYS'];
					if($columns['SHORT_NAME'])
						$short_name = $columns['SHORT_NAME'];
					else
						$short_name = $current[1]['SHORT_NAME'];

					$teacher = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME FROM STAFF WHERE SYEAR='".UserSyear()."' AND STAFF_ID='$staff_id'"));
					$period = DBGet(DBQuery("SELECT TITLE FROM SCHOOL_PERIODS WHERE PERIOD_ID='$period_id' AND SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));
					if(GetMP($marking_period_id,'TABLE')!='SCHOOL_YEARS')
						$mp_title = GetMP($marking_period_id,'SHORT_NAME').' - ';
					if(strlen($days)<5)
						$mp_title .= $days.' - ';
					if($short_name)
						$mp_title .= paramlib_validation($column=SHORT_NAME,$short_name).' - ';
                                 
					$title = str_replace("'","''",$period[1]['TITLE'].' - '.$mp_title.$teacher[1]['FIRST_NAME'].' '.$teacher[1]['MIDDLE_NAME'].' '.$teacher[1]['LAST_NAME']);
					$sql .= "TITLE='$title',";

					if(isset($columns['MARKING_PERIOD_ID']))
					{
						if(GetMP($columns['MARKING_PERIOD_ID'],'TABLE')=='SCHOOL_YEARS')
							$columns['MP'] = 'FY';
						elseif(GetMP($columns['MARKING_PERIOD_ID'],'TABLE')=='SCHOOL_SEMESTERS')
							$columns['MP'] = 'SEM';
						else
							$columns['MP'] = 'QTR';
					}
				}
                                                                    if(!(isset($columns['TITLE']) && trim($columns['TITLE'])==''))
                                                                    {
                                                                        foreach($columns as $column=>$value)
                                                                        {
                                                                                if(($column=='PERIOD_ID') && $scheduleAssociation){
                                                                                    $asso_err[]='You can not edit Period because it has association';
                                                                                    continue;
                                                                                }
                                                                                elseif($column=='DAYS' && $scheduleAssociation){
                                                                                    $asso_err[]='You can not edit Days because it has association';
                                                                                    continue;
                                                                                }
                                                                                elseif($column=='MARKING_PERIOD_ID' && $scheduleAssociation){
                                                                                    $asso_err[]='You can not edit Marking Period because it has association';
                                                                                    continue;
                                                                                }
                                                                                elseif($column=='GRADE_SCALE_ID' && $gradeAssociation){
                                                                                    $asso_err[]='You can not edit Grading Scale because it has association';
                                                                                    continue;
                                                                                }
                                                                                elseif($column=='CREDITS' && $gradeAssociation){
                                                                                    $asso_err[]='You can not edit Credits because it has association';
                                                                                    continue;
                                                                                }
                                                                                elseif($column=='TOTAL_SEATS' && isset($cur_total_seat) && $value<$cur_total_seat){
                                                                                    $asso_err[]='You can not reduce Seats because it has association';
                                                                                    continue;
                                                                                }
                                                                                elseif($column=='ROOM' && trim($value)==''){
                                                                                    continue;
                                                                                }
                                                                                elseif($column=='SHORT_NAME' && trim($value)==''){
                                                                                    continue;
                                                                                }
                                                                                $value=paramlib_validation($column,$value);
                                                                                if($column=='GRADE_SCALE_ID' && str_replace("\'","''",$value)==''){
                                                                                    $sql .= $column." = NULL,";
                                                                                }
                                                                                else
                                                                                {$go=true;	#$sql .= $column."='".str_replace("\'","''",$value)."',";
                                                                                    if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux'))
                                                                                    {
                                                                                        $value =  mysql_real_escape_string($value);
                                                                                        $value=str_replace('%u201D', "\"", $value);
                                                                                    }
                                                                                    if(str_replace("\'","''",$value)=='')
                                                                                        $sql .= $column." = NULL,";
                                                                                    else
                                                                                        $sql .= $column."='".$value."',";
                                                                                }
                                                                        }
                                                                        $sql = substr($sql,0,-1) . " WHERE ".$where[$table_name]."='$id'";
                                                                        if($go)
                                                                            DBQuery($sql);
                                                                        UpdateMissingAttendance($id);
                                                                    }
				// ----------------------------------------------- //
				/*
				if($_REQUEST['w_course_period_id'])
				{
					
					$sql_1 = "UPDATE $table_name SET PARENT_ID=".$_REQUEST['w_course_period_id']." WHERE ".$where[$table_name]."='$id'";
					DBQuery($sql_1);
				} 
				*/
				// ----------------------------------------------- //
                                                                    if(($scheduleAssociation || $gradeAssociation)&& is_array($asso_err)){
                                                                        foreach($asso_err as $err){
                                                                            ShowErrPhp($err);
                                                                        }
                                                                    }

			}
			else
			{
				$sql = "INSERT INTO $table_name ";

				if($table_name=='COURSE_SUBJECTS')
				{
					//$id = DBGet(DBQuery("SELECT ".db_seq_nextval('COURSE_SUBJECTS_SEQ').' AS ID'.FROM_DUAL));
                                                                                          $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'COURSE_SUBJECTS'"));
                                                                                          $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
					$fields = 'SCHOOL_ID,SYEAR,';
					$values = "'".UserSchool()."','".UserSyear()."',";
					$_REQUEST['subject_id'] = $id[1]['ID'];
				}
				elseif($table_name=='COURSES')
				{
					// $id = DBGet(DBQuery("SELECT ".db_seq_nextval('COURSES_SEQ').' AS ID'.FROM_DUAL));
                                          /* $id = DBGet(DBQuery("SELECT max(COURSE_ID) AS ID from COURSES"));
					$_REQUEST['course_id'] = $id[1]['ID']; */
                                                                                          $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'COURSES'"));
                                                                                          $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
                                                                                          $_REQUEST['course_id'] = $id[1]['ID'];
					$fields = 'SUBJECT_ID,SCHOOL_ID,SYEAR,';
					$values = "'$_REQUEST[subject_id]','".UserSchool()."','".UserSyear()."',";
				}

				elseif($table_name=='COURSE_PERIODS')
				{
                                                                                    //$id = DBGet(DBQuery("SELECT ".db_seq_nextval('COURSE_PERIODS_SEQ').' AS ID'.FROM_DUAL));
                                                                                    // edited at 4.11.2009 start
                                                                                    $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'COURSE_PERIODS'"));
                                                                                    $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
                                                                                    // edited at 4.11.2009 end
                                                                                    $fields = 'SYEAR,SCHOOL_ID,COURSE_ID,TITLE,';
                                                                                    $teacher = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME FROM STAFF WHERE SYEAR='".UserSyear()."' AND STAFF_ID='$columns[TEACHER_ID]'"));
                                                                                    $period = DBGet(DBQuery("SELECT TITLE FROM SCHOOL_PERIODS WHERE PERIOD_ID='$columns[PERIOD_ID]' AND SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));

                                                                                    if(!isset($columns['PARENT_ID']))
                                                                                        $columns['PARENT_ID'] = $id[1]['ID'];

                                                                                    if(isset($columns['MARKING_PERIOD_ID']))
                                                                                    {
                                                                                        if(GetMP($columns['MARKING_PERIOD_ID'],'TABLE')=='SCHOOL_YEARS')
                                                                                            $columns['MP'] = 'FY';
                                                                                        elseif(GetMP($columns['MARKING_PERIOD_ID'],'TABLE')=='SCHOOL_SEMESTERS')
                                                                                            $columns['MP'] = 'SEM';
                                                                                        else
                                                                                            $columns['MP'] = 'QTR';

                                                                                        if(GetMP($columns['MARKING_PERIOD_ID'],'TABLE')!='SCHOOL_YEARS')
                                                                                            $mp_title = GetMP($columns['MARKING_PERIOD_ID'],'SHORT_NAME').' - ';
                                                                                    }

                                                                                    if(strlen($columns['DAYS'])<5)
                                                                                    $mp_title .= $columns['DAYS'].' - ';
                                                                                    if($columns['SHORT_NAME'])
                                                                                        $mp_title .= paramlib_validation($column=SHORT_NAME,$columns['SHORT_NAME']).' - ';
                                                                                    $title = str_replace("'","''",$period[1]['TITLE'].' - '.$mp_title.$teacher[1]['FIRST_NAME'].' '.$teacher[1]['MIDDLE_NAME'].' '.$teacher[1]['LAST_NAME']);

                                                                                    $values = "'".UserSyear()."','".UserSchool()."','$_REQUEST[course_id]','$title',";
                                                                                    $_REQUEST['course_period_id'] = $id[1]['ID'];
				}
				
				$go = 0;
				foreach($columns as $column=>$value)
				{
                                                                                if($value!='')
                                                                                {
                                                                                        //echo "<BR>".$column."<BR>".$value;
                                                                                        $value=trim(paramlib_validation($column,$value));
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

				if($go){
					DBQuery($sql);
                                      
                                                                        }
				// ----------------------------------------------- //
				if($_REQUEST['w_course_period_id'])
				{
					$max_id = DBGet(DBQuery("SELECT MAX(COURSE_PERIOD_ID) AS CP_ID FROM COURSE_PERIODS;"));
					$sql_2 = "UPDATE COURSE_PERIODS SET PARENT_ID=".$_REQUEST['w_course_period_id']." WHERE COURSE_PERIOD_ID = ".$max_id[1]['CP_ID'];
					DBQuery($sql_2);
				}
				// ----------------------------------------------- //
				
			}
		}
	}
	unset($_REQUEST['tables']);
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete' && AllowEdit())
{
 	unset($sql);
        $course_period_id=paramlib_validation($colmn=PERIOD_ID,$_REQUEST[course_period_id]);
        $course_id=paramlib_validation($colmn=PERIOD_ID,$_REQUEST[course_id]);
        $subject_id=paramlib_validation($colmn=PERIOD_ID,$_REQUEST[subject_id]);
         
	if(clean_param($_REQUEST['course_period_id'],PARAM_ALPHANUM))
	{
			$table = 'course period';
			$sql[] = "UPDATE COURSE_PERIODS SET PARENT_ID=NULL WHERE PARENT_ID='$course_period_id'";
			$sql[] = "DELETE FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='$course_period_id'";
			$sql[] = "DELETE FROM SCHEDULE WHERE COURSE_PERIOD_ID='$course_period_id'";
	}

	elseif(clean_param($_REQUEST['course_id'],PARAM_ALPHANUM))
	{
		$table = 'course';
			$course_period=DBGet(DBQuery("SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_ID='$course_id'"));
			//print_r($course_period['COURSE_PERIOD_ID']);
			foreach($course_period as $course1)
			
			if($course1['COURSE_PERIOD_ID']=='') {
			//echo 'hiii';exit;
			$sql[] = "DELETE FROM COURSES WHERE COURSE_ID='$course_id'";
			#$sql[] = "UPDATE COURSE_PERIODS SET PARENT_ID=NULL WHERE PARENT_ID IN (SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_ID='$_REQUEST[course_id]')";
			
                       ############# query error solved	##############	
			$extra_sql="SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_ID='$course_id'";
			$result_sql = DBGet(DBQuery($extra_sql));
			$sql[] = "UPDATE COURSE_PERIODS SET PARENT_ID=NULL WHERE PARENT_ID = '".$result_sql."'";
			

                        $sql[] = "DELETE FROM COURSE_PERIODS WHERE COURSE_ID='$course_id'";
			$sql[] = "DELETE FROM SCHEDULE WHERE COURSE_ID='$course_id'";
			$sql[] = "DELETE FROM SCHEDULE_REQUESTS WHERE COURSE_ID='$course_id'";
			
			if(DeletePromptCommon($table))
		{	//echo 'hii'exit;
			if(BlockDelete($table))
			{
				//foreach($sql as $query)
					DBQuery($sql);
				unset($_REQUEST['modfunc']);
			}
		}
			
		}
		
		
		
			if ($course1['COURSE_PERIOD_ID']!='') {
				PopTable('header','Unable to Delete');
					DrawHeaderHome('<font color=red>Course cannot be deleted.</font>');
					echo '<div align=right><a href=Modules.php?modname=School_Setup/Courses.php&subject_id='.$subject_id.'&course_id='.$course_id.' style="text-decoration:none"><b>back to Course</b></a></div>';
					PopTable('footer');
	
			}
			else {
			if(DeletePromptCommon($table))
		{	
		//echo 'hii'exit;
			if(BlockDelete($table))
			{
				$sql[] = "DELETE FROM COURSES WHERE COURSE_ID='$course_id'";
			#$sql[] = "UPDATE COURSE_PERIODS SET PARENT_ID=NULL WHERE PARENT_ID IN (SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_ID='$_REQUEST[course_id]')";
			
                       ############# query error solved	##############	
			$extra_sql="SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_ID='$course_id'";
			$result_sql = DBGet(DBQuery($extra_sql));
			$sql[] = "UPDATE COURSE_PERIODS SET PARENT_ID=NULL WHERE PARENT_ID = '".$result_sql."'";
			

                        $sql[] = "DELETE FROM COURSE_PERIODS WHERE COURSE_ID='$course_id'";
			$sql[] = "DELETE FROM SCHEDULE WHERE COURSE_ID='$course_id'";
			$sql[] = "DELETE FROM SCHEDULE_REQUESTS WHERE COURSE_ID='$course_id'";
				foreach($sql as $query)
					DBQuery($query);
				unset($_REQUEST['modfunc']);
			}
		}
		}
	//if ($course['COURSE_PERIOD_ID']==''){	
	/*else {	
	
	}*/
	//}
	}
	elseif(clean_param($_REQUEST['subject_id'],PARAM_ALPHANUM))
	{
		$table = 'subject';
		$subject=DBGet(DBQuery("SELECT COURSE_ID FROM COURSES WHERE SUBJECT_ID='$subject_id'"));
		foreach($subject as $subject1) 
			if ($subject1['COURSE_ID']=='') {
			$sql[] = "DELETE FROM COURSE_SUBJECTS WHERE SUBJECT_ID='$subject_id'";
			$courses = DBGet(DBQuery("SELECT COURSE_ID FROM COURSES WHERE SUBJECT_ID='$subject_id'"));
			if(count($courses))
			{
				foreach($courses as $course)
				{
					$sql[] = "DELETE FROM COURSES WHERE COURSE_ID='$course[COURSE_ID]'";
					#$sql[] = "UPDATE COURSE_PERIODS SET PARENT_ID=NULL WHERE PARENT_ID IN (SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_ID='$course[COURSE_ID]')";
					
                                        				
					$extra_sql2="SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_ID='$course[COURSE_ID]'";
					$result_sql2 = DBGet(DBQuery($extra_sql2));
					$sql[] = "UPDATE COURSE_PERIODS SET PARENT_ID=NULL WHERE PARENT_ID = '".$result_sql2."'";

                                        $sql[] = "DELETE FROM COURSE_PERIODS WHERE COURSE_ID='$course[COURSE_ID]'";
					$sql[] = "DELETE FROM SCHEDULE WHERE COURSE_ID='$course[COURSE_ID]'";
					$sql[] = "DELETE FROM SCHEDULE_REQUESTS WHERE COURSE_ID='$course[COURSE_ID]'";
				}
			}
			
			
			}
			if($subject1['COURSE_ID']!='') {
				PopTable('header','Unable to Delete');
					DrawHeaderHome('<font color=red>Subject cannot be deleted.</font>');
					echo '<div align=right><a href=Modules.php?modname=School_Setup/Courses.php&subject_id='.$subject_id.' style="text-decoration:none"><b>back to Subject</b></a></div>';
					PopTable('footer');
			
			}
			else {
			if(DeletePromptCommon($table))
		{	
		//echo 'hii'exit;
			if(BlockDelete($table))
			{
			$sql[] = "DELETE FROM COURSE_SUBJECTS WHERE SUBJECT_ID='$subject_id'";
			$courses = DBGet(DBQuery("SELECT COURSE_ID FROM COURSES WHERE SUBJECT_ID='$subject_id'"));
			if(count($courses))
			{
				foreach($courses as $course)
				{
					$sql[] = "DELETE FROM COURSES WHERE COURSE_ID='$course[COURSE_ID]'";
					#$sql[] = "UPDATE COURSE_PERIODS SET PARENT_ID=NULL WHERE PARENT_ID IN (SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_ID='$course[COURSE_ID]')";
					
                                        				
					$extra_sql2="SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_ID='$course[COURSE_ID]'";
					$result_sql2 = DBGet(DBQuery($extra_sql2));
					$sql[] = "UPDATE COURSE_PERIODS SET PARENT_ID=NULL WHERE PARENT_ID = '".$result_sql2."'";

                                        $sql[] = "DELETE FROM COURSE_PERIODS WHERE COURSE_ID='$course[COURSE_ID]'";
					$sql[] = "DELETE FROM SCHEDULE WHERE COURSE_ID='$course[COURSE_ID]'";
					$sql[] = "DELETE FROM SCHEDULE_REQUESTS WHERE COURSE_ID='$course[COURSE_ID]'";
					
			
			}
	}
			foreach($sql as $query)
					DBQuery($query);
				unset($_REQUEST['modfunc']);
			}
		}
	}

}

/*if ($course1['COURSE_PERIOD_ID']=='') {
	if(DeletePrompt($table))
	{	
		if(BlockDelete($table))
		{
			foreach($sql as $query)
			echo ''
				DBQuery($query);
			unset($_REQUEST['modfunc']);
		}
	}
}*/
	/*if ($subject =='') {
	if(DeletePrompt($table))
	{
		if(BlockDelete($table))
		{
			foreach($sql as $query)
				DBQuery($query);
			unset($_REQUEST['modfunc']);
		}
	}
	}*/
	if($_REQUEST['course_period_id']) {
		if(DeletePromptCommon($table))
		{	//echo 'hii'exit;
			if(BlockDelete($table))
			{
				foreach($sql as $query)
					DBQuery($query);
				unset($_REQUEST['modfunc']);
			}
		}
	
	
	}

}

if((!$_REQUEST['modfunc'] || clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='choose_course') && !$_REQUEST['course_modfunc'])
{
	if($_REQUEST['modfunc']!='choose_course')
		DrawBC("Scheduling > ".ProgramTitle());
	$sql = "SELECT SUBJECT_ID,TITLE FROM COURSE_SUBJECTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY TITLE";
	$QI = DBQuery($sql);
	$subjects_RET = DBGet($QI);

	if($_REQUEST['modfunc']!='choose_course')
	{
		if(AllowEdit())
			$delete_button = "<INPUT type=button class=btn_medium value=Delete onClick='javascript:window.location=\"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=$_REQUEST[course_period_id]\"'> ";
		// ADDING & EDITING FORM
		if(clean_param($_REQUEST['course_period_id'],PARAM_ALPHANUM))
		{
			if($_REQUEST['course_period_id']!='new')
			{
				$sql = "SELECT PARENT_ID,TITLE,SHORT_NAME,PERIOD_ID,DAYS,
								MP,MARKING_PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID,CALENDAR_ID,
								ROOM,TOTAL_SEATS,DOES_ATTENDANCE,
								GRADE_SCALE_ID,DOES_HONOR_ROLL,DOES_CLASS_RANK,
								GENDER_RESTRICTION,HOUSE_RESTRICTION,CREDITS,
								HALF_DAY,DOES_BREAKOFF
						FROM COURSE_PERIODS
						WHERE COURSE_PERIOD_ID='$_REQUEST[course_period_id]'";
				$QI = DBQuery($sql);
				$RET = DBGet($QI);
				$RET = $RET[1];
				$title = $RET['TITLE'];
				$new = false;
			}
			else
			{
				$sql = "SELECT TITLE
						FROM COURSES
						WHERE COURSE_ID='$_REQUEST[course_id]'";
				$QI = DBQuery($sql);
				$RET = DBGet($QI);
				$title = $RET[1]['TITLE'].' - New Period';
				unset($delete_button);
				unset($RET);
				$checked = 'CHECKED';
				$new = true;
			}

			echo "<FORM name=F2 id=F2 action=Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=$_REQUEST[course_period_id] method=POST>";
                       echo '<input type="hidden" name="get_status" id="get_status" value="" />';
                       echo '<input type="hidden" name="cp_id" id="cp_id" value="'.$_REQUEST['course_period_id'].'"/>';
                DrawHeaderHome($title,$delete_button.SubmitButton('Save','','class=btn_medium onclick="formcheck_scheduling_course_F2();"'));
			
			$header .= '<TABLE cellpadding=3 width=760 >';
			$header .= '<TR>';

			$header .= '<TD>' . TextInput($RET['SHORT_NAME'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][SHORT_NAME]','Short Name','class=cell_floating') . '</TD>';

			$teachers_RET = DBGet(DBQuery("SELECT STAFF_ID,LAST_NAME,FIRST_NAME,MIDDLE_NAME FROM STAFF WHERE (SCHOOLS IS NULL OR FIND_IN_SET('".UserSchool()."',SCHOOLS)>0) AND SYEAR='".UserSyear()."' AND PROFILE='teacher' AND ISNULL(IS_DISABLE) ORDER BY LAST_NAME,FIRST_NAME "));
			if(count($teachers_RET))
			{
				foreach($teachers_RET as $teacher)
					$teachers[$teacher['STAFF_ID']] = $teacher['LAST_NAME'].', '.$teacher['FIRST_NAME'].' '.$teacher['MIDDLE_NAME'];
			}
			$header .= '<TD>' . SelectInput($RET['TEACHER_ID'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][TEACHER_ID]','Teacher',$teachers) . '</TD>';
			
			$header .= '<TD>' . SelectInput($RET['SECONDARY_TEACHER_ID'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][SECONDARY_TEACHER_ID]','Secondary Teacher',$teachers) . '</TD>';
			$header .= '<TD></TD>';
                        $header .= '</TR><TR>';
                        
			$header .= '<TD>' . TextInput($RET['ROOM'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][ROOM]','Room','class=cell_floating') . '</TD>';

			$periods_RET = DBGet(DBQuery("SELECT PERIOD_ID,TITLE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
			if(count($periods_RET))
			{
				foreach($periods_RET as $period)
					$periods[$period['PERIOD_ID']] = $period['TITLE'];
			}
                    //   $header .= '<TD>' . SelectInput($RET['PERIOD_ID'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][PERIOD_ID]','Period',$periods) . '</TD>';
			$header .= '<TD>' . SelectInput($RET['PERIOD_ID'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][PERIOD_ID]','Period',$periods,'N/A','id="cp_period" onchange="formcheck_periods_F2();"') . '</TD>';
                        $mp_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,SHORT_NAME,'2' AS t,SORT_ORDER FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' UNION SELECT MARKING_PERIOD_ID,SHORT_NAME,'1' AS t,SORT_ORDER FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' UNION SELECT MARKING_PERIOD_ID,SHORT_NAME,'0' AS t,SORT_ORDER FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY 3,4"));
			unset($options);
			if($_REQUEST['course_period_id']!='new')
			{
				$available_seats = DBGet(DBQuery("SELECT (TOTAL_SEATS - FILLED_SEATS) AS AVAILABLE_SEATS FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
				$available_seats = $available_seats[1]['AVAILABLE_SEATS'];
			}

			if(count($mp_RET))
			{
				foreach($mp_RET as $mp)
					$options[$mp['MARKING_PERIOD_ID']] = $mp['SHORT_NAME'];
			}
			$header .= '<TD>' . SelectInput($RET['MARKING_PERIOD_ID'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][MARKING_PERIOD_ID]','Marking Period',$options,false) . '</TD>';

                        $header .= '<TD><TABLE cellpadding=0 cellspacing=0><TR>';
                        $header .= '<TD>' . TextInput($RET['TOTAL_SEATS'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][TOTAL_SEATS]','Seats','size=4 class=cell_floating') . '</TD>';
			if($_REQUEST['course_period_id']!='new')
			$header .= '<TD style=padding-left:10px;><FONT color=green>' .$available_seats. '</FONT><BR><FONT color=gray><SMALL>Available Seats</SMALL></FONT></TD>';
                        $header .= '</TR></TABLE>';
                        $header .= '</TD>';

                        $header .= '</TR><TR>';

                        $header .= '<TD>' . SelectInput($RET['GENDER_RESTRICTION'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][GENDER_RESTRICTION]','Gender Restriction',array('N'=>'None','M'=>'Male','F'=>'Female'),false) . '</TD>';
                        $options_RET = DBGet(DBQuery("SELECT TITLE,ID FROM REPORT_CARD_GRADE_SCALES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
			$options = array();
			foreach($options_RET as $option)
				$options[$option['ID']] = $option['TITLE'];
			$header .= '<TD>' . SelectInput($RET['GRADE_SCALE_ID'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][GRADE_SCALE_ID]','Grading Scale',$options,'Not Graded') . '</TD>';


                        
			$header .= '<TD>';
			if($new==false)
				$header .= '<DIV id=days><div onclick=\'addHTML("';
			$header .= '<TABLE><TR>';
			$days = array('U','M','T','W','H','F','S');
			foreach($days as $day)
			{
				if(strpos($RET['DAYS'],$day)!==false || ($new && $day!='S' && $day!='U'))
					$value = 'Y';
				else
					$value = '';

				$header .= '<TD>'.str_replace('"','\"',CheckboxInput($value,'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][DAYS]['.$day.']',($day=='U'?'S':$day),$checked,false,'','',false)).'</TD>';
			}
			$header .= '</TR></TABLE>';
			
			if($new==false)
				$header .= '","days",true);\'>'.$RET['DAYS'].'</div></DIV><small><FONT color='.Preferences('TITLES').'>Meeting Days</FONT></small>';
			$header .= '</TD>';
			$options_RET = DBGet(DBQuery("SELECT TITLE,CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY DEFAULT_CALENDAR"));
			$options = array();
			foreach($options_RET as $option)
				$options[$option['CALENDAR_ID']] = $option['TITLE'];
			$header .= '<TD>' . SelectInput($RET['CALENDAR_ID'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][CALENDAR_ID]','Calendar',$options,false) . '</TD>';
			

			$header .= '</TR>';

			$header .= '<TR>';
                        //BJJ Added to handle credits
                        $header .= '<TD>' . TextInput(sprintf('%0.3f',$RET['CREDITS']),'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][CREDITS]','Credits','size=4 class=cell_floating') . '</TD>';
                     // $header .= '<TD>' . CheckboxInput($RET['DOES_ATTENDANCE'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][DOES_ATTENDANCE]','Takes Attendance',$checked,$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD>';
			$header .= '<TD>' . CheckboxInput($RET['DOES_ATTENDANCE'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][DOES_ATTENDANCE]','Takes Attendance',$checked,$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>',true,'id="cp_does_attendance" onclick="formcheck_periods_attendance_F2(this);"') . '<br><div id="ajax_output"></div></TD>';
                        $header .= '<TD>' . CheckboxInput($RET['DOES_HONOR_ROLL'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][DOES_HONOR_ROLL]','Affects Honor Roll',$checked,$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD>';
			$header .= '<TD>' . CheckboxInput($RET['HALF_DAY'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][HALF_DAY]','Half Day',$checked,$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD>';

			$header .= '</TR>';

			$header .= '<TR>';
                        $header .= '<TD>' . CheckboxInput($RET['DOES_BREAKOFF'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][DOES_BREAKOFF]','Allow Teacher Gradescale',$checked,$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD>';
                        $header .= '<TD>' . CheckboxInput($RET['DOES_CLASS_RANK'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][DOES_CLASS_RANK]','Affects Class Rank',$checked,$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD>';

                        //BJJ moved parent course select here:
            if($_REQUEST['course_period_id']!='new' && $RET['PARENT_ID']!=$_REQUEST['course_period_id'])
            {
                $parent = DBGet(DBQuery("SELECT cp.TITLE as CP_TITLE,c.TITLE AS C_TITLE FROM COURSE_PERIODS cp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.COURSE_PERIOD_ID='".$RET['PARENT_ID']."'"));
                $parent = $parent[1]['C_TITLE'].' : '.$parent[1]['CP_TITLE'];
            }
            elseif($_REQUEST['course_period_id']!='new')
            {
                $children = DBGet(DBQuery("SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS WHERE PARENT_ID='".$_REQUEST['course_period_id']."' AND COURSE_PERIOD_ID!='".$_REQUEST['course_period_id']."'"));
                if(count($children))
                    $parent = 'N/A';
                else
                    $parent = 'None';
            }

         //		--------------------------------------------- Temp Coment ------------------------------------------------- 	//
				# misc/ChooseCourse.php
		     # $header .= "<TD colspan=2><DIV id=course_div>".$parent."</DIV> ".($parent!='N/A'?"<A HREF=# onclick='window.open(\"for_window.php?modname=".$_REQUEST['modname']."&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Choose</A><BR>":'')."<small><FONT color=".Preferences('TITLES').">Parent Course Period</FONT></small></TD>";
                if($_REQUEST['course_period_id']!='new' && $RET['PARENT_ID']!=$_REQUEST['course_period_id']){
                    $header .= "<TD colspan=2><DIV id=course_div>".$parent."</DIV> ".($parent!='N/A' && AllowEdit()?"<A HREF=# onclick='window.open(\"for_window.php?modname=misc/ChooseParentCourse.php\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Choose</A>"."&nbsp;&nbsp;"."<INPUT type=checkbox name='tables[COURSE_PERIODS][".$_REQUEST['course_period_id']."][PARENT_ID]' value='".$_REQUEST['course_period_id']."' >&nbsp;Remove" ."<BR>":'')."<small><FONT color=".Preferences('TITLES').">Parent Course Period</FONT></small></TD>";
                }else{
                    $header .= "<TD colspan=2><DIV id=course_div>".$parent."</DIV> ".($parent!='N/A' && AllowEdit()?"<A HREF=# onclick='window.open(\"for_window.php?modname=misc/ChooseParentCourse.php\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Choose</A><BR>":'')."<small><FONT color=".Preferences('TITLES').">Parent Course Period</FONT></small></TD>";
                }

		//		--------------------------------------------- Temp Coment ------------------------------------------------- 	//

			
			//BJJ Parent course select was here.

			$header .= '</TR>';


			//$header .= '<TD>' . CheckboxInput($RET['HOUSE_RESTRICTION'],'tables[COURSE_PERIODS]['.$_REQUEST['course_period_id'].'][HOUSE_RESTRICTION]','Restricts House','',$new) . '</TD>';
			
			
                        //BJJ added cells to place parent selection in last column.


			$header .= '</TABLE>';
			#PopTable_wo_header ('header');
			DrawHeaderHome($header);
			#PopTable ('footer');
			echo '</FORM>';
		}

		elseif(clean_param($_REQUEST['course_id'],PARAM_ALPHANUM))
		{
			if($_REQUEST['course_id']!='new')
			{
				$sql = "SELECT TITLE,SHORT_NAME,GRADE_LEVEL
						FROM COURSES
						WHERE COURSE_ID='$_REQUEST[course_id]'";
				$QI = DBQuery($sql);
				$RET = DBGet($QI);
				$RET = $RET[1];
				$title = $RET['TITLE'];
			}
			else
			{
				$sql = "SELECT TITLE
						FROM COURSE_SUBJECTS
						WHERE SUBJECT_ID='$_REQUEST[subject_id]' ORDER BY TITLE";
				$QI = DBQuery($sql);
				$RET = DBGet($QI);
				$title = $RET[1]['TITLE'].' - New Course';
				unset($delete_button);
				unset($RET);
			}

			echo "<FORM name=F3 id=F3 action=Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id] method=POST>";
			DrawHeaderHome($title,$delete_button.SubmitButton('Save','','class=btn_medium onclick="formcheck_scheduling_course_F3();"'));
			$header .= '<TABLE cellpadding=3 width=100%>';
			$header .= '<TR>';

			$header .= '<TD>' . TextInput($RET['TITLE'],'tables[COURSES]['.$_REQUEST['course_id'].'][TITLE]','Title','class=cell_mod_wide') . '</TD>';
			$header .= '<TD>' . TextInput($RET['SHORT_NAME'],'tables[COURSES]['.$_REQUEST['course_id'].'][SHORT_NAME]','Short Name','class=cell_floating') . '</TD>';
			if($_REQUEST['modfunc']!='choose_course')
			{
				foreach($subjects_RET as $type)
					$options[$type['SUBJECT_ID']] = $type['TITLE'];

				$header .= '<TD>' . SelectInput($RET['SUBJECT_ID']?$RET['SUBJECT_ID']:$_REQUEST['subject_id'],'tables[COURSES]['.$_REQUEST['course_id'].'][SUBJECT_ID]','Subject',$options,false) . '</TD>';
			}
			$header .= '</TR>';
			$header .= '</TABLE>';
			DrawHeaderHome($header);
			echo '</FORM>';
		}
		elseif(clean_param($_REQUEST['subject_id'],PARAM_ALPHANUM))
		{
			if($_REQUEST['subject_id']!='new')
			{
				$sql = "SELECT TITLE
						FROM COURSE_SUBJECTS
						WHERE SUBJECT_ID='$_REQUEST[subject_id]'";
				$QI = DBQuery($sql);
				$RET = DBGet($QI);
				$RET = $RET[1];
				$title = $RET['TITLE'];
			}
			else
			{
				$title = 'New Subject';
				unset($delete_button);
			}

			echo "<FORM name=F4 id=F4 action=Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id] method=POST>";
			DrawHeaderHome($title,$delete_button.SubmitButton('Save','','class=btn_medium onclick="formcheck_scheduling_course_F4();"'));
			$header .= '<TABLE cellpadding=3 width=100%>';
			$header .= '<TR>';

			$header .= '<TD>' . TextInput($RET['TITLE'],'tables[COURSE_SUBJECTS]['.$_REQUEST['subject_id'].'][TITLE]','Title','class=cell_wide') . '</TD>';

			$header .= '</TR>';
			$header .= '</TABLE>';
			DrawHeader($header);
			echo '</FORM>';
		}
	}

	// DISPLAY THE MENU
	$LO_options = array('save'=>false,'search'=>false);

	if(!$_REQUEST['subject_id'] || clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='choose_course')
		#DrawHeader('Courses');
	DrawHeaderHome('Courses',"<A HREF=for_window.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&course_modfunc=search>Search</A>");

	echo '<TABLE><TR>';

	if(count($subjects_RET))
	{
		if(clean_param($_REQUEST['subject_id'],PARAM_ALPHANUM))
		{
			foreach($subjects_RET as $key=>$value)
			{
				if($value['SUBJECT_ID']==$_REQUEST['subject_id'])
					$subjects_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
			}
		}
	}

	echo '<TD valign=top>';
	$columns = array('TITLE'=>'Subject');
	$link = array();
	$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]";
	//$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]\");'";
	$link['TITLE']['variables'] = array('subject_id'=>'SUBJECT_ID');
	if($_REQUEST['modfunc']!='choose_course')
		$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&subject_id=new";
		//$link['add']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&subject_id=new\");'";
	else
		$link['TITLE']['link'] .= "&modfunc=$_REQUEST[modfunc]";

	ListOutput($subjects_RET,$columns,'Subject','Subjects',$link,array(),$LO_options);
	echo '</TD>';

	if(clean_param($_REQUEST['subject_id'],PARAM_ALPHANUM) && $_REQUEST['subject_id']!='new')
	{
		$sql = "SELECT COURSE_ID,TITLE FROM COURSES WHERE SUBJECT_ID='$_REQUEST[subject_id]' ORDER BY TITLE";
		$QI = DBQuery($sql);
		$courses_RET = DBGet($QI);

		if(count($courses_RET))
		{
			if(clean_param($_REQUEST['course_id'],PARAM_ALPHANUM))
			{
				foreach($courses_RET as $key=>$value)
				{
					if($value['COURSE_ID']==$_REQUEST['course_id'])
						$courses_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
				}
			}
		}

		echo '<TD valign=top>';
		$columns = array('TITLE'=>'Course');
		$link = array();
		$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]";
		//$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]\");'";
		$link['TITLE']['variables'] = array('course_id'=>'COURSE_ID');
		if($_REQUEST['modfunc']!='choose_course')
			$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=new";
			//$link['add']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=new\");'";
		else
			$link['TITLE']['link'] .= "&modfunc=$_REQUEST[modfunc]";

		ListOutput($courses_RET,$columns,'Course','Courses',$link,array(),$LO_options);
		echo '</TD>';

		if(clean_param($_REQUEST['course_id'],PARAM_ALPHANUM) && $_REQUEST['course_id']!='new')
		{

                #$sql = "SELECT COURSE_PERIOD_ID,TITLE,COALESCE(TOTAL_SEATS-FILLED_SEATS,0) AS AVAILABLE_SEATS FROM COURSE_PERIODS WHERE COURSE_ID='$_REQUEST[course_id]' ORDER BY TITLE";
				
				
				$sql_mp_filter = "SELECT MP_TYPE, PARENT_ID, GRANDPARENT_ID FROM MARKING_PERIODS WHERE MARKING_PERIOD_ID=".UserMP();
				$res_mp_filter = DBQuery($sql_mp_filter);
                $row_mp_filter = DBGet($res_mp_filter);
				
				$mp_type = $row_mp_filter[1]['MP_TYPE'];
				$p_id = $row_mp_filter[1]['PARENT_ID'];
				$gp_id = $row_mp_filter[1]['GRANDPARENT_ID'];
				
				if($mp_type == 'quarter')
				{
					$cond = " AND (MARKING_PERIOD_ID = ".UserMP()." OR MARKING_PERIOD_ID = ".$p_id." OR MARKING_PERIOD_ID = ".$gp_id.")";
				}
				if($mp_type == 'semester')
				{
					$cond = " AND (MARKING_PERIOD_ID = ".UserMP()." OR MARKING_PERIOD_ID = ".$p_id.")";
				}
				if($mp_type == 'year')
				{
					$cond = " AND MARKING_PERIOD_ID = ".UserMP();
				}
				
				
				$sql = "SELECT COURSE_PERIOD_ID,TITLE,COALESCE(TOTAL_SEATS-FILLED_SEATS,0) AS AVAILABLE_SEATS FROM COURSE_PERIODS WHERE COURSE_ID='$_REQUEST[course_id]'".$cond." ORDER BY TITLE";
				
                $QI = DBQuery($sql);
                $periods_RET = DBGet($QI);

                if(count($periods_RET))
                {
                    if(clean_param($_REQUEST['course_period_id'],PARAM_ALPHANUM))
                    {
                        foreach($periods_RET as $key=>$value)
                        {
                            if($value['COURSE_PERIOD_ID']==$_REQUEST['course_period_id'])
                                $periods_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
                        }
                    }
                }

                echo '<TD valign=top>';
                $columns = array('TITLE'=>'Course Period');
                if($_REQUEST['modname']=='Scheduling/Schedule.php')
                    $columns += array('AVAILABLE_SEATS'=>'Available Seats');
                $link = array();
                $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]";
				//$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]\");'";
                $link['TITLE']['variables'] = array('course_period_id'=>'COURSE_PERIOD_ID');
                if($_REQUEST['modfunc']!='choose_course')
                    $link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=new";
					//$link['add']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=new\");'";
                else
                    $link['TITLE']['link'] .= "&modfunc=$_REQUEST[modfunc]";

                ListOutput($periods_RET,$columns,'Period','Periods',$link,array(),$LO_options);
                echo '</TD>';
            
		}
	}

	echo '</TR></TABLE>';
}

if(clean_param($_REQUEST['modname'],PARAM_ALPHAEXT)=='Scheduling/Courses.php' && clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='choose_course' && clean_param($_REQUEST['course_period_id'],PARAM_ALPHANUM))
{
	$course_title = DBGet(DBQuery("SELECT TITLE FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
	$course_title = $course_title[1]['TITLE'] . '<INPUT type=hidden name=tables[parent_id] value='.$_REQUEST['course_period_id'].'>';

	echo "<script language=javascript>opener.document.getElementById(\"course_div\").innerHTML = \"$course_title</small>\"; window.close();</script>";
}


?>
