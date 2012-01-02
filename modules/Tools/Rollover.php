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
$next_syear = UserSyear()+1;
$_SESSION['DT'] = $DatabaseType; 
$_SESSION['DS'] = $DatabaseServer; 
$_SESSION['DU'] = $DatabaseUsername; 
$_SESSION['DP'] = $DatabasePassword; 
$_SESSION['DB'] = $DatabaseName; 
$_SESSION['DBP'] = $DatabasePort; 
$_SESSION['NY'] = $next_syear;
echo"<div id='start_date' style='color:red'></div>";
//$table_list = '<TABLE align=center cellpadding="0" cellspacing="0">';
//$table_list .= '<tr><td colspan=3 class=clear></td></tr>';
//$table_list .= '<tr><td colspan=3>Roll over is required at the end of each school year so that you can get your school operational <br>for the next calendar year. This is an irreversible process. Be absolutely sure you want to <br>perform this operation.<br/><br>If you want to continue, follow the three steps outlined below. You must logout and log back in <br>after completing each step.<br><br></td></tr>';
//$table_list .= '</table>';
//
//$table_list .= '<fieldset><legend>Step 1</legend><p align=left>Select the items listed below to roll first. Once completed  log out.</p><TABLE align=center>';
//$i = 1;
//
//foreach($tables as $table=>$name)
//{
//	$exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));
//	echo $exists_RET[$table][1]['COUNT'].'</br>';
//        if($exists_RET[$table][1]['COUNT']>0)
//	{
//		$table_list .= '<TR><td width=1%></td><TD width=5%><INPUT type=checkbox value=Y name=tables['.$table.'] checked disabled></TD><TD width=94%>'.$name.' ('.$exists_RET[$table][1]['COUNT'].')</TD></TR>';
//		if($i=="5")
//		{
//		$table_list .= '</table></fieldset><br/><fieldset><legend>Step 2</legend><p align=left>Log back in to the system. Select the School Year (dropdown menu at the top) from <br>where you want to roll.</p><p align=left>Select the items listed below to roll. Once completed log out.</p><TABLE align=center>';
//		}
//		elseif($i=="10")
//		{
//		$table_list .= '</table></fieldset><br/><fieldset><legend>Step 3</legend><p align=left>Log back in to the system. Select the School Year (dropdown menu at the top) from <br>where you want to roll.</p><p align=left> Select the item listed below to roll.</p><TABLE align=center>';
//		}
//	}
//	else
//	{
//                if($exists_RET['STAFF'][1]['COUNT']>0 && ($table=='COURSES' || $table=='STUDENT_ENROLLMENT' || $table=='ELIGIBILITY_ACTIVITIES' || $table=='ATTENDANCE_CODES' || $table=='STUDENT_ENROLLMENT_CODES')){
//                    $table_list .= '<TR><td width=1%></td><TD width=5%><INPUT type=checkbox value=Y name=tables['.$table.'] CHECKED></TD><TD width=94%>'.$name.'</TD></TR>';
//                }
//                elseif($exists_RET['COURSES'][1]['COUNT']>0 && $table=='REPORT_CARD_COMMENTS'){
//                    $table_list .= '<TR><td width=1%></td><TD width=5%><INPUT type=checkbox value=Y name=tables['.$table.'] CHECKED></TD><TD width=94%>'.$name.'</TD></TR>';
//                }
//                elseif($exists_RET['STAFF'][1]['COUNT']==0 && ($table=='STAFF' || $table=='SCHOOL_PERIODS' || $table=='SCHOOL_YEARS' || $table=='ATTENDANCE_CALENDARS' || $table=='REPORT_CARD_GRADE_SCALES')){
//                   $table_list .= '<TR><td width=1%></td><TD width=5%><INPUT type=checkbox value=Y name=tables['.$table.'] CHECKED></TD><TD width=94%>'.$name.'</TD></TR>';
//                }
//                elseif($exists_RET['STAFF'][1]['COUNT']==0 && ($table=='COURSES' || $table=='STUDENT_ENROLLMENT' || $table=='ELIGIBILITY_ACTIVITIES' || $table=='ATTENDANCE_CODES' || $table=='STUDENT_ENROLLMENT_CODES')){
//                    $table_list .= '<TR><td width=1%></td><TD width=5%><INPUT type=checkbox value=Y name=tables['.$table.'] disabled></TD><TD width=94%>'.$name.'</TD></TR>';
//                }
//                elseif($exists_RET['COURSES'][1]['COUNT']==0 && $exists_RET['STAFF'][1]['COUNT']==0 && $table=='REPORT_CARD_COMMENTS'){
//                    $table_list .= '<TR><td width=1%></td><TD width=5%><INPUT type=checkbox value=Y name=tables['.$table.']  disabled></TD><TD width=94%>'.$name.'</TD></TR>';
//                }
//                elseif($exists_RET['COURSES'][1]['COUNT']==0 && $exists_RET['STAFF'][1]['COUNT']>0 && $table=='REPORT_CARD_COMMENTS'){
//                    $table_list .= '<TR><td width=1%></td><TD width=5%><INPUT type=checkbox value=Y name=tables['.$table.'] disabled></TD><TD width=94%>'.$name.'</TD></TR>';
//                }
//		if($i=="5")
//		{
//		$table_list .= '</table></fieldset><br/><fieldset><legend>Step 2</legend><p align=left>Log back in to the system. Select the School Year (dropdown menu at the top) from <br>where you want to roll.</p><p align=left>Select the items listed below to roll. Once completed log out.</p><TABLE align=center>';
//		}
//		elseif($i=="10")
//		{
//		$table_list .= '</table></fieldset><br/><fieldset><legend>Step 3</legend><p align=left>Log back in to the system. Select the School Year (dropdown menu at the top) from <br>where you want to roll.</p><p align=left> Select the item listed below to roll.</p><TABLE align=center>';
//		}
//	}
//	$i++;
//}
//$table_list .= '</TABLE></CENTER><CENTER>';

