<?php

namespace MNDCo\IranianHoliday;

use Carbon\Carbon;
use Davincho\Tabula\Tabula;
use GeniusTS\HijriDate\Hijri;
use Morilog\Jalali\Jalalian;

class IranianHoliday
{
    public $holidays_json_file = '/../holidays/data.json';
    public $jalali_holidays = [
        1   => "عید نوروز",
        2   => "عید نوروز",
        3   => "عید نوروز",
        4   => "عید نوروز",
        12  => "روز جمهوری اسلامی",
        13  => "روز طبیعت",
        76  => "رحلت امام خمینی",
        77  => "قیام خونین ۱۵ خرداد",
        328 => "پیروزی انقلاب اسلامی",
        365 => "روز ملی شدن صنعت نفت"
    ];

    public $hijri_holidays = [
        "7/13"  => "ولادت امام علی علیه السلام و روز پدر",
        "8/15"  => "ولادت حضرت قائم عجل الله تعالی فرجه و جشن نیمه شعبان",
        "7/27"  => "مبعث رسول اکرم",
        "9/21"  => "شهادت حضرت علی علیه السلام",
        "10/1"  => "عید سعید فطر",
        "10/2"  => "تعطیل به مناسبت عید سعید فطر",
        "11/11" => "ولادت حضرت امام رضا (ع)",
        "10/25" => "شهادت امام جعفر صادق علیه السلام",
        "12/10" => "عید سعید قربان",
        "12/18" => "عید سعید غدیر خم",
        "1/9"   => "تاسوعای حسینی",
        "1/10"  => "عاشورای حسینی",
        "2/20"  => "اربعین حسینی",
        "2/28"  => "رحلت رسول اکرم؛شهادت امام حسن مجتبی علیه السلام",
        "2/30"  => "شهادت امام رضا علیه السلام",
        "3/8"   => "شهادت امام حسن عسکری علیه السلام",
        "3/17"  => "میلاد رسول اکرم و امام جعفر صادق علیه السلام",
        "6/3"   => "شهادت حضرت فاطمه زهرا سلام الله علیها"
    ];

    public function checkIsHoliday($date)
    {
        return (bool)$this->getHolidayTitle($date);

    }

    public function getHolidayTitle($date)
    {
        return $this->getJalaliHolidayTitle($date) ?: $this->getHijriHolidayTitle($date);
    }

    public function getJalaliHolidayTitle($date)
    {
        $jalalian_date_time = Jalalian::fromFormat('Y-m-d', $date);
        $jalali_day_of_week = $jalalian_date_time->getDayOfYear();
        if (isset($this->jalali_holidays[$jalali_day_of_week]))
            return $this->jalali_holidays[$jalali_day_of_week];
        return false;
    }

    public function getHijriHolidayTitle($date)
    {
        $jalalian_date_time = Jalalian::fromFormat('Y-m-d', $date);
        $greg_date_time = $jalalian_date_time->toCarbon();

        $arabic_date_time = Hijri::convertToHijri($greg_date_time->format('Y-m-d'));


        $holy_days = json_decode(file_get_contents(__DIR__ . $this->holidays_json_file), true);

        if (!@$holy_days[$jalalian_date_time->getYear()] && $jalalian_date_time->getYear() <= jdate(Carbon::now())->getYear() + 1 && $jalalian_date_time->getYear() >= 1369) {
            $full_array = [];

            $file_name = "Data%5Choliday-" . $jalalian_date_time->getYear() . ".pdf";
            if ($c = file_get_contents("https://calendar.ut.ac.ir/Fa/Tyear/" . $file_name)) {
                file_put_contents($file_name, $c);
            }

            if (!file_exists($file_name)) {
                return @$this->hijri_holidays[$arabic_date_time->month . "/" . $arabic_date_time->day];
            }

            $pdf = new Tabula($file_name);

            $content = $pdf->parse();
            unlink($file_name);


            foreach (explode("\n", $content) as $key => $row) {
                if ($key != 0) {
                    $cols = explode(',', $row);
                    if (@$cols[1] && $cols[2]) {

                        $date = $this->fix_jalali_months($cols[2]);
                        $date = str_replace(" ", "-", $jalalian_date_time->getYear() . " " . $date);
                        $jdate_array = explode('-', $date);

                        foreach ($jdate_array as $key => $ss)
                            if ($key !== 0)
                                $jdate_array[$key] = str_pad($jdate_array[$key], 2, 0, STR_PAD_LEFT);


                        $jdate = Jalalian::fromFormat('Y-m-d', implode($jdate_array, '-'));
                        $hdate_array = $this->fix_hijri_months($cols[1]);


                        $hdate = $hdate_array[1] . "/" . $hdate_array[2];
                        $full_array[$jdate->getYear()][$jdate->getDayOfYear()] = @$this->hijri_holidays[$hdate];
                    }
                }

            }
            foreach ($full_array as $key => $data)
                $holy_days[$key] = $data;

            file_put_contents(__DIR__ . $this->holidays_json_file, json_encode($holy_days));
        }


        if ($holiday_title = @$holy_days[$jalalian_date_time->getYear()][$jalalian_date_time->getDayOfYear()])
            return $holiday_title;

        if ($jalalian_date_time->getYear() > jdate(Carbon::now())->getYear() + 1 || $jalalian_date_time->getYear() < 1369) {
            if ($holiday_title = @$this->hijri_holidays[$arabic_date_time->format('n/d')])
                return $holiday_title;
        }


        return false;
    }


