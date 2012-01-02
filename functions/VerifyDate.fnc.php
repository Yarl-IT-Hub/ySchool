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

function VerifyDate($date)
{

/*
	if(strlen($date)==9) // ORACLE
	{
		$day = substr($date,0,2)*1;
		$month = MonthNWSwitch(substr($date,3,3),'tonum')*1;
		$year = substr($date,7,2);
		$year = (($year<50)?20:19) . $year;
	}
	elseif(strlen($date)==10) // POSTGRES
	{
		$day = substr($date,8,2)*1;
		$month = substr($date,5,2)*1;
		$year = substr($date,0,4);
	}
	else
		return false;
*/
	$vdate = explode("-", $date);
	if(count($vdate))
	{
		$day = $vdate[0];
		$month = MonthNWSwitch($vdate[1],'tonum');
		$year = $vdate[2];
	}
	else
	return false;
	
	return checkdate($month,$day,$year);
}
?>