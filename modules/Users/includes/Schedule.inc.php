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
include('../../../Redirect_includes.php');
if(GetTeacher(UserStaffID(),'','PROFILE',false)=='teacher')
{
	
	
	#$schedule_RET = DBGet(DBQuery("SELECT cp.PERIOD_ID,cp.ROOM,c.TITLE,cp.COURSE_WEIGHT,cp.MARKING_PERIOD_ID FROM COURSE_PERIODS cp,COURSES c WHERE cp.COURSE_ID=c.COURSE_ID AND cp.TEACHER_ID='".UserStaffID()."' AND cp.SYEAR='".UserSyear()."'"),array('PERIOD_ID'=>'GetPeriod','MARKING_PERIOD_ID'=>'GetMP'));
	
	//$schedule_RET = DBGet(DBQuery("SELECT cp.PERIOD_ID,cp.ROOM,c.TITLE,cp.COURSE_WEIGHT,cp.MARKING_PERIOD_ID, cp.DAYS, CONCAT(sp.START_TIME, ' to ', sp.END_TIME) AS DURATION FROM COURSE_PERIODS cp,COURSES c, SCHOOL_PERIODS sp WHERE cp.COURSE_ID=c.COURSE_ID AND cp.TEACHER_ID='".UserStaffID()."' AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."'"),array('PERIOD_ID'=>'GetPeriod','MARKING_PERIOD_ID'=>'GetMP'));
		$mp_select_RET = DBGet(DBQuery("SELECT DISTINCT cp.MARKING_PERIOD_ID, (SELECT TITLE FROM MARKING_PERIODS WHERE MARKING_PERIOD_ID=cp.MARKING_PERIOD_ID) AS TITLE FROM COURSE_PERIODS cp,COURSES c, SCHOOL_PERIODS sp WHERE cp.COURSE_ID=c.COURSE_ID AND (cp.TEACHER_ID='".UserStaffID()."' OR cp.SECONDARY_TEACHER_ID='".UserStaffID()."') AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID='".UserSchool()."'"));
		$print_mp=CreateSelect($mp_select_RET, 'marking_period_id', 'Show All', 'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&category_id='.$_REQUEST['category_id'].'&marking_period_id=');
 

   
                echo '<div style="padding:10px 0px 0px 25px;"><strong>Marking Periods :</strong> '.$print_mp.'</div>';
  if(!$_REQUEST['marking_period_id'])
  {
		$schedule_RET = DBGet(DBQuery("SELECT cp.PERIOD_ID,cp.ROOM,c.TITLE,cp.COURSE_WEIGHT,cp.MARKING_PERIOD_ID, cp.DAYS, CONCAT(sp.START_TIME, ' to ', sp.END_TIME) AS DURATION, sp.TITLE AS P_NAME FROM COURSE_PERIODS cp,COURSES c, SCHOOL_PERIODS sp WHERE cp.COURSE_ID=c.COURSE_ID AND (cp.TEACHER_ID='".UserStaffID()."' OR cp.SECONDARY_TEACHER_ID='".UserStaffID()."') AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID='".UserSchool()."' ORDER BY P_NAME"),array('PERIOD_ID'=>'GetPeriod','MARKING_PERIOD_ID'=>'GetMP'));
  }
  else if($_REQUEST['marking_period_id'])
  {
		$schedule_RET = DBGet(DBQuery("SELECT cp.PERIOD_ID,cp.ROOM,c.TITLE,cp.COURSE_WEIGHT,cp.MARKING_PERIOD_ID, cp.DAYS, CONCAT(sp.START_TIME, ' to ', sp.END_TIME) AS DURATION, sp.TITLE AS P_NAME FROM COURSE_PERIODS cp,COURSES c, SCHOOL_PERIODS sp WHERE cp.COURSE_ID=c.COURSE_ID AND cp.MARKING_PERIOD_ID=".$_REQUEST['marking_period_id']." AND (cp.TEACHER_ID='".UserStaffID()."' OR cp.SECONDARY_TEACHER_ID='".UserStaffID()."') AND cp.SCHOOL_ID='".UserSchool()."' AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' ORDER BY P_NAME"),array('PERIOD_ID'=>'GetPeriod','MARKING_PERIOD_ID'=>'GetMP'));
  }
                
	ListOutput($schedule_RET,array('TITLE'=>'Course','PERIOD_ID'=>'Period','DAYS'=>'Days','DURATION'=>'Time','ROOM'=>'Room','MARKING_PERIOD_ID'=>'Marking Period'),'Course','Courses');


	#ListOutput($schedule_RET,array('TITLE'=>'Course','PERIOD_ID'=>'Period','ROOM'=>'Room','MARKING_PERIOD_ID'=>'Marking Period'),'Course','Courses');
	#echo '<HR>';
}

$_REQUEST['category_id'] = 2;
include('modules/Users/includes/Other_Info.inc.php');

function CreateSelect($val, $name, $opt, $link='')
	{
	 	//$html .= "<table width=600px><tr><td align=right width=45%>";
		//$html .= $cap." </td><td width=55%>";
		if($link!='')
		$html .= "<select name=".$name." id=".$name." onChange=\"window.location='".$link."' + this.options[this.selectedIndex].value;\">";
		else
		$html .= "<select name=".$name." id=".$name." >";
		$html .= "<option value=''>".$opt."</option>";

				foreach($val as $key=>$value)
				{
					if($value[strtoupper($name)]==$_REQUEST[$name])
						$html .= "<option selected value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";
					else
						$html .= "<option value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";
				}



		$html .= "</select>";
		return $html;
	}
?>