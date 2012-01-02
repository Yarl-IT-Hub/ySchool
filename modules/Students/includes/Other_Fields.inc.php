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
include_once('modules/Students/includes/functions.php');

//echo '<pre>'; var_dump($fields_RET); echo '</pre>';
echo '<TABLE cellpadding=5 width=100%>';
foreach($fields_RET as $field)
{
	//echo '<pre>'; var_dump($field); echo '</pre>';
	switch($field['TYPE'])
	{
		case 'text':
			echo '<TR><TD>';
			echo _makeTextInput('CUSTOM_'.$field['ID'],$field['TITLE'],'',$request);
			echo '</TD></TR>';
			break;

		case 'autos':
			echo '<TR><TD>';
			echo _makeAutoSelectInput('CUSTOM_'.$field['ID'],$field['TITLE'],$request);
			echo '</TD></TR>';
			break;

		case 'edits':
			echo '<TR><TD>';
			echo _makeAutoSelectInput('CUSTOM_'.$field['ID'],$field['TITLE'],$request);
			echo '</TD></TR>';
			break;

		case 'numeric':
			echo '<TR><TD>';
			echo _makeTextInput('CUSTOM_'.$field['ID'],$field['TITLE'],'size=5 maxlength=10',$request);
			echo '</TD></TR>';
			break;

		case 'date':
			echo '<TR><TD>';
			echo _makeDateInput('CUSTOM_'.$field['ID'],$field['TITLE'],$request);
			echo '</TD></TR>';
			break;

		case 'codeds':
		case 'select':
			echo '<TR><TD>';
			echo _makeSelectInput('CUSTOM_'.$field['ID'],$field['TITLE'],$request);
			echo '</TD></TR>';
			break;

		case 'multiple':
			echo '<TR><TD>';
			echo _makeMultipleInput('CUSTOM_'.$field['ID'],$field['TITLE'],$request);
			echo '</TD></TR>';
			break;

		case 'radio':
			echo '<TR><TD>';
			echo _makeCheckboxInput('CUSTOM_'.$field['ID'],$field['TITLE'],$request);
			echo '</TD></TR>';
			break;

		case'textarea':
			echo '<TR><TD>';
			echo _makeTextareaInput('CUSTOM_'.$field['ID'],$field['TITLE'],$request);
			echo '</TD></TR>';
			break;
	}
}
echo '</TABLE>';

?>