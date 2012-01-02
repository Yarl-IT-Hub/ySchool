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
if(!$_REQUEST['modfunc'] && $_REQUEST['search_modfunc']!='list')
        unset($_SESSION['MassDrops.php']);

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHA)=='save')
{
               $END_DATE = $_REQUEST['day'].'-'.$_REQUEST['month'].'-'.$_REQUEST['year'];
                $end_date_mod=date('Y-m-d',strtotime($END_DATE));
               if(!VerifyDate($END_DATE))
               {
                        DrawHeader('<table><tr><td><IMG SRC=assets/x.gif></td><td>The date you entered is not valid</td></tr></table>');
                        for_error_sch();
               }
               else
               {
                    $mp_table = GetMPTable(GetMP($_REQUEST['marking_period_id'],'TABLE'));
                    //$current_RET = DBGet(DBQuery("SELECT STUDENT_ID FROM SCHEDULE WHERE COURSE_PERIOD_ID='".$_SESSION['MassDrops.php']['course_period_id']."' AND SYEAR='".UserSyear()."' AND (('".$start_date."' BETWEEN START_DATE AND END_DATE OR END_DATE IS NULL) AND '".$start_date."'>=START_DATE)"),array(),array('STUDENT_ID'));
                    $current_RET = DBGet(DBQuery("SELECT STUDENT_ID FROM SCHEDULE WHERE COURSE_PERIOD_ID='".$_SESSION['MassDrops.php']['course_period_id']."' "));
                if(count($_REQUEST['student'])>0)
                {
                    foreach($_REQUEST['student'] as $student_id=>$yes)
                    {
                                $start_end_RET = DBGet(DBQuery("SELECT START_DATE,END_DATE,SCHEDULER_LOCK FROM SCHEDULE WHERE STUDENT_ID='".$student_id."' AND COURSE_PERIOD_ID='".$_SESSION['MassDrops.php']['course_period_id']."'"));
                                if(count($start_end_RET))
                                {
                                if($start_end_RET[1]['SCHEDULER_LOCK']=='Y' || $start_end_RET[1]['START_DATE']>$end_date_mod)
                                    {
                                         $select_stu = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STUDENTS WHERE STUDENT_ID='".$student_id."'"));
                                         $select_stu = $select_stu[1]['FIRST_NAME']."&nbsp;".$select_stu[1]['LAST_NAME'];
                                         if($start_end_RET[1]['SCHEDULER_LOCK']=='Y')
                                        {
                                          $inactive_schedule2 .= $select_stu."<br>";
                                         $inactive_schedule_found = 2;
                                        }
                                         if($start_end_RET[1]['START_DATE']>$end_date_mod)
                                         {
                                             $inactive_schedule .= $select_stu."<br>";
                                             $inactive_schedule_found = 1;
                                         }
                                    }
                                
                                    else
                                    {
                                         DBQuery("UPDATE SCHEDULE SET END_DATE='".$END_DATE."' WHERE STUDENT_ID='".clean_param($student_id,PARAM_INT)."' AND COURSE_PERIOD_ID='".clean_param($_SESSION['MassDrops.php']['course_period_id'],PARAM_INT)."'");
                                         DBQuery("CALL SEAT_COUNT()"); 
                                         $note = "Selected students have been dropped from the course period.";
                                    }
                                    
                                }
//                              DBQuery("CALL SEAT_FILL()");
                        }
                        UpdateMissingAttendance($_SESSION['MassDrops.php']['course_period_id']);
                        unset($_REQUEST['modfunc']);
                        unset($_SESSION['MassDrops.php']);
                        if($note)
                            DrawHeader('<table><tr><td><IMG SRC=assets/check.gif></td><td>'.$note.'</td></tr></table>');
                        if($inactive_schedule_found==1)
                            DrawHeaderHome('<IMG SRC=assets/warning_button.gif><br>'.$inactive_schedule.' have farthar schedule date');
                        if($inactive_schedule_found==2)
                             DrawHeaderHome('<IMG SRC=assets/warning_button.gif><br>Dropped date can not be changed for '.$inactive_schedule2.'.This schedule is locked.');
                   }
                   else 
                  {
                        unset($_REQUEST['modfunc']);
                        unset($_SESSION['MassDrops.php']);
                        DrawHeader('<table><tr><td><IMG SRC=assets/x.gif></td><td>No Studetn selected</td></tr></table>');
                  }
                }
               
}


