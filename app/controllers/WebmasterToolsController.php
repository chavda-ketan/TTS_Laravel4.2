<?php

class WebmasterToolsController extends BaseController {

	public function index()
	{
		$derp = $this->getData();
		return $derp;
	}

	protected function getData()
	{
		$email = $_ENV['wmtuser'];
		$passwd = $_ENV['wmtpass'];
		$storage = storage_path().'/cache';

		try {
			//$startdate = "2014-07-01";
			//$enddate = "2014-08-09";
			
			//$days = (strtotime($enddate) - strtotime($startdate)) / (60 * 60 * 24);
					
			$website = "http://techknowspace.com/";
			
			$currentDate = date('Y-m-d');	
			$pdate = date('Y-m-d', strtotime($currentDate .' - 3 day'));	
			
			$file = str_replace('-','',$pdate);
			
			$query = "SELECT * FROM seo_position_webmaster where date='$pdate'";
					
			$result = DB::connection('mysql')->select($query);

			$num_rows = count($result);

			$gdata = new GWTdata();
			
			if($num_rows <= 0) { 

				if($gdata->LogIn($email, $passwd) === true) 
				{ 
					$daterange = array($pdate, $pdate);

					$gdata->SetDaterange($daterange);
					
					$gdata->DownloadCSV($website, $storage);
					sleep(10);		 
				}
			}
		} catch (Exception $e) {
			die($e->getMessage());
		}
	
		/* Once file  downloaded below code called to read file and insert into db */
		sleep(10);
	
		if(file_exists($file.".csv"))
		{
			
			$filename = $file.".csv";
			echo "<pre>";
			$header = NULL;
			$data = array();
			if (($handle = fopen($filename, 'r')) !== FALSE)
			{
				while (($row = fgetcsv($handle, 1000, ",")) !== FALSE)
				{ //echo "1235";	
					if(!$header)
						$header = $row;
					else
						$data[] = array_combine($header, $row);
						//echo "5535";
				}
				fclose($handle);
			}
			
			if(count($data) > 0) {

				if($num_rows <= 0) { 
			
					$update_sql="UPDATE `rerun-places-time` SET tvalue=NOW() where tid=2";
					@mysql_query($update_sql);
						
					for($i=0; $i < count($data); $i++)
					{
						$dq = $data[$i]['Query'];
						$di = $data[$i]['Impressions'];
						$dc = $data[$i]['Clicks'];
						$da = $data[$i]['Avg. position'];

						$insertsql="INSERT INTO seo_position_webmaster (keyword, impressions, clicks, position, date)
									VALUES('$dq','$di','$dc','$da','$pdate')";
						@mysql_query($insertsql);
					}
							
					$message = "Data Downloaded and Inserted into Database for the date - ".$pdate;
				}
				else 
				{
					$message = "Data is latest already up to - ".$pdate;
				}
			}
						
			// "file is available";
		}
		else
		{
			$message = 'weenie';
			// "file is not available";
		}
		
		return $message;
	}
}
