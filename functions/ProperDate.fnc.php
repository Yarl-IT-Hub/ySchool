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

/*
Outputs a pretty date when sent an oracle or postgres date.
*/

function ProperDate($date='',$length='long')
{
	$months_number['JAN'] = '1';
	$months_number['FEB'] = '2';
	$months_number['MAR'] = '3';
	$months_number['APR'] = '4';
	$months_number['MAY'] = '5';
	$months_number['JUN'] = '6';
	$months_number['JUL'] = '7';
	$months_number['AUG'] = '8';
	$months_number['SEP'] = '9';
	$months_number['OCT'] = '10';
	$months_number['NOV'] = '11';
	$months_number['DEC'] = '12';
		
	if($date && strlen($date)==9)
	{
		$year = substr($date,7);
		$month = $months_number[strtoupper(substr($date,3,3))];
		$day = substr($date,0,2)*1;
		$comment = '<!-- '.(($year<50)?20:19).$year.MonthNWSwitch(substr($date,3,3),'tonum').(substr($date,0,2)).' -->';
	}
	elseif($date)
	{
		$year = substr($date,0,4);
		$month = substr($date,5,2)*1;
		$day = substr($date,8)*1;
		$comment = '<!-- '.$year.substr($date,5,2).(substr($date,8)).' -->';
	}
	
	//if((Preferences('MONTH')=='m' || Preferences('MONTH')=='M') && (Preferences('DAY')=='j' || Preferences('DAY')=='d') && Preferences('YEAR'))
		$sep = '/';
//	else
//		$sep = ' ';
	
	if($date)
		return date((($length=='long' || Preferences('MONTH')!='F')?Preferences('MONTH'):'M').$sep.Preferences('DAY').$sep.Preferences('YEAR'),mktime(0,0,0,$month,$day,$year));
                                    //return date((Preferences('MONTH')).$sep.Preferences('DAY').$sep.Preferences('YEAR'),mktime(0,0,0,$month,$day,$year));
}

function ShortDate($date='',$column='')
{
	return ProperDate($date,'short');
}
?>