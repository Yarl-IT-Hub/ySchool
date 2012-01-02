
function ShowBar(){

       check_content('ajax.php?modname=Discipline/Decision_Graph.php&TAB=2');
};

function ShowChart(){

       check_content('ajax.php?modname=Discipline/Decision_Graph.php&TAB=3');
};

function ShowBar1(){

       check_content('ajax.php?modname=Discipline/ChartReport.php&TAB=2');
};

function ShowChart1(){

       check_content('ajax.php?modname=Discipline/ChartReport.php&TAB=3');
};
function ShowBar2(){

       check_content('ajax.php?modname=Discipline/BehaviorGraph.php&TAB=2');
};

function ShowChart2(){

       check_content('ajax.php?modname=Discipline/BehaviorGraph.php&TAB=3');
};
function ShowBar3(){

       check_content('ajax.php?modname=Discipline/BehaviorGraph.php&TAB=4');
};

function ShowChart3(){

       check_content('ajax.php?modname=Discipline/BehaviorGraph.php&TAB=5');
};

var discipline = {};



discipline.showPerpetrator = function(identifier){
	check_content('ajax.php?modname=Discipline/incidenteditor.php&module=1&identifier='+identifier);
};

discipline.showDetails = function(identifier){
	check_content('ajax.php?modname=Discipline/incidenteditor.php&identifier='+identifier);
};

discipline.showVictim = function(identifier){
	check_content('ajax.php?modname=Discipline/incidenteditor.php&module=2&identifier='+identifier);
};

discipline.showDiscipline = function(identifier){
	check_content('ajax.php?modname=Discipline/incidenteditor.php&module=3&identifier='+identifier);
};

