//Format Date to ISO
function formatDate(inputDate) {
    var year = inputDate.getFullYear();
    var month = ('0' + (inputDate.getMonth() + 1)).slice(-2); // Month is zero-based, so add 1
    var day = ('0' + inputDate.getDate()).slice(-2);
    var hours = ('0' + inputDate.getHours()).slice(-2);
    var minutes = '00';
    var seconds = '00';
    var week = inputDate.getWeek();
    if(week >= 0 && week <= 9) {
        week = "0" + week;
    }
    var inputDate = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds + ' ' + week;
    return inputDate
}

//Add hours to date
Date.prototype.addHours = function (h) {
    this.setTime(this.getTime() + (h * 60 * 60 * 1000));
    return this;
}

//Get week
Date.prototype.getWeek = function (dowOffset) {
    dowOffset = typeof (dowOffset) == 'number' ? dowOffset : 0; //default dowOffset to zero
    var newYear = new Date(this.getFullYear(), 0, 1);
    var day = newYear.getDay() - dowOffset; //the day of week the year begins on
    day = (day >= 0 ? day : day + 7);
    var daynum = Math.floor((this.getTime() - newYear.getTime() -
        (this.getTimezoneOffset() - newYear.getTimezoneOffset()) * 60000) / 86400000) + 1;
    var weeknum;
    //if the year starts before the middle of a week
    if (day < 4) {
        weeknum = Math.floor((daynum + day - 1) / 7) + 1;
        if (weeknum > 52) {
            nYear = new Date(this.getFullYear() + 1, 0, 1);
            nday = nYear.getDay() - dowOffset;
            nday = nday >= 0 ? nday : nday + 7;
            /*if the next year starts before the middle of
              the week, it is week #1 of that year*/
            weeknum = nday < 4 ? 1 : 53;
        }
    }
    else {
        weeknum = Math.floor((daynum + day - 1) / 7);
    }
    return weeknum;
};