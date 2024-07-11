<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateConverter
{

    public string $debug_info = "";
    protected array $year_np_eq_to_english = [
        2100 => '14-Apr-2043',
        2099 => '14-Apr-2042',
        2098 => '14-Apr-2041',
        2097 => '13-Apr-2040',
        2096 => '15-Apr-2039',
        2095 => '14-Apr-2038',
        2094 => '14-Apr-2037',
        2093 => '14-Apr-2036',
        2092 => '13-Apr-2035',
        2091 => '14-Apr-2034',
        2090 => '14-Apr-2033',
        2089 => '14-Apr-2032',
        2088 => '15-Apr-2031',
        2087 => '14-Apr-2030',
        2086 => '14-Apr-2029',
        2085 => '13-Apr-2028',
        2084 => '14-Apr-2027',
        2083 => '14-Apr-2026',
        2082 => '14-Apr-2025',
        2081 => '13-Apr-2024',
        2080 => '14-Apr-2023',
        2079 => '14-Apr-2022',
        2078 => '14-Apr-2021',
        2077 => '13-Apr-2020',
        2076 => '14-Apr-2019',
        2075 => '14-Apr-2018',
        2074 => '14-Apr-2017',
        2073 => '13-Apr-2016',
        2072 => '14-Apr-2015',
        2071 => '14-Apr-2014',
        2070 => '14-Apr-2013',
        2069 => '13-Apr-2012',
        2068 => '14-Apr-2011',
        2067 => '14-Apr-2010',
        2066 => '14-Apr-2009',
        2065 => '13-Apr-2008',
        2064 => '14-Apr-2007',
        2063 => '14-Apr-2006',
        2062 => '14-Apr-2005',
        2061 => '13-Apr-2004',
        2060 => '14-Apr-2003',
        2059 => '14-Apr-2002',
        2058 => '14-Apr-2001',
        2057 => '13-Apr-2000',
        2056 => '14-Apr-1999',
        2055 => '14-Apr-1998',
        2054 => '13-Apr-1997',
        2053 => '13-Apr-1996',
        2052 => '14-Apr-1995',
        2051 => '14-Apr-1994',
        2050 => '13-Apr-1993',
        2049 => '13-Apr-1992',
        2048 => '14-Apr-1991',
        2047 => '14-Apr-1990',
        2046 => '13-Apr-1989',
        2045 => '13-Apr-1988',
        2044 => '14-Apr-1987',
        2043 => '14-Apr-1986',
        2042 => '13-Apr-1985',
        2041 => '13-Apr-1984',
        2040 => '14-Apr-1983',
        2039 => '14-Apr-1982',
        2038 => '13-Apr-1981',
        2037 => '13-Apr-1980',
        2036 => '14-Apr-1979',
        2035 => '14-Apr-1978',
        2034 => '13-Apr-1977',
        2033 => '13-Apr-1976',
        2032 => '14-Apr-1975',
        2031 => '14-Apr-1974',
        2030 => '13-Apr-1973',
        2029 => '13-Apr-1972',
        2028 => '14-Apr-1971',
        2027 => '14-Apr-1970',
        2026 => '13-Apr-1969',
        2025 => '13-Apr-1968',
        2024 => '14-Apr-1967',
        2023 => '13-Apr-1966',
        2022 => '13-Apr-1965',
        2021 => '13-Apr-1964',
        2020 => '14-Apr-1963',
        2019 => '13-Apr-1962',
        2018 => '13-Apr-1961',
        2017 => '13-Apr-1960',
        2016 => '14-Apr-1959',
        2015 => '13-Apr-1958',
        2014 => '13-Apr-1957',
        2013 => '13-Apr-1956',
        2012 => '14-Apr-1955',
        2011 => '13-Apr-1954',
        2010 => '13-Apr-1953',
        2009 => '13-Apr-1952',
        2008 => '14-Apr-1951',
        2007 => '13-Apr-1950',
        2006 => '13-Apr-1949',
        2005 => '13-Apr-1948',
        2004 => '14-Apr-1947',
        2003 => '13-Apr-1946',
        2002 => '13-Apr-1945',
        2001 => '13-Apr-1944',
        2000 => '14-Apr-1943',
        1999 => '13-Apr-1942',
        1998 => '13-Apr-1941',
        1997 => '13-Apr-1940',
        1996 => '13-Apr-1939',
        1995 => '13-Apr-1938',
        1994 => '13-Apr-1937',
        1993 => '13-Apr-1936',
        1992 => '13-Apr-1935',
        1991 => '13-Apr-1934',
        1990 => '13-Apr-1933',
        1989 => '13-Apr-1932',
        1988 => '13-Apr-1931',
        1987 => '13-Apr-1930',
        1986 => '13-Apr-1929',
        1985 => '13-Apr-1928',
        1970 => '13-Apr-1913',
        1971 => '13-Apr-1914',
        1972 => '13-Apr-1915',
        1973 => '13-Apr-1916',
        1974 => '13-Apr-1917',
        1975 => '12-Apr-1918',
        1976 => '13-Apr-1919',
        1977 => '13-Apr-1920',
        1978 => '13-Apr-1921',
        1979 => '13-Apr-1922',
        1980 => '13-Apr-1923',
        1981 => '13-Apr-1924',
        1982 => '13-Apr-1925',
        1983 => '13-Apr-1926',
        1984 => '13-Apr-1927'
    ];
    private array $bs = [
        0 => [1970, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        1 => [1971, 31, 31, 32, 31, 32, 30, 30, 29, 30, 29, 30, 30],
        2 => [1972, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 30],
        3 => [1973, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31],
        4 => [1974, 31, 31, 32, 30, 31, 31, 30, 29, 30, 29, 30, 30],
        5 => [1975, 31, 31, 32, 32, 30, 31, 30, 29, 30, 29, 30, 30],
        6 => [1976, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        7 => [1977, 30, 32, 31, 32, 31, 31, 29, 30, 29, 30, 29, 31],
        8 => [1978, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        9 => [1979, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        10 => [1980, 30, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        11 => [1981, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 30, 30],
        12 => [1982, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        13 => [1983, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        14 => [1984, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        15 => [1985, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 30, 30],
        16 => [1986, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        17 => [1987, 31, 32, 31, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        18 => [1988, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        19 => [1989, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30],
        20 => [1990, 30, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        21 => [1991, 31, 32, 31, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        22 => [1992, 31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 30],
        23 => [1993, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30],
        24 => [1994, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30],
        25 => [1995, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30],
        26 => [1996, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30],
        27 => [1997, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30,],
        28 => [1998, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30],
        29 => [1999, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30],
        30 => [2000, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 29, 31],
        31 => [2001, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        32 => [2002, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        33 => [2003, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        34 => [2004, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31],
        35 => [2005, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        36 => [2006, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        37 => [2007, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        38 => [2008, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 29, 31],
        39 => [2009, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        40 => [2010, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        41 => [2011, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        42 => [2012, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 30, 30],
        43 => [2013, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        44 => [2014, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        45 => [2015, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        46 => [2016, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 30, 30],
        47 => [2017, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        48 => [2018, 31, 32, 31, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        49 => [2019, 31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31],
        50 => [2020, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30],
        51 => [2021, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        52 => [2022, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 30],
        53 => [2023, 31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31],
        54 => [2024, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30],
        55 => [2025, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        56 => [2026, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        57 => [2027, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31],
        58 => [2028, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        59 => [2029, 31, 31, 32, 31, 32, 30, 30, 29, 30, 29, 30, 30],
        60 => [2030, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        61 => [2031, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31],
        62 => [2032, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        63 => [2033, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        64 => [2034, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        65 => [2035, 30, 32, 31, 32, 31, 31, 29, 30, 30, 29, 29, 31],
        66 => [2036, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        67 => [2037, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        68 => [2038, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        69 => [2039, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 30, 30],
        70 => [2040, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        71 => [2041, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        72 => [2042, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        73 => [2043, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 30, 30],
        74 => [2044, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        75 => [2045, 31, 32, 31, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        76 => [2046, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        77 => [2047, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30],
        78 => [2048, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        79 => [2049, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 30],
        80 => [2050, 31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31],
        81 => [2051, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30],
        82 => [2052, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        83 => [2053, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 30],
        84 => [2054, 31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31],
        85 => [2055, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        86 => [2056, 31, 31, 32, 31, 32, 30, 30, 29, 30, 29, 30, 30],
        87 => [2057, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        88 => [2058, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31],
        89 => [2059, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        90 => [2060, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        91 => [2061, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        92 => [2062, 30, 32, 31, 32, 31, 31, 29, 30, 29, 30, 29, 31],
        93 => [2063, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        94 => [2064, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        95 => [2065, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        96 => [2066, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 29, 31],
        97 => [2067, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        98 => [2068, 31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        99 => [2069, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        100 => [2070, 31, 31, 31, 32, 31, 31, 29, 30, 30, 29, 30, 30],
        101 => [2071, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        102 => [2072, 31, 32, 31, 32, 31, 30, 30, 29, 30, 29, 30, 30],
        103 => [2073, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 31],
        104 => [2074, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30],
        105 => [2075, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        106 => [2076, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 30],
        107 => [2077, 31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31],
        108 => [2078, 31, 31, 31, 32, 31, 31, 30, 29, 30, 29, 30, 30],
        109 => [2079, 31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
        110 => [2080, 31, 32, 31, 32, 31, 30, 30, 30, 29, 29, 30, 30],
        111 => [2081, 31, 31, 32, 32, 31, 30, 30, 30, 29, 30, 30, 30],
        112 => [2082, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30],
        113 => [2083, 31, 31, 32, 31, 31, 30, 30, 30, 29, 30, 30, 30],
        114 => [2084, 31, 31, 32, 31, 31, 30, 30, 30, 29, 30, 30, 30],
        115 => [2085, 31, 32, 31, 32, 30, 31, 30, 30, 29, 30, 30, 30],
        116 => [2086, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30],
        117 => [2087, 31, 31, 32, 31, 31, 31, 30, 30, 29, 30, 30, 30],
        118 => [2088, 30, 31, 32, 32, 30, 31, 30, 30, 29, 30, 30, 30],
        119 => [2089, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30],
        120 => [2090, 30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30]
    ];
    private array $nep_date = array('year' => '', 'month' => '', 'date' => '', 'day' => '', 'nmonth' => '', 'num_day' => '');
    private array $eng_date = array('year' => '', 'month' => '', 'date' => '', 'day' => '', 'emonth' => '', 'num_day' => '');

    /**
     * Calculates whether english year is leap year or not
     *
     * @param integer $year
     * @return boolean
     */
    public function is_leap_year(int $year): bool
    {
        $a = $year;
        if ($a % 100 == 0) {
            if ($a % 400 == 0) {
                return true;
            } else {
                return false;
            }

        } else {
            if ($a % 4 == 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getNepaliDateToday(): array|bool
    {
        $today = date("Y-m-d");
        $today = explode("-", $today);
        return $this->toNepali($today[0], $today[1], $today[2]);

    }

    /**
     * @description currently can only calculate the date between AD 1944-2033...
     * @param $yy
     * @param $mm
     * @param $dd
     * @return array|bool
     */
    public function toNepali($yy, $mm, $dd): array|bool
    {
        $lookupNearestAdDate = null;
        $month_day = [];
        $input_date = Carbon::parse($yy . '-' . $mm . '-' . $dd);

        $equivalentNepaliYear = 1970;
        foreach ($this->year_np_eq_to_english as $key => $eq_to_english) {
            $adEquivalentDateForNewNepaliYear = explode('-', $eq_to_english);
            if ($yy == $adEquivalentDateForNewNepaliYear[2]) {
                $lookupNearestAdDate = Carbon::parse($eq_to_english);
                $month_day = array_filter($this->bs, function ($item) use ($key) {
                    return $item[0] == $key;
                });
                $month_day = array_values($month_day)[0];

                $equivalentNepaliYear = $key;

                if ($input_date->isBefore($lookupNearestAdDate)) {
                    $lookupNearestAdDate = Carbon::parse($this->year_np_eq_to_english[$key - 1]);
                    $month_day = array_filter($this->bs, function ($item) use ($key) {
                        return $item[0] == ($key - 1);
                    });
                    $month_day = array_values($month_day)[0];
                    $equivalentNepaliYear = $key - 1;
                }
            }
        }
        $days_difference = $lookupNearestAdDate->diffInDays($input_date);

        $nepMonth = 0;
        $nepDay = 1;
        array_shift($month_day);
        while ($days_difference != 0) {
            if ($days_difference > 0) {

                $daysInMonth = $month_day[$nepMonth];
                $nepDay++;
                if ($nepDay > $daysInMonth) {
                    $nepMonth++;
                    $nepDay = 1;
                }

                if ($nepMonth >= 12) {
                    $equivalentNepaliYear++;
                    $nepMonth = 0;
                }

                $days_difference--;
            }
        }

        $nepMonth += 1;

        $this->nep_date["year"] = $equivalentNepaliYear;
        $this->nep_date["month"] = $nepMonth;
        $this->nep_date["date"] = $nepDay;
        $this->nep_date["nmonth"] = $this->get_nepali_month($nepMonth);
        $this->nep_date["num_day"] = $nepDay;
        $this->nep_date["full_date"] = $equivalentNepaliYear . "-" . str_pad($nepMonth, 2, '0', STR_PAD_LEFT) . "-" . $nepDay;

        return $this->nep_date;
    }

    /**
     * @description Get Nepali Month Name
     *
     * @param $m
     * @return bool|string
     */
    public function get_nepali_month($m): bool|string
    {
        $n_month = false;

        switch ($m) {
            case 1:
                $n_month = "Baishak";
                break;

            case 2:
                $n_month = "Jestha";
                break;

            case 3:
                $n_month = "Ashad";
                break;

            case 4:
                $n_month = "Shrawn";
                break;

            case 5:
                $n_month = "Bhadra";
                break;

            case 6:
                $n_month = "Ashwin";
                break;

            case 7:
                $n_month = "kartik";
                break;

            case 8:
                $n_month = "Mangshir";
                break;

            case 9:
                $n_month = "Poush";
                break;

            case 10:
                $n_month = "Magh";
                break;

            case 11:
                $n_month = "Falgun";
                break;

            case 12:
                $n_month = "Chaitra";
                break;
        }
        return $n_month;
    }

    public function toNepaliFromString($date): string
    {
        $nArray = explode('-', $date);
        $dArray = $this->toNepali($nArray[0], $nArray[1], $nArray[2]);
        $y = $dArray['year'];
        $m = sprintf("%02d", $dArray['month']);
        $d = sprintf("%02d", $dArray['date']);

        return $y . '-' . $m . '-' . $d;
    }

    public function getNepaliDate($value): string
    {
        $_date = explode("-", Carbon::parse($value)->format('Y-m-d'));
        $_time = date("g:i a", strtotime(Carbon::parse($value)->toTimeString()));
        $a = $this->toNepali($_date[0], $_date[1], $_date[2]);
        return $a['nmonth'] . ' ' . $a['date'] . ', ' . $a['year'];
    }

    public function getNepaliDateTime($value): string
    {
        $_date = explode("-", Carbon::parse($value)->format('Y-m-d'));
        $_time = date("g:i a", strtotime(Carbon::parse($value)->toTimeString()));
        $a = $this->toNepali($_date[0], $_date[1], $_date[2]);
        return $a['full_date'] . ' ' . $_time;
    }

    public function getEnglishDateTime($value): string
    {
        $_time = date("g:i a", strtotime(Carbon::parse($value)->toTimeString()));
        $a = $this->toEnglish(Carbon::parse($value)->toDateString());
        return $a['full_date'] . ' ' . $_time;
    }

    /**
     * currently can only calculate the date between BS 2000-2089
     *
     * @param $nepali_date
     * @return array|bool
     */
    public function toEnglish($nepali_date): array|bool
    {
        $nepali_date_explode = explode('-', $nepali_date);

        $nepali_year = $nepali_date_explode[0];
        $nepali_month = $nepali_date_explode[1];
        $nepali_day = $nepali_date_explode[2];

        $start_date_np = 1970;
        $days_elapsed_since_start = 0;
        $days_index = ($nepali_year - $start_date_np);
        for ($j = 1; $j <= ($nepali_month - 1); $j++) {
            $days_elapsed_since_start += $this->bs[$days_index][$j];
        }
        $days_elapsed_since_start = $days_elapsed_since_start + $nepali_day;
        $np_equivalent_ad_date = $this->year_np_eq_to_english[$nepali_year];
        $eng_date = Carbon::parse($np_equivalent_ad_date)->addDays($days_elapsed_since_start - 1);


        $this->eng_date["year"] = $eng_date->year;
        $this->eng_date["month"] = $eng_date->month;
        $this->eng_date["date"] = $eng_date->day;
        $this->eng_date["day"] = $this->get_day_of_week($eng_date->dayOfWeek);
        $this->eng_date["emonth"] = $this->get_english_month($eng_date->month);
        $this->eng_date["num_day"] = $eng_date->day;
        $this->eng_date["full_date"] = $eng_date->year . "-" . $eng_date->month . "-" . $eng_date->day;

        return $this->eng_date;
    }

    /**
     * @description Get Name of Day
     * @param $day
     * @return string
     */
    private function get_day_of_week($day): string
    {
        switch ($day) {
            case 1:
                $day = "Sunday";
                break;

            case 2:
                $day = "Monday";
                break;

            case 3:
                $day = "Tuesday";
                break;

            case 4:
                $day = "Wednesday";
                break;

            case 5:
                $day = "Thursday";
                break;

            case 6:
                $day = "Friday";
                break;

            case 7:
                $day = "Saturday";
                break;
        }
        return $day;
    }

    /**
     * @description Get English Month Name
     *
     * @param $m
     * @return bool|string
     */
    public function get_english_month($m): bool|string
    {
        $eMonth = false;
        switch ($m) {
            case 1:
                $eMonth = "January";
                break;
            case 2:
                $eMonth = "February";
                break;
            case 3:
                $eMonth = "March";
                break;
            case 4:
                $eMonth = "April";
                break;
            case 5:
                $eMonth = "May";
                break;
            case 6:
                $eMonth = "June";
                break;
            case 7:
                $eMonth = "July";
                break;
            case 8:
                $eMonth = "August";
                break;
            case 9:
                $eMonth = "September";
                break;
            case 10:
                $eMonth = "October";
                break;
            case 11:
                $eMonth = "November";
                break;
            case 12:
                $eMonth = "December";
        }
        return $eMonth;
    }

    public function getNepaliDateFormat($value): ?string
    {
        if ($value) {
            $_date = explode("-", Carbon::parse($value)->format('Y-m-d'));
            $a = $this->toNepali($_date[0], $_date[1], $_date[2]);
            return $a['full_date'];
        }
        return null;
    }

    public function getNepaliMonthName($date): bool|string
    {
        $nArray = explode('-', $date);
        $dArray = $this->toNepali($nArray[0], $nArray[1], $nArray[2]);
        $m = sprintf("%02d", $dArray['month']);
        return $this->get_nepali_month($m);
    }

    public function getNepaliMonthStartDateInEnglishDate($date)
    {
        $_date = explode("-", Carbon::parse($date)->format('Y-m-d'));
        $a = $this->toNepali($_date[0], $_date[1], $_date[2]);
        $year = $a['year'];
        $month = $a['month'];
        $last_date = $year . '-' . $month . '-' . 1;
        return $this->getEnglishDate($last_date);
    }

    public function getEnglishDate($value)
    {
        $a = $this->toEnglish($value);
        return $a['full_date'];
    }

    public function getNepaliMonthEndDateInEnglishDate($date)
    {

        $_date = explode("-", Carbon::parse($date)->format('Y-m-d'));
        $a = $this->toNepali($_date[0], $_date[1], $_date[2]);
        $year = $a['year'];
        $month = $a['month'];
        foreach ($this->bs as $arr) {
            if ($arr[0] == $year) {
                $need_aaray = $arr;
                break;
            }
        }
        $last_day_in_month = $need_aaray[$month];
        $last_date = $year . '-' . $month . '-' . $last_day_in_month;
        return $this->getEnglishDate($last_date);

    }

    function convertYMD($day): string
    {
        $years = floor($day / 365);
        $months = floor(($day - ($years * 365)) / 30);
        $days = ($day - ($years * 365) - ($months * 30));
        if ($years > 0) {
            return $years . ' years, ' . $months . ' months, ' . $days . ' days';
        } elseif ($months > 0) {
            return $months . ' months, ' . $days . ' days';
        } else {
            return $days . ' days';
        }

    }

    /**
     * @description Check English Date Range Is OK
     * @param $yy
     * @param $mm
     * @param $dd
     * @return bool
     */
    private function is_range_eng($yy, $mm, $dd): bool
    {
        if ($yy < 1934 || $yy > 2033) {
            $this->debug_info = "Supported only between 1944-2022";
            return false;
        }

        if ($mm < 1 || $mm > 12) {
            $this->debug_info = "Error! value 1-12 only";
            return false;
        }

        if ($dd < 1 || $dd > 31) {
            $this->debug_info = "Error! value 1-31 only";
            return false;
        }

        return true;
    }

    /**
     * @description Check Nepali Date Range Is OK
     * @param $yy
     * @param $mm
     * @param $dd
     * @return bool
     */
    private function is_range_nep($yy, $mm, $dd): bool
    {
        if ($yy < 1970 || $yy > 2089) {
            $this->debug_info = "Supported only between 1000-2089";
            return false;
        }

        if ($mm < 1 || $mm > 12) {
            $this->debug_info = "Error! value 1-12 only";
            return false;
        }

        if ($dd < 1 || $dd > 32) {
            $this->debug_info = "Error! value 1-31 only";
            return false;
        }

        return true;
    }

}
