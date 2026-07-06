<?php

namespace App\Helpers;

class SystemData
{
    /**
     * Get list of countries
     *
     * @return array
     */
    public static function getCountries()
    {
        return [
            'VN' => 'Vietnam',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'JP' => 'Japan',
            'SG' => 'Singapore',
            'CA' => 'Canada',
            'FR' => 'France',
            'DE' => 'Germany',
            'HK' => 'Hong Kong',
            'AU' => 'Australia',
            'CN' => 'China',
            'KR' => 'South Korea',
        ];
    }

    /**
     * Get list of standard currencies
     *
     * @return array
     */
    public static function getCurrencies()
    {
        return [
            'VND' => 'Vietnamese Dong',
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'JPY' => 'Japanese Yen',
            'SGD' => 'Singapore Dollar',
            'CAD' => 'Canadian Dollar',
            'AUD' => 'Australian Dollar',
        ];
    }

    /**
     * Get list of Locales
     *
     * @return array
     */
    public static function getLocales()
    {
        return [
            'vi_VN' => 'Vietnamese (Vietnam)',
            'en_US' => 'English (United States)',
            'en_GB' => 'English (United Kingdom)',
            'ja_JP' => 'Japanese (Japan)',
            'zh_CN' => 'Chinese (China)',
            'fr_FR' => 'French (France)',
        ];
    }

    /**
     * Get list of standard Timezones
     *
     * @return array
     */
    public static function getTimezones()
    {
        return [
            'Asia/Ho_Chi_Minh' => 'Asia/Ho_Chi_Minh',
            'Asia/Singapore' => 'Asia/Singapore',
            'Asia/Tokyo' => 'Asia/Tokyo',
            'Europe/London' => 'Europe/London',
            'Europe/Paris' => 'Europe/Paris',
            'America/New_York' => 'America/New_York',
            'America/Los_Angeles' => 'America/Los_Angeles',
            'UTC' => 'UTC',
        ];
    }
}
