# UnamWebPanelPlus v0.2.0

This is a modified version of Unam's [UnamWebPanel](https://github.com/UnamSanctam/UnamWebPanel).

## What is different?

### General

* Added the XSS patch from https://github.com/UnamSanctam/UnamWebPanel/issues/317#issuecomment-1884683799
* Database is different, includes a statistics table and a country column in the miners table.

### Miners
* Added a country column

### Configurations
* Increased the height of the configurations boxes.
* Added an examples box, so users can easily view how a configuration should look like. (Might be missing some options)

### Plus
* Added Plus page
* Total hashrate section. View the current hashrates, current miners for each hashrate, and average hashrate per miner.
* Statistics sections. Here you can view hourly data for Hashrate, Total Miners, Total Online miners, etc. You can also choose between viewing the data hourly, daily, weekly or monthly.
* Countries section. Here you get to view a pie chart of the amount of miners from each country.

### Video

I have a video showcasing the panel. Statistics pages is unchanged. The IP addresses shown in the video are fake, it's only testing data. Here is the video, sorry for the flickering:
(this video is outdated)

https://github.com/Alcinzal/UnamWebPanelPlus/assets/153958388/03144cbe-210d-443a-a33c-0dafddeb7eec

## Why/How?

### Statistics
A PHP file that saves the statistics gets called each time the endpoint gets called. This saves the current data to the database. The PHP file will write the data to the current hour, meaning that data saved 12:03 will get overwritten by data saved 12:50. This is to prevent large buildups of data. So I guess one could say that the data gets saved hourly. When choosing the intervals between daily, weekly and monthly, it simply takes the values and gets the average of them, it also saves the chosen interval and hidden/shown legends(labels) to local storage.

### Countries
To find which country the miners are from, https://country.is/ is used. Usage is simply: https://api.country.is/8.8.8.8. The country only gets checked if the miner does not exist in the database, aka it's a new miner.

## What needs to be worked on?

### Bugs
* When visting between the Plus page and the Statistics page, the charts at Statistics disappear until you refresh the page.
* The charts at the plus statistics section will sometimes say that the end of the year is week 1, but it should say week 52 or 53.
* The charts dont update when resizing the browser window.
* ~~IMPORTANT: The JSON files are available to whoever visits them directly.~~ 

### Ideas
Here are some ideas, brainstorming I guess, so some of them might be dumb or useless.
* ~~Instead of saving all statistics to a json file, save it to the database instead.~~
* Add settings section, where one can change the config.php file from the panel (might pose a security risk).
* Add another settings section where the user can adjust how often or for how long the plusStats gets saved.
* Being able to disable the WebPanel in case it gets hacked, so the miners just use their chosen settings instead of the Panels settings. However if the WebPanel is hacked I suppose the hacker could just enable it again anyways haha.
* Offline miners being removed automatically after a chosen period of time.
* Add backuping, being able to backup the database file (and statistics json files) every now and then to another server, or to a cloud service like MEGA.io or Google drive. This way if you get banned for whatever reason, you still have your database and statistics files.
* ~~Save which legends are hidden or shown at the statistics section.~~
* Adding a table to the countries section, so I can view more details on each country, like how many miners, what the total hashrate is for each country for each algorithm. Maybe also add a region table/chart.
* Automatically choosing a configuration based on where the miner is based. Don't know how important this would be, but I think I saw someone suggesting it at Unam's Github. 
* Adding translation for the different languages at the Plus section.
* Add a chart or table that shows which processes cause the most stealth pausing.
* Add a chart or table for top online GPUs and CPUs and how much they mine.
* Maybe some sort of indicator in the statistics chart that shows when you changed the configuration, this way you can sort of optimize your configuration by testing different configs and then look at the statistics when you had the results you're looking for.
* Adding an advanced page, where you can add miners/data to the database, for testing.
* Adding a update section, where you can check for updates from the github, and if there is a new update it updates it automatically.

Here you can see a screenshot of how it might look below the countries section, if I ever complete it.
![image](https://github.com/Alcinzal/UnamWebPanelPlus/assets/153958388/36d8684d-bdac-43dc-b569-a2dcc3ad7d2f)


## Supported Projects

* [SilentCryptoMiner](https://github.com/UnamSanctam/SilentCryptoMiner)

## Changelog
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
### 1.7.1 (06/01/2023)
* Moved miner statistics to a new "Statistics" page
* Added more statistics such as GPU, CPU, Version and Algorithm graphs
* Fixed "Hide Offline Miners" bug
* Reworked endpoint again for better performance
* Added inactive journal size limit and higher cache limit
* Reduced WAL file growth and added cleaning
* Changed SQLite synchronous mode to OFF for higher performance
* Added Spanish translation (Xeneht)
### 1.7.0 (25/12/2022)
* Greatly improved database performance
* Greatly improved endpoint performance
* Added configurable hashrate history feature
* Added "Total Hashrate" graphs for each algorithm
* Added individual "Hashrate History" to each miner
* Added miner status statistics
* Fixed datatable width scaling
* Added "Hide Offline Miners" option
* Fixed status priority for offline and error statuses
* Added Russian translation (marat2509)
* Added Ukrainian translation (Zem0rt)
### 1.6.0 (01/06/2022)
* Added support for reporting the executable name of the program that triggered "Stealth" and displaying it in the status text
* Added offline miner removal tool which removes miners who have been offline for longer than the chosen number of days
* Added support for new miner ID per build to allow for running multiple miners of the same type at the same time
* Added Polish translation (Werlrlivx)
* Changed database settings to allow for better performance during large amounts of activity
* Changed offline status time threshold from five minutes to three minutes
* Changed endpoint text when the request isn't from the miner to reduce confusion
* Changed string sanitation away from FILTER_SANITIZE_STRING due to PHP 8.1 deprication
* Moved database to its own folder to allow for broader database file blocks
### 1.5.0 (01/05/2022)
* Added new field "Version" that shows the miner version
* Added new field "Active Window" that shows the currently active foreground windows title
* Added new field "Run Time" that shows how long the current session of the miner has been running for
* Added "First Connection" field that shows the date and time when the miner first connected
* Added new miner statuses "Starting" and "Error"
* Added text next to the "Offline" status that shows how long the miner has been offline
* Added error text when an XMR miner cannot connect to its pool
* Added German and French datatable translation files
* Fixed miner table ordering

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
