# UnamWebPanelPlus v0.3.6

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

## Checklist

A checklist including what is done, what is planned, and a few ideas.

- [x] Add total current hashrate for all algorithms
	- [x] How much each computer mines on each algorithm on average, right below the total hashrate of each algorithm. Also add how many miners are on each algorithm.
	- [x] It is only green if hashrate is more than 0, not if miners is more than 0
- [ ] Statistics
	- [x] Php throws an error if there is not data.
	- [ ] The statistics section in the Plus page can take some time to load with big amounts of data, since most of the computing is done on the server with PHP. Some of it should be done in javascript, it is done in PHP because PHP has much better date and time integration.
	- [x] Save timestamp to database instead of date, because then it is easier to translate to selected date format.
	- [x] Change online and offline meaning in stats, so that online includes everything, except if it has been more than 180 seconds. Online simply means it has connected to the server in the last 3 minutes. Change "Total Online/Offliner Miners" to something like "Total Active/Offline Miners".
	- [x] Also add a total active + idle option in the total extra
	- [x] Optimize statssaver
	- [x] Change so that hourly statistics does not show week number.
	- [x] I need to fix that week is bugged on new year. It says 2023-12-31 is week 1.
	- [x] Optimize the statistics, since almost all the graphs are the exact same code, just a little different.
	- [ ] Add something that allows the user to see how the hashrate or how the miners are when the config gets changed?
	- [x] Save what hidden and shown legends to localStorage. Also what interval is chosen, and what "Miners" is chosen at the Total Algorithms section.
	- [x] Instead of saving all statistics to a json file, save it to the database instead.
	- [x] IMPORTANT: The JSON files are available to whoever visits them directly.
	- [x] Statistics should get updated each time the endpoint is called, but only write to current hour, and overwrite hour if it exists.
	- [x] Save:
		- [x] Date
		- [x] Total Hashrate
		- [x] Total Miners
		- [x] Total Online
		- [x] Total Offline
		- [x] Online Miners
		- [x] Offline Miners
		- [x] Total Active
		- [x] Total Idle
		- [x] Total Starting
		- [x] Total Paused
		- [x] Total Stopped
		- [x] Total Error
		- [x] Total VRAM
		- [x] Total Unknown
- [ ] Configurations
	- [x] Make configurations longer by default, and also add a section that allows the user to see how the configuration should look like and what values are allowed.
	- [ ] Edit configurations so instead of text you get to toggle between options and write in a textbox for each option.
- [ ] Add top processes that pauses the miner.
- [ ] GPU and CPU
	- [ ] Also maybe ranking the top GPUs and CPUs and how much they mine combined, and maybe on average too.
	- [ ] Being able to transform the GPU and CPU charts into a ranking table.
- [ ] Geo
	- [x] If you use cloudflare, you can get the country by reading the header "CF-IPCountry", also need to get their IP from "CF-Connecting-IP", if not it gets cloudflares IP.
	- [x] Optimize endpoint so the json file not always gets called, rather it gets called when the data should be displayed, in the table or in the stats.
	- [x] Save the Countries and Continents as JSON files.
	- [x] Change "Countries" to geo
	- [x] Save countries to database
	- [ ] Add more statistics for geo
		- [x] How many are online from each country and continent
		- [ ] How much each country and continent mines for each algorithm
	- [ ] Add an option that would allow each region to get their own configuration.
- [ ] In the miners tab also add a little toggle button that allows you to toggle between hiding the user or not, because for some reason if the user is hidden then you can see a lot more columns.
- [ ] Which applications cause the most stealth pausing.
- [x] Switching from plus back to statistics panel makes those stats disappear.
- [ ] Add translations for plus and settings.
- [ ] Settings
	- [ ] Should look kinda like chrome settings
	- [ ] Disable the webpanel, in case it gets hacked.
	- [ ] Config section
		- [ ] Password - string
		- [ ] Database file - string
		- [ ] Hashrate history enable - toggle
			- [ ] Hashrate history limit - int (time)
		- [ ] Failed login blocktime - int
		- [ ] Failed login blocktries - int
	- [ ] Automatic offline miners remover
	- [ ] Change timezone, so that the statistics show the correct clock and time.
	- [ ] Lifetime statistics
		- [ ] How often they are saved - int (time) (maybe)
		- [ ] How long they are saved - int (time)
	- [ ] Database
		- [ ] Importing database, so that if someone updates to a newer version with a different type of database, it can import the old data into the new data.
	- [ ] Backup section, backuping statistics file to mega.nz or something similar.
	- [ ] Update section, where you can check for updates from the github, and if there is a new update it updates it automatically.
	- [ ] Advanced tab with some advanced things? Like adding miners manually, to test stuff.
- [ ] Change the responsiveness of the Plus page. Zooming in and out and resizing window and such.
- [ ] Change responsiveness of the statistics page.
- [x] Limit algorithm to 15 characters to avoid getting xss attempts.
- [x] Instead of limiting length to algorithm, add blacklisted words and check for those in the fields.

## Supported Projects

* [SilentCryptoMiner](https://github.com/UnamSanctam/SilentCryptoMiner)

## Changelog
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
