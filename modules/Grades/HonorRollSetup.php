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
include 'modules/Grades/DeletePromptX.fnc.php';
DrawBC("Gradebook > ".ProgramTitle());

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update')
{
        
	if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && ($_POST['values'] || $_REQUEST['ajax']))
	{
		foreach($_REQUEST['values'] as $id=>$columns)
			{
				 if($id!='new')
                                             {
					$sql = "UPDATE PROGRAM_CONFIG SET ";
					foreach($columns as $column=>$value)
                                        {
                                              $value= paramlib_validation($column,$value);
                                               $values .= ' " '.trim(str_replace("\'","'",$value)).' ",';
						if($value)
                                              $sql .= $column . '="'.trim(str_replace("\'","'",$value)).'",';
                                               else
                                                   $sql .= $column . '=NULL ,';
                                        }
					$sql = substr($sql,0,-1) . " WHERE TITLE='$id'";
					DBQuery($sql);
				}
				else
				{
                                    
					
						$sql = 'INSERT INTO PROGRAM_CONFIG ';
						$fields = 'SCHOOL_ID,SYEAR,PROGRAM,';
						$values = '\''.UserSchool().'\',\''.UserSyear().'\',"Honor_Roll",';
				

					$go = false;
					foreach($columns as $column=>$value){
						if(trim($value)!='')
						{
                                                        $value= paramlib_validation($column,$value);
							$fields .= $column.',';
							$values .= '\''.str_replace("\'","''",$value).'\',';
							$go = true;
      						}
                                                }
					$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
					if($go)
						DBQuery($sql);
                                                                           
				}
			}
		
	}
	unset($_REQUEST['modfunc']);
}




if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='remove')
{



		if(DeletePromptX('Honor Roll'))
		{
			DBQuery("DELETE FROM PROGRAM_CONFIG WHERE PROGRAM = 'Honor_Roll' AND TITLE='$_REQUEST[id]' ");
		}

}


if(!$_REQUEST['modfunc'])
{
                        $sql = 'SELECT TITLE,VALUE, TITLE as ID FROM PROGRAM_CONFIG WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' AND PROGRAM = "Honor_Roll" ORDER BY VALUE';
                       $functions = array('TITLE'=>'_makeTextInput', 'VALUE'=>'makeTextInputt');
                        $LO_columns = array('TITLE'=>'Honor Roll',
                                                    'VALUE'=>'Breakoff');
                       $link['add']['html'] = array('TITLE'=>_makeTextInput('','TITLE'),'VALUE'=>makeTextInputt('','VALUE'));
                        $link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
                       $link['remove']['variables'] = array('id'=>'ID');
                        $link['add']['html']['remove'] = button('add');
                        $LO_ret = DBGet(DBQuery($sql),$functions);
                        $tabs = array();
                       $tabs[] = array('title'=>'Honor Roll Setup');
                        echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
                        echo '<BR>';
                        echo '<style type="text/css">#div_margin { margin-top:-20px; _margin-top:-1px; }</style>';
                        echo WrapTabs($tabs,"");
                        echo '<div id="div_margin">';
                        
                        PopTable_wo_header ('header');
                        ListOutputMod($LO_ret,$LO_columns,'','',$link,array(),array('count'=>false,'download'=>false,'search'=>false));
                        echo '<BR>';
                        echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_honor_roll();"').'</CENTER>';
                        PopTable ('footer');
                        echo '</div>';
                        echo '</FORM>';

}

function _makeTextInput($value,$name)
{	global $THIS_RET;
   	if($THIS_RET['TITLE'])
            $id = $THIS_RET[TITLE];
	else
		
		$id = 'new';
            $extra = 'size=30 maxlength=50';
       
	return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}
function makeTextInputt($value,$name)
{	global $THIS_RET;
   	if($THIS_RET['TITLE'])
            $id = $THIS_RET[TITLE];
	else

		$id = 'new';

	return TextInput($value,'values['.$id.']['.$name.']','','class=cell_floating');
}
?>
