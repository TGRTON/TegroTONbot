Основной исполняемый файл бота: tgbot.php

1) В файле config.php заполнить данные пользователя, а именно:

###########################
$admin = 00000; //   ChatID of a manager/owner
$verifyrecepient = "XXXX"; // TON wallet for getting vefification transactions
$refpercent = 5; // Is not in use
$depopercent = 5; // A referral percent for deposits
$NFTRefPercent = 10; // Referral percent
$wallet2donate = "XXXXX"; // TON wallet for donations
$verifRefFee = 0.05; // Comission to referral from verification payment
$api_key = 'XXX'; // Tegro Money API Key
$roskassa_publickey = 'XXXX'; // Tegro Money Public Key
$roskassa_secretkey = 'XXXX'; // Tegro Money Secret Key
$tegrotonrate = 0.1; // Tegro and CryptoBot comission
$NFTwalletTON = "XXXXX"; // TON Wallet for incoming payments
$nftCatRate = 45; // Is not in use
$nftDogRate = 65;// Is not in use
$toncenterAPIKey = "XXXXX"; // API Key of Toncenter website
$CryptoPayAPIToken = ""; // CryptoPay API Token
define('TOKEN', 'XXXXX'); // Add the Bot API Token
###########################

2) Зарегистрировать бота в Cryptopay, указав постбек УРЛ: postback_cryptopay.php

3) Указать постбек УРЛ в кабинете Tegro Money: postback.php

4) Заполнить данные MySQL базы в файле global.php

5) Импортировать структуру базы MYSQL из файла database.sql

6) Выполнить установку вебхука в https://api.telegram.org/ для скрипта tgbot.php
https://api.telegram.org/botXXXXX/setWebhook?url=https://yourdomain/BotFolder/tgbot.php

7) Тексты бота можно редактировать в файле langs.php

8) Поместить скрипт https://yourdomain/BotFolder/tonratechecker.php на крон с исполнением 1 раз в сутки