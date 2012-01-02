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
	if(UserStudentID())
	{

	$extra['WHERE'] = " AND s.STUDENT_ID ='".UserStudentID()."'";

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
	$columns = array('DAYS'=>'Days','DURATION'=>'Time','PERIOD_TITLE'=>'Period - Teacher','ROOM'=>'Room/Location','MARKING_PERIOD_ID'=>'Term','DAYS'=>'Days','COURSE_TITLE'=>'Course');

	$extra['SELECT'] .= ',c.TITLE AS COURSE_TITLE,p_cp.TITLE AS PERIOD_TITLE,sg.TITLE AS GRD_LVL,sr.MARKING_PERIOD_ID,p_cp.DAYS, CONCAT(sp.START_TIME, " to ", sp.END_TIME) AS DURATION,p_cp.ROOM';
	$extra['FROM'] .= ' LEFT OUTER JOIN SCHEDULE sr ON (sr.STUDENT_ID=ssm.STUDENT_ID),COURSES c, SCHOOL_GRADELEVELS sg, COURSE_PERIODS p_cp,SCHOOL_PERIODS sp ';
	$extra['WHERE'] .= " AND p_cp.PERIOD_ID=sp.PERIOD_ID AND ssm.SYEAR=sr.SYEAR AND sr.COURSE_ID=c.COURSE_ID AND sr.COURSE_PERIOD_ID=p_cp.COURSE_PERIOD_ID AND p_cp.PERIOD_ID=sp.PERIOD_ID AND ssm.GRADE_ID=sg.ID AND ('$date' BETWEEN sr.START_DATE AND sr.END_DATE $date_extra)";
	if($_REQUEST['mp_id'])
		$extra['WHERE'] .= ' AND sr.MARKING_PERIOD_ID IN ('.GetAllMP(GetMPTable(GetMP($_REQUEST['mp_id'],'TABLE')),$_REQUEST['mp_id']).')';

	$extra['functions'] = array('MARKING_PERIOD_ID'=>'GetMP','DAYS'=>'_makeDays');
	$extra['group'] = array('STUDENT_ID');
	$extra['ORDER'] = ',sp.SORT_ORDER';
	if($_REQUEST['mailing_labels']=='Y')
		$extra['group'][] = 'ADDRESS_ID';
	Widgets('mailing_labels');

	$RET_stu = GetStuList($extra);

	$sel_mp = $_REQUEST['sel_mp'];
	$sql_mp_detail = "SELECT title, start_date, end_date, parent_id, grandparent_id from MARKING_PERIODS WHERE marking_period_id = $sel_mp";
	$res_mp_detail = mysql_query($sql_mp_detail);
	$row_mp_detail = mysql_fetch_array($res_mp_detail);

	$mp_string = '(s.marking_period_id='.$sel_mp.'';

	if($row_mp_detail['parent_id'] != -1)
		$mp_string.=' or s.marking_period_id='.$row_mp_detail['parent_id'].'';
	if($row_mp_detail['grandparent_id'] != -1)
		$mp_string.=' or s.marking_period_id='.$row_mp_detail['grandparent_id'].'';
        	# -------------------------- Date Function Start ------------------------------- #

		function cov_date($dt)
		{
			$temp_date = explode("-",$dt);
			$final_date = $temp_date[1].'-'.$temp_date[2].'-'.$temp_date[0];
			return $final_date;
		}
         
		# -------------------------- Date Function End ------------------------------- #
		
	if(count($RET_stu))
	{
		$handle = PDFStart();

		foreach($RET_stu as $student_id=>$courses)
		{
		echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
			echo "<tr><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">".GetSchool(UserSchool())."<div style=\"font-size:12px;\">Student Daily Schedule</div></td><td style=\"font-size:15px; font-weight:bold; padding-top:20px;\"> Schedule for: ". $row_mp_detail['title'] ." : ". cov_date($row_mp_detail['start_date']). " - " . cov_date($row_mp_detail['end_date']) ."</td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=3 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";


		# --------------------------------------- Start Change ------------------------------------------- #


			$st_data = DBGet(DBQuery("SELECT * FROM STUDENTS WHERE student_id = ".$courses[1]['STUDENT_ID']));

				unset($_openSIS['DrawHeader']);
				echo '<br>';
				echo '<table  border=0>';
					/*echo '<tr><td>Student ID:</td>';
					echo '<td>'.$courses[1]['STUDENT_ID'] .'</td></tr>';*/
					echo '<tr><td>Student ID:</td>';
					echo '<td>'.$courses[1]['STUDENT_ID'] .'</td></tr>';
					echo '<tr><td>Student Name:</td>';
					echo '<td><b>'.$courses[1]['FULL_NAME'] .'</b></td></tr>';
					echo '<tr><td>'.$courses[1]['GRD_LVL'] .'</td>';
					echo '<td>'.$st_data[1]['CUSTOM_10'] .'</td></tr>';
				echo '</table>';

                        		$sch_exist= DBGet(DBQuery("SELECT COUNT(s.id) AS SCH_COUNT FROM SCHEDULE s WHERE s.syear=".UserSyear()."
					AND s.student_id='".$courses[1]['STUDENT_ID']."'
					AND s.school_id=".UserSchool()."
					AND $mp_string )"));


					$sch_exist_yn = $sch_exist[1]['SCH_COUNT'];
					if($sch_exist_yn != 0)
					{

			echo '<table style="border-collapse: collapse;" width="100%" align="center" border="1px solid #a9d5e9 " cellpadding="6" cellspacing="1">';
					echo '<tr><td width=15% bgcolor="#d3d3d3"><strong>Days</strong></td>';
					echo '<td bgcolor="#d3d3d3"><strong>Start Time</strong></td>';
					echo '<td bgcolor="#d3d3d3"><strong>End Time</strong></td>';
					echo '<td bgcolor="#d3d3d3"><strong>Period - Teacher</strong></td>';
					echo '<td bgcolor="#d3d3d3"><strong>Marking Period</strong></td>';
					echo '<td bgcolor="#d3d3d3"><strong>Room/Location</strong></td>';
					echo '</tr>';
		$ar=array('Sunday'=>'U','Monday'=>'M','Tuesday'=>'T','Wednesday'=>'W','Thursday'=>'H','Friday'=>'F','Saturday'=>'S');
		foreach($ar as $day=>$value)
		{
			$counter=0;


                        $r_ch= DBGet(DBQuery("SELECT cp.title AS cp_title, cp.short_name, cp.room, sp.start_time, sp.end_time, mp.title,sp.sort_order
			FROM SCHOOL_PERIODS sp, COURSE_PERIODS cp, SCHEDULE s, MARKING_PERIODS mp
			WHERE cp.syear=".UserSyear()."
			AND s.syear=".UserSyear()."
			AND s.student_id='".$courses[1]['STUDENT_ID']."'
			AND s.course_period_id=cp.course_period_id
			AND sp.period_id=cp.period_id
                        AND s.start_date<='".date('Y-m-d')."'
                        AND (s.end_date IS NULL OR s.end_date>='".date('Y-m-d')."')
			AND cp.days like '%".$value."%'
			AND s.school_id=".UserSchool()."
			AND s.marking_period_id=mp.marking_period_id
			AND ". $mp_string.") order by sp.sort_order"));



			$rs=DBQuery("SELECT cp.title AS cp_title, cp.short_name, cp.room, sp.start_time, sp.end_time, mp.title,sp.sort_order
			FROM SCHOOL_PERIODS sp, COURSE_PERIODS cp, SCHEDULE s, MARKING_PERIODS mp
			WHERE cp.syear=".UserSyear()."
			AND s.syear=".UserSyear()."
			AND s.student_id='".$courses[1]['STUDENT_ID']."'
			AND s.course_period_id=cp.course_period_id
			AND sp.period_id=cp.period_id
                        AND s.start_date<='".date('Y-m-d')."'
                        AND (s.end_date IS NULL OR s.end_date>='".date('Y-m-d')."')
			AND cp.days like '%".$value."%'
			AND s.school_id=".UserSchool()."
			AND s.marking_period_id=mp.marking_period_id
			AND ". $mp_string.") order by sp.sort_order");




			$no_record=mysql_num_rows($rs);

			foreach($r_ch as $sch)
			{
			echo "<tr>";
			if($counter==0){

			if($value=='U')
			{
			echo "<td rowspan='".$no_record."'>".$day."</td>";
			}

			elseif($value=='M')
			{
			echo "<td rowspan='".$no_record."'>".$day."</td>";
			}
			elseif($value=='T')
			{
			echo "<td rowspan='".$no_record."'>".$day."</td>";
			}
			elseif($value=='W')
			{
			echo "<td rowspan='".$no_record."'>".$day."</td>";
			}
			elseif($value=='H')
			{
			echo "<td rowspan='".$no_record."'>".$day."</td>";
			}
			elseif($value=='F')
			{
			echo "<td rowspan='".$no_record."'>".$day."</td>";
			}
			elseif($value=='S')
			{
			echo "<td rowspan='".$no_record."'>".$day."</td>";
			}
			}
			echo "<td>".$sch['START_TIME']."</td>";
			echo "<td>".$sch['END_TIME']."</td>";
                        echo "<td>".$sch['CP_TITLE']."</td>";
			echo "<td>".$sch['TITLE']."</td>";
			echo "<td>".$sch['ROOM']."</td></tr>";

			$counter++;
			}


			}
			echo "</table>";


			}
			else
			{
				echo 'No Schedule Found';
			}



				echo '<div style="page-break-before: always;">&nbsp;</div><!-- NEW PAGE -->';


		# --------------------------------------- End Change --------------------------------------------- #






		}
		PDFStop($handle);
	}
	else
		BackPrompt('No Records were found.');
	}

}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Scheduling >> ".ProgramTitle());
        echo "<FORM name=schs id=schs action=Modules.php?modname=$_REQUEST[modname] method=POST >";
	PopTable_wo_header ('header');

		# ---------------------------------------- Marking period selection Start ------------------------------------------ #
                        echo '<TABLE border=0 align=left><tr><td>Please select the Marking Period :</td><TD valign=middle style=padding-top:25px;>';
			echo '</TD><TD valign=middle>';
			$RET1 = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM MARKING_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY MARKING_PERIOD_ID"));
			$link='Modules.php?modname='.$_REQUEST[modname].'&sel_mp=';
                      //  echo "<SELECT name=sel_mp id=sel_mp onChange='this.form.submit();'>";
                        echo "<SELECT name=sel_mp id=sel_mp onChange=\"window.location='".$link."' + this.options[this.selectedIndex].value;\">";
			if(count($RET1))
			{if($_REQUEST['sel_mp'])
                            $mp=$_REQUEST['sel_mp'];
                            else{
                                $mp=UserMP();

                                }
				foreach($RET1 as $quarter)
				{
					echo "<OPTION value=$quarter[MARKING_PERIOD_ID]".($mp===$quarter['MARKING_PERIOD_ID']?' SELECTED':'').">".$quarter['TITLE']."</OPTION>";
				}
			}
			echo "</SELECT></TD></TR></TABLE>";
//echo "</FORM>";
###################################################################################################################
                       
 $sql="SELECT CONCAT(s.LAST_NAME,', ',coalesce(s.COMMON_NAME,s.FIRST_NAME)) AS FULL_NAME,s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,s.ALT_ID,ssm.SCHOOL_ID,ssm.GRADE_ID ,c.TITLE AS COURSE_TITLE,p_cp.TITLE AS PERIOD_TITLE,sr.MARKING_PERIOD_ID,p_cp.DAYS, CONCAT(sp.START_TIME, ' to ', sp.END_TIME) AS DURATION,p_cp.ROOM FROM STUDENTS s,STUDENT_ENROLLMENT ssm LEFT OUTER JOIN SCHEDULE sr ON (sr.STUDENT_ID=ssm.STUDENT_ID),COURSES c,COURSE_PERIODS p_cp,SCHOOL_PERIODS sp WHERE ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ssm.SCHOOL_ID='".UserSchool()."' AND ('".DBDate()."' BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND '".DBDate()."'>ssm.START_DATE)) AND ssm.STUDENT_ID='".UserStudentID()."' AND s.STUDENT_ID = '".UserStudentID()."' AND p_cp.PERIOD_ID=sp.PERIOD_ID AND ssm.SYEAR=sr.SYEAR AND sr.COURSE_ID=c.COURSE_ID AND sr.COURSE_PERIOD_ID=p_cp.COURSE_PERIOD_ID AND p_cp.PERIOD_ID=sp.PERIOD_ID AND ('".DBDate()."' BETWEEN sr.START_DATE AND sr.END_DATE OR sr.END_DATE IS NULL) ORDER BY FULL_NAME,sp.SORT_ORDER";
echo "<br>";echo "<br>";
$stu_id=UserStudentID();
$RET_show[$stu_id]=DBGet(DBQuery($sql));
$date=date(Y."-".m."-".d);
 if(!$_REQUEST['sel_mp'])
 {  $sel_mp =GetCurrentMP('QTR',$date);
            if(!$sel_mp)
            {
                 $sel_mp =GetCurrentMP('SEM',$date);
                 if(!$sel_mp)
            {
                 $sel_mp =GetCurrentMP('FY',$date);
            }
            }
 }
 else
       $sel_mp =$_REQUEST['sel_mp'];
	 $sql_mp_detail = "SELECT title, start_date, end_date, parent_id, grandparent_id from MARKING_PERIODS WHERE marking_period_id = $sel_mp";
	$res_mp_detail = mysql_query($sql_mp_detail);
	$row_mp_detail = mysql_fetch_array($res_mp_detail);

	$mp_string = '(s.marking_period_id='.$sel_mp.'';
if($row_mp_detail['parent_id'] != -1)
		$mp_string.=' or s.marking_period_id='.$row_mp_detail['parent_id'].'';
	if($row_mp_detail['grandparent_id'] != -1)
		$mp_string.=' or s.marking_period_id='.$row_mp_detail['grandparent_id'].'';
      
           
if(count($RET_show))
	{
        foreach($RET_show as $student_id=>$courses)
		{
		
					$sch_exist= DBGet(DBQuery("SELECT COUNT(s.id) AS SCH_COUNT FROM SCHEDULE s WHERE s.syear=".UserSyear()."
					AND s.student_id='".$courses[1]['STUDENT_ID']."'
					AND s.school_id=".UserSchool()."
					AND $mp_string )"));


					$sch_exist_yn = $sch_exist[1]['SCH_COUNT'];
					if($sch_exist_yn != 0)
					{

			echo '<table class="grid" width="100%" align="center" cellpadding="6" cellspacing="1">';
					echo '<tr><td width=15% class="column_heading"><strong>Days</strong></td>';

					echo '<td class="column_heading"><strong>Start Time</strong></td>';
					echo '<td class="column_heading"><strong>End Time</strong></td>';
					echo '<td class="column_heading"><strong>Period - Teacher</strong></td>';
					echo '<td class="column_heading"><strong>Marking Period</strong></td>';
					echo '<td class="column_heading"><strong>Room/Location</strong></td>';
					echo '</tr>';
		$ar=array('Sunday'=>'U','Monday'=>'M','Tuesday'=>'T','Wednesday'=>'W','Thursday'=>'H','Friday'=>'F','Saturday'=>'S');
		foreach($ar as $day=>$value)
		{
			$counter=0;

                        $r_ch= DBGet(DBQuery("SELECT cp.title AS cp_title, cp.short_name, cp.room, sp.start_time, sp.end_time, mp.title,sp.sort_order
			FROM SCHOOL_PERIODS sp, COURSE_PERIODS cp, SCHEDULE s, MARKING_PERIODS mp
			WHERE cp.syear=".UserSyear()."
			AND s.syear=".UserSyear()."
			AND s.student_id='".$courses[1]['STUDENT_ID']."'
			AND s.course_period_id=cp.course_period_id
			AND sp.period_id=cp.period_id
                        AND s.start_date<='".date('Y-m-d')."'
                        AND (s.end_date IS NULL OR s.end_date>='".date('Y-m-d')."')
			AND cp.days like '%".$value."%'
			AND s.school_id=".UserSchool()."
			AND s.marking_period_id=mp.marking_period_id
			AND ". $mp_string.") order by sp.sort_order"));



			$rs=DBQuery("SELECT cp.title AS cp_title, cp.short_name, cp.room, sp.start_time, sp.end_time, mp.title,sp.sort_order
			FROM SCHOOL_PERIODS sp, COURSE_PERIODS cp, SCHEDULE s, MARKING_PERIODS mp
			WHERE cp.syear=".UserSyear()."
			AND s.syear=".UserSyear()."
			AND s.student_id='".$courses[1]['STUDENT_ID']."'
			AND s.course_period_id=cp.course_period_id
			AND sp.period_id=cp.period_id
                        AND s.start_date<='".date('Y-m-d')."'
                        AND (s.end_date IS NULL OR s.end_date>='".date('Y-m-d')."')
			AND cp.days like '%".$value."%'
			AND s.school_id=".UserSchool()."
			AND s.marking_period_id=mp.marking_period_id
			AND ". $mp_string.") order by sp.sort_order");



			$no_record=mysql_num_rows($rs);

			foreach($r_ch as $sch)
			{
			echo '<tr class="even">';
			if($counter==0){

			if($value=='U')
			{
			echo "<td  class='even' rowspan='".$no_record."'>".$day."</td>";
			}

			elseif($value=='M')
			{
			echo "<td rowspan='".$no_record."'>".$day."</td>";
			}
			elseif($value=='T')
			{
			echo "<td rowspan='".$no_record."'>".$day."</td>";
			}
			elseif($value=='W')
			{
			echo "<td rowspan='".$no_record."'>".$day."</td>";
			}
			elseif($value=='H')
			{
			echo "<td rowspan='".$no_record."'>".$day."</td>";
			}
			elseif($value=='F')
			{
			echo "<td rowspan='".$no_record."'>".$day."</td>";
			}
			elseif($value=='S')
			{
			echo "<td rowspan='".$no_record."'>".$day."</td>";
			}
			}    //$$counter if ends
			echo "<td>".$sch['START_TIME']."</td>";
			echo "<td>".$sch['END_TIME']."</td>";
                        echo "<td>".$sch['CP_TITLE']."</td>";
			echo "<td>".$sch['TITLE']."</td>";
			echo "<td>".$sch['ROOM']."</td></tr>";
			//echo "<tr><td clospan='4'></td></tr>";
			$counter++;
			}  //inner foreach end


			}//outer foreach end

			echo "</table>";


			}
			else
			{
				$error= 'No Schedule Found';
			}

                }

                if($error){
                   
                    echo $error;}
                }

                else
                {
                    BackPrompt('No Students were found.');
                }
#############################################################################################
                        PopTable ('footer');
                        echo "</FORM>";
               echo "<FORM name=sch id=sch action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_openSIS_PDF=true method=POST target=_blank>";
               echo "<input type=hidden name=sel_mp value=$sel_mp>";
		echo '<BR><CENTER><INPUT type=submit class=btn_medium value=\'Print\'></CENTER>';
		echo "</FORM>";


}
function _makeDays($value,$column)
{
	foreach(array('U','M','T','W','H','F','S') as $day)
	{
		foreach(array('U') as $day)
		{
			if(strpos($value,$day)!==false)
				$return = 'Sunday';
			}
		foreach(array('M') as $day)
		{
			if(strpos($value,$day)!==false)
				$return1 = 'Monday';
			}

		foreach(array('T') as $day)
		{
			if(strpos($value,$day)!==false)
				$return2 = 'Tuesday';
			}
		foreach(array('F') as $day)
		{
			if(strpos($value,$day)!==false)
				$return3 = 'FridaY';
		}
		return $return.$return1.$return2.$return3 ;
	}
}
function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}
?>
