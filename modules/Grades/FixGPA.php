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
DrawBC("Gradebook > ".ProgramTitle());

$mps = GetAllMP('PRO',UserMP());
$mps = explode(',',str_replace("'",'',$mps));
$message = '<TABLE><TR><TD colspan=7 align=center>';
foreach($mps as $mp)
{
	if($mp && $mp!='0')
		$message .= '<INPUT type=radio name=marking_period_id value='.$mp.($mp==UserMP()?' CHECKED':'').'>'.GetMP($mp).'<BR>';
}

$message .= '</TD></TR></TABLE>';

$go = Prompt_Home('Confirm','When do you want to recalculate the running GPA numbers?',$message); 

if($go)
{	
	$students_RET = GetStuList($extra);

		DBQuery("SELECT calc_cum_gpa_mp('".$_REQUEST['marking_period_id']."')");
		DBQuery("SELECT set_class_rank_mp('".$_REQUEST['marking_period_id']."')");

	unset($_REQUEST['modfunc']);
	DrawHeader('<IMG SRC=assets/check.gif> The grades for '.GetMP($_REQUEST['marking_period_id']).' has been recalculated.');
	Prompt('Confirm','When do you want to recalculate the running GPA numbers?',$message);
}

?>