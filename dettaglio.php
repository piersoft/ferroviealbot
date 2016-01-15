<?php
$dettaglio=$_GET["action"];
$id_corsa=$_GET["id_corsa"];
$id_partenza=$_GET["id_partenza"];
$id_arrivo=$_GET["id_arrivo"];
//extract($_POST);
$url = 'http://ferrovieappulolucane.it/wp-admin/admin-ajax.php';
$ch = curl_init();

$options="action=".$dettaglio."&id_corsa=".$id_corsa."&id_partenza=".$id_partenza."&id_arrivo=".$id_arrivo;
//$options="action=get_corsa&id_corsa=31090&id_partenza=16287&id_arrivo=16414";
//$options='tipo_mezzo='.$mezzo.'&'.$partenza.'&data='.$datad.'&ore='.$ore.'&minuti='.$minuti;
$file = fopen('db/faldettaglio.txt', 'w+'); //da decommentare se si vuole il file locale
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8','Accept: Application/json','X-Requested-With: XMLHttpRequest','Content-Type: application/octet-stream','Content-Type: application/download','Content-Type: application/force-download','Content-Transfer-Encoding: binary '));
curl_setopt($ch,CURLOPT_POSTFIELDS,$options );
curl_setopt($ch, CURLOPT_FILE, $file);
curl_exec($ch);
curl_close($ch);


$html = file_get_contents("db/faldettaglio.txt");
$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
echo $html;
/*
//  $html=str_replace("<tr class=\"dettagli\">","",$html);
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
//$text="Numero Bus,Partenza,Ora,Arrivo,Ora,Durata,Garantito in caso di sciopero,Info\n";
$text="";
for ($i=0;$i<$count;$i++){
array_push($allerta,$data[$i]);
$countr++;
}
//var_dump($allerta);
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
$text=str_replace("|","\n",$text);
$text=utf8_decode($text);

echo $text;
$filecsv = fopen('db/faldettaglio.csv', 'w+');
fwrite($filecsv,$text); // Write information to the file
fclose($filecsv);

//echo $text;


$urlgd  ="https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20%2A&key=12EaiyVJBbpoFWK9V8i1lYBVNhulu9ikJuaVwKWLuNxo&gid=0";

$csv = array_map('str_getcsv', file($filecsv));
//var_dump($csv);
$countcsv = 0;
foreach($csv as $data=>$csv1){
  $countcsv = $countcsv+1;
}
//echo $countcsv;
for ($i=1;$i<$countcsv;$i++){
  $homepage .="</br>";
  if ($csv[$i][0]!=NULL) $homepage .="Fermata: ".$csv[$i][0]."</br>";
  if ($csv[$i][1]!=NULL) $homepage .="Arrivo: ".$csv[$i][1]."</br>";
  if ($csv[$i][2]!=NULL) $homepage .="Partenza: ".$csv[$i][2]."</br>";
  if ($csv[$i][3]!=NULL) $homepage .="F. a richiesta: ".$csv[$i][3]."</br>";
  if ($csv[$i][4]!=NULL) $homepage .="Note: ".$csv[$i][4]."</br>";
//  $homepage .="____________</br>";

}

echo $homepage;
*/
 ?>
