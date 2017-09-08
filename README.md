# MAILUP CLIENT CLASS for Laravel 5.x

This class helps you to use the mailing functionality of MailUp platform with your Laravel 5.x framework.

## Installation

Using *composer* insert into **composer.json** the following block code:
```
"repositories": [
        {
            "url": "https://github.com/caereservices/mailup.git",
            "type": "git"
        }
    ], ...

"require": {
	 "caereservices/mailup": "dev-master", ...
```
then run **composer update**

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
      $result = $mailUp->login($USER, $PASSWORD, $LISTNAME);
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
```
   $result = $mailUp->login(<USER>, <PASSWORD> [, <LISTNAME>]);
```

Parameter:
* **USER** : Username for Mailup platform (usually *mXXXXX*)
* **PASSWORD** : Password for Mailup platform
* **LISTNAME** : *(optional)* The name of list of recipients to use

Return values:
* **MailupStatus::OK** - logged in correctly
* **MailupStatus::ERR_NOT_LOGGED_IN** - Username or password are incorrectly
* **MailupStatus::ERR_INVALID_PARAMETER** - One or many parameter ar invalid or empty
* **MailupStatus::ERR_MAILUP_EXCEPTION** - Mailup Platform exception
* **MailupStatus::ERR_LIST_NOT_FOUND** - If *LISTNAME* is specified but not exist in Mailup platform

### changeList
```
   $result = $mailUp->changeList(<LISTNAME>);
```
If *LISTNAME* doesn't exist the class try to create immediately.

Parameter:
* **LISTNAME** : The name of list of recipients to use

Return values:
* **MailupStatus::OK** - List changed correctly
* **MailupStatus::ERR_NOT_LOGGED_IN** - The method are called without make login
* **MailupStatus::ERR_INVALID_PARAMETER** - One or many parameter ar invalid or empty
* **MailupStatus::ERR_MAILUP_EXCEPTION** - Mailup Platform exception
* **MailupStatus::ERR_LIST_NOT_CREATED** - The *LISTNAME* not exist in Mailup platform and cannot be created

### addGroup
```
   $result = $mailUp->addGroup(<GROUPNAME>);
```
Parameter:
* **GROUPNAME** : The name of the group to be created

Return values:
* **(number > 0)** - *Group* created and it's ID are returned, if the *Group* exist the method return it's ID.
* **MailupStatus::ERR_NOT_LOGGED_IN** - The method are called without make login
* **MailupStatus::ERR_INVALID_PARAMETER** - One or many parameter ar invalid or empty
* **MailupStatus::ERR_MAILUP_EXCEPTION** - Mailup Platform exception
* **MailupStatus::ERR_CREATING_GROUPS** - The *GROUPNAME* not exist in Mailup platform and cannot be created

## Reference
For all reference and specification on API call for Mailup platform refer [here](http://help.mailup.com/display/mailupapi/Introducing+the+MailUp+API)

## Author
**Massimo Villalta** - *(c) 2017* - [Caereservices.it](http://www.caereservice.it)
