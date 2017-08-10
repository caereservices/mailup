<?php

namespace Caereservices\Mailup;

class MailupStatus {
   const OK = 0;
   const ERR_GETTING_GROUPS = 1;
   const ERR_CREATING_GROUPS = 2;
   const ERR_CREATE_RECIPIENT = 3;
   const ERR_INVALID_USERDATA = 4;
   const ERR_CHECK_RECIPIENT = 5;
   const ERR_MAILUP_EXCEPTION = 6;
   const ERR_INVALID_PARAMETER = 7;
   const ERR_NOT_LOGGED_IN = 8;
   const ERR_GETTING_FIELDS = 9;
   const ERR_GETTING_USERDATA = 10;
   const ERR_USERDATA_NOTFOUND = 11;
}