//DrawBC("Tools > ".ProgramTitle());
//echo '<div id="calculating" style="display: none" align="center"><img src="assets/ajax-loader.gif" /><br/><br/><strong>Rollover...</strong></div>';
echo '<table width="80%" cellpadding="6" cellspacing="6"><tr><td width="50%" valign="top"><div id="calculating" style="display: none; padding-top:60px;" align="center"><img src="assets/rollover_anim.gif" /><br/><br/><strong>School year rolling over, please wait...</strong></div><div id="response" style="font-size:14px"></div></td>';
$notice_roll_date=DBGet(DBQuery("SELECT SYEAR FROM SCHOOL_YEARS WHERE SYEAR>'".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
$schoolname=DBGet(DBQuery("SELECT TITLE FROM SCHOOLS WHERE ID='".UserSchool()."'"));
$rolled_school_name=$schoolname[1]['TITLE'];
$rolled=count($notice_roll_date);
if($_REQUEST['month_roll_start_date'] == 'JAN')
	$month = 01;
elseif($_REQUEST['month_roll_start_date'] == 'FEB')
	$month = 02;
elseif($_REQUEST['month_roll_start_date'] == 'MAR')
	$month = 03;
elseif($_REQUEST['month_roll_start_date'] == 'APR')
        $month = 04;
elseif($_REQUEST['month_roll_start_date'] == 'MAY')
	$month = 05;
elseif($_REQUEST['month_roll_start_date'] == 'JUN')
	$month = 06;
elseif($_REQUEST['month_roll_start_date'] == 'JUL')
	$month = 07;
elseif($_REQUEST['month_roll_start_date'] == 'AUG')
	$month = 08;
elseif($_REQUEST['month_roll_start_date'] == 'SEP')
	$month = 09;
elseif($_REQUEST['month_roll_start_date'] == 'OCT')
	$month = 10;
elseif($_REQUEST['month_roll_start_date'] == 'NOV')
	$month = 11;
elseif($_REQUEST['month_roll_start_date'] == 'DEC')
	$month = 12;
$_SESSION['roll_start_date']=$_REQUEST['year_roll_start_date']."-".$month."-".$_REQUEST['day_roll_start_date'];
if($rolled==0)
{
if(Prompt_rollover('Confirm Rollover','Are you sure you want to roll the data for '.UserSyear().'-'.(UserSyear()+1).' to the next school year?'))
{
//	if($_REQUEST['tables']['COURSES'] && ((!$_REQUEST['tables']['STAFF'] && $exists_RET['STAFF'][1]['COUNT']<1) || (!$_REQUEST['tables']['SCHOOL_PERIODS'] && $exists_RET['SCHOOL_PERIODS'][1]['COUNT']<1) || (!$_REQUEST['tables']['SCHOOL_YEARS'] && $exists_RET['SCHOOL_YEARS'][1]['COUNT']<1) || (!$_REQUEST['tables']['ATTENDANCE_CALENDARS'] && $exists_RET['ATTENDANCE_CALENDARS'][1]['COUNT']<1) || (!$_REQUEST['tables']['REPORT_CARD_GRADE_SCALES'] && $exists_RET['REPORT_CARD_GRADE_SCALES'][1]['COUNT']<1)))
//		BackPrompt('You must roll users, school periods, marking periods, calendars, and report card codes at the same time or before rolling courses.');
//	if($_REQUEST['tables']['REPORT_CARD_COMMENTS'] && ((!$_REQUEST['tables']['COURSES'] && $exists_RET['COURSES'][1]['COUNT']<1)))
//		BackPrompt('You must roll  courses at the same time or before rolling report card comments.');
    
       echo "<script type='text/javascript'>ajax_rollover('STAFF');</script>";
  
	// --------------------------------------------------------------------------------------------------------------------------------------------------------- //
        		// ---------------------------------------------------------------------- data write start ----------------------------------------------------------------------- //
			$string .= "<"."?php \n";
			$string .= "$"."DatabaseType = '".$_SESSION['DT']."'; \n"	;
			$string .= "$"."DatabaseServer = '".$_SESSION['DS']."'; \n"	;
			$string .= "$"."DatabaseUsername = '".$_SESSION['DU']."'; \n" ;
			$string .= "$"."DatabasePassword = '".$_SESSION['DP']."'; \n";
			$string .= "$"."DatabaseName = '".$_SESSION['DB']."'; \n";
			$string .= "$"."DatabasePort = '".$_SESSION['DBP'] ."'; \n";
			$string .= "#$"."DefaultSyear = '".$_SESSION['NY']."'; \n";
			$string .="?".">";
			$err = "Can't write to file";
			$myFile = "data.php";
			$fh = fopen($myFile, 'w') or exit($err);
			fwrite($fh, $string);
			fclose($fh);
		// ---------------------------------------------------------------------- data write end ------------------------------------------------------------------------ //
}
}
 else {
    Prompt_rollover_back('Rollover Completed','Data has been rolledover for '.UserSyear().'-'.(UserSyear()+1).' for '.$rolled_school_name.'');

}
echo'   
		<td align="left" valign="top">
        <TABLE><TR><TD><div id="STAFF"></div></TD></TR>
        <TR><TD><div id="SCHOOL_PERIODS"></div></TD></TR>
        <TR><TD><div id="SCHOOL_YEARS"></div></TD></TR>
        <TR><TD><div id="ATTENDANCE_CALENDARS"></div></TD></TR>
        <TR><TD><div id="REPORT_CARD_GRADE_SCALES"></div></TD></TR>
        <TR><TD><div id="COURSES"></div></TD></TR>
        <TR><TD><div id="STUDENT_ENROLLMENT"></div></TD></TR>
        <TR><TD><div id="ELIGIBILITY_ACTIVITIES"></div></TD></TR>
        <TR><TD><div id="ATTENDANCE_CODES"></div></TD></TR>
        <TR><TD><div id="STUDENT_ENROLLMENT_CODES"></div></TD></TR>
        <TR><TD><div id="REPORT_CARD_COMMENTS"></div></TD></TR></TABLE>
		<td></tr></table>';
?>