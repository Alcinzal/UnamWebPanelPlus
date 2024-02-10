### PLUS 0.4.0 (2024-02-10)
* Merged this fork with Unams 1.8.0 update.
    * Changed almost all dates to Unix Timestamp (including database dates).
    * Changed all charts to display date in 24-hour format.
    * Integrated cloudflare support.
    * Integrated plus page.
    * Integrated total hashrate functionality.
    * Integrated statistics functionality.
        * Integrated statistics table to database.
        * Removed tooltips in the statistics viewer.
    * Integrated geolocation functionality.
        * Integrated country column to database
    * Did not integrate my previous configuration modifications as I plan to redesign this entire page in the future.
    * Did not integrate my previous XSS prevention attempts as 1.8.0 renders it obsolete.
* Added some branding/identity to my plus fork (for fun).
    * Added a more detailed drawing of Unam.
    * Changed "UnamWebPanel" to "UnamWebPanel+".
* Changed terminology: "Active" -> "Mining (Active)", "Idle" -> "Mining (Idle)", Both of these combined -> "Mining".
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
* Added Countries section.