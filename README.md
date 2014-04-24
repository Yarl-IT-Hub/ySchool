###ySchool###
A simple web based school management system.

####Version####

* ySchool Version : 1.0-SNAPSHOT
* Build Number   : 1.0.63

####Main Technologies####

* Programming language    : Java 1.7
* DataBase                : MySQL Community Server 5.5.20
* Build Tool              : Maven 3.0.4


####How to Start####

1. Install WAMP/LAMP server.
   Import the SQL schema ySchool/data/v1.0.6/ySchool-1.0.6.sql into mysql server to create yschool database for you.
   Load the data ySchool/data/v1.0.6/sampledata-1.0.6.sql
   If your mysql user,password is otherthan "root","" then update it in applicationContext.xml in both repository and yschoolweb modules. 

2. Install git setup and fork ySchool.

3. Install Maven, go to directory containing ySchool project root in command-prompt/terminal and run
  * For Windows
  *  ```mvn install:install-file -Dfile=ySchool\modules\repository\lib\hbnpojogen-persistence-1.4.4.jar -DgroupId=com.felees -DartifactId=hbnpojogen-persistence -Dversion=1.4.4 -Dpackaging=jar```
   * ```mvn install:install-file -Dfile=ySchool\modules\analytics\lib\class-analyzer-1.0.3.jar -DgroupId=com.arima.classanalyzer -DartifactId=class-analyzer -Dversion=1.0.3 -Dpackaging=jar```
   * For Linux
   * ```mvn install:install-file -Dfile=ySchool/modules/repository/lib/hbnpojogen-persistence-1.4.4.jar -DgroupId=com.felees -DartifactId=hbnpojogen-persistence -Dversion=1.4.4 -Dpackaging=jar```
    ```mvn install:install-file -Dfile=ySchool/modules/analytics/lib/class-analyzer-1.0.3.jar -DgroupId=com.arima.classanalyzer -DartifactId=class-analyzer -Dversion=1.0.3 -Dpackaging=jar```

4. Now jump into yschool project root directory and run, with having an active internet connection.
   ```mvn clean install```

5. Run the command from yschool main module (ySchool/yschool)
   ```mvn clean jetty:run-war```

6. Open localhost:8080/yschool in your browser to see ySchool up and running,type anything for username and password to get in.
