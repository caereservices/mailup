# MAILUP CLIENT CLASS for Laravel 5.x

This class helps you to use the mailing functionality of MailUp platform with your Laravel 5.x framework.

## Installation

Using *composer* insert into **composer.json** the following code block:
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

## Get started

Following example show the basic steps for use this class in your code.

```
use Caereservices\Mailup\MailupStatus;
use Caereservices\Mailup\MailupException;
use Caereservices\Mailup\MailupClient;

$CLIENT_ID = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";
$CLIENT_SECRET = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";
$CALLBACK_URI = "http://localhost/callback_uri";

$USER = "mXXXXXX";
$PASSWORD = "xxxxxxxxx";

$mailUp = null;
try {
   $mailUp = new MailupClient($CLIENT_ID, $CLIENT_SECRET, $CALLBACK_URI);
   if( $mailUp ) {
      $result = $mailUp->login($USER, $PASSWORD);
      if( $result == MailupStatus::OK ) {
         ...
      }
   }
} catch (MailupException $e) {
   ...
}
```
**$CLIENT_ID** and **$CLIENT_SECRET** can be obtained follow these [guide](http://help.mailup.com/display/mailupapi/Authenticating+with+OAuth+v2)

## Available Methods

### login
```
   $result = $mailUp->login(<USER>, <PASSWORD> [, <LISTNAME>]);
```

Parameters:
* **USER** : Username for Mailup platform (usually *mXXXXX*)
* **PASSWORD** : Password for Mailup platform
* **LISTNAME** : *(optional)* The name of list of recipients to use, if not specified default list of Mailup is used

Return values:
* **MailupStatus::OK** - logged in correctly
* **MailupStatus::ERR_NOT_LOGGED_IN** - Username or password are incorrectly
* **MailupStatus::ERR_INVALID_PARAMETER** - One or many parameter ar invalid or empty
* **MailupStatus::ERR_MAILUP_EXCEPTION** - Mailup Platform exception
* **MailupStatus::ERR_LIST_NOT_FOUND** - If *LISTNAME* is specified but not exist in Mailup platform

### createList
```
   $result = $mailUp->createList(<LISTNAME>, <LISTDATA>);
```

Parameter:
* **LISTNAME** : The name of list of recipients to use
* **LISTDATA** : Array with fields for fill List data

**LISTDATA Fields**
* *name* - Name of the List
* *main_mail* - Main email address linked to the list
* *reply_to* - Mail address for reply from user
* *sender_name* - Sender name that appear to user when receive mail
* *company_name* - Your company name
* *contact_name* - Contact name in company (unnecessary match *main_mail* or *reply_to* owner)
* *address* - Your company address
* *city* - Your company city
* *country_code* - Your country code (ex. IT)
* *perm_remind* - Permission reminder (default "") see [here](http://help.mailup.com/display/mailupapi/Manage+Lists+and+Groups#ManageListsandGroups-Quicklistcreation) for information
* *web_site* - Your website URL
All fields are **mandatory**

Return values:
* **MailupStatus::OK** - List created correctly
* **MailupStatus::ERR_NOT_LOGGED_IN** - The method are called without make login
* **MailupStatus::ERR_INVALID_PARAMETER** - One or many parameter ar invalid or empty
* **MailupStatus::ERR_MAILUP_EXCEPTION** - Mailup Platform exception
* **MailupStatus::ERR_LIST_NOT_CREATED** - The *LISTNAME* not exist in Mailup platform and cannot be created
* **MailupStatus::ERR_NO_LIST_DATA** - The *LISTDATA* array is empty or null
* **MailupStatus::ERR_INVALID_LIST_DATA** - The *LISTDATA* array have one or many fields empty or null

### changeList
```
   $result = $mailUp->changeList(<LISTNAME> [, <LISTDATA>]);
```
If *LISTNAME* doesn't exist the and *LISTDATA* array is specified method try to create it

Parameter:
* **LISTNAME** : The name of list of recipients to use
* **LISTDATA** : *(optional)* Array with fields for fill List data (see **createList** for array structure)

Return values:
* **MailupStatus::OK** - List changed correctly
* **MailupStatus::ERR_NOT_LOGGED_IN** - The method are called without make login
* **MailupStatus::ERR_INVALID_PARAMETER** - One or many parameter ar invalid or empty
* **MailupStatus::ERR_MAILUP_EXCEPTION** - Mailup Platform exception
* **MailupStatus::ERR_LIST_NOT_CREATED** - The *LISTNAME* not exist in Mailup platform and cannot be created
* **MailupStatus::ERR_LIST_NOT_CHANGED** - The *LISTNAME* is invalid and the current list remain unchanged

### addGroup
```
   $result = $mailUp->addGroup(<GROUPNAME>);
```
Parameter:
* **GROUPNAME** : The name of the group to be created

Return values:
* **(number > 0)** - *Group* created and it's ID are returned, if the *Group* exist the method return it's ID.
* **MailupStatus::ERR_NOT_LOGGED_IN** - The method are called without make login
* **MailupStatus::ERR_INVALID_PARAMETER** - One or many parameter are invalid or empty
* **MailupStatus::ERR_MAILUP_EXCEPTION** - Mailup Platform exception
* **MailupStatus::ERR_CREATING_GROUPS** - The *GROUPNAME* not exist in Mailup platform and cannot be created

### addUserToGroup
```
   $result = $mailUp->addUserToGroup(<USERDATA>, <GROUPNAME>);
```
Parameters:
* **USERDATA** : Array with User data to be added to group
* **GROUPNAME** : The name of the group (can be a Group ID also)

**USERDATA Fields**
* *mail* - User email (**mandatory**)
* *name* - User firstname
* *surname* - User lastname
* *mobile* - User mobile number without international prefix (ex. +39xxxxxxx)
* *company* - User company name (if available)

Return values:
* **MailupStatus::OK** - *User* created created or *User* exist in platform
* **MailupStatus::ERR_NOT_LOGGED_IN** - The method are called without make login
* **MailupStatus::ERR_INVALID_PARAMETER** - One or many parameter are invalid or empty
* **MailupStatus::ERR_MAILUP_EXCEPTION** - Mailup Platform exception
* **MailupStatus::ERR_GETTING_FIELDS** This error is returned when we have a problem with dynamic fields of Mailup (see [here](http://help.mailup.com/display/mailupapi/Recipients#Recipients-Addasinglerecipient/subscriber-synchronousimport) for details)
* **MailupStatus::ERR_INVALID_USERDATA** - The *USERDATA* contains invalid data or incorrect field
* **MailupStatus::ERR_ADDING_USER** - The user cannot be created (added)

### sendMessage
```
   $result = $mailUp->sendMessage(<SUBJECT>, <MESSAGE>, <GROUPNAME>, <USERMAILS>, <ATTACHMENT>);
```
Parameters:
* **SUBJECT** - Subject of message
* **MESSAGE** - Text of message (can be plain text or HTML)
* **GROUPNAME** - The name of the group to send a message
* **USERMAILS** - The mail of the user(s) to send a message, can be a single mail address or array of mail address
* **ATTACHMENT** - Path to the file/image/other to attach at the message (MUST be, if present, absolute path to file, if it stay on the server, or URL), if the class don't found the attachment no data is attached to message

If either of *GROUPNAME* and/or *USERMAILS* aren't specified ("" or null is passed) the message is sent to ALL recipients (users) present in the current List.

Return values:
* **MailupStatus::MESSAGE_SENDED** - The message is correctly queued and be sended as soon as possible
* **MailupStatus::ERR_NOT_LOGGED_IN** - The method are called without make login
* **MailupStatus::ERR_INVALID_PARAMETER** - One or many parameter ar invalid or empty
* **MailupStatus::ERR_MAILUP_EXCEPTION** - Mailup Platform exception
* **MailupStatus::ERR_MESSAGE_NOT_SENDED** - The message cannot be sended
* **MailupStatus::ERR_CANT_CREATE_MESSAGE** - The system cannot create (prepare) the message before send it
* **MailupStatus::ERR_MESSAGE_TEXT_EMPTY** - The *MESSAGE* parameter is empty or null

### getTemplateList
```
   $result = $mailUp->getTemplateList();
```
Almost one Mail Template MUST be created belong Mailup platform before using this method and methods linked, you can find all information and guide on templates [here](http://help.mailup.com/display/MUG/List+building+tools)

Return values:
* **TemplateListStructure** - Template list present on Mailup platform
* **MailupStatus::ERR_NO_TEMPLATES** - There isn't template in Mailup platform
* **MailupStatus::ERR_NOT_LOGGED_IN** - The method are called without make login
* **MailupStatus::ERR_MAILUP_EXCEPTION** - Mailup Platform exception
* **MailupStatus::ERR_UNKNOW_LIST** - The current List have a problem

### sendFromTemplate
```
   $result = $mailUp->sendFromTemplate(<TEMPLATEID>, <GROUPNAME>, <USERMAILS>, <ATTACHMENT>);
```
**SUBJECT** and **MESSAGE** are obviously unnecessary :)

Refer to **sendMessage** for most parameter and return values except follows

Parameter:
* **TEMPLATEID** - Template's ID obtained from selected item of *getTemplateList* returned list

Return values:
* **MailupStatus::ERR_NO_TEMPLATES** - The template ID is incorrect or invalid

## Reference
For all reference and specification on API call for Mailup platform refer [here](http://help.mailup.com/display/mailupapi/Introducing+the+MailUp+API)

## Author
**Massimo Villalta** - *(c) 2017* - [Caereservices.it](http://www.caereservice.it)
