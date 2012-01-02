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

# CheckboxInput($this_address['BUS_PICKUP'],'values[ADDRESS][BUS_PICKUP]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>')
include('../../../Redirect_includes.php');
echo '<TABLE width=100% border=0 cellpadding=6>';
echo '<TR>';
// IMAGE
if(clean_param($_REQUEST['staff_id'],PARAM_ALPHANUM)!='new' && $UserPicturesPath && (($file = @fopen($picture_path=$UserPicturesPath.UserSyear().'/'.UserStaffID().'.JPG','r')) || $staff['ROLLOVER_ID'] && ($file = @fopen($picture_path=$UserPicturesPath.(UserSyear()-1).'/'.$staff['ROLLOVER_ID'].'.JPG','r'))))
{
	fclose($file);
	echo '<TD width=150><IMG SRC="'.$picture_path.'" width=150></TD><TD valign=top>';
}
else
	echo '<TD colspan=2>';

echo '<TABLE width=100% cellpadding=5><TR>';

echo '<TD>';
if(clean_param($_REQUEST['staff_id'],PARAM_ALPHA)=='new')
	echo '<TABLE><TR><TD>'.SelectInput($staff['TITLE'],'staff[TITLE]','Title',array('Mr.'=>'Mr.','Mrs.'=>'Mrs.','Ms.'=>'Ms.','Miss'=>'Miss', 'Dr'=>'Dr', 'Rev'=>'Rev'),'').'</TD><TD>'.TextInput($staff['FIRST_NAME'],'staff[FIRST_NAME]','<FONT class=red>First</FONT>','maxlength=50 class=cell_floating').'</TD><TD>'.TextInput($staff['MIDDLE_NAME'],'staff[MIDDLE_NAME]','Middle','maxlength=50 class=cell_floating').'</TD><TD>'.TextInput($staff['LAST_NAME'],'staff[LAST_NAME]','<FONT color=red>Last</FONT>','maxlength=50 class=cell_floating').'</TD></TR></TABLE>';
	else
		echo '<DIV id=user_name><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',SelectInput($staff['TITLE'],'staff[TITLE]','Title',array('Mr.'=>'Mr.','Mrs.'=>'Mrs.','Ms.'=>'Ms.','Miss'=>'Miss', 'Dr'=>'Dr', 'Rev'=>'Rev'),'','',false)).'</TD><TD>'.str_replace('"','\"',TextInput($staff['FIRST_NAME'],'staff[FIRST_NAME]',(!$staff['FIRST_NAME']?'<FONT color=red>':'').'First'.(!$staff['FIRST_NAME']?'</FONT>':''),'maxlength=50',false)).'</TD><TD>'.str_replace('"','\"',TextInput($staff['MIDDLE_NAME'],'staff[MIDDLE_NAME]','Middle','size=3 maxlength=50',false)).'</TD><TD>'.str_replace('"','\"',TextInput($staff['LAST_NAME'],'staff[LAST_NAME]',(!$staff['LAST_NAME']?'<FONT color=red>':'').'Last'.(!$staff['LAST_NAME']?'</FONT>':''),'maxlength=50',false)).'</TD></TR></TABLE>","user_name",true);\'>'.(!$staff['TITLE']&&!$staff['FIRST_NAME']&&!$staff['MIDDLE_NAME']&&!$staff['LAST_NAME']?'-':$staff['TITLE'].' '.$staff['FIRST_NAME'].' '.$staff['MIDDLE_NAME'].' '.$staff['LAST_NAME']).'</div></DIV><small>'.(!$staff['FIRST_NAME']||!$staff['LAST_NAME']?'<FONT color=red>':'<FONT color='.Preferences('TITLES').'>').'Name</FONT></small>';
echo '</TD>';

echo '<TD colspan=1>';
echo NoInput($staff['STAFF_ID'],'Staff ID');;
echo '</TD>';

echo '<TD colspan=1>';
echo NoInput($staff['ROLLOVER_ID'],'Last Year Staff ID');;
echo '</TD>';

echo '</TR><TR>';

echo '<TD>';
echo TextInput($staff['USERNAME'],'staff[USERNAME]','Username','size=12 maxlength=100 class=cell_floating  onkeyup="usercheck_init(this)"');
echo '<br><div id="ajax_output"></div>';
echo '</TD>';

echo '<TD>';

echo TextInput(array($staff['PASSWORD'],str_repeat('*',strlen($staff['PASSWORD']))),'staff[PASSWORD]','Password','size=12 maxlength=100 class=cell_floating AUTOCOMPLETE = off');
echo '</TD>';

