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
* Added Countries section.
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
### v1.4.2 (01/04/2022)
* Added French translation (Kolhax)
* Added German translation (leisefuxX)
### v1.4.1 (11/01/2022)
* Fixed null hashrate datatable formatting error
* Changed project versioning to x.x.x formatting
### v1.4.0 (09/01/2022)
* Added functionality to remove miners from the list
* Added JSON validation functionality to warn when saving incorrect configurations
* Added username display into the miner list
* Added "Auto refresh" toggle button for automatic miner list refreshing
* Added robots.txt file to stop search engines from indexing the web panel
* Added directory listing block in .htaccess for better privacy
* Added previously ignored "Logs" folder back
* Changed "Default" configuraiton into "Default ethminer" and "Default xmrig" configurations to allow different default configurations for the two different miners
* Fixed possible database "corruption" when null hashrates were submitted
* Fixed broken miner searching and sorting
### v1.3.0 (09/11/2021)
* Added Unique ID generation on the panel side instead of the miner side
* Changed all file calls to be relative to allow easier deployment of the panel in subfolders
* Removed unnecessary configuration options due to everything being relative now
### v1.2.0 (09/11/2021)
* Added GPU and CPU to the miners datatable
* Added GPU and CPU to the database
### v1.1.0 (09/11/2021)
* Added unamwebpanel.db into the .htaccess and web.config files as a forbidden path to secure the SQLite database on Apache and IIS servers without having to place the database in a non-public folder
* Removed recommendation to move the database file to a non-public folder due to the added protection files for Apache and IIS
* Downgraded web panels required PHP version to 7.0
* Added miner type to the miners datatable to make it easier to differentiate what base miner it is using
* Fixed broken miner status condition
### v1.0.0 (08/11/2021)
* Initial release