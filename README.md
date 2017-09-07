# MAILUP CLIENT CLASS for Laravel 5.x

This class helps you to use the mailing functionality of MailUp platform with your Laravel 5.x framework.

### Initialization

Following example show the basic steps for use this class in your code.

```
use Caereservices\Mailup\MailupStatus;
use Caereservices\Mailup\MailupException;
use Caereservices\Mailup\MailupClient;

$mailUp = null;
try {
   $mailUp = new MailupClient($CLIENT_ID, $CLIENT_SECRET, $CALLBACK_URI);
   if( $mailUp ) {
      $result = $mailUp->login($USER, $PASSWORD [, $LISTNAME]);
      if( $result != MailupStatus::OK ) {
         $mailUp = null;
      }
   }
} catch (MailupException $e) {
   ...
}
```
## Available Methods

### login
Parameter:
* USER : Username for Mailup platform (usually mXXXXX)
* PASSWORD : Password for Mailup platform
* LISTNAME : *(optional)* The name of list of recipients to use
Return values:
* MailupStatus::OK - logged in correctly
* MailupStatus::ERR_NOT_LOGGED_IN - Username or password are incorrectly
* MailupStatus::ERR_INVALID_PARAMETER - One or many parameter ar invalid or empty
* MailupStatus::ERR_MAILUP_EXCEPTION - Mailup Platform exception
* MailupStatus::ERR_LIST_NOT_FOUND - If LISTNAME is specified by not exist in Mailup platform

## Author
* **Massimo Villalta** - *(c) 2017* - [Caereservices.it](http://www.caereservice.it)
