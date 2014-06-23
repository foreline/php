<?php	
	/**
	 * Работа с датами
	 * 
	 * @package Dates
	 * @author Simakin Dima dima@foreline.ru
	 */
	
	/**
	 * Класс для работы с датами
	 * 
	 * @package Dates
	 */
	
	class Dates {
		
		/** @var array $months Месяцы */
		static public $months = array(
			'01'	=> 'января',
			'02'	=> 'февраля',
			'03'	=> 'марта',
			'04'	=> 'апреля',
			'05'	=> 'мая',
			'06'	=> 'июня',
			'07'	=> 'июля',
			'08'	=> 'августа',
			'09'	=> 'сентября',
			'10'	=> 'октября',
			'11'	=> 'ноября',
			'12'	=> 'декабря',
		);
		
		/** @var array $arMonths Месяцы в именительном падеже */
		static public $arMonths = array(
			'1'		=> 'январь',
			'01'	=> 'январь',
			'2'		=> 'февраль',
			'02'	=> 'февраль',
			'3'		=> 'март',
			'03'	=> 'март',
			'4'		=> 'апрель',
			'04'	=> 'апрель',
			'5'		=> 'май',
			'05'	=> 'май',
			'6'		=> 'июнь',
			'06'	=> 'июнь',
			'7'		=> 'июль',
			'07'	=> 'июль',
			'8'		=> 'август',
			'08'	=> 'август',
			'9'		=> 'сентябрь',
			'09'	=> 'сентябрь',
			'10'	=> 'октябрь',
			'11'	=> 'ноябрь',
			'12'	=> 'декабрь',
		);
		
		/** @var array $weekDays Дни недели */
		public static $weekDays = array(
			0	=> 'вс',
			1	=> 'пн',
			2	=> 'вт',
			3	=> 'ср',
			4	=> 'чт',
			5	=> 'пт',
			6	=> 'сб',
		);
		
		/**
		 * Парсит строку с датой и возвращает число,
		 * представляющее собой количество секунд,
		 * истекших с полуночи 1 января 1970 года GMT+0 до даты, указанной в строке
		 * 
		 * @param string $date Строка с датой
		 * 
		 * @return integer|boolean $timestamp
		 */
		
		public function parse($date = '') {
			
			if ( 10 > strlen($date) ) {
				return false;
			}
			
			$date = trim($date);
			
			$pattern = '#[0-9]{2}.[0-9]{2}.[0-9]{4}#';
			
			$pattern1 = '#[0-9]{4}.[0-9]{2}.[0-9]{2}#';
			
			$pattern3 = '#^([0-9]{2,4}).([0-9]{2}).([0-9]{2,4})#';
			
			$pattern4 = '#[0-9]{1,4}[./-][0-9]{1,2}[./-][0-9]{2,4}([^0-9][0-9]{1,2}:[0-9]{2}:[0-9]{2})*#';
			
			if ( preg_match($pattern, $date) ) {
				$day	= substr($date,0,2);
				$month	= substr($date,3,2);
				$year	= substr($date,6,4);
			} else if ( preg_match($pattern1, $date) ) {
				$year	= substr($date,0,4);
				$month	= substr($date,5,2);
				$day	= substr($date,8,2);
			} else if ( preg_match($pattern3, $date, $matches) ) {
				$nDate = intval($matches[1]) . '.' . intval($matches[2]) . '.' . intval($matches[3]);
				return dates::parse($nDate);
				//return false;
			} else if ( 0 && preg_match($pattern4, $date, $matches)) {
				
			} else {
				return false;
			}
			
			if ( 19 == strlen($date) ) {
				$hour	= substr($date, 11, 2);
				$min	= substr($date, 14,2);
				$sec	= substr($date, 17,2);
			} else {
				$hour	= 0;
				$min	= 0;
				$sec	= 0;
			}
			
			$timeStamp = mktime($hour, $min, $sec, $month, $day, $year);
			
			return $timeStamp;
		}
		
		/**
		 * Возвращает разницу в днях между двумя датами
		 * 
		 * @param string $dateFrom Дата "с"
		 * @param string $dateTo Дата "по"
		 * 
		 * @return integer $fullDays Количество полных дней между датами 
		 */
		
		public function dateDiff($dateFrom, $dateTo) {
			
			$timeStampFrom	= Dates::parse($dateFrom);
			$timeStampTo	= Dates::parse($dateTo);
			
			$dateDiff = $timeStampTo - $timeStampFrom;
			$fullDays = floor($dateDiff/(60*60*24));
			
			return $fullDays;
		}
		
		/**
		 * Возвращает разницу в днях между заданными датами. Разницей считается количество переходов через полночь.
		 * 
		 * @param string $dateFrom Дата с
		 * @param string $dateTo Дата по
		 * 
		 * @return integer $daysDiff
		 */
		
		public function daysDiff($dateFrom = '', $dateTo = '') {
			
			$tsDateFrom = dates::parse($dateFrom);
			$tsDateTo	= dates::parse($dateTo);
			
			if ( date('z', $tsDateFrom) == date('z', $tsDateTo) && (86400 > $tsDateTo - $tsDateFrom) ) {
				return 0;
			}
			
			return ceil( abs(($tsDateTo - $tsDateFrom)/(24*60*60)) );
		}
		
		/**
		 * Возвращает разницу в минутах между датами
		 * 
		 * @param string $dateFrom дата "с"
		 * @param string $dateTo дата "по"
		 * 
		 * @return integer $minDiff Разница в минутах между датами
		 */
		
		public function minDiff($dateFrom, $dateTo) {
			
			$timeStampFrom	= Dates::parse($dateFrom);
			$timeStampTo	= Dates::parse($dateTo);
			
			$dateDiff = $timeStampTo - $timeStampFrom;
			$fullMins = floor($dateDiff/60);
			
			return $fullMins;
		}
		
		/**
		 * Возвращает разницу в секундах между двумя датами
		 * 
		 * @param string $dateFrom дата "с"
		 * @param string $dateTo дата "по"
		 * 
		 * @return integer $secDiff Разница в секундах между датами
		 */
		
		public function secDiff($dateFrom, $dateTo) {
			
			$timeStampFrom	= Dates::parse($dateFrom);
			$timeStampTo	= Dates::parse($dateTo);
			
			$dateDiff = $timeStampTo - $timeStampFrom;
			
			return $dateDiff;
		}
		
		/**
		 * Выводит отформатированную дату "сегодня в 7:00 | вчера в 19:35 | 1 сентября"
		 * Время выводится только для "сегодня" и "вчера", если задано
		 * 
		 * @param string $date исходная дата
		 * @param boolean $today [optional] Заменять ли дату на "сегодня" и "вчера", по умолчанию true
		 * @param boolean $weekDays [optional] Выводить ли дополнительно дни недели: 14 января, пн, по умолчанию false
		 * @param boolean $showMonth [optional] Выводить ли название месяца, по умолчанию true
		 * 
		 * @return string $formatedDate отформатированная дата
		 */
		
		public function dateFormat($date, $today = true, $weekDays = false, $showMonth = true) {
			
			if ( empty($date) ) {
				return;
			}
			
			$timeStamp = dates::parse($date);
			
			$day	= date('j', $timeStamp);
			$month	= date('m', $timeStamp);
			$year	= date('Y', $timeStamp);
			$hour	= date('H', $timeStamp);
			$min	= date('i', $timeStamp);
			$sec	= date('s', $timeStamp);
			
			$dateDiff = dates::dateDiff($date, date('d.m.Y H:i:s'));
			
			// Определяем сегодняшнее ли это дата/время
			$isToday = false;
			$todayTimeStampStart = dates::parse(date('Y.m.d 00:00:00'));
			$todayTimeStampEnd = dates::parse(date('Y.m.d 23:59:59'));
			
			if ( $todayTimeStampStart < $timeStamp && $timeStamp < $todayTimeStampEnd ) {
				$isToday = true;
			}
			
			// Определяем вчерашние ли это дата/время
			$isYesterday = false;
			$yesterdayTimeStampStart = ( dates::parse(date('Y.m.d 00:00:00')) - 86400 );
			$yesterdayTimeStampEnd = ( dates::parse(date('Y.m.d 23:59:59')) - 86400 );
			
			if ( $yesterdayTimeStampStart < $timeStamp && $timeStamp < $yesterdayTimeStampEnd ) {
				$isYesterday = true;
			}
			
			/*
			 * Форматируем вывод
			 */
			
			if ( TRUE === $today && TRUE === $isToday ) {
				// сегодня
				$formatedDate = '<span>сегодня</span>';
			} else if ( TRUE === $today && TRUE === $isYesterday ) {
				// вчера, в 18:16
				$formatedDate = '<span>вчера</span>';
			} else {
				$formatedDate = $day . ($showMonth ? '&nbsp;' . dates::$months[$month] : '') . (date('Y') != $year ? ' ' . $year : '');
			}
			
			if ( TRUE === $weekDays ) {
				$formatedDate .= ', ' . dates::$weekDays[date('w', $timeStamp)];
			}
			
			return $formatedDate;
		}
		
		/**
		 * Выводит отформатированную дату "сегодня в 7:00 | вчера в 19:35 | 1 сентября, 11:19"
		 * Время выводится, если задано
		 * 
		 * @param string $date исходная дата
		 * @param boolean $today Заменять ли дату на "сегодня" и "вчера"
		 * @param boolean $weekDays Выводить ли дополнительно дни недели: 14 января 15:35, пн
		 * 
		 * @return string $formatedDate
		 */
		
		public function dateTimeFormat($date, $today = true, $weekDays = false) {
			
			if ( empty($date) ) {
				return;
			}
			
			$timeStamp = dates::parse($date);
			
			$day	= date('j', $timeStamp);
			$month	= date('m', $timeStamp);
			$year	= date('Y', $timeStamp);
			$hour	= date('H', $timeStamp);
			$min	= date('i', $timeStamp);
			$sec	= date('s', $timeStamp);
			
			/*
			 * 
			 */
			
			$dateDiff = dates::dateDiff($date, date('d.m.Y H:i:s'));
			
			// Определяем сегодняшнее ли это дата/время
			$isToday = false;
			$todayTimeStampStart = dates::parse(date('Y.m.d 00:00:00'));
			$todayTimeStampEnd = dates::parse(date('Y.m.d 23:59:59'));
			
			if ( $todayTimeStampStart < $timeStamp && $timeStamp < $todayTimeStampEnd ) {
				$isToday = true;
			}
			
			// Определяем вчерашние ли это дата/время
			$isYesterday = false;
			$yesterdayTimeStampStart = ( dates::parse(date('Y.m.d 00:00:00')) - 86400 );
			$yesterdayTimeStampEnd = ( dates::parse(date('Y.m.d 23:59:59')) - 86400 );
			
			if ( $yesterdayTimeStampStart < $timeStamp && $timeStamp < $yesterdayTimeStampEnd ) {
				$isYesterday = true;
			}
			
			/*
			 * Форматируем вывод
			 */
			
			if ( TRUE === $today && TRUE === $isToday ) {
				// сегодня, в 18:16
				$formatedDate = '<span>сегодня</span>' . ( 0 < strlen($hour) && 0 < strlen($min) ? ' в&nbsp;' . $hour . ':' . $min : '');
			} else if ( TRUE === $today && TRUE === $isYesterday ) {
				// вчера, в 18:16
				$formatedDate = '<span>вчера</span>' . ( 0 < strlen($hour) && 0 < strlen($min) ? ' в&nbsp;' . $hour . ':' . $min : '');
			} else {
				$formatedDate = $day . '&nbsp;' . Dates::$months[$month] . (date('Y') != $year ? ' ' . $year : '') . ( !empty($hour)&&!empty($min) ? ', '  . $hour . ':' . $min : '');
			}
			
			if ( TRUE === $weekDays ) {
				$formatedDate .= ', ' . dates::$weekDays[date('w', $timeStamp)];
			}
			
			return $formatedDate;
		}
		
		/**
		 * Возвращает время в формате чч:мм из минут
		 * 
		 * @param integer $minutes минуты
		 * @return string $timeFormat
		 */
		
		public function timeFromMinutes($minutes = 0, $showDays = false) {
			
			$minutes = 0 < intval($minutes) ? intval($minutes) : 0;
			
			if ( 0 >= $minutes ) {
				//return false;
			}
			
			$hours	= floor($minutes/60);
			$min	= round($minutes - ($hours * 60));
			
			if ( TRUE === $showDays ) {
				
				if ( 24 <= $hours ) {
					$days = floor($hours/24);
					$hours = $hours - 24*$days;
				}
				
				$output = '';
				if ( 0 < $days ) {
					$output .= $days . 'дн.&nbsp;';
				}
			}
			
			$output .= (10 > $hours ? '0' : '') . $hours . ':' . (10 > $min ? '0' : '') . $min;
			
			return $output;
		}

		/**
		 * Возвращает время в формате мм:сс из секундв
		 * 
		 * @param integer $seconds минуты
		 * @return string $timeFormat
		 */
		
		public function timeFromSeconds($seconds = 0, $showHours = false) {
			
			$seconds = 0 < intval($seconds) ? intval($seconds) : 0;
			
			if ( 0 >= $seconds ) {
				//return false;
			}
			
			$min	= floor($seconds/60);
			$sec	= round($seconds - ($min * 60));
			
			if ( TRUE === $showHours ) {
				
				if ( 60 <= $min ) {
					$hours = floor($min/60);
					$min = $min - floor($hours*60);
				} else {
					$hours = 0;
				}
				
				$output = '';
				if ( 0 < $hours ) {
					$output .= $hours . 'ч.&nbsp;';
				}
			}
			
			$output .= '<span title="ч. мм:сс">' . (10 > $min ? '0' : '') . $min . ':' . (10 > $sec ? '0' : '') . $sec . '</span>';
			
			return $output;
		}
		
	}
	