# FlakePHP UnitTests

FlakePHP UnitTests provides remote data testing for Flash Builder Mobile Apps using SQLite

## Installation

* Clone/Copy the files in this directory into `app/plugins/flakephp_tests`
* simply post an array of fixtures so that cake receives it as $this->data['LoadCakeFixtures'] to http://your_app/flakephp_tests (eg: $this->data['LoadCakeFixtures] = array('app.post', 'app.comment', 'app.tag'))
* all posted fixtures will output their fixture records as a json encoded array (with no layouts rendered of course)
* see config/bootstrap.php for optional configuration variables

