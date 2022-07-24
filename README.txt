Main bot executable script: tgbot.php

1) Fill in the user data in the config.php file, namely:

############################
$admin = 00000; // ChatID of a manager/owner
$verifyrecipient = "XXXX"; // TON wallet for getting vefification transactions
$reference = 5; // Is not in use
$depopercent = 5; // A referral percent for deposits
$NFTRefPercent = 10; // Referral percent
$wallet2donate = "XXXXX"; // TON wallet for donations
$verifRefFee = 0.05; // Commission to referral from verification payment
$api_key = 'XXX'; // Tegro Money API Key
$roskassa_publickey = 'XXXX'; // Tegro Money Public Key
$roskassa_secretkey = 'XXXX'; // Tegro Money Secret Key
$tegrotonrate = 0.1; // Tegro and CryptoBot commission
$NFTwalletTON = "XXXXX"; // TON Wallet for incoming payments
$nftCatRate = 45; // Is not in use
$nftDogRate = 65;// Is not in use
$toncenterAPIKey = "XXXXX"; // API Key of Toncenter website
$CryptoPayAPIToken = ""; // CryptoPay API Token
define('TOKEN', 'XXXXX'); // Add the Bot API Token
############################

2) Register the bot in Cryptopay by specifying the postback URL: https://yourdomain/BotFolder/postback_cryptopay.php

3) Set the postback URL in the Tegro Money account: https://yourdomain/BotFolder/postback.php

4) Fill in the MySQL database data in the global.php file

5) Import MYSQL database structure from database.sql file

6) Install the webhook at https://api.telegram.org/ for the tgbot.php script:
https://api.telegram.org/botXXXXX/setWebhook?url=https://yourdomain/BotFolder/tgbot.php

7) Bot texts can be edited in the langs.php file

8) Place the script https://yourdomain/BotFolder/tonratechecker.php on cron with execution once a day