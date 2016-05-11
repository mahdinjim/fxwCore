<?php
namespace Acmtool\AppBundle\Entity;

class WorkedDay
{
	private $week;
	private $day_of_week;
	private $totalhours;
	private $month;
	private $day;
	private $year;
	public function getDayOfTheWeek()
	{
		return $this->day_of_week;
	}
	public function getTotalHours()
	{
		return $this->totalhours;
	}
	public function getMonth()
	{
		return $this->month;
	}
	public function getDay()
	{
		return $this->day;
	}
	public function getYear()
	{
		return $this->year;
	}
	public function getWeek()
	{
		return $this->week;
	}
	public function setWeek($week)
	{
		$this->week=$week;
	}
	public function setDayOfTheWeek($day_of_week)
	{
		$this->day_of_week=$day_of_week;
	}
	public function setTotalHours($totalhours)
	{
		$this->totalhours=$totalhours;
	}
	public function setMonth($month)
	{
		$this->month=$month;
	}
	public function setDay($day)
	{
		$this->day=$day;
	}
	public function setYear($year)
	{
		$this->year=$year;
	}
	public function serialize()
	{
		return array("day"=>$this->getDay(),"month"=>$this->getMonth(),"totalhours"=>$this->getTotalHours(),"year"=>$this->getYear(),"dayofweek"=>$this->getDayOfTheWeek(),"week"=>$this->getWeek());
	}
}