# iranian-holidays
Checks whether a certain date in Iran is an official holiday or not



## installation 
`` composer required mndco/iranian-holidays ``

## usage
```
<?php
include "vendor/autoload.php";

use MNDCo\IranianHoliday\IranianHoliday;

$holiday = new IranianHoliday();
$date = "1399-07-26";
if($holiday->checkIsHoliday($date))
    var_dump($date . $holiday->getHolidayTitle($date));

```

## Credits
The source of information of this package is the website of the Calendar Center of the Institute of Geophysics, University of Tehran ( [Calendar.ut.ac.ir](https://calendar.ut.ac.ir/Fa/) )


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.