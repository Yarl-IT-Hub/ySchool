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
include('../../Redirect_modules.php');
$QI = DBQuery("SELECT PERIOD_ID,TITLE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER ");
$RET = DBGet($QI);

$SCALE_RET = DBGet(DBQuery("SELECT * from SCHOOLS where ID = '".UserSchool()."'"));

DrawBC("Gradebook > ".ProgramTitle());
 
$RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,SEMESTER_ID FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
if($RET)
$mps = GetAllMP('PRO',UserMP());
else
$mps = GetAllMP('TRAN',UserMP());   
$mps = explode(',',str_replace("'",'',$mps));
$table = '<TABLE><TR><TD valign=top><TABLE>
	</TR>
		<TD align=right valign=top><font color=gray>Calculate GPA for</font></TD>
		<TD>';

foreach($mps as $mp)
{
	if($mp!='0')
		$table .= '<INPUT type=radio name=marking_period_id value='.$mp.($mp==UserMP()?' CHECKED':'').'>'.GetMP($mp).'<BR>';
}

$table .= '</TD>
	</TR>
	<TR>
		<TD colspan = 2 align=center><font color=gray>GPA based on a scale of '.$SCALE_RET[1]['REPORTING_GP_SCALE'].'</TD>
	</TR>'.
//	<TR>
//		<TD align=right valign=top><font color=gray>Base class rank on</font></TD>
//		<TD><INPUT type=radio name=rank value=WEIGHTED_GPA CHECKED>Weighted GPA<BR><INPUT type=radio name=rank value=GPA>Unweighted GPA</TD>
'</TABLE></TD><TD width=350><small>GPA calculation modifies existing records.<BR><BR>Weighted and unweighted GPA is calculated by dividing the weighted and unweighted grade points configured for each letter grade (assigned in the Report Card Codes setup program) by the base grading scale specified in the school setup.  </small></TD></TR></TABLE>';

$go = Prompt_Home('GPA Calculation','Calculate GPA and Class Rank',$table);
if($go)
{
	DBQuery("SELECT CALC_CUM_GPA_MP('".$_REQUEST['marking_period_id']."')");
    DBQuery("SELECT SET_CLASS_RANK_MP('".$_REQUEST['marking_period_id']."')");
  
	
	unset($_REQUEST['delete_ok']);
	DrawHeader('<table><tr><td><IMG SRC=assets/check.gif></td><td>GPA and class rank for '.GetMP($_REQUEST['marking_period_id']).' has been calculated.</td></tr></table>');
	Prompt('GPA Calculation','Calculate GPA and Class Rank',$table);
}
?>