echo '<TD>';
echo NoInput(ProperDate(substr($staff['LAST_LOGIN'],0,10)).substr($staff['LAST_LOGIN'],10),'Last Login');
echo '</TD>';

echo '</TR>';

echo '<TR>';
echo '<TD colspan=3>';
echo CheckboxInput($staff['IS_DISABLE'],'staff[IS_DISABLE]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'Disable User';
echo '</TD>';
echo '</TR>';
echo '</TABLE>';
echo '</TD></TR></TABLE>';
echo '<div class=break></div>';
echo '<TABLE border=0 cellpadding=6 width=100%>';
if(basename($_SERVER['PHP_SELF'])!='index.php')
{
	echo '<TR>';
	echo '<TD>';
	echo '<TABLE><TR><TD>';
	unset($options);
		$profiles_options = DBGet(DBQuery("SELECT PROFILE ,TITLE, ID FROM USER_PROFILES WHERE ID <> 0 ORDER BY ID"));
		$i = 1;
		foreach($profiles_options as $options)
		{
			
			$option[$options['ID']] = $options['TITLE'];
			$i++;
		}
			
	echo SelectInput($staff['PROFILE_ID'],'staff[PROFILE]',(!$staff['PROFILE']?'<FONT color=red>':'').'User Profile'.(!$staff['PROFILE']?'</FONT>':''),$option);
	

	echo '</TD></TR><TR><TD>';
	unset($profiles);
	if(clean_param($_REQUEST['staff_id'],PARAM_ALPHANUM)!='new')
	{
		$profiles_RET = DBGet(DBQuery("SELECT ID,TITLE FROM USER_PROFILES WHERE TITLE='$staff[PROFILE]' AND ID <>0 ORDER BY ID"));
		foreach($profiles_RET as $profile)
			$profiles[$profile['ID']] = 'Default';
		$na = 'Custom';
	}
	else
		$na = 'Default';
	echo SelectInput($staff['PROFILE_ID'],'staff[Permissions]','Permissions',$profiles,$na);
	echo '</TD></TR></TABLE>';
	echo '</TD>';
                  $schools = substr(str_replace(",","','",User('SCHOOLS')),2,-2);
	echo '<TD>';
	$sql = "SELECT ID,TITLE FROM SCHOOLS WHERE ID IN($schools)";
	$QI = DBQuery($sql);
	$schools_RET = DBGet($QI);
	unset($options);
	if(count($schools_RET) && User('PROFILE')=='admin')
	{
		$i = 0;
		$staff_school_chkbox_id=0;
		echo '<TABLE><TR>';
		
		foreach($schools_RET as $value)
		{
			$staff_school_chkbox_id++;
			if($i%3==0)
				echo '</TR><TR>';
			echo '<TD>'.CheckboxInputWithID(((strpos($staff['SCHOOLS'],','.$value['ID'].',')!==false)?'Y':''),'staff[SCHOOLS]['.$value['ID'].']','staff_SCHOOLS'.$staff_school_chkbox_id,'','',true,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').$value['TITLE'].'</TD>';
			$i++;
			
		}
		
		echo '</TR></TABLE>';
		
                if(!$staff['PROFILE']) echo '<FONT color=red>Schools</FONT>';
                else echo '<FONT color='.Preferences('TITLES').'>Schools</FONT>';
	}
	elseif(User('PROFILE')!='admin')
	{
		$i = 0;
		echo '<TABLE><TR><TD>Schools : </TD>';
		foreach($schools_RET as $value)
		{
			if($i%3==0)
				echo '</TR><TR>';
			if(strpos($staff['SCHOOLS'],','.$value['ID'].',')!==false)
			echo '<TD align = center>'.$value['TITLE'].'</TD><TD>&nbsp;</TD>';
			$i++;
		}
		echo '</TR></TABLE>';
	}
	
	echo '</TD><TD>';
	echo '</TD>';
	echo '</TR>';
}
echo '<TR>';
echo '<TD>';
echo TextInput($staff['EMAIL'],'staff[EMAIL]','Email Address','size=12 maxlength=100 class=cell_floating');
echo '</TD>';
echo '<TD>';
echo TextInput($staff['PHONE'],'staff[PHONE]','Phone Number','size=12 maxlength=100 class=cell_floating');
echo '</TD>';
echo '</TR>';
echo '</TABLE>';

$_REQUEST['category_id'] = 1;
include('modules/Users/includes/Other_Info.inc.php');
?>