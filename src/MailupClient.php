<?php

namespace Caereservices\Mailup;

use Caereservices\Mailup\MailupStatus as MailupStatus;
use Caereservices\Mailup\MailupException as MailupException;
use Caereservices\Mailup\MailupClass as MailupClass;

/**
*  MailupClient Class - A Mailup.com platform API Interface
*
*  @author Massimo Villalta
*/
class MailupClient {

   private $mailUp = null;
   private $clientLogged = false;

   protected function makeRecipientsRequest($userData , $_dynafields) {
      if( !isset($userData["mail"]) || $userData["mail"] == "" ) return "";
      $fields = [];
      $dynafields = json_decode($_dynafields, true);
      foreach( $dynafields["Items"] as $df ) {
         if( $df["Description"] == "firstname" ) {
            if( isset($userData["name"]) && $userData["name"] != "" ) {
               $df["Value"] = $userData["name"];
               $fields[] = $df;
            }
         }
         if( $df["Description"] == "lastname" ) {
            if( isset($userData["surname"]) && $userData["surname"] != "" ) {
               $df["Value"] = $userData["surname"];
               $fields[] = $df;
            }
         }
         if( $df["Description"] == "phone" ) {
            if( isset($userData["mobile"]) && $userData["mobile"] != "" ) {
               $df["Value"] = $userData["mobile"];
               $fields[] = $df;
            }
         }
      }
      $retVal = [
         "Email" => $userData["mail"],
         "Fields" => $fields
      ];
      if( isset($userData["mobile"]) && $userData["mobile"] != "" ) {
         $retVal["MobileNumber"] = (substr($userData["mobile"], 0, 3) == "+39" ? substr($userData["mobile"], 5) : substr($userData["mobile"], 3));
         $retVal["MobilePrefix"] = "0039" . (substr($userData["mobile"], 0, 3) == "+39" ? substr($userData["mobile"], 3, 3) : substr($userData["mobile"], 0, 3));
      }
      return json_encode($retVal);
   }

   function __construct($inClientId = "", $inClientSecret = "", $inCallbackUri = "") {
      if( ($inClientId != "") && ($inClientSecret != "") && ($inCallbackUri != "") ) {
         $this->mailUp = new MailupClass($inClientId, $inClientSecret, $inCallbackUri);
      }
   }

   function login($user = "", $password = "") {
      if( $this->mailUp && ($user != "") && ($password != "") ) {
         try {
            $this->clientLogged = $this->mailUp->logOnWithPassword($user, $password);
            if( $this->clientLogged ) return MailupStatus::OK;
            return MailupStatus::ERR_NOT_LOGGED_IN;
         } catch ( MailUpException $e ) {
            return MailupStatus::ERR_MAILUP_EXCEPTION;
         }
      }
      return MailupStatus::ERR_INVALID_PARAMETER;
   }

   function delUserFromGroup($mail = "", $groupName = "") {
      if( $this->clientLogged ) {
         if( $mail != "" && $groupName != "" ) {
            try {
               $url = $this->mailUp->getConsoleEndpoint() . "/Console/List/1/Recipients/Subscribed?filterby=\"Email.Contains('" . $mail . "')\"";
               $result = $this->mailUp->callMethod($url, "GET", null, "JSON");
               if( $result === false ) return MailupStatus::ERR_GETTING_USERDATA;
               $result = json_decode($result);
               $itemID = -1;
               if( count($result->Items) > 0 ) {
                  $itemID = $result->Items[0]->idRecipient;
                  $url = $this->mailUp->getConsoleEndpoint() . "/Console/List/1/Groups";
                  $result = $this->mailUp->callMethod($url, "GET", null, "JSON");
                  if( $result === false ) return MailupStatus::ERR_GETTING_GROUPS;
                  $result = json_decode($result);
                  $groupId = -1;
                  $arr = $result->Items;
                  for( $i = 0; $i < count($arr); $i++ ) {
                     $group = $arr[$i];
                     if( $groupName == $group->Name) {
                        $groupId = $group->idGroup;
                        break;
                     }
                  }
                  if( $groupId != -1 && $itemID != -1 ) {
                     $url = $this->mailUp->getConsoleEndpoint() . "/Console/Group/" . $groupId . "/Unsubscribe/" . $itemID;
                     $result = $this->mailUp->callMethod($url, "DELETE", null, "JSON");
                     return MailupStatus::OK;
                  }
                  return MailupStatus::ERR_GETTING_GROUPS;
               }
               return MailupStatus::ERR_USERDATA_NOTFOUND;
            } catch (MailUpException $e) {
               return MailupStatus::ERR_MAILUP_EXCEPTION;
            }
         } else {
            return MailupStatus::ERR_INVALID_PARAMETER;
         }
      } else {
         return MailupStatus::ERR_NOT_LOGGED_IN;
      }
   }

