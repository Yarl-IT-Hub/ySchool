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
if($_REQUEST['create_pdf']=='true')
{
    $handle = PDFStart();
    echo '<!-- MEDIA SIZE 11x8.5in -->';
    echo '<!-- MEDIA TOP 0.5in -->';
    echo '<!-- MEDIA BOTTOM 0.25in -->';
    echo '<!-- MEDIA LEFT 0.25in -->';
    echo '<!-- MEDIA RIGHT 0.25in -->';
    echo '<!-- FOOTER RIGHT "" -->';
    echo '<!-- FOOTER LEFT "" -->';
    echo '<!-- FOOTER CENTER "" -->';
    echo '<!-- HEADER RIGHT "" -->';
    echo '<!-- HEADER LEFT "" -->';
    echo '<!-- HEADER CENTER "" -->';
    echo CreateList($_REQUEST['degree_level_id'], $_REQUEST['prog_level_id'], $_REQUEST['subject_id'], $_REQUEST['course_id'], $_REQUEST['marking_period_id'], $_REQUEST['mp_name']);
    PDFStop($handle, 'sis.pdf');
    exit();
}

if(clean_param($_REQUEST['create_excel'],PARAM_ALPHAMOD)=='true')
{

    echo CreateExcel($_REQUEST['degree_level_id'], $_REQUEST['prog_level_id'], $_REQUEST['subject_id'], $_REQUEST['course_id'], $_REQUEST['marking_period_id'], $_REQUEST['mp_name']);

}
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='choose_course')
{
    DrawBC("Courses -> ".$_REQUEST['draw_header']);
    $sql = "SELECT PARENT_ID,TITLE,SHORT_NAME,PERIOD_ID,DAYS,
                                MP,MARKING_PERIOD_ID,TEACHER_ID,CALENDAR_ID,
                                ROOM,TOTAL_SEATS,DOES_ATTENDANCE,
                                GRADE_SCALE_ID,DOES_HONOR_ROLL,DOES_CLASS_RANK,
                                GENDER_RESTRICTION,HOUSE_RESTRICTION,CREDITS,
                                HALF_DAY,DOES_BREAKOFF
                        FROM COURSE_PERIODS
                        WHERE COURSE_PERIOD_ID='$_REQUEST[course_period_id]'";
    $QI = DBQuery($sql);
    $RET = DBGet($QI);
    $RET = $RET[1];
    $title = $RET['TITLE']." , <b>Course:</b> ".$C_RET[1]['TITLE'].", <b>Number of Credits:</b> ".$C_RET[1]['NUMBER_OF_CREDITS'];
    $new = false;
    //print_r($RET);
    if(count($RET))
    {
        $header .= '<TABLE cellpadding=3 width=100%>';
        $header .= '<TR>';

        $header .= '<TD><b>' .$RET['SHORT_NAME'] . '</b><br>Short Name</TD>';

        $teachers_RET = DBGet(DBQuery("SELECT concat((COALESCE(LAST_NAME,' '), ', ', COALESCE(FIRST_NAME,' '), ' ', COALESCE(MIDDLE_NAME,' '))) as Teacher FROM STAFF WHERE (SCHOOLS IS NULL OR strpos(SCHOOLS,',".UserSchool().",')>0) AND SYEAR='".UserSyear()."' AND PROFILE='teacher' and STAFF_ID='".$RET['TEACHER_ID']."' ORDER BY LAST_NAME,FIRST_NAME"));

        $header .= '<TD><b>' . $teachers_RET[1]['TEACHER'] . '</b><br>Teacher</TD>';
        $header .= '<TD><b>' . $RET['ROOM'] . '</b><br>Location</TD>';
        $sql = "SELECT TITLE,START_TIME,END_TIME FROM SCHOOL_PERIODS WHERE PERIOD_ID='".$RET['PERIOD_ID']."'";
        $periods_RET = DBGet(DBQuery($sql));
        $header .= '<TD><b>' . $periods_RET[1]['TITLE'] . '</b><br>Period</TD>';
        $header .= '<TD><b>'. $RET['DAYS'] .'</b><br>Days</td>';
        $header .= '</TR><TR>';
        $mp= $RET['MARKING_PERIOD_ID'];
        $mp_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'2' AS TABLE,SORT_ORDER FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' and marking_period_id='".$mp."' UNION SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'1' AS TABLE,SORT_ORDER FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' and marking_period_id='".$mp."' UNION SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'0' AS TABLE,SORT_ORDER FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' and marking_period_id='".$mp."' ORDER BY 3,4"));


        $header .= '<TD><b>' . $mp_RET[1]['TITLE']. '</b><br>Marking Period</TD>';
        $header .= '<TD><b>' . $RET['TOTAL_SEATS'] . '</b><br>Total Seats</TD>';

        $header .= '<TD><b>' . gr($RET['GENDER_RESTRICTION']).'</b><br>Gender Restriction' . '</TD>';

        if($RET['GRADE_SCALE_ID']!='')
        {
            $sql="SELECT TITLE,ID FROM REPORT_CARD_GRADE_SCALES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' and ID='".$RET['GRADE_SCALE_ID']."'";
            $options_RET = DBGet(DBQuery($sql));
            $header .= '<TD><b>' . $options_RET[1]['TITLE'] . '</b><br>Grading Scale</TD>';
        }
        else
        $header .= '<TD><b>' . 'Not Graded' . '</b><br>Grading Scale</TD>';

        if($RET['CALENDAR_ID']!='')
        {
            $sql = "SELECT TITLE,CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE CALENDAR_ID='".$RET['CALENDAR_ID']."'";

            $options_RET = DBGet(DBQuery($sql));
            $header .= '<TD><b>' . $options_RET[1]['TITLE'].'</b><br>Calendar'. '</TD>';
        }
        else
        $header .= '<TD><b>' . 'No Calendar Selected'.'</b><br>Calendar' . '</TD>';
        $header .= "</TR><TR>";
        $header .= '<TD><b>' . cbr($RET['DOES_ATTENDANCE']) .'</b><br>Takes Attendance' . '</TD>';
        $header .= '<TD><b>' . cbr($RET['DOES_HONOR_ROLL']).'</b><br>Affects Honor Roll' . '</TD>';
        $header .= '<TD><b>' . cbr($RET['DOES_CLASS_RANK']).'</b><br>Affects Class Rank'. '</TD>';
        $header .= '<TD><b>' . cbr($RET['HALF_DAY']).'</b><br>Half Day'. '</TD>';
        $header .= '<TD><b>' . cbr($RET['DOES_BREAKOFF']).'</b><br>Allow Teacher Gradescale' . '</TD>';
        $header .= "</TR><TR>";

        if($RET['PARENT_ID']!='')
        {
            $sql = "SELECT TITLE,COURSE_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$RET['PARENT_ID']."'";
            //echo $sql;
            $children = DBGet(DBQuery($sql));
            if(count($children))
            $header .= '<TD colspan=2><b>' . $children[1]['TITLE'].'</b><br>Parent Course' . '</TD>';
            else
            $header .= '<TD><b>' . 'No Parent Course Selected'.'</b><br>Parent Course'. '</TD>';
            //print_r($children);
        }
        else
        $header .= '<TD><b>' . 'No Parent Course Selected'.'</b><br>Parent Course'. '</TD>';
        $header .= '<TD><b>' . $periods_RET[1]['START_TIME'].'</b><br>Start Time'. '</TD>';
        $header .= '<TD><b>' . $periods_RET[1]['END_TIME'].'</b><br>End Time' . '</TD><td></td>';
        $header .= '</TR>';
        $header .= '</TABLE>';
        DrawHeaderHome($header);
    }
    echo "</div></div></div><div class='tab_footer'>";

}
else
{
    unset($_SESSION['_REQUEST_vars']['subject_id']);unset($_SESSION['_REQUEST_vars']['course_id']);unset($_SESSION['_REQUEST_vars']['course_weight']);unset($_SESSION['_REQUEST_vars']['course_period_id']);

    DrawBC("Courses > ".ProgramTitle());
    if($_REQUEST['print']!='list')
    {
        echo PopTable('header','Quick Search');
    }
    #echo "<FORM id='search' name='search' method=POST action=Modules.php?modname=$_REQUEST[modname]>";
    if($_REQUEST['print']!='list')
    {
        echo "<FORM name=search id=search action=for_export.php?modname=$_REQUEST[modname]&modfunc=print&marking_period_id=".$_REQUEST['marking_period_id']."&_openSIS_PDF=true&report=true&print=list method=POST target=_blank>";
        echo '<table width=100%><tr><td align="center"><INPUT type=submit class=btn_medium value=\'Print\'></td></tr></table>';
        echo '<TABLE align=left cellpadding="5" border="0"><TR>';
        echo '<TD valign=top align=left>';
    }

    $mp_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'2'  FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' UNION SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'1' FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' UNION SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'0' FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY 3,4"));
    unset($options);
    if(count($mp_RET))
    {
        foreach($mp_RET as $key=>$value)
        {
            if($value['MARKING_PERIOD_ID']==$_REQUEST['marking_period_id'])
            $mp_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
        }

        $columns = array('TITLE'=>'Marking Periods');
        $link = array();
        $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]";
        $link['TITLE']['variables'] = array('marking_period_id'=>'MARKING_PERIOD_ID', 'mp_name' => 'SHORT_NAME');
        $link['TITLE']['link'] .= "&modfunc=$_REQUEST[modfunc]";
        if($_REQUEST['print']!='list')
        echo CreateSelect($mp_RET, 'marking_period_id', 'All', 'Select Marking Period: ', 'Modules.php?modname='.$_REQUEST['modname'].'&marking_period_id=');
        echo '</TD>';

        if($_REQUEST['marking_period_id'] && $_REQUEST['marking_period_id']!='')
        {
            $sql = "SELECT subject_id,TITLE FROM COURSE_SUBJECTS WHERE SCHOOL_ID=".UserSchool()." ORDER BY TITLE";
            $QI = DBQuery($sql);
            $subjects_RET = DBGet($QI);

            if(count($subjects_RET))
            {
                if($_REQUEST['subject_id'])
                {
                    foreach($subjects_RET as $key=>$value)
                    {
                        if($value['SUBJECT_ID']==$_REQUEST['subject_id'])
                        $subjects_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
                    }
                }


                echo '<TD valign=top>';
                $columns = array('TITLE'=>'Subject');
                $link = array();
                $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&marking_period_id=$_REQUEST[marking_period_id]&mp_name=$_REQUEST[mp_name]";
                $link['TITLE']['variables'] = array('subject_id'=>'SUBJECT_ID');
                $link['TITLE']['link'] .= "&modfunc=$_REQUEST[modfunc]";
                if($_REQUEST['print']!='list')
                echo CreateSelect($subjects_RET, 'subject_id', 'All', 'Select Subject: ', 'Modules.php?modname='.$_REQUEST['modname'].'&marking_period_id='.$_REQUEST['marking_period_id'].'&subject_id=');
                echo '</TD>';

                //For Courses
                if($_REQUEST['subject_id'] && $_REQUEST['subject_id']!='' )
                {
                    #$sql = "SELECT COURSE_ID,TITLE FROM COURSES WHERE SUBJECT_ID='$_REQUEST[subject_id]' ORDER BY TITLE";
                    $sql = "SELECT COURSE_ID,TITLE FROM COURSES WHERE SUBJECT_ID='$_REQUEST[subject_id]' AND SCHOOL_ID=".UserSchool()." ORDER BY TITLE";
                    $QI = DBQuery($sql);
                    $courses_RET = DBGet($QI);

                    if(count($courses_RET))
                    {
                        if($_REQUEST['course_id'])
                        {
                            foreach($courses_RET as $key=>$value)
                            {
                                if($value['COURSE_ID']==$_REQUEST['course_id'])
                                $courses_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
                            }
                        }
                        echo '<TD valign=top>';
                        $columns = array('TITLE'=>'Course');
                        $link = array();
                        $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&marking_period_id=$_REQUEST[marking_period_id]&mp_name=$_REQUEST[mp_name]&subject_id=$_REQUEST[subject_id]";
                        $link['TITLE']['variables'] = array('course_id'=>'COURSE_ID');
                        $link['TITLE']['link'] .= "&modfunc=$_REQUEST[modfunc]";

                        //ListOutputMod($courses_RET,$columns,'Course','Courses',$link,array(),$LO_options);
                        if($_REQUEST['print']!='list')
                        echo CreateSelect($courses_RET, 'course_id', 'All', 'Select Course: ', 'Modules.php?modname='.$_REQUEST['modname'].'&marking_period_id='.$_REQUEST['marking_period_id'].'&subject_id='.$_REQUEST['subject_id'].'&course_id=');
                        echo '</TD></tr>';

                    }//If subject

                } //if(count($degree_level_RET))
            }
        }
    }
    else
    echo "<tr><td><br><br><b>No Class List Found</b></td>";
    echo '</TR></TABLE></form>';
    echo "<br><br><br>";

    /*echo "<div style='width:700px; text-align:center;' align=center >
    <input type=button class=btn_medium value='Print' onclick='window.open(\"for_export.php?modname=".$_REQUEST['modname']."&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&marking_period_id=$_REQUEST[marking_period_id]&mp_name=$_REQUEST[mp_name]&course_weight=$_REQUEST[course_weight]&draw_header=Class+List&modfunc=choose_course&create_pdf=true&_openSIS_PDF=true\",\"\",\"scrollbars=yes,resizable=yes,width=820,height=700\");'></div>";*/
    if($_REQUEST['print']!='list')
    echo "<div style='width:700px; min-width:700px; padding:0px 20px 20px 20px; float:left; overflow-x:auto; overflow-y:hidden;' >";
    echo CreateList($_REQUEST['degree_level_id'],$_REQUEST['program_level_id'],$_REQUEST['subject_id'], $_REQUEST['course_id'], $_REQUEST['marking_period_id'], $_REQUEST['mp_name']);
    echo "<div style=\"page-break-before: always;\"></div>";
    if($_REQUEST['print']!='list')
    echo "</div>";

    echo PopTable('footer');
}

