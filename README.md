INTRODUCTION
------------

Date Day Count module provide a way to calculate the difference between two dates.


INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. Visit
   https://www.drupal.org/node/1897420 for further information.


USAGE
-------------

* go to /count-days page.
* Select Start Date and End Date and then click on submit button.
* It will show the number of days between two dates.
* Developer can use the service to calculate the days between two date.
* Use \Drupal::service('date_day_count.day_count')->dayCount($start_date, $end_date); service directly anywhere in the Drupal
  to calculate the number of days.
