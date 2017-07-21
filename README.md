# REST Log

REST Log is a small extension for [CiviCRM](https://civicrm.org) which maintains a log of API calls made through the [REST interface](https://wiki.civicrm.org/confluence/display/CRMDOC/REST+interface). This is useful if you have a external service which interfaces with CiviCRM through REST, and you would like an audit trail for the actions which the service performs.

## Features

* Log calls to a file and to a database table.
* View and filter the logged API calls through an interface within CiviCRM.

## Usage

1. When enabled, this extension will immediately begin logging REST API calls to both a file and a database table.
1. To view the log entries, go to **Administer > REST Log** (or `/civicrm/a/#/restlog`).
1. Also find log entries in a file called `REST.log` alongside your CiviCRM log file. (e.g. in Drupal installations this is in: `sites/default/files/civicrm/ConfigAndLog`) 
1. To stop logging, disable the extension.

## Known limitations

* There is not yet an interface for *clearing* the log entries.

