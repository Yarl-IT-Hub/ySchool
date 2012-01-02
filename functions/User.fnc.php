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
function User($item)
{	global $_openSIS,$DefaultSyear;

	if(!$_SESSION['UserSyear'])
		$_SESSION['UserSyear'] = $DefaultSyear;

	if(!$_openSIS['User'] || $_SESSION['UserSyear']!=$_openSIS['User'][1]['SYEAR'])
	{
		if($_SESSION['STAFF_ID'])
		{
			$sql = "SELECT STAFF_ID,USERNAME,CONCAT(FIRST_NAME,' ',LAST_NAME) AS NAME,PROFILE,PROFILE_ID,SCHOOLS,CURRENT_SCHOOL_ID,EMAIL,SYEAR FROM STAFF WHERE SYEAR='$_SESSION[UserSyear]' AND USERNAME=(SELECT USERNAME FROM STAFF WHERE STAFF_ID='$_SESSION[STAFF_ID]')";
			$_openSIS['User'] = DBGet(DBQuery($sql));
		}
		elseif($_SESSION['STUDENT_ID'])
		{
			$sql = "SELECT s.USERNAME,CONCAT(s.FIRST_NAME,' ',s.LAST_NAME) AS NAME,'student' AS PROFILE,'0' AS PROFILE_ID,CONCAT(',',se.SCHOOL_ID,',') AS SCHOOLS,se.SYEAR,se.SCHOOL_ID FROM STUDENTS s,STUDENT_ENROLLMENT se WHERE s.STUDENT_ID='$_SESSION[STUDENT_ID]' AND se.SYEAR='$_SESSION[UserSyear]' AND se.STUDENT_ID=s.STUDENT_ID ORDER BY se.END_DATE DESC LIMIT 1";
			$_openSIS['User'] = DBGet(DBQuery($sql));
			$_SESSION['UserSchool'] = $_openSIS['User'][1]['SCHOOL_ID'];
		}
		else
			exit('Error in User()');
	}

	return $_openSIS['User'][1][$item];
}

function Preferences($item,$program='Preferences')
{	global $_openSIS;

	if($_SESSION['STAFF_ID'] && !$_openSIS['Preferences'][$program])
	{
		$QI=DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='$_SESSION[STAFF_ID]' AND PROGRAM='$program'");
		$_openSIS['Preferences'][$program] = DBGet($QI,array(),array('TITLE'));
	}

	$defaults = array('NAME'=>'Common',
				'SORT'=>'Name',
				'SEARCH'=>'Y',
				'DELIMITER'=>'Tab',
				'COLOR'=>'#FFFFCC',
				'HIGHLIGHT'=>'#85E1FF',
				'TITLES'=>'gray',
				'THEME'=>'Brushed-Steel',
				'HIDDEN'=>'Y',
				'MONTH'=>'M',
				'DAY'=>'j',
				'YEAR'=>'Y',
				'DEFAULT_ALL_SCHOOLS'=>'N',
				'ASSIGNMENT_SORTING'=>'ASSIGNMENT_ID',
				'ANOMALOUS_MAX'=>'100'
				);

	if(!isset($_openSIS['Preferences'][$program][$item][1]['VALUE']))
		$_openSIS['Preferences'][$program][$item][1]['VALUE'] = $defaults[$item];

	if($_SESSION['STAFF_ID'] && User('PROFILE')=='parent' || $_SESSION['STUDENT_ID'])
		$_openSIS['Preferences'][$program]['SEARCH'][1]['VALUE'] = 'N';

	return $_openSIS['Preferences'][$program][$item][1]['VALUE'];
}
?>