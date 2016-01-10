<?php
//$partenza="Bari-Matera";
//$ore="5";
//$minuti="11";
//$datad="11/01/2016";
$mezzo="1";

$testo=$_POST["q"];
//$testo="bm%11/01/2016?5-11";
$datad=extractString($testo,"%","?");
$ore=extractString($testo,"?","-");
$minuti=substr($testo, -2, 2);
$partenza=substr($testo, 0, 2);
//  echo $partenza;
//  echo $minuti;

function extractString($string, $start, $end) {
    $string = " ".$string;
    $ini = strpos($string, $start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

if ($partenza=="bm"||$partenza=="BM"){
    $partenza="partenza=36&arrivo=5";
}else{
    $partenza="partenza=5&arrivo=36";

}
if (intval($ore)<10){
  $ore="0".$ore;
}
if (intval($minuti)<10){
  $minuti="0".$minuti;
}
$datad=str_replace("/","%2F",$datad);

extract($_POST);
$url = 'http://ferrovieappulolucane.it/ricerca-corse/';
$ch = curl_init();
$options="";
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
//echo $count;
for ($i=1;$i<$countcsv;$i++){
  $homepage .="\n";
  $homepage .="Numero Bus: ".$csv[$i][0]."\n";
  $homepage .="Partenza: ".$csv[$i][1]."\n";
  $homepage .="Alle ore: ".$csv[$i][2]."\n";
  $homepage .="Arrivo: ".$csv[$i][3]."\n";
  $homepage .="Alle ore: ".$csv[$i][4]."\n";
  $homepage .="Durata: ".$csv[$i][5]."\n";
  $homepage .="Garantito in caso di sciopero: ".$csv[$i][6]."\n";
  $homepage .="____________\n";

}
echo $homepage;
?>
