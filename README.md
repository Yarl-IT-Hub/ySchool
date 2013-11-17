###ySchool###
A simple web based school management system.

####Version####

* ySchool Version : 1.0-SNAPSHOT
* Build Version   : 1.0.51
* Last Update     : 17.11.2013.21.48

####Main Technologies####

* Programming language    : Java 1.7
* DataBase                : MySQL Community Server 5.5.20
* Build Tool              : Maven 3.0.4


####How to Start####

1. Install WAMP/LAMP server.
   Import the SQL schema yschool/docs/ySchool_V1.1.sql into mysql server to create yschool database for you.
2. Install git setup and fork ySchool.
3. Install Maven, go to directory containing yschool root in command-prompt/terminal and run
   ```mvn install:install-file -Dfile=yschool\modules\repository\lib\hbnpojogen-persistence-1.4.4.jar -DgroupId=com.felees -DartifactId=hbnpojogen-persistence -Dversion=1.4.4 -Dpackaging=jar```
   ```mvn install:install-file -Dfile=yschool\modules\analytics\lib\class-analyzer-1.0.3.jar -DgroupId=com.arima -DartifactId=class -Dversion=1.0.3 -Dpackaging=jar```
   ```mvn clean install```
4. Run the command from yschool main module (yschool/yschool)
   ```mvn clean jetty:run-war``` 
5. Open localhost:8080/yschool in your browser to see ySchool up and running.
