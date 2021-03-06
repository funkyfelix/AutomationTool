 <?php 

	require_once('DBclass.php');

    class UPDATEDB {   
		
		public $i = 0;
			
		public function _start() {		
				
			$command = "C:\\xampp_1.8.2\\htdocs\Tool\cronjob\cronjob_start.bat stop";				
			shell_exec($command." 2>&1");
			
			$datei_lesen_eins = "C:\\xampp_1.8.2\htdocs\Tool\sql\data\\task_fcap17.txt";
			$datei_schreiben_eins = "C:\\xampp_1.8.2\htdocs\Tool\sql\data\\task_fcap17_sql.txt";
			$inhalt_eins = array();
			changeCSV( $datei_lesen_eins, $datei_schreiben_eins, $inhalt_eins);
			
			$datei_lesen_zwei = "C:\\xampp_1.8.2\htdocs\Tool\sql\data\\task_fcap09.txt";
			$datei_schreiben_zwei = "C:\\xampp_1.8.2\htdocs\Tool\sql\data\\task_fcap09_sql.txt";	
			$inhalt_zwei = array();
			changeCSV($datei_lesen_zwei, $datei_schreiben_zwei, $inhalt_zwei);
		
			$datei_lesen_drei = "C:\\xampp_1.8.2\htdocs\Tool\sql\data\\fcap09.txt";
			$datei_schreiben_drei = "C:\\xampp_1.8.2\htdocs\Tool\sql\data\\fcap09_sql.txt";
			$inhalt_drei = array();
			changeServerCSV($datei_lesen_drei, $datei_schreiben_drei, $inhalt_drei);
			
			$datei_lesen_vier = "C:\\xampp_1.8.2\htdocs\Tool\sql\data\\fcap17.txt";
			$datei_schreiben_vier = "C:\\xampp_1.8.2\htdocs\Tool\sql\data\\fcap17_sql.txt";
			$inhalt_vier = array();					
			changeServerCSV($datei_lesen_vier, $datei_schreiben_vier, $inhalt_vier);		
				
			updateDB();
			
			//updateTableRows();
		}
		
    }   
	
	
/** 
  * SERVER CSV DATEI ÄNDERN
  **/
function changeServerCSV($lesen, $schreiben, $inhalt) {
	$i = 0;
	if (!$handle = fopen($lesen, "r+")) {
		print "Kann die Datei $lesen nicht öffnen";
		exit;
	} else {
		while(!feof($handle))	{
			$zeile = fgets($handle	,1024);
			$nZeile = str_replace("\",\"" , ";", $zeile);
			$nNZeile = str_replace("\"", "" , $nZeile);
			if( $nNZeile != '' ){
				$inhalt[] .= $i .";". $nNZeile;				
			}
			$i++;
		}	
	}			
	$handle2 = fopen($schreiben, "w");	
	
	foreach($inhalt as $value){
		fputs($handle2,$value);
	}	
	fclose($handle);
	fclose($handle2);
}
/** 
  * TASK CSV DATEI ÄNDERN 
  **/
function changeCSV($lesen, $schreiben, $inhalt){
		if (!$handle = fopen($lesen, "r+")) {
			print "Kann die Datei $lesen nicht öffnen";
			exit;
		} else {
			global $i;
			while(!feof($handle))
			{
				$zeile = fgets($handle	,1024);
				$nZeile = str_replace("\",\"" , ";", $zeile);
				$nNZeile = str_replace("\"", "" , $nZeile);
				if( $nNZeile != '' ){
					$inhalt[] .= $i .";". $nNZeile;				
				}
				$i++;
			}	
		}	
		
	$handle2 = fopen($schreiben, "w");	
	
	foreach($inhalt as $value){
		fputs($handle2,$value);
	}
	
	fclose($handle);
	fclose($handle2);
}
/** 
  * DATENBANK UPDATE 
  **/
function updateDB(){	
	$DB = new DB;
	$DB->set_database("Lindorff_DB");
	$DB->_connect();
	$query = "
			DELETE FROM [Task]
			
			BULK
			INSERT [Task]
			FROM 'C:\\xampp_1.8.2\\htdocs\\Tool\\sql\\data\\task_fcap17_sql.txt'
			WITH
			(
				FIRSTROW = 2,
				FIELDTERMINATOR = ';',
				ROWTERMINATOR = '\n'
			)
			
			BULK
			INSERT [Task]
			FROM 'C:\\xampp_1.8.2\\htdocs\\Tool\\sql\\data\\task_fcap09_sql.txt'
			WITH
			(
				FIRSTROW = 2,
				FIELDTERMINATOR = ';',
				ROWTERMINATOR = '\n'
			)
			
			DROP TABLE [Fcap17]
			CREATE TABLE [Fcap17]
			(
				ID integer,
				TaskName nvarchar(50),
				NextRunTime nvarchar(50),
				Status char(20)
			)
			BULK
			INSERT [Fcap17]
			FROM 'C:\\xampp_1.8.2\\htdocs\\Tool\\sql\\data\\fcap17_sql.txt'
			WITH
			(
				FIRSTROW = 2,
				FIELDTERMINATOR = ';',
				ROWTERMINATOR = '\n'
			)
			
			UPDATE [Fcap17]
			SET
				TaskName = REPLACE(TaskName, '\"', ''),
				NextRunTime = REPLACE(NextRunTime, '\"', ''),
				Status = REPLACE(Status, '\"', '')
				
			DROP TABLE [Fcap09]
			CREATE TABLE [Fcap09]
			(
				ID integer,
				TaskName nvarchar(50),
				NextRunTime nvarchar(50),
				Status char(20)
			)
			BULK
			INSERT [Fcap09]
			FROM 'C:\\xampp_1.8.2\\htdocs\\Tool\\sql\\data\\fcap09_sql.txt'
			WITH
			(
				FIRSTROW = 2,
				FIELDTERMINATOR = ';',
				ROWTERMINATOR = '\n'
			)
			
			UPDATE [Fcap09]
			SET
				TaskName = REPLACE(TaskName, '\"', ''),
				NextRunTime = REPLACE(NextRunTime, '\"', ''),
				Status = REPLACE(Status, '\"', '') 			
			";
	$DB->set_sql($query);
	$DB->_query();	
	
	$DB->_close();
}
/** 
  * FEHLERHAFTE EINTRÄGE BEARBEITEN
  **/
function updateTableRows() {
	$DB = new DB; 
	$DB->set_database("Lindorff_DB");
	$DB->_connect();
	$sql = "UPDATE [Task]
			SET TaskName = '\Bertrand_WN990_WN991_Anzahl_taeglich'
			WHERE TaskName = '\Bertrand_WN990_WN991_Anzahl_täglich%' ";
	$DB->set_sql($sql);
	$DB->_query();
	$sql = "UPDATE [Fcap09]
			SET TaskName = '\Bertrand_WN990_WN991_Anzahl_taeglich'
			WHERE TaskName = '\Bertrand_WN990_WN991_Anzahl_täglich%' ";
	$DB->set_sql($sql);		
	$DB->_query();
	$DB->_close();
}
?>