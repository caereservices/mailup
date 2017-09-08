<?php

namespace Caereservices\Mailup;

class MailupStatus {
   const OK = -99;
   const MESSAGE_SENDED = -1;
   const ERR_GETTING_GROUPS = -10;
   const ERR_CREATING_GROUPS = -11;
   const ERR_CREATE_RECIPIENT = -12;
   const ERR_INVALID_USERDATA = -13;
   const ERR_CHECK_RECIPIENT = -14;
   const ERR_MAILUP_EXCEPTION = -15;
   const ERR_INVALID_PARAMETER = -16;
   const ERR_NOT_LOGGED_IN = -17;
   const ERR_GETTING_FIELDS = -18;
   const ERR_GETTING_USERDATA = -19;
   const ERR_USERDATA_NOTFOUND = -20;
   const ERR_NO_TEMPLATES = -21;
   const ERR_NO_RECIPIENTS = -22;
   const ERR_CANT_CREATE_MESSAGE = -23;
   const ERR_MESSAGE_TEXT_EMPTY = -24;
   const ERR_MESSAGE_NOT_SENDED = -25;
   const ERR_UNKNOW_LIST = -26;
   const ERR_ADDING_USER = -27;
   const ERR_GETTING_DATA = -28;
   const ERR_LIST_NOT_CREATED = -29;
   const ERR_LIST_NOT_FOUND = -30;
   const ERR_ATTACH_FILE_NOT_EXIST = -31;
   const ERR_NO_LIST_DATA = -32;
   const ERR_INVALID_LIST_DATA = -33;
   const ERR_LIST_NOT_CHANGED = -34;
}
