<?php
/**
* Telegram Bot example
* @author Francesco Piero Paolicelli @piersoft
*/

include("Telegram.php");
include("settings_t.php");
class mainloop{
const MAX_LENGTH = 4096;
function start($telegram,$update)
{

	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");
	$text = $update["message"] ["text"];
	$chat_id = $update["message"] ["chat"]["id"];
	$user_id=$update["message"]["from"]["id"];
	$location=$update["message"]["location"];
	$reply_to_msg=$update["message"]["reply_to_message"];

	$this->shell($telegram,$text,$chat_id,$user_id,$location,$reply_to_msg);
	$db = NULL;

}

//gestisce l'interfaccia utente
 function shell($telegram,$text,$chat_id,$user_id,$location,$reply_to_msg)
{
	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");


	if ($text == "/start" || $text == "Informazioni") {
		$img = curl_file_create('logo.png','image/png');
		$contentp = array('chat_id' => $chat_id, 'photo' => $img);
		$telegram->sendPhoto($contentp);
		$reply = "Benvenuto. Questo è un servizio automatico (bot da Robot) per gli orari delle ".NAME.".
		Puoi ricercare gli orari dei Bus e Treni per Matera da Bari e viceversa.
		Per cercare i prossimi Treni o Bus clicca nel menù in basso o segui la sezione Istruzioni.
		In qualsiasi momento scrivendo /start ti ripeterò questo messaggio.\nQuesto bot è stato realizzato da @piersoft, senza fini di lucro e a titolo di Demo, non ha collegamenti con l'azienda Ferrovie Appulo Lucane, non è ufficiale e l'autore declina da ogni responsabilità. La fonte dati è realtime quella del sito http://ferrovieappulolucane.it/. Il codice sorgente è liberamente riutilizzabile con licenza MIT.";
		$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
		$telegram->sendMessage($content);
		$log=$today. ",new chat started," .$chat_id. "\n";
			file_put_contents(LOG_FILE, $log, FILE_APPEND | LOCK_EX);
		$this->create_keyboard_temp($telegram,$chat_id);

		exit;
	}	elseif ($text == "/istruzioni" || $text == "Istruzioni") {
		$reply = "Devi seguire alcune semplici regole. Il formato è 1bm%11/01/2016?5-11 dove il primo numero è 2 per i Treni e 1 per Bus, bm è per Bari->Matera e quindi mb per Matera->Bari, poi il carattere % e la data nel formato gg/mm/aaaa, quindi il carattere ? e infine l'ora e i minuti separati dal carattere - (meno). Attenzione! Se cercate nella giornata odierna, inserite sempre un orario successivo all'ora attuale.";
		$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
		$telegram->sendMessage($content);
		$log=$today. ",istruzioni," .$chat_id. "\n";
			file_put_contents(LOG_FILE, $log, FILE_APPEND | LOCK_EX);
		$this->create_keyboard_temp($telegram,$chat_id);

		exit;

	}
		elseif ($text == "Prossimi Treni da BA" || $text == "Prossimi Treni da MT") {
			$ore=date("H");
			$minuti=$todaym;
			$datad = date("d/m/Y");
			$minuti = date("i");

		if ($text == "Prossimi Treni da BA"){
			$partenza="bm";
			$mezzo="2";
		}else{
			$partenza="mb";
			$mezzo="2";
		}
			if ($mezzo=="1"){
				$bus="Bus";
				if ($partenza=="bm"|| $partenza=="BM"){
						$partenza="partenza=5&arrivo=36";
						$reply = "Sto cercando i ".$bus." che partono da Bari\n";
				}else{
							$partenza="partenza=36&arrivo=5";

						$reply = "Sto cercando i ".$bus." che partono da Matera\n";
				}
			}else {
				$bus="Treni";
				if ($partenza=="bm"|| $partenza=="BM"){
						$partenza="partenza=16532&arrivo=16540";
						$reply = "Sto cercando i ".$bus." che partono da Bari Centrale\n";
								$log=$today. ",next T da B," .$chat_id. "\n";
				}else{
						$partenza="partenza=16540&arrivo=16532";
						$reply = "Sto cercando i ".$bus." che partono da Matera Centrale\n";
								$log=$today. ",next T da M," .$chat_id. "\n";

				}
			}




							$reply .="dalle ore ".$ore." e ".$minuti." del giorno ".$datad;
							$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
							$telegram->sendMessage($content);
							sleep(2);
							$datad=str_replace("/","%2F",$datad);
							extract($_POST);
							$url = 'http://ferrovieappulolucane.it/ricerca-corse/';
							$ch = curl_init();
							$options='tipo_mezzo='.$mezzo.'&'.$partenza.'&data='.$datad.'&ore='.$ore.'&minuti='.$minuti;
							$file = fopen('db/fal.txt', 'w+'); //da decommentare se si vuole il file locale
							curl_setopt($ch,CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8','Accept: Application/json','X-Requested-With: XMLHttpRequest','Content-Type: application/octet-stream','Content-Type: application/download','Content-Type: application/force-download','Content-Transfer-Encoding: binary '));
							curl_setopt($ch,CURLOPT_POSTFIELDS,$options );
							curl_setopt($ch, CURLOPT_FILE, $file);
							curl_exec($ch);
							curl_close($ch);
								$html = file_get_contents("db/fal.txt");
							  $html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
							  $html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
							  $html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
							  $html=str_replace("</td>","</td>;",$html);
							  $html=str_replace("</th>","</th>;",$html);
							//  $html=str_replace("<tr class=\"dettagli\">","",$html);
								$html=str_replace("class=\"mostra_dettagli\" ","action=get_corsa&",$html);
								$html=str_replace("id=\"","id_corsa=",$html);
								$html=str_replace("\" data-id_partenza=\"","&id_partenza=",$html);
								$html=str_replace("\" data-id_arrivo=\"","&id_arrivo=",$html);


								$html=str_replace("<a href=\"#\"","",$html);
								$html=str_replace("\">dettagli</a>","dettagli",$html);
							$doc = new DOMDocument;
							$doc->loadHTML($html);

							$xpa    = new DOMXPath($doc);
							$count=0;
							$rows = $doc->getElementsByTagName('table');
							foreach($rows as $row) {
							    $values = array();
							    foreach($row->childNodes as $cell) {
							        $values[] = $cell->textContent;
							    }
							    $data[] = $values;
							    $count++;
							}
							$allertatmp =[];
							$allerta =[];
							$countr=0;
							$text="Numero Bus,Partenza,Ora,Arrivo,Ora,Durata,Garantito in caso di sciopero"."\n";

							for ($i=0;$i<$count;$i++){
							  array_push($allerta,$data[$i]);
							$countr++;
							}

							for ($tt=0;$tt<20;$tt++){
							for ($t=1;$t<20;$t++){
							  //array_push($allertatmp, explode(';',$allerta[$tt][$t]));
							//  preg_replace( "", "", $allerta[$tt][$t]);
							$allerta[$tt][$t]=str_replace("  ","",$allerta[$tt][$t]);
							$allerta[$tt][$t]=str_replace("   ","",$allerta[$tt][$t]);
							$allerta[$tt][$t]=str_replace("    ","",$allerta[$tt][$t]);
							$allerta[$tt][$t]=str_replace("

							    						","",$allerta[$tt][$t]);
							$allerta[$tt][$t]=str_replace("
							  						","",$allerta[$tt][$t]);
							$allerta[$tt][$t]=str_replace("							 						","",$allerta[$tt][$t]);
							$allerta[$tt][$t]=str_replace("					 ","",$allerta[$tt][$t]);
							$allerta[$tt][$t]=str_replace(array("\r\n", "\r", "\n"),"",$allerta[$tt][$t]);
							$allerta[$tt][$t]=str_replace("\n","",$allerta[$tt][$t]);
							$allerta[$tt][$t]=str_replace("<br>","",$allerta[$tt][$t]);

							$text .=$allerta[$tt][$t];
							  //$text .=$allerta[$tt][$t].",";
							//var_dump(explode(';',$allerta[$tt][$t]));
							}
							}
							$text=str_replace("						","",$text);
							$text=str_replace("					","",$text);
							$text=str_replace("				 													","",$text);
							$text=str_replace("				 	","",$text);
							$text=str_replace("    ","",$text);
							$text=str_replace("   ","",$text);
							$text=str_replace("  ","",$text);
							$text=str_replace("Â","",$text);
							$text=str_replace(";",",",$text);
							$text=str_replace(",,","",$text);
							$text=str_replace("Info","\n",$text);
							$text=str_replace("dettagli","\n",$text);
							$text=utf8_decode($text);


							//header('Content-type: text/csv');
							//header("Content-Disposition: attachment;filename=delibere.csv");

							//echo $text;
							//$allerta .=preg_replace('/\s+?(\S+)?$/', '', substr($allerta, 0, 400))."....\n";
							$filecsv = fopen('db/fal.csv', 'w+');
							fwrite($filecsv,$text); // Write information to the file
							   fclose($filecsv);

							$csv = array_map('str_getcsv', file('db/fal.csv'));
							//var_dump($csv);
							$countcsv = 0;
							foreach($csv as $data=>$csv1){
							  $countcsv = $countcsv+1;
							}
							if ($countcsv==1){
								$content = array('chat_id' => $chat_id, 'text' => "La ricerca non ha prodotto risultati",'disable_web_page_preview'=>true);
								$telegram->sendMessage($content);
								$this->create_keyboard_temp($telegram,$chat_id);
								exit;
									}
							//echo $count;
							for ($i=1;$i<$countcsv;$i++){
							  $homepage .="\n";
							  $homepage .="Numero mezzo: ".$csv[$i][0]."\n";
							  $homepage .="Partenza: ".$csv[$i][1]."\n";
							  $homepage .="Alle ore: ".$csv[$i][2]."\n";
							  $homepage .="Arrivo: ".$csv[$i][3]."\n";
							  $homepage .="Alle ore: ".$csv[$i][4]."\n";
							  $homepage .="Durata: ".$csv[$i][5]."\n";
							  $homepage .="Garantito in caso di sciopero: ".$csv[$i][6]."\n";
								$homepage .="Dettagli fermate intermedie:\n";
								$longUrl="http://www.piersoft.it/falbot/dettaglio.php?".$csv[$i][7];
								$longUrl=str_replace(" ","",$longUrl);

								$apiKey = API;

								$postData = array('longUrl' => $longUrl, 'key' => $apiKey);
								$jsonData = json_encode($postData);

								$curlObj = curl_init();

								curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key='.$apiKey);
								curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
								curl_setopt($curlObj, CURLOPT_HEADER, 0);
								curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
								curl_setopt($curlObj, CURLOPT_POST, 1);
								curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

								$response = curl_exec($curlObj);

								// Change the response json string to object
								$json = json_decode($response);

								curl_close($curlObj);
								//  $reply="Puoi visualizzarlo su :\n".$json->id;
								$shortLink = get_object_vars($json);
								//return $json->id;

								$homepage .=$shortLink['id']."\n";

							  $homepage .="____________\n";

							}
							$chunks = str_split($homepage, self::MAX_LENGTH);
							foreach($chunks as $chunk) {
								$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
								$telegram->sendMessage($content);

								}

								$content = array('chat_id' => $chat_id, 'text' => "Approfondimenti e biglietti online su http://ferrovieappulolucane.it/",'disable_web_page_preview'=>true);
								$telegram->sendMessage($content);

		file_put_contents(LOG_FILE, $log, FILE_APPEND | LOCK_EX);
	//		$this->create_keyboard_temp($telegram,$chat_id);
exit;
			}
			elseif ($text == "Prossimi Bus da BA" || $text == "Prossimi Bus da MT") {
				$ore=date("H");
				$minuti=$todaym;
				$datad = date("d/m/Y");
				$minuti = date("i");
				if ($text == "Prossimi Bus da BA"){
					$partenza="bm";
					$mezzo="1";
				}else{
					$partenza="mb";
					$mezzo="1";
				}
				if ($mezzo=="1"){
						$bus="Bus";
						if ($partenza=="bm"|| $partenza=="BM"){
									$partenza="partenza=5&arrivo=36";
								$reply = "Sto cercando i ".$bus." che partono da Bari\n";
						}else{
								$partenza="partenza=36&arrivo=5";
								$reply = "Sto cercando i ".$bus." che partono da Matera\n";

						}
					}else {
						$bus="Treni";
						if ($partenza=="bm"|| $partenza=="BM"){
								$partenza="partenza=16532&arrivo=16540";
								$reply = "Sto cercando i ".$bus." che partono da Bari Centrale\n";
										$log=$today. ",next T da B," .$chat_id. "\n";
						}else{
								$partenza="partenza=16540&arrivo=16532";
								$reply = "Sto cercando i ".$bus." che partono da Matera Centrale\n";
										$log=$today. ",next T da M," .$chat_id. "\n";

						}
					}



									$reply .="dalle ore ".$ore." e ".$minuti." del giorno ".$datad;
									$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
									$telegram->sendMessage($content);
									sleep(2);
									$datad=str_replace("/","%2F",$datad);
									extract($_POST);
									$url = 'http://ferrovieappulolucane.it/ricerca-corse/';
									$ch = curl_init();
									$options='tipo_mezzo='.$mezzo.'&'.$partenza.'&data='.$datad.'&ore='.$ore.'&minuti='.$minuti;
									$file = fopen('db/fal.txt', 'w+'); //da decommentare se si vuole il file locale
									curl_setopt($ch,CURLOPT_URL, $url);
									curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8','Accept: Application/json','X-Requested-With: XMLHttpRequest','Content-Type: application/octet-stream','Content-Type: application/download','Content-Type: application/force-download','Content-Transfer-Encoding: binary '));
									curl_setopt($ch,CURLOPT_POSTFIELDS,$options );
									curl_setopt($ch, CURLOPT_FILE, $file);
									curl_exec($ch);
									curl_close($ch);
										$html = file_get_contents("db/fal.txt");
										$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
										$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
										$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
										$html=str_replace("</td>","</td>;",$html);
										$html=str_replace("</th>","</th>;",$html);
										$html=str_replace("<tr class=\"dettagli\">","",$html);
									$doc = new DOMDocument;
									$doc->loadHTML($html);

									$xpa    = new DOMXPath($doc);
									$count=0;
									$rows = $doc->getElementsByTagName('table');
									foreach($rows as $row) {
											$values = array();
											foreach($row->childNodes as $cell) {
													$values[] = $cell->textContent;
											}
											$data[] = $values;
											$count++;
									}
									$allertatmp =[];
									$allerta =[];
									$countr=0;
									$text="Numero Bus,Partenza,Ora,Arrivo,Ora,Durata,Garantito in caso di sciopero"."\n";

									for ($i=0;$i<$count;$i++){
										array_push($allerta,$data[$i]);
									$countr++;
									}

									for ($tt=0;$tt<20;$tt++){
									for ($t=1;$t<20;$t++){
										//array_push($allertatmp, explode(';',$allerta[$tt][$t]));
									//  preg_replace( "", "", $allerta[$tt][$t]);
									$allerta[$tt][$t]=str_replace("  ","",$allerta[$tt][$t]);
									$allerta[$tt][$t]=str_replace("   ","",$allerta[$tt][$t]);
									$allerta[$tt][$t]=str_replace("    ","",$allerta[$tt][$t]);
									$allerta[$tt][$t]=str_replace("

										  						","",$allerta[$tt][$t]);
									$allerta[$tt][$t]=str_replace("
																","",$allerta[$tt][$t]);
									$allerta[$tt][$t]=str_replace("							 						","",$allerta[$tt][$t]);
									$allerta[$tt][$t]=str_replace("					 ","",$allerta[$tt][$t]);
									$allerta[$tt][$t]=str_replace(array("\r\n", "\r", "\n"),"",$allerta[$tt][$t]);
									$allerta[$tt][$t]=str_replace("\n","",$allerta[$tt][$t]);
									$allerta[$tt][$t]=str_replace("<br>","",$allerta[$tt][$t]);

									$text .=$allerta[$tt][$t];
										//$text .=$allerta[$tt][$t].",";
									//var_dump(explode(';',$allerta[$tt][$t]));
									}
									}
									$text=str_replace("						","",$text);
									$text=str_replace("					","",$text);
									$text=str_replace("				 													","",$text);
									$text=str_replace("				 	","",$text);
									$text=str_replace("    ","",$text);
									$text=str_replace("   ","",$text);
									$text=str_replace("  ","",$text);
									$text=str_replace("Â","",$text);
									$text=str_replace(";",",",$text);
									$text=str_replace(",,","",$text);
									$text=str_replace("Info","\n",$text);
									$text=str_replace("dettagli","\n",$text);
									$text=utf8_decode($text);


									//header('Content-type: text/csv');
									//header("Content-Disposition: attachment;filename=delibere.csv");

									//echo $text;
									//$allerta .=preg_replace('/\s+?(\S+)?$/', '', substr($allerta, 0, 400))."....\n";
									$filecsv = fopen('db/fal.csv', 'w+');
									fwrite($filecsv,$text); // Write information to the file
										 fclose($filecsv);

									$csv = array_map('str_getcsv', file('db/fal.csv'));
									//var_dump($csv);
									$countcsv = 0;
									foreach($csv as $data=>$csv1){
										$countcsv = $countcsv+1;
									}
									if ($countcsv==1){
										$content = array('chat_id' => $chat_id, 'text' => "La ricerca non ha prodotto risultati",'disable_web_page_preview'=>true);
										$telegram->sendMessage($content);
										$this->create_keyboard_temp($telegram,$chat_id);
										exit;
											}
									//echo $count;
									for ($i=1;$i<$countcsv;$i++){
										$homepage .="\n";
										$homepage .="Numero mezzo: ".$csv[$i][0]."\n";
										$homepage .="Partenza: ".$csv[$i][1]."\n";
										$homepage .="Alle ore: ".$csv[$i][2]."\n";
										$homepage .="Arrivo: ".$csv[$i][3]."\n";
										$homepage .="Alle ore: ".$csv[$i][4]."\n";
										$homepage .="Durata: ".$csv[$i][5]."\n";
										$homepage .="Garantito in caso di sciopero: ".$csv[$i][6]."\n";
										$homepage .="____________\n";

									}
									$chunks = str_split($homepage, self::MAX_LENGTH);
									foreach($chunks as $chunk) {
										$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
										$telegram->sendMessage($content);

										}

										$content = array('chat_id' => $chat_id, 'text' => "Approfondimenti e biglietti online su http://ferrovieappulolucane.it/",'disable_web_page_preview'=>true);
										$telegram->sendMessage($content);

				file_put_contents(LOG_FILE, $log, FILE_APPEND | LOCK_EX);
			//		$this->create_keyboard_temp($telegram,$chat_id);
			exit;

}
elseif($location!=null)
		{

	//		$this->location_manager($telegram,$user_id,$chat_id,$location);
			exit;

		}
//elseif($text !=null)

		elseif(strpos($text,'?') !== false && strpos($text,'%') !== false && strpos($text,'-') !== false ){
				//$mezzo="1";

				function extractString($string, $start, $end) {
						$string = " ".$string;
						$ini = strpos($string, $start);
						if ($ini == 0) return "";
						$ini += strlen($start);
						$len = strpos($string, $end, $ini) - $ini;
						return substr($string, $ini, $len);
				}
				//$testo=$_POST["q"];
				//$testo="bm%11/01/2016?5-11";
				$datad=extractString($text,"%","?");
				$ore=extractString($text,"?","-");
				$minuti=substr($text, -2, 2);
				$partenza=substr($text, 1, 2);
					$mezzo=substr($text, 0, 1);
				//  echo $partenza;
				//  echo $minuti;

if ($mezzo=="1"){
	$bus="Bus";
	if ($partenza=="bm"|| $partenza=="BM"){

$partenza="partenza=5&arrivo=36";
			$reply = "Sto cercando i ".$bus." che partono da Bari\n";
	}else{
			$partenza="partenza=36&arrivo=5";
			$reply = "Sto cercando i ".$bus." che partono da Matera\n";

	}
}else {
	$bus="Treni";
	if ($partenza=="bm"|| $partenza=="BM"){
			$partenza="partenza=16532&arrivo=16540";
			$reply = "Sto cercando i ".$bus." che partono da Bari Centrale\n";
	}else{
			$partenza="partenza=16540&arrivo=16532";
			$reply = "Sto cercando i ".$bus." che partono da Matera Centrale\n";

	}
}


				if (intval($ore)<10){
				  $ore="0".$ore;
				}
				if (intval($minuti)<10){
				  $minuti="0".$minuti;
					if ($minuti=="000") $minuti=str_replace("000","00",$minuti);
				}

				$reply .="dalle ore ".$ore." e ".$minuti." del giorno ".$datad;
				$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
				$telegram->sendMessage($content);
				sleep(2);
				$datad=str_replace("/","%2F",$datad);
				extract($_POST);
				$url = 'http://ferrovieappulolucane.it/ricerca-corse/';
				$ch = curl_init();
				$options='tipo_mezzo='.$mezzo.'&'.$partenza.'&data='.$datad.'&ore='.$ore.'&minuti='.$minuti;
				$file = fopen('db/fal.txt', 'w+'); //da decommentare se si vuole il file locale
				curl_setopt($ch,CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8','Accept: Application/json','X-Requested-With: XMLHttpRequest','Content-Type: application/octet-stream','Content-Type: application/download','Content-Type: application/force-download','Content-Transfer-Encoding: binary '));
				curl_setopt($ch,CURLOPT_POSTFIELDS,$options );
				curl_setopt($ch, CURLOPT_FILE, $file);
				curl_exec($ch);
				curl_close($ch);
					$html = file_get_contents("db/fal.txt");
				  $html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
				  $html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
				  $html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
				  $html=str_replace("</td>","</td>;",$html);
				  $html=str_replace("</th>","</th>;",$html);
				  $html=str_replace("<tr class=\"dettagli\">","",$html);
				$doc = new DOMDocument;
				$doc->loadHTML($html);

				$xpa    = new DOMXPath($doc);
				$count=0;
				$rows = $doc->getElementsByTagName('table');
				foreach($rows as $row) {
				    $values = array();
				    foreach($row->childNodes as $cell) {
				        $values[] = $cell->textContent;
				    }
				    $data[] = $values;
				    $count++;
				}
				$allertatmp =[];
				$allerta =[];
				$countr=0;
				$text="Numero Bus,Partenza,Ora,Arrivo,Ora,Durata,Garantito in caso di sciopero"."\n";

				for ($i=0;$i<$count;$i++){
				  array_push($allerta,$data[$i]);
				$countr++;
				}

				for ($tt=0;$tt<20;$tt++){
				for ($t=1;$t<20;$t++){
				  //array_push($allertatmp, explode(';',$allerta[$tt][$t]));
				//  preg_replace( "", "", $allerta[$tt][$t]);
				$allerta[$tt][$t]=str_replace("  ","",$allerta[$tt][$t]);
				$allerta[$tt][$t]=str_replace("   ","",$allerta[$tt][$t]);
				$allerta[$tt][$t]=str_replace("    ","",$allerta[$tt][$t]);
				$allerta[$tt][$t]=str_replace("

				    						","",$allerta[$tt][$t]);
				$allerta[$tt][$t]=str_replace("
				  						","",$allerta[$tt][$t]);
				$allerta[$tt][$t]=str_replace("							 						","",$allerta[$tt][$t]);
				$allerta[$tt][$t]=str_replace("					 ","",$allerta[$tt][$t]);
				$allerta[$tt][$t]=str_replace(array("\r\n", "\r", "\n"),"",$allerta[$tt][$t]);
				$allerta[$tt][$t]=str_replace("\n","",$allerta[$tt][$t]);
				$allerta[$tt][$t]=str_replace("<br>","",$allerta[$tt][$t]);

				$text .=$allerta[$tt][$t];
				  //$text .=$allerta[$tt][$t].",";
				//var_dump(explode(';',$allerta[$tt][$t]));
				}
				}
				$text=str_replace("						","",$text);
				$text=str_replace("					","",$text);
				$text=str_replace("				 													","",$text);
				$text=str_replace("				 	","",$text);
				$text=str_replace("    ","",$text);
				$text=str_replace("   ","",$text);
				$text=str_replace("  ","",$text);
				$text=str_replace("Â","",$text);
				$text=str_replace(";",",",$text);
				$text=str_replace(",,","",$text);
				$text=str_replace("Info","\n",$text);
				$text=str_replace("dettagli","\n",$text);
				$text=utf8_decode($text);


				//header('Content-type: text/csv');
				//header("Content-Disposition: attachment;filename=delibere.csv");

				//echo $text;
				//$allerta .=preg_replace('/\s+?(\S+)?$/', '', substr($allerta, 0, 400))."....\n";
				$filecsv = fopen('db/fal.csv', 'w+');
				fwrite($filecsv,$text); // Write information to the file
				   fclose($filecsv);

				$csv = array_map('str_getcsv', file('db/fal.csv'));
				//var_dump($csv);
				$countcsv = 0;
				foreach($csv as $data=>$csv1){
				  $countcsv = $countcsv+1;
				}
				if ($countcsv==1){
					$content = array('chat_id' => $chat_id, 'text' => "La ricerca non ha prodotto risultati",'disable_web_page_preview'=>true);
					$telegram->sendMessage($content);
					$this->create_keyboard_temp($telegram,$chat_id);
					exit;
						}
				//echo $count;
				for ($i=1;$i<$countcsv;$i++){
				  $homepage .="\n";
				  $homepage .="Numero mezzo: ".$csv[$i][0]."\n";
				  $homepage .="Partenza: ".$csv[$i][1]."\n";
				  $homepage .="Alle ore: ".$csv[$i][2]."\n";
				  $homepage .="Arrivo: ".$csv[$i][3]."\n";
				  $homepage .="Alle ore: ".$csv[$i][4]."\n";
				  $homepage .="Durata: ".$csv[$i][5]."\n";
				  $homepage .="Garantito in caso di sciopero: ".$csv[$i][6]."\n";
				  $homepage .="____________\n";

				}
				$chunks = str_split($homepage, self::MAX_LENGTH);
				foreach($chunks as $chunk) {
					$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
					$telegram->sendMessage($content);

					}
					$content = array('chat_id' => $chat_id, 'text' => "Approfondimenti e biglietti online su http://ferrovieappulolucane.it/",'disable_web_page_preview'=>true);
					$telegram->sendMessage($content);
					
					$log=$today. ",ricerca," .$chat_id. "\n";
						file_put_contents(LOG_FILE, $log, FILE_APPEND | LOCK_EX);

}

$this->create_keyboard_temp($telegram,$chat_id);
exit;

	}

	function create_keyboard_temp($telegram, $chat_id)
	 {
			 $option = array(["Prossimi Treni da MT","Prossimi Treni da BA"],["Prossimi Bus da MT","Prossimi Bus da BA"],["Istruzioni","Informazioni"]);
			 $keyb = $telegram->buildKeyBoard($option, $onetime=false);
			 $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[Digita la sequenza di caratteri ad esempio 1bm%11/01/2016?5-11 oppure clicca sulle prossime partenze]");
			 $telegram->sendMessage($content);
	 }



}

?>
