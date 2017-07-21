#!/usr/bin/env php
<?php

/**
 * When run from the command line, this script will output SQL which can be
 * piped into MySQL to produce a full table of sample data.
 */

/**
 * @param array $array
 * @return array
 */
function array_rand_values($array) {
  return $array[array_rand($array)];
}

$sql = "insert into civicrm_restlog (
  time_stamp,
  calling_contact_id,
  entity,
  action,
  parameters )
values\n";

$start = new DateTime('2013-01-01');
$end   = new DateTime('2017-01-01');

$rows = [];
$date = $start;
while ($date < $end) {
  $oneDay = new DateInterval('P1D');
  $date->add($oneDay);
  $dateString = $date->format('Y-m-d H:i:s');
  $rows[]
    = "("
    . "'$dateString', "
    . array_rand_values(array(50, 100, 189, 203)) . ", "
    . "'" . array_rand_values(array('contact', 'contribution', 'event', 'activity')) . "', "
    . "'" . array_rand_values(array('get', 'delete', 'mycustomaction', 'getfields', 'getactions', 'update')) . "', "
    . "NULL"
    . ")";
}

$rowsSql = implode(",\n", $rows);
$sql .= $rowsSql . ";\n";

echo $sql;