if(!$_REQUEST['modfunc'])
{
       if($_REQUEST['search_modfunc']=='list')
        {
                echo "<FORM name=ww id=ww action=Modules.php?modname=$_REQUEST[modname]&modfunc=save method=POST>";
        }
        if($_REQUEST['search_modfunc']!='list')
                unset($_SESSION['MassDrops.php']);
        $extra['SELECT'] = ",CAST(NULL AS CHAR(1)) AS CHECKBOX";
        $extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
        $extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
        $extra['new'] = true;

        if($_SESSION['MassDrops.php']['course_period_id'])
        {
                        $extra['FROM'] .= ",SCHEDULE w_ss";
                        $extra['WHERE'] .= " AND w_ss.STUDENT_ID=s.STUDENT_ID AND w_ss.SYEAR=ssm.SYEAR AND w_ss.SCHOOL_ID=ssm.SCHOOL_ID AND w_ss.COURSE_PERIOD_ID='".$_SESSION['MassDrops.php']['course_period_id']."' AND (".(($_REQUEST['include_inactive'])?"": "w_ss.START_DATE <='".DBDate()."'AND")." (w_ss.END_DATE>='".DBDate()."' OR w_ss.END_DATE IS NULL))";

                        $course = DBGet(DBQuery("SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,cp.COURSE_ID FROM COURSE_PERIODS cp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.COURSE_PERIOD_ID='".$_SESSION['MassDrops.php']['course_period_id']."'"));
                        $_openSIS['SearchTerms'] .= '<font color=gray><b>Course Period: </b></font>'.$course[1]['COURSE_TITLE'].': '.$course[1]['TITLE'].'<BR>';
        }
        $extra['search'] .= "<TR><TD align='left' width='160' valign='top'>Course Period</TD><TD valign='top'><DIV id=course_div></DIV> <A HREF=# onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Choose </A></TD></TR>";

         if($_REQUEST['search_modfunc']=='search_fnc' || !$_REQUEST['search_modfunc'])
        {
            echo '<BR>';
            echo '<script language=JavaScript>parent.help.location.reload();</script>';
            PopTable('header','Find Students to Drop');
            echo "<FORM name=search id=search action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&search_modfunc=list&next_modname=$_REQUEST[next_modname]".$extra['action']." method=POST>";
            echo '<TABLE border=0><TR><TD colspan="2">';
            echo $extra['search'];
            echo '</TD></TR><TR><TD width="160">Include advance schedule</TD><TD valign="top" align="left"> <INPUT type=checkbox name=include_inactive value=Y /></TD></TR></TABLE>';
            echo '<DIV id=cp_detail></DIV>';
            echo '<TABLE width=100%><TR><TD align=center><BR>';
            //echo Buttons('Submit','Reset');
            echo "<INPUT type=SUBMIT class=btn_medium id=submit value='Submit' onclick='return formcheck_mass_drop();formload_ajax(\"search\");'>&nbsp<INPUT type=RESET class=btn_medium value='Reset' onclick='document.getElementById(\"course_div\").innerHTML =\"\";document.getElementById(\"cp_detail\").innerHTML =\"\";' >";
            echo '</TD></TR>';
            echo '</TABLE>';
            echo '</FORM>';
            PopTable('footer');

        }
        else
        {
                  DrawBC("Scheduling > ".ProgramTitle());
                  echo '<input type="hidden" name="marking_period_id" value='.$_REQUEST['marking_period_id'].' >';
                  $students_RET = GetStuList($extra);

	$LO_columns = array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','ALT_ID'=>'Alternate ID','GRADE_ID'=>'Grade','PHONE'=>'Phone');

//	if(is_array($extra['link']))
//		$link = $extra['link'] + $name_link;
//	else
//		$link = $name_link;
	if(is_array($extra['columns_before']))
	{
		$columns = $extra['columns_before'] + $LO_columns;
		$LO_columns = $columns;
	}

	if(is_array($extra['columns_after']))
		$columns = $LO_columns + $extra['columns_after'];
	if(!$extra['columns_before'] && !$extra['columns_after'])
		$columns = $LO_columns;
                if(count($students_RET)>0)
                {
                        echo '<TABLE><TR><TD>Drop Date</TD><TD>'.PrepareDate(DBDate(),'').'</TD></TR></TABLE>';
                }
	if(count($students_RET) > 1 || $link['add'] || !$link['FULL_NAME'] || $extra['columns_before'] || $extra['columns_after'] || ($extra['BackPrompt']==false && count($students_RET)==0) || ($extra['Redirect']===false && count($students_RET)==1))
                  {
                        $tmp_REQUEST = $_REQUEST;
                        unset($tmp_REQUEST['expanded_view']);
                        if($_REQUEST['expanded_view']!='true' && !UserStudentID() && count($students_RET)!=0)
                        {
                            DrawHeader("<div><A HREF=".PreparePHP_SELF($tmp_REQUEST) . "&expanded_view=true class=big_font ><img src=\"themes/Blue/expanded_view.png\" />Expanded View</A></div><div class=break ></div>",$extra['header_right']);
                            DrawHeader(str_replace('<BR>','<BR> &nbsp;',substr($_openSIS['SearchTerms'],0,-4)));
                        }
                        elseif(!UserStudentID() && count($students_RET)!=0)
                        {
                            DrawHeader("<div><A HREF=".PreparePHP_SELF($tmp_REQUEST) . "&expanded_view=false class=big_font><img src=\"themes/Blue/expanded_view.png\" />Original View</A></div><div class=break ></div>",$extra['header_right']);
                            DrawHeader(str_replace('<BR>','<BR> &nbsp;',substr($_openSIS['Search'],0,-4)));
                        }
                        DrawHeader($extra['extra_header_left'],$extra['extra_header_right']);
                        if($_REQUEST['LO_save']!='1' && !$extra['suppress_save'])
                        {
                            $_SESSION['List_PHP_SELF'] = PreparePHP_SELF($_SESSION['_REQUEST_vars']);
                            echo '<script language=JavaScript>parent.help.location.reload();</script>';
                        }
                        if(!$extra['singular'] || !$extra['plural'])
                            $extra['singular'] = 'Student';
                            $extra['plural'] = 'Students';

                        echo "<div id='students' >";
                        ListOutput($students_RET,$columns,$extra['singular'],$extra['plural'],$link,$extra['LO_group'],$extra['options']);
                        echo "</div>";
	}

                    if(count($students_RET)>0)
                    {
                        echo '<BR><CENTER>'.SubmitButton('','','class=btn_group_drops onclick=\'formload_ajax("ww");\'').'</CENTER>';
                        echo "</FORM>";
                    }
        }
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAEXT)=='choose_course')
{
        if(!clean_param($_REQUEST['course_period_id'],PARAM_INT))
                include 'modules/Scheduling/CoursesforWindow.php';
        else
        {
                $_SESSION['MassDrops.php']['subject_id'] = clean_param($_REQUEST['subject_id'],PARAM_INT);
                $_SESSION['MassDrops.php']['course_id'] = clean_param($_REQUEST['course_id'],PARAM_INT);
                //$_SESSION['MassDrops.php']['course_weight'] = $_REQUEST['course_weight'];
                $_SESSION['MassDrops.php']['course_period_id'] = clean_param($_REQUEST['course_period_id'],PARAM_INT);

                $course_title = DBGet(DBQuery("SELECT TITLE FROM COURSES WHERE COURSE_ID='".$_SESSION['MassDrops.php']['course_id']."'"));
                $course_title = $course_title[1]['TITLE'];
                $cp_RET = DBGet(DBQuery("SELECT TITLE,(SELECT TITLE FROM SCHOOL_PERIODS sp WHERE sp.PERIOD_ID=cp.PERIOD_ID) AS PERIOD_TITLE,MARKING_PERIOD_ID,(SELECT CONCAT(FIRST_NAME,' ',LAST_NAME) FROM STAFF st WHERE st.STAFF_ID=cp.TEACHER_ID) AS TEACHER,ROOM,TOTAL_SEATS-FILLED_SEATS AS AVAILABLE_SEATS FROM COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID='".$_SESSION['MassDrops.php']['course_period_id']."'"));
                $cp_title = $cp_RET[1]['TITLE'];
                $cp_teacher = $cp_RET[1]['TEACHER'];
                $period_title=$cp_RET[1]['PERIOD_TITLE'];
                $mp_title = GetMP($cp_RET[1]['MARKING_PERIOD_ID']);
                $room=$cp_RET[1]['ROOM'];
                $seats=$cp_RET[1]['AVAILABLE_SEATS'];
                echo "<script language=javascript>opener.document.getElementById(\"course_div\").innerHTML = \"$cp_title\";opener.document.getElementById(\"cp_detail\").innerHTML = <TABLE border=\"0\"><TR><TD colspan=\"2\"><STRONG>Course period details</STRONG> </TD></TR><TR><TD width=\"160\" valign=\"top\" align=\"left\">Course Name </TD><TD valign=\"top\" align=\"left\">$course_title</TD></TR><TR><TD valign=\"top\" align=\"left\">Teacher </TD><TD valign=\"top\" align=\"left\">$cp_teacher</TD></TR><TR><TD valign=\"top\" align=\"left\">Period </TD><TD valign=\"top\" align=\"left\">$period_title</TD></TR><TR><TD valign=\"top\" align=\"left\">Marking Period </TD><TD valign=\"top\" align=\"left\">$mp_title</TD></TR><TR><TD valign=\"top\" align=\"left\">Room </TD><TD valign=\"top\" align=\"left\">$room</TD></TR><TR><TD valign=\"top\" align=\"left\">Available Seats </TD><TD valign=\"top\" align=\"left\">$seats</TD></TR></TABLE>; opener.document.getElementById(\"submit\").focus(); window.close();</script>";
        }
}

function _makeChooseCheckbox($value,$title)
{
    global $THIS_RET;
    return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}

?>
