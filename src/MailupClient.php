<?php

namespace Caereservices\Mailup;

use MailupStatus;
use MailupException;
use MailupClass;

/**
*  MailupClient Class - A Mailup.com platform API Interface
*
*  @author Massimo Villalta
*/
class MailupClient {

   private $mailUp = null;
   private $clientLogged = false;

   protected function makeRecipientsRequest($userData) {
      if( !isset($userData["mail"]) || $userData["mail"] == "" ) return "";
      if( !isset($userData["name"]) || $userData["name"] == "" ) return "";
      $retVal = [
         "Email" => $userData["mail"],
         "Fields" => [
            "Description" => (isset($userData["description"]) && $userData["description"] != "" ? $userData["description"] : "")
         ],
         "MobileNumber" => (isset($userData["mobile"]) && $userData["mobile"] != "" ? substr($userData["mobile"], 3) : ""),
         "MobilePrefix" => (isset($userData["mobile"]) && $userData["mobile"] != "" ? substr($userData["mobile"], 0, 3) : ""),
         "Name" => $userData["name"]
      ];
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

   function addMailToGroup($userData = [], $groupName = "HOMELIKE") {
      if( $this->clientLogged ) {
         if( is_array($userData) && (count($userData) > 0) && ($groupName != "") ) {
            try {
               $url = $this->mailUp->getConsoleEndpoint() . "/Console/List/1/Groups";
               $result = $this->mailUp->callMethod($url, "GET", null, "JSON");
               if( $result === false ) return Mailup::ERR_GETTING_GROUPS;
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
                  $url = $this->mailUp->getConsoleEndpoint() . "/Console/Recipient/DynamicFields";
                  $result = $this->mailUp->callMethod($url, "GET", null, "JSON");
                  if( $result === false ) return MailupStatus::ERR_CREATE_RECIPIENT;
                  $url = $this->mailUp->getConsoleEndpoint() . "/Console/Group/" . $groupId . "/Recipients";
                  $recipientRequest = $this->makeRecipientsRequest($userData);
                  if( $recipientRequest != "" ) {
                     $result = $this->mailUp->callMethod($url, "POST", $recipientRequest, "JSON");
                     if( $result === false ) return MailupStatus::ERR_INVALID_USERDATA;
                     $importId = $result;
                     $url = $this->mailUp->getConsoleEndpoint() . "/Console/Import/" . $importId;
                     $result = $this->mailUp->callMethod($url, "GET", null, "JSON");
                     if( $result === false ) return MailupStatus::ERR_CHECK_RECIPIENT;
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