function CreateList($dli='', $pli='', $sli='', $cli='', $mp='', $mp_name='')
{
    $PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

    if($sli!='')
    $s_ret = DBGet(DBQuery("select title from COURSE_SUBJECTS where subject_id='".$sli."'"));

   if($cli!='')
     $c_ret = DBGet(DBQuery("select title from COURSES where course_id='".$cli."'"));

   if($mp!='')
    {
        $sql = "SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'2'  FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' and marking_period_id='".$mp."' UNION SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'1' FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' and marking_period_id='".$mp."' UNION SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'0'  FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' and marking_period_id='".$mp."' ORDER BY 3,4";
        $mp_ret1 = DBGet(DBQuery($sql));
        $mp_name = $mp_ret1[1]['TITLE'];
    }

    if($mp=='')
    {
        $where = '';
        $heading= "All available classes";
    }
    else
    {
        if($sli==''){
            $where = "and marking_period_id='".$mp."' and course_id in (select course_id from  COURSES where subject_id in (select subject_id from COURSE_SUBJECTS))";
            $heading ="All available classes for <font color='black'>".$mp_name." -> ".$d_ret[1]['TITLE']." -> ".$p_ret[1]['TITLE']."</font>";
        }
        else{
            if($cli=='')
            {
                $where = "and marking_period_id='".$mp."' and course_id in (select Course_Id from COURSES where subject_id = '".$_REQUEST['subject_id']."' and School_Id='".UserSchool()."')";
                #$where = "and marking_period_id='".$mp."' and course_id in (select Course_Id from courses where subject_id = '".$_REQUEST['subject_id']."' and School_Id='".UserSchool()."')";
                $heading ="All available classes for <font color='black'>".$mp_name." -> ".$d_ret[1]['TITLE']." -> ".$p_ret[1]['TITLE']." -> ".$s_ret[1]['TITLE']."</font>";
            }
            else
            {
                $where = "and marking_period_id='".$mp."' and course_id='".$cli."'";
                $heading ="All available classes for <font color='black'>".$mp_name." -> ".$d_ret[1]['TITLE']." -> ".$p_ret[1]['TITLE']." -> ".$s_ret[1]['TITLE']." -> ".$c_ret[1]['TITLE']."</font>";
            }
        }
    }


    /*
	
	$sql = "select
                (select title from COURSES where course_id=COURSE_PERIODS.course_id) as course,
                (select title from COURSE_SUBJECTS where subject_id=(select subject_id from COURSES where 						course_id=COURSE_PERIODS.course_id)) as subject,
                short_name,(select CONCAT(START_TIME,' - ',END_TIME,' ') from SCHOOL_PERIODS where period_id=COURSE_PERIODS.period_id) as period,

                marking_period_id,
                (select CONCAT(LAST_NAME,' ',FIRST_NAME,' ',MIDDLE_NAME,' ') from STAFF where staff_id=COURSE_PERIODS.teacher_id) as teacher,
                room as location,days,course_period_id
                from COURSE_PERIODS where school_id='".UserSchool()."' and syear='".UserSyear()."' ".$where."";
				
				*/
				
	$sql = "select
                (select title from COURSES where course_id=COURSE_PERIODS.course_id) as course,
                (select title from COURSE_SUBJECTS where subject_id=(select subject_id from COURSES where 						course_id=COURSE_PERIODS.course_id)) as subject,
                short_name,(select CONCAT(START_TIME,' - ',END_TIME,' ') from SCHOOL_PERIODS where period_id=COURSE_PERIODS.period_id) as period_time, (select title from SCHOOL_PERIODS where period_id=COURSE_PERIODS.period_id) as period, marking_period_id, (select title from MARKING_PERIODS where marking_period_id=COURSE_PERIODS.marking_period_id) as mp,
                (select CONCAT(LAST_NAME,' ',FIRST_NAME,' ') from STAFF where staff_id=COURSE_PERIODS.teacher_id) as teacher, room as location,days,course_period_id from COURSE_PERIODS where school_id='".UserSchool()."' and syear='".UserSyear()."' ".$where."";


	
	$ret = DBGet(DBQuery($sql));
    $html = "<b>".$heading."</b><br>
        <A HREF=".str_replace('Modules.php', 'for_export.php', $PHP_tmp_SELF)."&create_excel=true&LO_save=1&_openSIS_PDF=true > <IMG SRC=assets/download.png border=0 vspace=0 hspace=0></A>
        <br>";
    $html .= "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
    $html .= "<tr><td  style=\"font-size:15px; font-weight:bold;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Student Schedules Report</div></td><td align=right style=\"padding-top:10px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";

    ########################################List Output Generation ####################################################

    $columns = array('SUBJECT'=>'Subject','COURSE'=>'Course','MP'=>'Marking Period','PERIOD_TIME'=>'Time','PERIOD'=>'Period','DAYS'=>'Days','LOCATION'=>'Location','TEACHER'=>'Teacher');
   if($_REQUEST['print']=='list')
	{
	echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
	echo "<tr><td  style=\"font-size:15px; font-weight:bold;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Course Catalog</div></td><td align=right style=\"padding-top:10px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
	 }
	echo '<style type="text/css">.print-div td{font-size:12px;font-family:arial;}</style><div class="print-div">';
    ListOutputFloat($ret,$columns,'Course','Courses','','',array('search'=>false,'count'=>false));
	echo '</div>';
    echo "<div style=\"page-break-before: always;\"></div>";
    ##########################################################################################################
}