    protected function fix_hijri_months($string)
    {
        $ro = preg_replace('/\s+/', ' ', $string);

        $ro = str_replace("مﺮﺤﻣ", "1", $ro);
        $ro = str_replace("ﺮﻔﺻ", "2", $ro);
        $ro = str_replace("لوﻻا‌ ﻊﻴﺑر", "3", $ro);
        $ro = str_replace("لوﻻا ﻊﻴﺑر", "3", $ro);
        $ro = str_replace("لوﻻاﻊ ﻴﺑر", "3", $ro);
        $ro = str_replace("لوﻻا ﻊﯿﺑر", "3", $ro);
        $ro = str_replace("1422ﻲﻧﺎﺜﻟا يدﺎﻤﺟ", "1422 6", $ro);
        $ro = str_replace("ﻪﻴﻧﺎﺜﻟا يدﺎﻤﺟ", "6", $ro);
        $ro = str_replace("ﺔﯿﻧﺎﺜﻟاي دﺎﻤﺟ", "6", $ro);
        $ro = str_replace("ﺔﻴﻧﺎﺜﻟاي دﺎﻤﺟ", "6", $ro);
        $ro = str_replace("ﺔﻴﻧﺎﺜﻟا يدﺎﻤﺟ", "6", $ro);
        $ro = str_replace("ﻲﻧﺎﺜﻟا يدﺎﻤﺟ", "6", $ro);
        $ro = str_replace("ﻲﻧﺎﺜﻟاي دﺎﻤﺟ", "6", $ro);
        $ro = str_replace("ﺐﺟر", "7", $ro);
        $ro = str_replace("نﺎﺒﻌﺷ", "8", $ro);
        $ro = str_replace("نﺎﻀﻣر", "9", $ro);
        $ro = str_replace("لاﻮﺷ", "10", $ro);
        $ro = str_replace("هﺪﻌﻘﻟا يذ", "11", $ro);
        $ro = str_replace("هﺪﻌﻘﻳذ", "11", $ro);
        $ro = str_replace("ﻪﺠﺤﻟا يذ", "12", $ro);
        $ro = str_replace("ﺮﺧآ", "30", $ro);
        $date = str_replace(" ", "-", $ro);
        return explode('-', $date);
    }

    protected function fix_jalali_months($string)
    {
        $ro = preg_replace('/\s+/', ' ', $string);
        $ro = str_replace("ﻦﻳدروﺮﻓ", "1", $ro);
        $ro = str_replace("ﺖﺸﻬﺒﯾدرا", "2", $ro);
        $ro = str_replace("ﺖﺸﻬﺒﻳدرا", "2", $ro);
        $ro = str_replace("دادﺮﺧ", "3", $ro);
        $ro = str_replace("ﺮﻴﺗ", "4", $ro);
        $ro = str_replace("ﺮﯿﺗ", "4", $ro);
        $ro = str_replace("دادﺮﻣ", "5", $ro);
        $ro = str_replace("رﻮﻳﺮﻬﺷ", "6", $ro);
        $ro = str_replace("رﻮﯾﺮﻬﺷ", "6", $ro);
        $ro = str_replace("ﺮﻬﻣ", "7", $ro);
        $ro = str_replace("نﺎﺑآ", "8", $ro);
        $ro = str_replace("رذآ", "9", $ro);
        $ro = str_replace("يد", "10", $ro);
        $ro = str_replace("ﻦﻤﻬﺑ", "11", $ro);
        return str_replace("ﺪﻨﻔﺳا", "12", $ro);
    }
}