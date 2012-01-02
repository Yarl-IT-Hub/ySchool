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
Widgets('request');
Widgets('mailing_labels');
$extra['force_search'] = true;
if(!$_REQUEST['search_modfunc'] || $_openSIS['modules_search'])
{
	DrawBC("Scheduling > ".ProgramTitle());

	$extra['new'] = true;
	$extra['action'] .= "&_openSIS_PDF=true";
	$extra['pdf'] = true;
	Search('student_id',$extra);
}
else
{
	$columns = array('COURSE_TITLE'=>'Course','MARKING_PERIOD_ID'=>'Marking Period','WITH_TEACHER_ID'=>'With Teacher','WITH_PERIOD_ID'=>'In Period','NOT_TEACHER_ID'=>'Not with Teacher','NOT_PERIOD_ID'=>'Not in Period');
	$extra['SELECT'] .= ',c.TITLE AS COURSE_TITLE,srp.PRIORITY,srp.MARKING_PERIOD_ID,srp.WITH_TEACHER_ID,srp.NOT_TEACHER_ID,srp.WITH_PERIOD_ID,srp.NOT_PERIOD_ID';
	$extra['FROM'] .= ',COURSES c,SCHEDULE_REQUESTS srp';
	$extra['WHERE'] .= ' AND ssm.STUDENT_ID=srp.STUDENT_ID AND ssm.SYEAR=srp.SYEAR AND srp.COURSE_ID = c.COURSE_ID';
	
	$extra['functions'] += array('WITH_FULL_NAME'=>'_makeExtra');
	$extra['group'] = array('STUDENT_ID');
	if($_REQUEST['mailing_labels']=='Y')
		$extra['group'][] = 'ADDRESS_ID';	
	
	$RET = GetStuList($extra);

	if(count($RET))
	{
		$__DBINC_NO_SQLSHOW = true;
		$handle = PDFStart();
        	echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
			echo "<tr><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Student Print Request</div></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br \>Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
		foreach($RET as $student_id=>$courses)
		{
			if($_REQUEST['mailing_labels']=='Y')
			{
				foreach($courses as $address)
				{
					//echo '<BR><BR><BR>';
					unset($_openSIS['DrawHeader']);
					//DrawHeader(Config('TITLE').' Student Requests');
					//DrawHeader($address[1]['FULL_NAME'],$address[1]['STUDENT_ID']);
					//DrawHeader($address[1]['GRADE_ID']);
					//DrawHeader(GetSchool(UserSchool()));
					//DrawHeader(ProperDate(DBDate()));
					echo "</table >";
			echo '<BR><BR>';
    		echo '<table border=0>';
    		echo "<tr><td>Student ID:</td>";
			echo "<td>".$address[1]['STUDENT_ID']."</td></tr>";
    		echo "<tr><td>Student Name:</td>";
			echo "<td>".$address[1]['FULL_NAME']."</td></tr>";
			echo "<tr><td>Student Grade:</td>";
			echo "<td>".$address[1]['GRADE_ID']."</td></tr>";
			if($address[1]['MAILING_LABEL'] !='')
			{
			echo "<tr><td>Student Mailling Label :</td>";
			echo "<td> ".$address[1]['MAILING_LABEL']."</td></tr>";
			}
			echo'</table>';
				//echo '<BR><BR><TABLE width=100%><TR><TD width=50> &nbsp; </TD><TD>'.$address[1]['MAILING_LABEL'].'</TD></TR></TABLE><BR>';
					ListOutputPrint($address,$columns,'Request','Requests',array(),array(),array('center'=>false,'print'=>false));
					echo '<!-- NEW PAGE -->';				
				}
			}
			else
			{
				unset($_openSIS['DrawHeader']);
				//DrawHeader(Config('TITLE').' Student Requests');
				//DrawHeader($courses[1]['FULL_NAME'],$courses[1]['STUDENT_ID']);
				//DrawHeader($courses[1]['GRADE_ID']);
				//DrawHeader(GetSchool(UserSchool()));
				//DrawHeader(ProperDate(DBDate()));
				
			
			echo "</table >";
			echo '<BR><BR>';
    		echo '<table border=0>';
    		echo "<tr><td>Student ID:</td>";
			echo "<td>".$courses[1]['STUDENT_ID']."</td></tr>";
    		echo "<tr><td>Student Name:</td>";
			echo "<td>".$courses[1]['FULL_NAME']."</td></tr>";
			echo "<tr><td>Student Grade:</td>";
			echo "<td>".$courses[1]['GRADE_ID']."</td></tr>";
			if($address[1]['MAILING_LABEL'] !='')
			{
			echo "<tr><td>Student Mailling Label :</td>";
			echo "<td> ".$courses[1]['MAILING_LABEL']."</td></tr>";
			}
			echo'</table>';
			include("classes/db/db.mysqli.class.php");
			foreach($courses as $key=>$value){
				// set MARKING_PERIOD_ID
				if($courses[$key]['WITH_TEACHER_ID']){
				$stmt = $mysqli->prepare("select title from MARKING_PERIODS
									  where marking_period_id=? limit 1");
				$stmt->bind_param("i",$marking_period_id); //binding name as string
				$marking_period_id = $courses[$key]['MARKING_PERIOD_ID'];
				$stmt->execute();
				$title='';
				$stmt->bind_result($title);
				$stmt->fetch();
				$courses[$key]['MARKING_PERIOD_ID']=$title;
				unset($stmt);
				}
				// set WITH_TEACHER_ID
				if($courses[$key]['WITH_TEACHER_ID']){
				$stmt = $mysqli->prepare("select CONCAT(first_name,' ',last_name) as title from STAFF where staff_id=? limit 1");
				$stmt->bind_param("i",$staff_id); //binding name as string
				$staff_id = $courses[$key]['WITH_TEACHER_ID'];
				$stmt->execute();
				$title='';
				$stmt->bind_result($title);
				$stmt->fetch();
				$courses[$key]['WITH_TEACHER_ID']=$title;
				unset($stmt);
				}
				// set NOT_TEACHER_ID
				if($courses[$key]['NOT_TEACHER_ID']){
				$stmt = $mysqli->prepare("select CONCAT(first_name,' ',last_name) as title from STAFF where staff_id=? limit 1");
				$stmt->bind_param("i",$staff_id); //binding name as string
				$staff_id = $courses[$key]['NOT_TEACHER_ID'];
				$stmt->execute();
				$title='';
				$stmt->bind_result($title);
				$stmt->fetch();
				$courses[$key]['NOT_TEACHER_ID']=$title;
				unset($stmt);
				}
				// set WITH_PERIOD_ID
				if($courses[$key]['WITH_PERIOD_ID']){
				$stmt = $mysqli->prepare("select title from SCHOOL_PERIODS where period_id=? limit 1");
				$stmt->bind_param("i",$period_id); //binding name as string
				$period_id = $courses[$key]['WITH_PERIOD_ID'];
				$stmt->execute();
				$title='';
				$stmt->bind_result($title);
				$stmt->fetch();
				$courses[$key]['WITH_PERIOD_ID']=$title;
				unset($stmt);
				}
				// set NOT_PERIOD_ID
				if($courses[$key]['NOT_PERIOD_ID']){
				$stmt = $mysqli->prepare("select title from SCHOOL_PERIODS where period_id=? limit 1");
				$stmt->bind_param("i",$period_id); //binding name as string
				$period_id = $courses[$key]['NOT_PERIOD_ID'];
				$stmt->execute();
				$title='';
				$stmt->bind_result($title);
				$stmt->fetch();
				$courses[$key]['NOT_PERIOD_ID']=$title;
				unset($stmt);
				}
			}
				/*echo '<pre>';print_r($courses);echo '</pre>';*/
				ListOutputPrint($courses,$columns,'Request','Requests',array(),array(),array('center'=>false,'print'=>false));
				echo '<!-- NEW PAGE -->';
			}
		}
		PDFStop($handle);
	}
	else
		BackPrompt('No Students were found.');
}

function _makeExtra($value,$title='')
{	global $THIS_RET;

	if($THIS_RET['WITH_TEACHER_ID'])
		$return .= 'With:&nbsp;'.GetTeacher($THIS_RET['WITH_TEACHER_ID']).'<BR>';
	if($THIS_RET['NOT_TEACHER_ID'])
		$return .= 'Not With:&nbsp;'.GetTeacher($THIS_RET['NOT_TEACHER_ID']).'<BR>';
	if($THIS_RET['WITH_PERIOD_ID'])
		$return .= 'On:&nbsp;'.GetPeriod($THIS_RET['WITH_PERIOD_ID']).'<BR>';
	if($THIS_RET['NOT_PERIOD_ID'])
		$return .= 'Not On:&nbsp;'.GetPeriod($THIS_RET['NOT_PERIOD_ID']).'<BR>';
	if($THIS_RET['PRIORITY'])
		$return .= 'Priority:&nbsp;'.$THIS_RET['PRIORITY'].'<BR>';
	if($THIS_RET['MARKING_PERIOD_ID'])
		$return .= 'Marking Period:&nbsp;'.GetMP($THIS_RET['MARKING_PERIOD_ID']).'<BR>';

	return $return;
}

?>