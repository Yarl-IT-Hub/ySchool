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

if($_REQUEST['modfunc']=='currenc'){

         if($_REQUEST['values']['CURRENCY'])
         {
             $currency_info = DBGet(DBQuery("SELECT * FROM PROGRAM_CONFIG WHERE PROGRAM='Currency' AND VALUE ='". $_REQUEST['values']['CURRENCY']."'"));
             if(count($currency_info[1]))
             {
              $currency_info_exist = DBGet(DBQuery("SELECT * FROM PROGRAM_CONFIG WHERE PROGRAM='Currency' AND SYEAR ='".UserSyear()."' AND SCHOOL_ID ='".UserSchool()."'"));
                 if(count($currency_info_exist[1]))
                 {
                        
                         $currency_info_upd = DBQuery("UPDATE PROGRAM_CONFIG SET TITLE='".$currency_info[1]['TITLE']."',VALUE='".$_REQUEST['values']['CURRENCY']."' WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND PROGRAM='Currency'");
                          unset($_SESSION['_REQUEST_vars']['modfunc']);
	                    unset($_REQUEST['modfunc']);
                 }
                 else {
                    
                    $currency_info_ins = DBQuery("INSERT INTO PROGRAM_CONFIG (SYEAR,SCHOOL_ID,PROGRAM,TITLE,VALUE)VALUES ('".UserSyear()."','".UserSchool()."','".$currency_info[1]['PROGRAM']."','".$currency_info[1]['TITLE']."','".$_REQUEST['values']['CURRENCY']."')");
                    unset($_SESSION['_REQUEST_vars']['modfunc']);
	               unset($_REQUEST['modfunc']);
                }
             }
         }
}
unset($_REQUEST['modfunc']);
$currency_info_exist = DBGet(DBQuery("SELECT * FROM PROGRAM_CONFIG WHERE PROGRAM='Currency' AND SYEAR ='".UserSyear()."' AND SCHOOL_ID ='".UserSchool()."'"));
$val=$currency_info_exist[1]['VALUE'];

$values = DBGet(DBQuery("SELECT  VALUE AS ID,TITLE FROM PROGRAM_CONFIG WHERE PROGRAM='Currency' "));
foreach($values as $symbol)
            $symbols[$symbol['ID']] = $symbol['TITLE'];

echo "<FORM name=failure id=failure action=Modules.php?modname=$_REQUEST[modname]&modfunc=currenc&page_display=CURRENCY method=POST>";
echo '<table width="330px;" cellpadding="4">';
echo '<tr><td  align="right">Currency:</td><td align="left">'.SelectInput($val,'values[CURRENCY]','',$symbols,'N/A').'</td></tr>';
echo '<tr><td colspan="2"></td></tr>';
echo '<tr><td colspan="2"><CENTER>'.SubmitButton('Save','','class=btn_medium').'</CENTER></td></tr>';
echo '</table>';
echo '</FORM>';
