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

function AllowEdit($modname=false)
{	global $_openSIS;

	if(!$modname)
		$modname = $_REQUEST['modname'];

	if($modname=='Students/Student.php' && $_REQUEST['category_id'])
		$modname = $modname.'&category_id='.$_REQUEST['category_id'];

	if(User('PROFILE')=='admin')
	{
		if(!$_openSIS['AllowEdit'])
		{
			if(User('PROFILE_ID'))
				$_openSIS['AllowEdit'] = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_EDIT='Y'"),array(),array('MODNAME'));
			else
				$_openSIS['AllowEdit'] = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND CAN_EDIT='Y'"),array(),array('MODNAME'));
		}

		if(!$_openSIS['AllowEdit'])
			$_openSIS['AllowEdit'] = array(true);

		if(count($_openSIS['AllowEdit'][$modname]))
			return true;
		else
			return false;
	}
	else
		return $_openSIS['allow_edit'];
}

function AllowUse($modname=false)
{	global $_openSIS;

	if(!$modname)
		$modname = $_REQUEST['modname'];

	if($modname=='Students/Student.php' && $_REQUEST['category_id'])
		$modname = $modname.'&category_id='.$_REQUEST['category_id'];

	if(!$_openSIS['AllowUse'])
	{
		if(User('PROFILE_ID')!='')
			$_openSIS['AllowUse'] = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
		else
			$_openSIS['AllowUse'] = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
	}

	if(!$_openSIS['AllowUse'])
		$_openSIS['AllowUse'] = array(true);

	if(count($_openSIS['AllowUse'][$modname]))
		return true;
	else
		return false;
}

function ProgramLink($modname,$title='',$options='')
{
	if(AllowUse($modname))
		$link = '<A HREF=Modules.php?modname='.$modname.$options.'>';
	if($title)
		$link .= $title;
	if(AllowUse($modname))
		$link .= '</A>';

	return $link;
}

function ProgramLinkforExport($modname,$title='',$options='')
{
	if(AllowUse($modname))
		$link = '<A HREF=for_export.php?modname='.$modname.$options.'>';
	if($title)
		$link .= $title;
	if(AllowUse($modname))
		$link .= '</A>';

	return $link;
}

?>