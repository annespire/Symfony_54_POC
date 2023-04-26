<?php

namespace App\Conveniences;

use DateTime;
use DateTimeZone;
use Exception;

class Dateformat
{
	public function __invoke($format, $ts, $tz): string
	{
		return $this->format($format, $ts, $tz);
	}

	public function format($format, $ts, $tz): string
	{
		$date = new DateTime("@$ts");
		try
		{
			$date->setTimezone(new DateTimeZone($tz));
		}
		catch(Exception $e)
		{
			// Fallback to box office config default
			$date->setTimezone(new DateTimeZone(date_default_timezone_get()));
		}

		return $date->format($format);
	}
}
