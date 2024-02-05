# UnamWebPanelPlus v0.3.7

A modified version of Unam's [UnamWebPanel](https://github.com/UnamSanctam/UnamWebPanel).

## Differences

* Lifetime statistics - Statistics are saved every hour forever. You can view the statistics and toggle between viewing the data hourly, daily, weekly or monthly.
* Geo - See which country and continent your miners are from in a pie chart. Country is also visible in the datatable.
* Total Hashrate - An overview of each algorithm, and their total hashrate, online miners and their average hashrate.
* Example configurations - View examples in the configurations page to better understand what settings are available. (In the future the whole configuration page will get reworked to ensure ease of use)
* Cloudflare support - If you use Cloudflare as a DNS server, the panel will still get the correct IP address and country code, from the headers 'CF-Connecting-IP' and 'CF-IPCountry'.
* Better timezones support - All dates are saved as Unix Timestamps in the database, ensuring that dates stay true no matter what timezone. (In the future you will be able to manually select timezone on a settings page).
* Fight off XSS attacks - A simple system that will simply ignore a endpoint call if it contains any blacklisted strings.
* More to come!

## Supported Projects

* [SilentCryptoMiner](https://github.com/UnamSanctam/SilentCryptoMiner)

## Changelog
### PLUS 0.3.7 (2024-02-05)
* Fixed XSS string checking.
### PLUS 0.3.6 (2024-02-02)
* Replaced character limit with blacklisted string checking, to better fight off XSS attacks.
### PLUS 0.3.5 (2024-01-31)
* Added character limit to algorithms so XSS attempts do not go through.
* Fixed small bug where statistics would throw an error if there was no statistics data.
* Changed total hashrate cards so yellow means active miners but no hashrate, and green means active miners with hashrate.
### PLUS 0.3.0 (2024-01-23)
* Changed dates in database to Unix Timestamp for better compatibility with changing timezones.
* Added Cloudflare support. Checks for headers 'CF-Connecting-IP' and 'CF-IPCountry'.
* Changed formatting in unamtChart to use a 24 hour clock instead of 12 hour.
* Changed countries to Geo.
* Added continents to Geo.
* Saved country names and continent names as json files, to prevent calling a static json from another server (country.io).
* Optimized the Plus statsSaver file.
* Changed the definition of online and offline miners. Offline meaning no connection for 180 seconds, and online meaning the opposite, no matter the status.
* Optimized the displaying of statistics at the Plus page.
* Fixed bug where 2023-12-31 was week 1. Now it is correct, showing week 53.
### PLUS 0.2.5 (2024-01-16)
* Fixed bug where statistics graphs would disappear.
* Fixed the charts not updating when the resizing the browser.
* Optimized code a little to not call the same files multiple times.
* Fixed non-existent file being called.
### PLUS 0.2.0 (2024-01-15)
* Greatly improved statistics. Fully reworked, no more json files, everything happens directly in the database.
* Statistics will now also save hidden or shown legends (labels) to local storage.
* Improved countries. New miners gets their ip checked, and the country gets added to the database.
### PLUS 0.1.0 (2024-01-14)
* Added the XSS patch by UNAM
* Increased the height of the configurations boxes.
* Added an examples configurations box.
* Added Plus page
* Added total hashrate section.
* Added statistics section. 
* Added countries section.

[You can view the full Changelog here](CHANGELOG.md)

## Plus Author

* **Alcinzal**

## Original Author

* **Unam Sanctam**

## Contributors

* **[Kolhax](https://github.com/Kolhax)** - French Translation
* **[leisefuxX](https://github.com/leisefuxX)** - German Translation
* **[Werlrlivx](https://github.com/Werlrlivx)** - Polish Translation
* **[marat2509](https://github.com/marat2509)** - Russian Translation
* **[Zem0rt](https://github.com/Zem0rt)** - Ukrainian Translation
* **[Xeneht](https://github.com/Xeneht)** - Spanish Translation

## Disclaimer

I, the creator, am not responsible for any actions, and or damages, caused by this software.

You bear the full responsibility of your actions and acknowledge that this software was created for educational purposes only.

This software's main purpose is NOT to be used maliciously, or on any system that you do not own, or have the right to use.

By using this software, you automatically agree to the above.

## License

This project is licensed under the MIT License - see the [LICENSE](/LICENSE) file for details

## Donate (UNAM)

XMR: 8BbApiMBHsPVKkLEP4rVbST6CnSb3LW2gXygngCi5MGiBuwAFh6bFEzT3UTufiCehFK7fNvAjs5Tv6BKYa6w8hwaSjnsg2N

BTC: bc1q26uwkzv6rgsxqnlapkj908l68vl0j753r46wvq

ETH: 0x40E5bB6C61871776f062d296707Ab7B7aEfFe1Cd

ETC: 0xd513e80ECc106A1BA7Fa15F1C590Ef3c4cd16CF3

RVN: RFsUdiQJ31Zr1pKZmJ3fXqH6Gomtjd2cQe

LINK: 0x40E5bB6C61871776f062d296707Ab7B7aEfFe1Cd

DOGE: DNgFYHnZBVLw9FMdRYTQ7vD4X9w3AsWFRv

LTC: Lbr8RLB7wSaDSQtg8VEgfdqKoxqPq5Lkn3
