<?php

namespace Drupal\date_day_count;

/**
 * The DateDiffDayCount service.
 */
class DateDiffDayCount {

  /**
   * Calculate the number of days.
   *
   * @param string $start_date
   *   The start date.
   * @param string $end_date
   *   The end date.
   *
   * @return int
   *   Returns the number of days.
   */
  public function dayCount($start_date, $end_date) {
    $start_date = $this->getDateArray($start_date);
    $end_date = $this->getDateArray($end_date);
    $day_count = 0;
    // Check if year is same.
    if ($start_date['y'] == $end_date['y']) {
      // Check if month is same.
      if ($start_date['m'] == $end_date['m']) {
        $day_count = $end_date['d'] - $start_date['d'];
      }
      else {
        $day_count = $this->dayCountInMonth($start_date, $end_date);
      }
    }
    else {
      $day_count = $this->dayCountInYear($start_date, $end_date);
    }
    return $day_count;
  }

  /**
   * Get the number of days in a month.
   *
   * @param int $month
   *   The month.
   * @param int $year
   *   The year.
   *
   * @return int
   *   Returns the days in month.
   */
  protected function dayInMonth($month, $year) {
    $totalDays = 0;
    // Feb month.
    if ($month == 2) {
      if ($this->isLeapYear($year)) {
        $totalDays = 29;
      }
      else {
        $totalDays = 28;
      }
    }
    // Jan, Mar, May, July, Aug, Oct, Dec month.
    if (in_array($month, [1, 3, 5, 7, 8, 10, 12])) {
      $totalDays = 31;
    }
    // April, Jun, Sept, Nov.
    if (in_array($month, [4, 6, 9, 11])) {
      $totalDays = 30;
    }
    return $totalDays;
  }

  /**
   * Calculate the number of days between two months.
   *
   * @param array $start_date
   *   The start date.
   * @param array $end_date
   *   The end date.
   *
   * @return int
   *   Returns the number of days.
   */
  protected function dayCountInMonth(array $start_date, array $end_date) {
    $days = 0;
    $start_month = $start_date['m'];
    $end_month = $end_date['m'];
    $count = $end_month - $start_month;
    // Check if we have more than two month.
    if ($count > 2) {
      for ($i = $start_month; $i <= $end_month; $i++) {
        if ($i == $start_month) {
          $days += $this->getStartMonthDayCount($start_date);
        }
        elseif ($i == $end_month) {
          $days += $this->getEndMonthDayCount($end_date);
        }
        else {
          $days += $this->dayInMonth($i, $start_date['y']);
        }
      }
    }
    else {
      $days = $this->getStartMonthDayCount($start_date);
      $days += $this->getEndMonthDayCount($end_date);
    }
    return $days;
  }

  /**
   * Convert string to array.
   *
   * @param string $date
   *   The start date.
   *
   * @return array
   *   Returns the array of date with y,m,d as array key.
   */
  protected function getDateArray($date) {
    $date = explode('-', $date);
    $date_array['d'] = (int) $date[2];
    $date_array['m'] = (int) $date[1];
    $date_array['y'] = (int) $date[0];

    return $date_array;
  }

  /**
   * Get start month day count.
   *
   * @param array $start_date
   *   The start date.
   *
   * @return int
   *   Returns the number of days.
   */
  protected function getStartMonthDayCount(array $start_date) {
    $days = 0;
    // Since its month of start date so We will calculate the days
    // from start day to the end of month day.
    for ($i = $start_date['d']; $i <= $this->dayInMonth($start_date['m'], $start_date['y']); $i++) {
      $days++;
    }
    return $days;
  }

  /**
   * Get end month day count.
   *
   * @param array $end_date
   *   The end date.
   *
   * @return int
   *   Returns the number of days.
   */
  protected function getEndMonthDayCount(array $end_date) {
    $days = 0;
    // Since its month of end date so We will calculate the days
    // from start day of month i.e 1st to the end of end date day.
    for ($i = 1; $i <= $end_date['d']; $i++) {
      $days++;
    }
    return $days;
  }

  /**
   * Calculate the number of days in a year.
   *
   * @param array $start_date
   *   The start date.
   * @param array $end_date
   *   The end date.
   *
   * @return int
   *   Returns the number of days.
   */
  protected function dayCountInYear(array $start_date, array $end_date) {
    $days = 0;
    $start_year = $start_date['y'];
    $end_year = $end_date['y'];
    $count = $end_year - $start_year;
    // Check if we have more tan two year between start and end date.
    if ($count > 2) {
      for ($i = $start_year; $i <= $end_year; $i++) {
        if ($i == $start_year) {
          $days += $this->getStartYearDayCount($start_date);
        }
        elseif ($i == $end_year) {
          $days += $this->getEndYearDayCount($end_date);
        }
        else {
          if ($this->isLeapYear($i)) {
            $days += 366;
          }
          else {
            $days += 365;
          }
        }
      }
    }
    else {
      $days += $this->getStartYearDayCount($start_date);
      $days += $this->getEndYearDayCount($end_date);
    }
    return $days;
  }

  /**
   * Get the start year day count.
   *
   * @param array $start_date
   *   The start date.
   *
   * @return int
   *   Returns the number of days.
   */
  protected function getStartYearDayCount(array $start_date) {
    $days = 0;
    // Since its month of start date so We will calculate the days
    // from start date day till the end of start_date year day i.e 31st Dec.
    for ($i = $start_date['m']; $i <= 12; $i++) {
      if ($i == $start_date['m']) {
        $days += $this->getStartMonthDayCount($start_date);
      }
      else {
        $days += $this->dayInMonth($i, $start_date['y']);
      }
    }
    return $days;
  }

  /**
   * Get the end year day count.
   *
   * @param array $end_date
   *   The end date.
   *
   * @return int
   *   Returns the number of days.
   */
  protected function getEndYearDayCount(array $end_date) {
    $days = 0;
    // Since its month of end_date so We will calculate the days
    // from start date day of year i.e 1st Jan till the end of
    // end_date year day.
    for ($i = 1; $i <= $end_date['m']; $i++) {
      if ($i == $end_date['m']) {
        $days += $this->getEndMonthDayCount($end_date);
      }
      else {
        $days += $this->dayInMonth($i, $end_date['y']);
      }
    }
    return $days;
  }

  /**
   * Get the start year day count.
   *
   * @param int $year
   *   The year.
   *
   * @return bool
   *   Returns true on leap year, false on non leap year.
   */
  protected function isLeapYear($year) {
    $isLeap = FALSE;
    if ((($year % 4 == 0) && ($year % 100 != 0)) || ($year % 400 == 0)) {
      $isLeap = TRUE;
    }
    return $isLeap;
  }

}
