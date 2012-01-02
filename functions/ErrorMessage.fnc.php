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
// If there are missing vals or similar, show them a msg.
//
// Pass in an array with error messages and this will display them
// in a standard fashion.
//
// in a program you may have:
/*
if(!$sch)
	$error[]="School not provided.";
if($count == 0)
	$error[]="Number of students is zero.";
ErrorMessage($error);
*/
// (note that array[], the brackets with nothing in them makes
// PHP automatically use the next index.

// Why use this?  It will tell the user if they have multiple errors
// without them having to re-run the program each time finding new
// problems.  Also, the error display will be standardized.

// If a 2ND is sent, the list will not be treated as errors, but shown anyway

function ErrorMessage($errors,$code='error')
{
	
	if($errors)
	{
		$return = "<div style=text-align:left><table cellpadding=5 cellspacing=5 class=alert_box ><tr>";
		if(count($errors)==1)
		{
			if($code=='error' || $code=='fatal')
				$return .= '<td class=note></td><td class=note_msg >';
			else
				$return .= '<td class=alert></td><td class=alert_msg >';
			$return .= ($errors[0]?$errors[0]:$errors[1]);
		}
		else
		{
			if($code=='error' || $code=='fatal')
				$return .= "<td class=note></td><td class=note_msg >";
			else
				$return .= '<td class=alert></td><td class=alert_msg >';
			$return .= '<ul>';
			foreach($errors as $value)
					$return .= "<LI>$value</LI>\n";
			$return .= '</ul>';
		}
		$return .= "</td></tr></table></div>";

		if($code=='fatal')
		{
			$css = getCSS();
				$return .= "</td></tr></table>";
				$return .= "</td></tr></table></div>";
				$return .= "</td></tr></table>";
				$return .= "</td></tr></table>";
				$return .= "</td></tr></table>";
				$return .= "</td></tr>";
				if(User('PROFILE')!='teacher')
				{
					$return .= "<tr>
								<td class=\"footer\">
								<table width=\"100%\" border=\"0\">
								<tr>
    <td align='center' class='copyright'>
       <center>Copyright &copy; 2011 Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>).
                openSIS is licensed under the <a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>GPL License</a>.
                </center></td>
  </tr>
								</table>
								</td>
								</tr>
								</table>";
				}
				$return .= "</td></tr></table></td></tr></table>";
			if($isajax=="")
			echo $return;
			if(!$_REQUEST['_openSIS_PDF'])
				Warehouse('footer');
			exit;
		}
		

		return $return;
	}
}

function ErrorMessage1($errors,$code='error')
{
	
	if($errors)
	{
		$return = "<div style=text-align:left><table cellpadding=5 cellspacing=5 class=alert_box ><tr>";
		if(count($errors)==1)
		{
			if($code=='error' || $code=='fatal')
				$return .= '<td class=note></td><td class=note_msg >';
			else
				$return .= '<td class=alert></td><td class=alert_msg >';
			$return .= ($errors[0]?$errors[0]:$errors[1]);
		}
		else
		{
			if($code=='error' || $code=='fatal')
				$return .= "<td class=note></td><td class=note_msg >";
			else
				$return .= '<td class=alert></td><td class=alert_msg >';
			$return .= '<ul>';
			foreach($errors as $value)
					$return .= "<LI>$value</LI>\n";
			$return .= '</ul>';
		}
		$return .= "</td></tr></table></div>";

		if($code=='fatal')
		{
			$css = getCSS();
				$return .= "</td></tr></table>";
				$return .= "</td></tr></table></div>";
				$return .= "</td></tr></table>";
				$return .= "</td></tr></table>";
				$return .= "</td></tr></table>";
				$return .= "</td></tr>";
				$return .= "<tr>
							<td class=\"footer\">
							<table width=\"100%\" border=\"0\">
							<tr>
								<td align='center' class='copyright'>
								   <center>Copyright &copy; 2011 Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>).
											openSIS is licensed under the <a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>GPL License</a>.
											</center></td>
							  </tr>
							</table>
							</td>
							</tr>
							</table>";
				$return .= "</td></tr></table></td></tr></table>";
			if($isajax=="")
		//	echo $return;
			if(!$_REQUEST['_openSIS_PDF'])
				Warehouse('footer');
			exit;
		}

		return $return;
	}
}

?>