   function addGroup($groupName = "") {
      if( $this->clientLogged ) {
         if( $groupName != "" ) {
            try {
               $url = $this->mailUp->getConsoleEndpoint() . "/Console/List/1/Groups";
               $result = $this->mailUp->callMethod($url, "GET", null, "JSON");
               if( $result === false ) return MailupStatus::ERR_GETTING_GROUPS;
               $result = json_decode($result);
               $groupId = -1;
               $arr = $result->Items;
               for( $i = 0; $i < count($arr); $i++ ) {
                  $group = $arr[$i];
                  if( $groupName == $group->Name) {
                     $groupId = $group->idGroup;
                     break;
                  }
               }
               if( $groupId == -1 ) {
                  $url = $this->mailUp->getConsoleEndpoint() . "/Console/List/1/Group";
                  $groupRequest = "{\"Deletable\":true,\"Name\":\"" . $groupName . "\",\"Notes\":\"". $groupName . "\"}";
                  $result = $this->mailUp->callMethod($url, "POST", $groupRequest, "JSON");
                  if( $result === false ) return MailupStatus::ERR_CREATING_GROUPS;
                  $result = json_decode($result);
                  $arr = $result->Items;
                  for( $i = 0; $i < count($arr); $i++ ) {
                     $group = $arr[$i];
                     if( $groupName == $group->Name) {
                        $groupId = $group->idGroup;
                        break;
                     }
                  }
               }
               if( $groupId != -1 ) {
                  return MailupStatus::OK;
               }
               return MailupStatus::ERR_CREATING_GROUPS;
            } catch (MailUpException $e) {
               return MailupStatus::ERR_MAILUP_EXCEPTION;
            }
         } else {
            return MailupStatus::ERR_INVALID_PARAMETER;
         }
      } else {
         return MailupStatus::ERR_NOT_LOGGED_IN;
      }
   }

   function addUserToGroup($userData = [], $groupName = "") {
      if( $this->clientLogged ) {
         if( is_array($userData) && (count($userData) > 0) && ($groupName != "") ) {
            try {
               $url = $this->mailUp->getConsoleEndpoint() . "/Console/List/1/Groups";
               $result = $this->mailUp->callMethod($url, "GET", null, "JSON");
               if( $result === false ) return MailupStatus::ERR_GETTING_GROUPS;
               $result = json_decode($result);
               $groupId = -1;
               $arr = $result->Items;
               for( $i = 0; $i < count($arr); $i++ ) {
                  $group = $arr[$i];
                  if( $groupName == $group->Name) {
                     $groupId = $group->idGroup;
                     break;
                  }
               }
               if( $groupId == -1 ) {
                  $url = $this->mailUp->getConsoleEndpoint() . "/Console/List/1/Group";
                  $groupRequest = "{\"Deletable\":true,\"Name\":\"" . $groupName . "\",\"Notes\":\"". $groupName . "\"}";
                  $result = $this->mailUp->callMethod($url, "POST", $groupRequest, "JSON");
                  if( $result === false ) return MailupStatus::ERR_CREATING_GROUPS;
                  $result = json_decode($result);
                  $arr = $result->Items;
                  for( $i = 0; $i < count($arr); $i++ ) {
                     $group = $arr[$i];
                     if( $groupName == $group->Name) {
                        $groupId = $group->idGroup;
                        break;
                     }
                  }
               }
               if( $groupId != -1 ) {
                  $url = $this->mailUp->getConsoleEndpoint() . "/Console/Recipient/DynamicFields?PageNumber=0&PageSize=30&orderby=\"Id+asc\"";
                  $result = $this->mailUp->callMethod($url, "GET", null, "JSON");
                  if( $result === false ) return MailupStatus::ERR_GETTING_FIELDS;
                  $recipientRequest = $this->makeRecipientsRequest($userData, $result);
                  if( $recipientRequest != "" ) {
                     $url = $this->mailUp->getConsoleEndpoint() . "/Console/Group/" . $groupId . "/Recipient";
                     $result = $this->mailUp->callMethod($url, "POST", $recipientRequest, "JSON");
                     if( $result === false ) return MailupStatus::ERR_INVALID_USERDATA;
                     return MailupStatus::OK;
                  }
               }
               return MailupStatus::ERR_CREATING_GROUPS;
            } catch (MailUpException $e) {
               return MailupStatus::ERR_MAILUP_EXCEPTION;
            }
         } else {
            return MailupStatus::ERR_INVALID_PARAMETER;
         }
      } else {
         return MailupStatus::ERR_NOT_LOGGED_IN;
      }
   }

}
