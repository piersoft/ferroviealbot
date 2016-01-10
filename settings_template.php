<?php

//Telegram
define('API',''); // api google shortner eventuale per shot link
define('TELEGRAM_BOT',''); // token Telegram Bot *obbligatorio*
define('BOT_WEBHOOK', ''); // url assoluto https per start.php
define('GDRIVEKEY', ''); // key dello sheet di google drive
define('GDRIVEGID1', ''); //gid del foglio di calcolo . di solito il primo gid=0 per esempio FAQ
define('GDRIVEGID2', ''); // gid dell'eventuale altro foglio di calcolo per esempio foglio Risposte
define('GDRIVEGID3', ''); // gid dell'eventuale altro foglio di calcolo per esempio sedi azienda/sindacato
define('NAME', 'Ferrovie Appulo Lucane'); // nome del Bot che appare nelle Informazioni /start
define('LOG_FILE', 'db/telegram.csv'); // db deve essere una dir scrivibile da apache/www-data
?>
