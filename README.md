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
## AVAILABLE METHODS

## ERROR CODES

## AUTHORS
* **Massimo Villalta** - *(c) 2017* - [Caereservices.it](http://www.caereservice.it)
