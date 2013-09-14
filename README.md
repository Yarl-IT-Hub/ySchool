####################################################################################
##### 				 ySchool				       #####
####################################################################################

Version
-------
ySchool Version : 1.0-SNAPSHOT
Build Version   : 1.0.40
Last Update     : 08.09.2013.12.00

Main Technologies
-----------------
Programming language    : Java 1.7
DB                      : MySQL Community Server 5.5.20
Build Tool              : Maven 3.0.4


How to Start
------------
1. Install WAMP/LAMP server.
    Import the SQL schema yschool/docs/yschool_lite_V1.1.sql into mysql server to create yschool database for you.
   Load SQL initial data with yschool/docs/yschool_lite_default_dataV1.1.sql.
2. Install git setup and fork ySchool.
3. Install Maven and,
    Go to yschool directory in command-prompt/ terminal and Run the command
mvn install:install-file -Dfile=modules\repository\hbnPojoGenResource\hbnpojogen-persistence-1.4.4.jar -DgroupId=com.felees -DartifactId=hbnpojogen-persistence -Dversion=1.4.4 -Dpackaging=jar
    Next run command
 mvn clean install
    Run the command mvn clean jetty:run-war within yschool main module (yschool /yschool).
4. Open localhost:8080/yschool in your browser to see ySchool up and running.
