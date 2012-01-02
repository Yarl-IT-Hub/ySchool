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
function MailingLabel($address_id)
{	global $THIS_RET,$_openSIS;

	$student_id = $THIS_RET['STUDENT_ID'];
	if($address_id && !$_openSIS['MailingLabel'][$address_id][$student_id])
	{
		$people_RET = DBGet(DBQuery("SELECT a.ADDRESS_ID,p.PERSON_ID,
			coalesce(a.MAIL_ADDRESS,a.ADDRESS) AS ADDRESS,coalesce(a.MAIL_CITY,a.CITY) AS CITY,coalesce(a.MAIL_STATE,a.STATE) AS STATE,coalesce(a.MAIL_ZIPCODE,a.ZIPCODE) AS ZIPCODE,a.PHONE,
				p.LAST_NAME,p.FIRST_NAME,p.MIDDLE_NAME
			FROM ADDRESS a,PEOPLE p,STUDENTS_JOIN_PEOPLE sjp
			WHERE a.ADDRESS_ID='$address_id' AND a.ADDRESS_ID=sjp.ADDRESS_ID AND p.PERSON_ID=sjp.PERSON_ID
				AND sjp.CUSTODY='Y' AND sjp.STUDENT_ID='".$student_id."'"),array(),array('LAST_NAME'));

		if(count($people_RET))
		{
			foreach($people_RET as $last_name=>$people)
			{
				for($i=1;$i<count($people);$i++)
					$return .= $people[$i]['FIRST_NAME'].' &amp; ';
				$return .= $people[$i]['FIRST_NAME'].' '.$people[$i]['LAST_NAME'].'<BR>';
			}
			// mab - this is a bit of a kludge but insert an html comment so people and address can be split later
			$return .= '<!-- -->'.$people[$i]['ADDRESS'].'<BR>'.$people[$i]['CITY'].', '.$people[$i]['STATE'].' '.$people[$i]['ZIPCODE'];
		}

		$_openSIS['MailingLabel'][$address_id][$student_id] = $return;
	}

	return $_openSIS['MailingLabel'][$address_id][$student_id];
}
?>