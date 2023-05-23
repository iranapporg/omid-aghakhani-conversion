<?php

	//call this function before use file_get_contents
	function readable_url($url) {
	
		$encodedUrl = urlencode($url);
        $fixedEncodedUrl = str_replace(['%2F', '%3A'], ['/', ':'], $encodedUrl);
		
		return $fixedEncodedUrl;
			
	}
	
    function generate_random_number($length = 15, $formatted = FALSE) {
        $nums = '0123456789';

        // First number shouldn't be zero
        $out = $nums[mt_rand(1, strlen($nums) - 1)];

        // Add random numbers to your string
        for ($p = 0; $p < $length - 1; $p++)
            $out .= $nums[mt_rand(0, strlen($nums) - 1)];

        // Format the output with commas if needed, otherwise plain output
        if ($formatted)
            return number_format($out);
        return $out;
    }

    /**
     * get today persian or gregorian date
     * format is :
     *
     * d for day
     *
     * m for month number
     *
     * M for month name
     *
     * y for year
     *
     * @param $gregorian
     * @param string $format
     * @return mixed|string
     */
    function get_today($format = "Y/n/j",$return_gregorian = FALSE) {

        if ($return_gregorian)
            return date($format,time());

        return gregorian2jalali(date(time()),$format);

    }

    /**
     * get persian date from gregorian date
     * format is :
     *
     * d for day
     *
     * m for month number
     *
     * M for month name
     *
     * y for year
     *
     * @param $datetime
     * @param string $format
     * @return mixed|string
     */
    function gregorian2jalali($datetime, $format = 'Y-m-d') {

		if (!preg_match('/^\d+$/',$datetime))
			$datetime = strtotime($datetime);

		$datetime = convert_numbers($datetime,FALSE);

        require_once APPPATH . 'helpers/jdf.php';

        $format =   str_replace('y','Y',$format);
        $format =   str_replace('m','n',$format);
        $format =   str_replace('M','F',$format);
        $format =   str_replace('d','j',$format);

        if (!preg_match('/^\d+$/',$datetime))
            $datetime = strtotime($datetime);

        return jdate($format, date($datetime),'','','en');

    }

    /**
     * get gregorian date from persian date
     * format is :
     *
     * d for day
     *
     * m for month number
     *
     * F for month name
     *
     * y for year
     *
     * @param $datetime
     * @param string $format
     * @return mixed|string
     */
    function jalali2gregorian($datetime,$format = 'Y-m-d') {

		$datetime = convert_numbers($datetime,FALSE);

        require_once APPPATH . 'helpers/jdf.php';

        if (strpos($datetime,'/') !== FALSE)
            $res = explode('/',$datetime);
        else
            $res = explode('-',$datetime);

        $res = jalali_to_gregorian($res[0],$res[1],$res[2]);

        return date($format,strtotime($res[0].'-'.$res[1].'-'.$res[2]));

    }

    /**
     * get day name for persian and english language
     * @param string $day day name
     * @param string $language english or persian
     * @return string day name
     */
    function get_day_name($day,$language = 'fa') {

        if ($day == 'Saturday') {
            if ($language == 'fa')
                return 'شنبه';
        }

        if ($day == 'Sunday') {
            if ($language == 'fa')
                return 'یکشنبه';
        }

        if ($day == 'Monday') {
            if ($language == 'fa')
                return 'دوشنبه';
        }

        if ($day == 'Tuesday') {
            if ($language == 'fa')
                return 'سه شنبه';
        }

        if ($day == 'Wednesday') {
            if ($language == 'fa')
                return 'چهارشنبه';
        }

        if ($day == 'Thursday') {
            if ($language == 'fa')
                return 'پنج شنبه';
        }

        if ($day == 'Friday') {
            if ($language == 'fa')
                return 'جمعه';
        }

    }

    /**
     * return UTC timestamp
     */
    function timestamp($unix = false) {
        date_default_timezone_set('UTC');
        if ($unix) {
            $d = new DateTime();
            return $d->getTimestamp();
        } else {
            return date('Y-m-d H:i:s');
        }
    }

    /**
     * Get different two datetime in format yyyy-mm-dd
     * @param $from format is yyyy-mm-dd
     * @param $to   format is yyyy-mm-dd
     * @return string is day
     */
    function get_different_date($from, $to) {
        $date1 = date_create($from);
        $date2 = date_create($to);
        $diff = date_diff($date1, $date2);
        return $diff->format("%R%a");
    }

    /**
     * base of datetime 2019-2-12 12:20:30
     * @param from
     * @param to
     * @return string
     */
    function get_different_datetime($from, $to) {

        $datetime1 = new DateTime($from);
        $datetime2 = new DateTime($to);
        $interval = $datetime1->diff($datetime2);

        $res = array();
        $res['year'] = $interval->format('%y');
        $res['month'] = $interval->format('%m');
        $res['day'] = $interval->format('%a');
        $res['hour'] = $interval->format('%h');
        $res['minute'] = $interval->format('%i');
        $res['second'] = $interval->format('%s');

        return $res;

    }

    /**
     * @param $date
     * @param $interval +1 days , -2 hours , +1 second , +1 week , last Sunday
     * @return string
     */
    function change_date_interval($date, $interval) {
        return date('Y-m-d', strtotime($date . " $interval"));
    }

    /**
     * How many time elapse from argument
     * @param datetime
     * @return string
     */
    function time_ago($datetime) {

        if (preg_match('/^\d+$/', $datetime)) {
            $last_date = $datetime;
        } else
            $last_date = strtotime($datetime);

        $now = time();
        $time = $last_date;
        // catch error
        if (!$time) {
            return $last_date;
        }
        // build period and length arrays
        $periods = array('ثانیه', 'دقیقه', 'ساعت', 'روز', 'هفته', 'ماه', 'سال', 'قرن');
        $lengths = array(60, 60, 24, 7, 4.35, 12, 10);
        // get difference
        $difference = $now - $time;
        // set descriptor
        if ($difference < 0) {
            $difference = abs($difference); // absolute value
            $negative = true;
        }
        // do math
        for ($j = 0; $difference >= $lengths[$j] and $j < count($lengths) - 1; $j++) {
            $difference /= $lengths[$j];
        }
        // round difference
        $difference = intval(round($difference));

        if ($difference == 0) return 'لحظاتی پیش';

        // return
        return number_format($difference) . ' ' . $periods[$j] . ' ' . (isset($negative) ? '' : 'پیش');

    }

    //example 12 is دوازده
    function number_to_word($strnum) {

        if ($strnum == '0') return 'صفر';
        $strnum = trim($strnum);
        $size_e = strlen($strnum);

        for ($i = 0; $i < $size_e; $i++) {
            if (!($strnum [$i] >= "0" && $strnum [$i] <= "9")) {
                die ("content of string must be number. " . 'فقط عدد وارد کنید' . $strnum);

            }
        }

        for ($i = 0; $i < $size_e && $strnum [$i] == "0"; $i++)
            ;

        $str = substr($strnum, $i);
        $size = strlen($str);

        $arr = array();
        $res = "";
        $mod = $size % 3;
        if ($mod) {
            $arr [] = substr($str, 0, $mod);
        }

        for ($j = $mod; $j < $size; $j += 3) {
            $arr [] = substr($str, $j, 3);
        }

        $arr1 = array("", "یک", "دو", "سه", "چهار", "پنج", "شش", "هفت", "هشت", "نه");
        $arr2 = array(1 => "یازده", "دوازده", "سیزده", "چهارده", "پانزده", "شانزده", "هفده", "هجده", "نوزده");
        $arr3 = array(1 => "ده", "بیست", "سی", "چهل", "پنجاه", "شصت", "هفتاد", "هشتاد", "نود");
        $arr4 = array(1 => "صد", "دویست", "سیصد", "چهارصد", "پانصد", "ششصد", "هفتصد", "هشتصد", "نهصد");
        $arr5 = array(1 => "هزار", "میلیون", "میلیارد", "تیلیارد");
        $explode = 'و';
        $size_arr = count($arr);

        if ($size_arr > count($arr5) + 1) {
            die ("عدد بسیار بزرگ است . " . 'this number is greate');

        }

        for ($i = 0; $i < $size_arr; $i++) {

            $flag_2 = 0;
            $flag_1 = 0;

            if ($i) {
                $res .= ' ' . $explode . ' ';
            }

            $p = $arr [$i];
            $ss = strlen($p);

            for ($k = 0; $k < $ss; $k++) {
                if ($p [$k] != "0") {
                    break;
                }
            }

            $p = substr($p, $k);
            $size_p = strlen($p);

            if ($size_p == 3) {
                $res .= $arr4 [( int )$p [0]];
                $p = substr($p, 1);
                $size_p = strlen($p);

                if ($p [0] == "0") {
                    $p = substr($p, 1);
                    $size_p = strlen($p);
                    if ($p [0] == "0") {
                        continue;
                    } else {
                        $flag_1 = 1;
                    }

                } else {
                    $flag_2 = 1;
                }

            }

            if ($size_p == 2) {

                if ($flag_2) {
                    $res .= ' ' . $explode . ' ';
                }

                if ($p >= "0" && $p <= "9") {
                    $res .= $arr1 [( int )$p];
                } elseif ($p >= "11" && $p <= "19") {
                    $res .= $arr2 [( int )$p [1]];
                } elseif ($p [0] >= "1" && $p [0] <= "9" && $p [1] == "0") {
                    $res .= $arr3 [( int )$p [0]];
                } else {
                    $res .= $arr3 [( int )$p [0]];
                    $res .= ' ' . $explode . ' ';
                    $res .= $arr1 [( int )$p [1]];
                }

            }

            if ($size_p == 1) {

                if ($flag_1) {
                    $res .= ' ' . $explode . ' ';
                }

                $res .= $arr1 [( int )$p];

            }

            if ($i + 1 < $size_arr) {
                $res .= ' ' . $arr5 [$size_arr - $i - 1];
            }

        }

        $res = rtrim($res, ' و');

        return $res . ' تومان';
    }

    function convert_numbers($srting, $toPersian = true) {
        $en_num = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $fa_num = array('٠', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $fa_num1 = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');

        if ($toPersian) return str_replace($en_num, $fa_num, $srting);
        else {
            $t = str_replace($fa_num1, $en_num, $srting);
            $t = str_replace($fa_num, $en_num, $t);
			$t = str_replace('۰','0',$t);
            return $t;
        }
    }

	function sanitize_string($string) {

		$string = convert_numbers($string,FALSE);
		$string = trim($string);
		$string = remove_arabic_character($string);

		return $string;

	}

	function sanitize_number($string) {

		$string = convert_numbers($string,FALSE);
		$string = trim($string);
		$string = str_replace(',','',$string);

		return intval($string);

	}

	function sanitize_data(&$array) {

		$ci = & get_instance();
		$ci->load->helper('security');

		$new = [];

		foreach ($array as $key => $value) {
			$new[$key] = convert_numbers($value, FALSE);
			$new[$key] = xss_clean($new[$key]);
			$new[$key] = trim($new[$key]);
		}

		$array = $new;
		return $new;
	}

    function is_timestamp($timestamp) {
        return ((string)(int)$timestamp === $timestamp)
            && ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX);
    }

    /**
     * example 1024 is 1kb
     * @param $number
     * @param int $precision
     * @param null $divisors
     * @return string
     */
    function shorten_number($number, $precision = 3, $divisors = null) {

        // Setup default $divisors if not provided
        if (!isset($divisors)) {
            $divisors = array(
                pow(1000, 0) => '', // 1000^0 == 1
                pow(1000, 1) => 'K', // Thousand
                pow(1000, 2) => 'M', // Million
                pow(1000, 3) => 'B', // Billion
                pow(1000, 4) => 'T', // Trillion
                pow(1000, 5) => 'Qa', // Quadrillion
                pow(1000, 6) => 'Qi', // Quintillion
            );
        }

        // Loop through each $divisor and find the
        // lowest amount that matches
        foreach ($divisors as $divisor => $shorthand) {
            if (abs($number) < ($divisor * 1000)) {
                // We found a match!
                break;
            }
        }

        // We found our match, or there were no matches.
        // Either way, use the last defined value for $divisor.
        return number_format($number / $divisor, $precision) . $shorthand;
    }

    function get_percentage($total, $number) {
        if ($total > 0) {
            return number_format(($number * 100) / $total, 0);
        } else {
            return 0;
        }
    }

    function remove_arabic_character($str) {
        $str = str_replace('ك','ک',$str);
        $str = str_replace('ي','ی',$str);
        return $str;
    }

    /**
     * convert second to human time
     * @param $seconds seconds value
     * @param bool $calculate_zero if set true so return lesser than zero second
     * @return string
     * @throws Exception
     */
	function seconds_to_time($time,$calculate_zero = FALSE) {

        $days = floor($time / (60 * 60 * 24));
        $time -= $days * (60 * 60 * 24);

        $hours = floor($time / (60 * 60));
        $time -= $hours * (60 * 60);

        $minutes = floor($time / 60);
        $time -= $minutes * 60;

        $seconds = floor($time);
        $time -= $seconds;

        $final = '';
        if ($days > 0)
            $final .= "{$days} روز";

        if ($hours > 0)
            $final .= " {$hours} ساعت";

        if ($minutes > 0)
            $final .= " {$minutes} دقیقه";

        if ($seconds > 0)
            $final .= " {$seconds} ثانیه";

        return $final;

	}

	/*
	 * convert base64 picture to file
	 */
    function base64_to_jpeg($base64_string, $output_file) {

        $res = str_replace(' ', '+', $base64_string);
        $ifp = fopen($output_file, "wb");
        fwrite($ifp, base64_decode($res));
        fclose($ifp);

        return $output_file;

    }

    function jpeg_to_base64($filename) {

        $type = pathinfo($filename, PATHINFO_EXTENSION);
        $data = file_get_contents($filename);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return $base64;

    }

    function int2bool($number) {

        if ($number > 0)
            return TRUE;
        else
            return FALSE;

    }
	
	function byte2human($size, $precision = 2) {
        $units = array(' بایت',' کیلو بایت',' مگابایت','GB','TB','PB','EB','ZB','YB');
        $step = 1024;
        $i = 0;
        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }
        return round($size, $precision).$units[$i];
    }

    /** give assoc array and extract custom field to simple array
     * @param array $array assoc array ex. (array(array('pid' => 1),array('pid' => 2))
     * @param string $key_name field name
     * @return array contain only simple array
     */
    function extract_field($array,$key_name) {

        $id = [];
        foreach ($array as $item)
            $id[] = $item[$key_name];

        return $id;

    }