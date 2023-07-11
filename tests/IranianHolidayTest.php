<?php

namespace MNDCo\IranianHoliday\Test;

use MNDCo\IranianHoliday\IranianHoliday;
use PHPUnit\Framework\TestCase;

class IranianHolidayTest extends TestCase
{

    public function test22BahmanIsHoliday()
    {
        $iranian_holiday = new IranianHoliday();
        $this->assertTrue($iranian_holiday->checkIsHoliday('1400-11-22'));
    }

    public function test17Mehr1399IsHoliday()
    {
        $iranian_holiday = new IranianHoliday();
        $this->assertTrue($iranian_holiday->checkIsHoliday('1399-07-17'));
    }

    public function test17Mehr1399IsArbaeen()
    {
        $iranian_holiday = new IranianHoliday();
        $this->assertEquals($iranian_holiday->getHolidayTitle('1399-07-17'), "اربعین حسینی");
    }
    public function test5Mehr1400IsArbaeen()
    {
        $iranian_holiday = new IranianHoliday();
        $this->assertEquals($iranian_holiday->getHolidayTitle('1400-07-05'), "اربعین حسینی");
    }
    public function test26Shahrivar1401IsArbaeen()
    {
        $iranian_holiday = new IranianHoliday();
        $this->assertEquals($iranian_holiday->getHolidayTitle('1401-06-26'), "اربعین حسینی");
    }
    public function test15Shahrivar1402IsArbaeen()
    {
        $iranian_holiday = new IranianHoliday();
        $this->assertEquals($iranian_holiday->getHolidayTitle('1402-06-15'), "اربعین حسینی");
    }

}