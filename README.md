# TegroTONbot - Enhanced Telegram Bot Solution

<p align="center">
  <h3 align="center">Explore the Advanced Capabilities of TegroTONbot</h3>

  <p align="center">
    Your Comprehensive Guide to Harnessing the Power of a Custom Telegram Bot
    <br/>
    <a href="https://github.com/TGRTON/TegroTONbot"><strong>Explore the Detailed Documentation »</strong></a>
    <br/>
    <a href="https://github.com/TGRTON/TegroTONbot">View a Live Demo</a> ·
    <a href="https://github.com/TGRTON/TegroTONbot/issues">Report a Bug</a> ·
    <a href="https://github.com/TGRTON/TegroTONbot/issues">Request Additional Features</a>
  </p>
</p>

![Downloads](https://img.shields.io/github/downloads/TGRTON/TegroTONbot/total) ![Contributors](https://img.shields.io/github/contributors/TGRTON/TegroTONbot?color=dark-green) ![Issues](https://img.shields.io/github/issues/TGRTON/TegroTONbot) ![License](https://img.shields.io/github/license/TGRTON/TegroTONbot) 

## Comprehensive Table Of Contents

- [Project Insights](#about-the-project)
- [Development Technologies](#built-with)
- [Initial Setup and Installation](#getting-started)
  - [System Requirements](#prerequisites)
  - [Detailed Installation Process](#installation)
- [Operational Guidelines](#usage)
- [Future Enhancements](#roadmap)
- [Community and Contributions](#contributing)
- [Licensing Terms](#license)
- [Project Contributors](#authors)
- [Special Acknowledgements](#acknowledgements)

## Project Insights

TegroTONbot is an all-encompassing Telegram bot, meticulously crafted for engaging with a targeted audience, facilitating TGR presales, enabling cryptocurrency transactions in TON, and offering a dynamic referral program. This bot is a one-stop solution for those seeking to quickly launch a versatile Telegram bot capable of handling various cryptocurrency transactions.

## Development Technologies

Crafted using procedural PHP version 7+ without reliance on external libraries, TegroTONbot offers seamless integration on any PHP and MySQL supported hosting. This design philosophy ensures ease of customization and scalability, making it an ideal choice for developers and enthusiasts alike.

## Initial Setup and Installation

### System Requirements

For optimal performance, TegroTONbot requires hosting support for PHP 7 and MySQL.

## Detailed Installation Guide for TegroTONbot

### Main Bot Execution Script
The primary script for running the bot is `tgbot.php`.

### Step 1: Configuring `config.php`
Configure your bot's settings by editing the `config.php` file with the necessary user data:
```php
############################
$admin = 00000; // ChatID of a manager, owner
$verifyrecipient = "XXXX"; // TON wallet for getting verification transactions
$reference = 5; // Currently not in use
$depopercent = 5; // Referral percentage for deposits
$NFTRefPercent = 10; // General referral percentage
$wallet2donate = "XXXXX"; // TON wallet for donations
$verifRefFee = 0.05; // Commission to referral from verification payment
$api_key = 'XXX'; // Tegro Money API Key
$roskassa_publickey = 'XXXX'; // Tegro Money Public Key
$roskassa_secretkey = 'XXXX'; // Tegro Money Secret Key
$tegrotonrate = 0.1; // Tegro and CryptoBot commission
$NFTwalletTON = "XXXXX"; // TON Wallet for incoming payments
$nftCatRate = 45; // Currently not in use
$nftDogRate = 65; // Currently not in use
$toncenterAPIKey = "XXXXX"; // API Key of Toncenter website
$CryptoPayAPIToken = ""; // CryptoPay API Token
define('TOKEN', 'XXXXX'); // Bot API Token
############################
```

2. **Integration with Cryptopay:**
   - Set up the bot in Cryptopay by specifying the [Postback URL](https://yourdomain/BotFolder/postback_cryptopay.php).

3. **Tegro Money Account Configuration:**
   - Implement the [Tegro Money Postback URL](https://yourdomain/BotFolder/postback.php) for seamless transactions.

4. **MySQL Database Setup:**
   - Enter relevant database details in the `global.php` file.

5. **Database Structure Initialization:**
   - Import the database structure from the `database.sql` file.

6. **Webhook Configuration for `tgbot.php`:**
   - Establish the webhook [here](https://api.telegram.org/botXXXXX/setWebhook?url=https://yourdomain/BotFolder/tgbot.php).

7. **Bot Text Customization:**
   - Modify the bot's responses in the `langs.php` file.

8. **Daily Rate Checker Script:**
   - Schedule the [TON Rate Checker Script](https://yourdomain/BotFolder/tonratechecker.php) to run daily via cron.

## Operational Guidelines

To interact with TegroTONbot, locate it in Telegram using `@YourBot` and initiate the conversation with `/start`.

## Future Enhancements

Stay informed about planned features and current issues by visiting our [Open Issues page](https://github.com/TGRTON/TegroTONbot/issues).

## Community and Contributions

We thrive on community contributions and value each participant in the open-source ecosystem. Your contributions, suggestions, and feedback are immensely appreciated.

- **Propose Enhancements:** Feel free to suggest changes or new features by [opening an issue](https://github.com/TGRTON/TegroTONbot/issues/new).
- **Attention to Detail:** Please ensure your contributions are well-documented and error-free.
- **Individual PRs:** Create a separate PR for each of your suggestions.
- **Adherence to Conduct:** Review our [Code Of Conduct](https://github.com/TGRTON/TegroTONbot/blob/main/CODE_OF_CONDUCT.md) before contributing.

### Creating A Pull Request

1. Fork the Project.
2. Create a Feature Branch (`git checkout -b feature/AmazingFeature`).
3. Commit Your Changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the Branch (`git push origin feature/AmazingFeature`).
5. Open a Pull Request.

## Licensing Terms

For licensing details, visit the [license page](https://github.com/TGRTON/TegroTONbot/commit/c208edcb519704cc98b2d9835d5ab9fef17b4d4e).

## Project Contributors

- **Lead Developer:** [Lana Cool](https://github.com/lana4cool/) - A seasoned developer specializing in Telegram bots built on PHP.

## Special Acknowledgements

- Heartfelt thanks to [Lana](https://github.com/lana4cool/), whose contributions have been pivotal in the development of TegroTONbot.