function CreateSelect($val, $name, $opt, $cap, $link)
{
    $html .= $cap;
    $html .= "<select name=".$name." id=".$name." onChange=\"window.location='".$link."' + this.options[this.selectedIndex].value;\">";
    $html .= "<option value=''>".$opt."</option>";

    foreach($val as $key=>$value)
    {
        if($value[strtoupper($name)]==$_REQUEST[$name])
        $html .= "<option selected value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";
        else
        $html .= "<option value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";
    }
    return $html;
}

function CreateExcel($dli='', $pli='', $sli='', $cli='', $mp='', $mp_name='')
{

    if($dli!='')
    $d_ret = DBGet(DBQuery("select title from COURSE_DEGREE_LEVEL where degree_level_id='".$dli."'"));

    if($pli!='')
    $p_ret = DBGet(DBQuery("select title from COURSE_PROG_LEVEL where prog_level_id='".$pli."'"));

    if($sli!='')
    $s_ret = DBGet(DBQuery("select title from COURSE_SUBJECTS where subject_id='".$sli."'"));

    if($cli!='')
    $c_ret = DBGet(DBQuery("select title from COURSES where course_id='".$cli."'"));

    if($mp!='')
    {
        $sql = "SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'2' AS TABLE,SORT_ORDER FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' and marking_period_id='".$mp."' UNION SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'1' AS TABLE,SORT_ORDER FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' and marking_period_id='".$mp."' UNION SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME,'0' AS TABLE,SORT_ORDER FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' and marking_period_id='".$mp."' ORDER BY 3,4";
        //echo $sql;
        $mp_ret1 = DBGet(DBQuery($sql));
        $mp_name = $mp_ret1[1]['TITLE'];
    }

    if($mp=='')
    {
        $where = '';
        #$heading= "All available classes";
    }
    else
    {
        if($dli==''){
            $where = " and marking_period_id='".$mp."'";
            $heading= "All available classes for <font color='black'>".$mp_name."</font>";
        }
        else
        {
            if($pli==''){
                $where = "and marking_period_id='".$mp."' and course_id in (select course_id from  COURSES where subject_id in (select subject_id from COURSE_SUBJECTS where degree_level_id='".$dli."'))";
                $heading ="All available classes for <font color='black'>".$mp_name." -> ".$d_ret[1]['TITLE']."</font>";
            }
            else
            {
                if($sli==''){
                    $where = "and marking_period_id='".$mp."' and course_id in (select course_id from  COURSES where subject_id in (select subject_id from COURSE_SUBJECTS where prog_level_id='".$pli."'))";
                    $heading ="All available classes for <font color='black'>".$mp_name." -> ".$d_ret[1]['TITLE']." -> ".$p_ret[1]['TITLE']."</font>";
                }
                else{
                    if($cli=='')
                    {
                        $where = "and marking_period_id='".$mp."' and course_id in (select Course_Id from COURSES where subject_id = '".$_REQUEST['subject_id']."' and School_Id='".UserSchool()."')";
                        #$where = "and marking_period_id='".$mp."' and course_id in (select course_id from  courses where subject_id = (select subject_id from course_subjects where subject_id='".$sli."'))";
                        $heading ="All available classes for <font color='black'>".$mp_name." -> ".$d_ret[1]['TITLE']." -> ".$p_ret[1]['TITLE']." -> ".$s_ret[1]['TITLE']."</font>";
                    }
                    else
                    {
                        $where = "and marking_period_id='".$mp."' and course_id='".$cli."'";
                        $heading ="All available classes for <font color='black'>".$mp_name." -> ".$d_ret[1]['TITLE']." -> ".$p_ret[1]['TITLE']." -> ".$s_ret[1]['TITLE']." -> ".$c_ret[1]['TITLE']."</font>";

                    }
                }
            }
        }

    }

    $sql = "select
                (select title from courses where course_id=course_periods.course_id) as course,
                (select title from course_subjects where subject_id=(select subject_id from courses where 						course_id=course_periods.course_id)) as subject,
                short_name,
                (select title from school_periods where period_id=course_periods.period_id) as period,
                marking_period_id,
                (select CONCAT(LAST_NAME,' ',FIRST_NAME,' ',MIDDLE_NAME,' ') from staff where staff_id=course_periods.teacher_id) as teacher,
                room as location,days,course_period_id
                from course_periods where school_id='".UserSchool()."' and syear='".UserSyear()."' ".$where."";


    $result = DBGet(DBQuery($sql));


    $column_names = array('SUBJECT'=>'Subject','COURSE'=>'Course','SHORT_NAME'=>'Class Name', 'PERIOD'=>'Period','TEACHER'=>'Teacher', 'LOCATION'=>'Location','DAYS'=>'Days' );

    if(!$options['save_delimiter'] && Preferences('DELIMITER')=='CSV')
    $options['save_delimiter'] = 'comma';

    ob_end_clean();
    if($options['save_delimiter']!='xml')
    {
        foreach($column_names as $key=>$value)
        $output .= str_replace('&nbsp;',' ',eregi_replace('<BR>',' ',ereg_replace('<!--.*-->','',$value))) . ($options['save_delimiter']=='comma'?',':"\t");
        $output .= "\n";
    }

    foreach($result as $item)
    {
        foreach($column_names as $key=>$value)
        {
            if($options['save_delimiter']=='comma' && !$options['save_quotes'])
            $item[$key] = str_replace(',',';',$item[$key]);
            $item[$key] = eregi_replace('<SELECT.*SELECTED\>([^<]+)<.*</SELECT\>','\\1',$item[$key]);
            $item[$key] = eregi_replace('<SELECT.*</SELECT\>','',$item[$key]);
            $output .= ($options['save_quotes']?'"':'') . ($options['save_delimiter']=='xml'?'<'.str_replace(' ','',$value).'>':'') . ereg_replace('<[^>]+>','',ereg_replace("<div onclick='[^']+'>",'',ereg_replace(' +',' ',ereg_replace('&[^;]+;','',str_replace('<BR>&middot;',' : ',str_replace('&nbsp;',' ',$item[$key])))))) . ($options['save_delimiter']=='xml'?'</'.str_replace(' ','',$value).'>'."\n":'') . ($options['save_quotes']?'"':'') . ($options['save_delimiter']=='comma'?',':"\t");
        }
        $output .= "\n";
    }

    header("Cache-Control: public");
    header("Pragma: ");
    header("Content-Type: application/$extension");
    header("Content-Disposition: inline; filename=\"".ProgramTitle().".xls\"\n");
    if($options['save_eval'])
    eval($options['save_eval']);
    echo $output;
    exit();

}

function cbr($val)
{
    if($val=='Y')
    return '<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>';
    else
    return '<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>';

}

function gr($val)
{
    if($val=='M')
    return 'Male';
    elseif($val=='F')
    return 'Female';
    else
    return 'None';
}

?>
