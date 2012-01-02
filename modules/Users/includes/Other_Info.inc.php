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
include_once('modules/Users/includes/functions.php');
$fields_RET = DBGet(DBQuery("SELECT ID,TITLE,TYPE,SELECT_OPTIONS,DEFAULT_SELECTION,REQUIRED FROM STAFF_FIELDS WHERE CATEGORY_ID='$_REQUEST[category_id]' ORDER BY SORT_ORDER,TITLE"));

if(UserStaffID())
{
	$custom_RET = DBGet(DBQuery("SELECT * FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
	$value = $custom_RET[1];
}

//echo '<pre>'; var_dump($fields_RET); echo '</pre>';
if(count($fields_RET))
	echo $separator;
echo '<TABLE cellpadding=5>';
$i = 1;
foreach($fields_RET as $field)
{
	//echo '<pre>'; var_dump($field); echo '</pre>';
	switch($field['TYPE'])
	{
		case 'text':
			if(($i-1)%3==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeTextInput('CUSTOM_'.$field['ID'],$field['TITLE'],'class=cell_floating');
			echo '</TD>';
			if($i%3==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'autos':
			if(($i-1)%3==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeAutoSelectInput('CUSTOM_'.$field['ID'],$field['TITLE']);
			echo '</TD>';
			if($i%3==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'edits':
			if(($i-1)%3==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeAutoSelectInput('CUSTOM_'.$field['ID'],$field['TITLE']);
			echo '</TD>';
			if($i%3==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'numeric':
			if(($i-1)%3==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeTextInput('CUSTOM_'.$field['ID'],$field['TITLE'],'size=5 maxlength=10 class=cell_floating');
			echo '</TD>';
			if($i%3==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'date':
			if(($i-1)%3==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeDateInput('CUSTOM_'.$field['ID'],$field['TITLE']);
			echo '</TD>';
			if($i%3==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'codeds':
		case 'select':
			if(($i-1)%3==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeSelectInput('CUSTOM_'.$field['ID'],$field['TITLE']);
			echo '</TD>';
			if($i%3==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'multiple':
			if(($i-1)%3==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeMultipleInput('CUSTOM_'.$field['ID'],$field['TITLE']);
			echo '</TD>';
			if($i%3==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;

		case 'radio':
			if(($i-1)%3==0)
				echo '<TR>';
			echo '<TD>';
			echo _makeCheckboxInput('CUSTOM_'.$field['ID'],$field['TITLE']);
			echo '</TD>';
			if($i%3==0)
				echo '</TR>';
			else
				echo '<TD width=50></TD>';
			$i++;
			break;
	}
}
if(($i-1)%3!=0)
	echo '</TR>';
echo '</TABLE><BR>';

echo '<TABLE cellpadding=5>';
$i = 1;
foreach($fields_RET as $field)
{
	if($field['TYPE']=='textarea')
	{
		if(($i-1)%2==0)
			echo '<TR>';
		echo '<TD>';
		echo _makeTextareaInput('CUSTOM_'.$field['ID'],$field['TITLE']);
		echo '</TD>';
		if($i%2==0)
			echo '</TR>';
		else
			echo '<TD width=50></TD>';
		$i++;
	}
}
if(($i-1)%2!=0)
	echo '</TR>';
echo '</TABLE>';

?>