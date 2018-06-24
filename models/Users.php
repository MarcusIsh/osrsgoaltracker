<?php



class Users
{

    /**
     * The table name
     *
     * @var string
     */
    public $table = "users";

    public function login($db,$username,$password) {
        try {

                // if there is no error below code run
                $userCount = $db->prepare( "select u.id, u.password, u.userTypeID, m.entityid, e.subscriptionTypeId from users u join members m on m.userID = u.id join entities e on m.entityid = e.id where u.username = '" . $username . "' and u.status = 'Active'" );
                $userCount->execute();
                $row = $userCount->fetchAll();
                
                if($userCount->rowCount() > 0 && password_verify($password,$row[0]['password']))
                {
                          // JWT could go here
                          return json_encode(array("status" => "success", "userid" => $row[0]['id'], "entityid" => $row[0]['entityid'], "userTypeID" => $row[0]['userTypeID'], "subscriptionTypeId" => $row[0]['subscriptionTypeId']));
                   } else {
                          return json_encode(array("status" => "failed",
                                                                    "error" => "Invalid Username or Password!"
                          ));
                   }
        } catch (PDOException $e) { // The authorization query failed verification
             return json_encode(array("status" => "failed",
                                                            "error" => "Catch Exception: " . $e->getMessage()
             ));
        }
    }


    public function registerapi($db,$firstName,$lastName,$email,$password, $company) {
        try {
            // check if username exists
            $userCount = $db->prepare("select * from users where username = '" . $email . "'");
            $userCount->execute();
            if ($userCount->rowCount() <= 0) {

                $entityid = 0; // Set default lastInsertId to send back

                $urls = json_encode(array());
                $configurationSettings = json_encode(array());

                // if there is no error below code run
                $entitiesResult = $db->prepare( "insert into entities (entityTypeID, name, urls, status, logoURL, configurationSettings, createdAt, updatedAt)
                                                values ('1', '" . $company . "', '" . $urls . "', 'Active', '', '" . $configurationSettings . "', '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "') ");

                $entitiesResult->execute();

                if ($entitiesResult->rowCount() <= 0) {
                    return json_encode(array("status" => "failed",
                                            "error" => "Unable to create company!"
                    ));
                }
                else {

                    $entityid = $db->lastInsertId();

                    // if there is no error below code run
                    $userResult = $db->prepare("insert into users (username, password, passwordResetCode, status, createdAt, updatedAt) values (:username, :password, :passwordResetCode, :status, :createdAt, :updatedAt)");
                    $userid = $userResult->execute(array("username" => $email,
                                                        "password" => password_hash($password, PASSWORD_BCRYPT),
                                                        "passwordResetCode" => crypt($password, SECRET_KEY),
                                                        "status" => "Active",
                                                        "createdAt" => date('Y-m-d H:i:s'),
                                                        "updatedAt" => date('Y-m-d H:i:s'))
                    );
                    $userid = $db->lastInsertId();
                    $code = crypt($password, SECRET_KEY);
                    // if there is no error below code run
                    $memberResult = $db->prepare("insert into members (userID, entityID, firstName, lastName, email, status, createdAt, updatedAt) values (:userID, :entityID, :firstName, :lastName, :email, :status, :createdAt, :updatedAt)");
                    $memberid = $memberResult->execute(array("userID" => $db->lastInsertId(),
                                                            "entityID" => $entityid,
                                                            "firstName" => $firstName,
                                                            "lastName" => $lastName,
                                                            "email" => $email,
                                                            "status" => "Active",
                                                            "createdAt" => date('Y-m-d H:i:s'),
                                                            "updatedAt" => date('Y-m-d H:i:s'))
                    );

                    return json_encode(array("status" => "success", "id" => $userid, "code" => $code)); 

                }

            }  
            else {
                return json_encode(array("status" => "failed",
                                            "error" => "Username Already Exists!"
                ));
            }

        } catch (PDOException $e) { // The authorization query failed verification
            return json_encode(array("status" => "failed",
                                        "error" => "Catch Exception: " . $e->getMessage()
            ));
        }
    }

    public function random_password($length = 8 ) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;
    }

    public function verifyaccount($id,$code) {
      try {
            $userurl = API_HOST . '/users/'.$id;
            $userdata = array("status" => "Active",
                      "updatedAt" => date('Y-m-d H:i:s')
            );
            // use key 'http' even if you send the request to https://...
            $useroptions = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'PUT',
                    'content' => http_build_query($userdata)
                )
            );
            $usercontext  = stream_context_create($useroptions);
            $result = json_decode(file_get_contents($userurl,false,$usercontext));
            if ($result > 0) {
                return true;
            } else {
                return false;
            }
      } catch (Exception $e) { // The authorization query failed verification
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: text/plain; charset=utf8');
            echo $e->getMessage();
            exit();
      }
    }

    public function checkforuniqueid($uniqueID) {
      try {
              $loginargs = array(
                    "transform"=>1,
                    "filter[]"=>"uniqueID,eq,".$uniqueID
              );
              $loginurl = API_HOST_URL . "/users?".http_build_query($loginargs);
              $loginoptions = array(
                  'http' => array(
                      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                      'method'  => 'GET'
                  )
              );
              $logincontext  = stream_context_create($loginoptions);
              $result = json_decode(file_get_contents($loginurl,false,$logincontext));
            if ( isset($result->users[0]->uniqueID) ) {
                echo $result->users[0]->uniqueID;
            } else {
                echo "success";
            }
      } catch (Exception $e) { // The authorization query failed verification
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: text/plain; charset=utf8');
            echo $e->getMessage();
            exit();
      }
    }

    public function getPasswordById($id) {
      try {
            $db = Flight::db();
            $result = $db->prepare( "select * from users where id = " . $id );
            $result->execute();
            $resultRow = $result->fetch();
//            print_r($resultRow);
            if(count($resultRow) > 0){
                return $resultRow["passwordResetCode"];
            } else {
                return "";
            }
      } catch (Exception $e) { // The authorization query failed verification
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: text/plain; charset=utf8');
            echo $e->getMessage();
            exit();
      }
    }

    public function checkforusername($username) {
      try {
              $usernameargs = array(
                    "transform"=>1,
                    "filter[]"=>"username,eq,".$username
              );
              $usernameurl = API_HOST_URL."/users?".http_build_query($usernameargs);
              $usernameoptions = array(
                  'http' => array(
                      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                      'method'  => 'GET'
                  )
              );
              $usernamecontext  = stream_context_create($usernameoptions);
              $result = json_decode(file_get_contents($usernameurl,false,$usernamecontext));
            if ( isset($result->users[0]->username) ) {
                echo $result->users[0]->username;
            } else {
                echo "success";
            }
      } catch (Exception $e) { // The authorization query failed verification
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: text/plain; charset=utf8');
            echo $e->getMessage();
            exit();
      }
    }

    public function resetpasswordapi($db, $id, $password) {
        try {
            $userResult = $db->prepare("update users set password = '".  password_hash($password, PASSWORD_BCRYPT) ."', passwordResetCode = '". crypt($password, SECRET_KEY) ."', updatedAt = NOW() where id = '". $id ."'");
            $userid = $userResult->execute();
            
            return true;
            
        } catch (Exception $e) { // The authorization query failed verification
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: text/plain; charset=utf8');
            echo $e->getMessage();
            exit();
        }
    }

    public function driverresetpasswordapi($username,$password) {
        try {
            $userurl = API_HOST_URL . '/users/'.$username;
            $userdata = array("password" => password_hash($password, PASSWORD_BCRYPT),
                      "updatedAt" => date('Y-m-d H:i:s')
            );
            // use key 'http' even if you send the request to https://...
            $useroptions = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'PUT',
                    'content' => http_build_query($userdata)
                )
            );
            $usercontext  = stream_context_create($useroptions);
            $userresult = file_get_contents($userurl, false, $usercontext);

            if ($userresult > 0) {
                    return "success";
            } else {
                    return "Failed";
            }
        } catch (Exception $e) { // The query failed!
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: text/plain; charset=utf8');
            echo $e->getMessage();
            exit();
        }
    }

    public function getUserValidateById($id) {
      try {
              $loginargs = array(
                    "transform"=>1,
                    "filter[0]"=>"id,eq,".$id,
                    "filter[1]"=>"status,eq,Active",
                    "filter[2]"=>"password,eq,"
              );
              $loginurl = API_HOST_URL . "/users?".http_build_query($loginargs);
              $loginoptions = array(
                  'http' => array(
                      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                      'method'  => 'GET'
                  )
              );
              $logincontext  = stream_context_create($loginoptions);
              $result = json_decode(file_get_contents($loginurl,false,$logincontext));
            if ( isset($result->users[0]->id) && $result->users[0]->id > 0 ) {
                return "success";
            } else {
                return "Failed";
            }
      } catch (Exception $e) { // The authorization query failed verification
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: text/plain; charset=utf8');
            echo $e->getMessage();
            exit();
      }
    }

    public function getMigratedUserValidateById($id,$password) {
      try {
              $loginargs = array(
                    "transform"=>1,
                    "filter[0]"=>"id,eq,".$id,
                    "filter[1]"=>"status,eq,Inactive"
              );

              $loginurl = API_HOST_URL . "/users?".http_build_query($loginargs);
              $loginoptions = array(
                  'http' => array(
                      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                      'method'  => 'GET'
                  )
              );
              $logincontext  = stream_context_create($loginoptions);
              $result = json_decode(file_get_contents($loginurl,false,$logincontext));

            if ( isset($result->users[0]->id) && $result->users[0]->id > 0 && $result->users[0]->id == $id && password_verify($password, $result->users[0]->password) ) {
                return "success";
            } else {
                return "Failed";
            }
      } catch (Exception $e) { // The authorization query failed verification
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: text/plain; charset=utf8');
            echo $e->getMessage();
            exit();
      }
    }

    public function setpasswordvalidateapi($username,$password) {
        try {
            $userurl = API_HOST_URL . '/users/'.$username;
            $userdata = array("password" => password_hash($password, PASSWORD_BCRYPT),
                      "updatedAt" => date('Y-m-d H:i:s')
            );
            // use key 'http' even if you send the request to https://...
            $useroptions = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'PUT',
                    'content' => http_build_query($userdata)
                )
            );
            $usercontext  = stream_context_create($useroptions);
            $userresult = file_get_contents($userurl, false, $usercontext);
            return true;
        } catch (Exception $e) { // The authorization query failed verification
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: text/plain; charset=utf8');
            echo $e->getMessage();
            exit();
        }
    }

    public function setmigratedpasswordvalidateapi($username,$password) {
        try {
            $userurl = API_HOST_URL . '/users/'.$username;
            $userdata = array("password" => password_hash($password, PASSWORD_BCRYPT),
                              "status" => 'Active',
                              "updatedAt" => date('Y-m-d H:i:s')
            );
            // use key 'http' even if you send the request to https://...
            $useroptions = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'PUT',
                    'content' => http_build_query($userdata)
                )
            );
            $usercontext  = stream_context_create($useroptions);
            $userresult = file_get_contents($userurl, false, $usercontext);
            return true;
        } catch (Exception $e) { // The authorization query failed verification
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: text/plain; charset=utf8');
            echo $e->getMessage();
            exit();
        }
    }

    public function maintenanceapi($type,$userID,$member_id,$entityID,$firstName,$lastName,$username,$password,$userTypeID,$uniqueID,$textNumber) {
          try {

                $userdata = array(
                            "userTypeID" => $userTypeID,
                            "username" => $username,
                            "uniqueID" => $uniqueID,
                            "textNumber" => $textNumber,
                            "status" => 'Active'
                );

                if ($password > "") {
                    $userdata["password"] = password_hash($password, PASSWORD_BCRYPT);
                }

                $userurl = API_HOST_URL . '/users';

                if ($type == "PUT") {
                    $userurl .= "/".$userID;
                    $userdata["updatedAt"] = date('Y-m-d H:i:s');
                } else {
                    $userdata["createdAt"] = date('Y-m-d H:i:s');
                    $userdata["updatedAt"] = date('Y-m-d H:i:s');
                }

                // use key 'http' even if you send the request to https://...
                $useroptions = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => $type,
                        'content' => http_build_query($userdata)
                    )
                );
                $usercontext = stream_context_create($useroptions);
                $userresult = file_get_contents($userurl, false, $usercontext);

                $memberdata = array(
                            "firstName" => $firstName,
                            "lastName" => $lastName
                );

                $memberurl = API_HOST_URL . '/members';

                if ($type == "PUT") {
                    $memberurl .= "/".$member_id;
                    $memberdata["updatedAt"] = date('Y-m-d H:i:s');
                } else {
                    $memberdata["createdAt"] = date('Y-m-d H:i:s');
                    $memberdata["updatedAt"] = date('Y-m-d H:i:s');
                    $memberdata["userID"] = $userresult;
                    $memberdata["entityID"] = $entityID;
                }

                // use key 'http' even if you send the request to https://...
                $memberoptions = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($memberdata)
                    )
                );

                $membercontext = stream_context_create($memberoptions);
                $memberresult = file_get_contents($memberurl, false, $membercontext);

                if ($userTypeID == 5 && $type == "POST") { // This is a driver being created - ONLY SEND EMAIL NOTIFICATOIN IF THIS IS A POST (CREATE)

                    // Send a text to the new driver
                    $messagecenter = Flight::messagecenter();
                    $msg = "Your NEC Driver account has been setup. Your login credentials are: User Login ID: " . $userresult . " Your Password: " . $password;
                    $messagecenter->sendSMS($textNumber, $msg);

                } else {

                    // Send email to driver
                    $numSent = 0;
                    $to = array($username => $firstName . " " . $lastName);
                    $from = array("operations@nationwide-equipment.com" => "Nationwide Operations Control Manager");
                    //$templateresult = json_decode(file_get_contents(API_HOST.'/api/email_templates?filter=title,eq,Authorize Account'));

                    $templateargs = array("filter"=>"title,eq,User Setup Notification");
                    $templateurl = API_HOST_URL."/email_templates?".http_build_query($templateargs);
                    $templateoptions = array(
                        'http' => array(
                            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method'  => 'GET'
                        )
                    );
                    $templatecontext  = stream_context_create($templateoptions);
                    $templateresult = json_decode(file_get_contents($templateurl,false,$templatecontext));
                    $subject = $templateresult->email_templates->records[0][6];
                    $body = "Hello " . $firstName . ",<br /><br />\n";
                    $body .= $templateresult->email_templates->records[0][2];
                    $body .= "<p>Your Username is: " . $username . "</p>\n";
                    $body .= "<p><a href=".HTTP_HOST."/setpassword/".$userresult.">Click HERE</a> to create a password and activate your account</p>\n";
                    if (count($templateresult) > 0) {
                      try {
                        $numSent = sendmail($to, $subject, $body, $from);
                      } catch (Exception $mailex) {
                        echo $mailex;
                      }
                    }

                }

                echo "success";

          } catch (Exception $e) { // The authorization query failed verification
                header('HTTP/1.1 404 Not Found');
                header('Content-Type: text/plain; charset=utf8');
                echo $e->getMessage();
                exit();
          }

    }

    public function forgotpasswordapi($db, $email) {
     
              // Get the data for the email to send
              $member = $db->prepare( "select members.*, users.passwordResetCode from members left join users on users.id = members.userID where members.email = '" . $email . "' and users.status = 'Active'" );
              $member->execute();
              //return "select * from members where email = '" . $email . "' and status = 'Active'";

              if ( $member->rowCount() > 0 ) {
                    $row = $member->fetch();
                    // Send email to new user
                    $user_id = $row['userID'];
                    $firstName = $row['firstName'];
                    $lastName = $row['lastName'];
                    $numSent = 0;
                    $code = $row['passwordResetCode'];
                    $code = str_replace("/", "-", $code);
                    $code = str_replace("?", "-", $code);
                    $to = $email;
//                    $from = array("Mcasswell@dubtel.com" => "Dubtel Mobile");

                     // Get the data for the email to send
                    $emailTemplate = $db->prepare( "select * from email_templates where title = 'Forgot Password' " );
                    $emailTemplate->execute();
                    //return "select * from email_templates where title = 'Forgot Password' and status = 'Active'" ;

                    if ($emailTemplate->rowCount() > 0) {
                            $templateRow = $emailTemplate->fetch();
                            $subject = "Dubtel One Password Reset";
                            $content = "Hello " . $firstName . ",<br /><br />\n";
                            $content .= $templateRow['body'];
                            $content .= "<p><a href='".HTTP_HOST."/resetpassword/".$user_id."/".$code."'>Click HERE</a> to reset your password.</p>\n";
                            
                            
                            $sg = sendGridMail($to, $subject, $content);
                            
                            if($sg->statusCode() == 202){
                                print "Email Sent successfully";
                                $status = "success";
                                return $status;
                            } else {
                                print "Email was not sent";
                                echo $sg->statusCode();
                                
                                echo $sg->body();
                            }
                               
                    }
              }
      }
      public function newuserpassword($db, $email) {
     
              // Get the data for the email to send
              $member = $db->prepare( "select members.*, users.passwordResetCode from members left join users on users.id = members.userID where members.email = '" . $email . "' and users.status = 'Active'" );
              $member->execute();
              //return "select * from members where email = '" . $email . "' and status = 'Active'";

              if ( $member->rowCount() > 0 ) {
                    $row = $member->fetch();
                    // Send email to new user
                    $user_id = $row['userID'];
                    $firstName = $row['firstName'];
                    $lastName = $row['lastName'];
                    $numSent = 0;
                    $code = $row['passwordResetCode'];
                    $code = str_replace("/", "-", $code);
                    $code = str_replace("?", "-", $code);
                    $to = $email;
//                    $from = array("Mcasswell@dubtel.com" => "Dubtel Mobile");

                     // Get the data for the email to send
                    $emailTemplate = $db->prepare( "select * from email_templates where title = 'User Setup Notification' " );
                    $emailTemplate->execute();
                    //return "select * from email_templates where title = 'Forgot Password' and status = 'Active'" ;

                    if ($emailTemplate->rowCount() > 0) {
                            $templateRow = $emailTemplate->fetch();
                            $subject = "Dubtel One Password Reset";
                            $content = "Hello " . $firstName . ",<br /><br />\n";
                            $content .= $templateRow['body'];
                            $content .= "<p><a href='".HTTP_HOST."/resetpassword/".$user_id."/".$code."'>Click HERE</a> to reset your password.</p>\n";
                            
                            
                            $sg = sendGridMail($to, $subject, $content);
                            
                            if($sg->statusCode() == 202){
//                                print "Email Sent successfully";
//                                $status = "success";
//                                return $status;
                                return true;
                                
                            } else {
//                                print "Email was not sent";
//                                echo $sg->statusCode();
//                                echo $sg->body();
                                return false;
                            }
                               
                    }
              }
      }

    public function proxylogin(&$db,$username) {

        try {

              $dbhandle = new $db('mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS);
              $result = $dbhandle->query("select users.id, users.username, users.status, users.userTypeID,
                                          members.id as memberID, members.entityID,
                                          entities.entityTypeID,
                                          user_types.name
                                         from users
                                         left join members on users.id = members.userID
                                         left join entities on entities.id = members.entityID
                                         left join user_types on user_types.id = users.userTypeID
                                         where users.username = '" . $username . "'");

              if (count($result) > 0) {
                  $row = $result->FetchAll();
                  if ($row[0]['status'] == "Active") {
                        $_SESSION['existinguserid'] = $_SESSION['userid'];
                        $_SESSION['userid'] = $row[0]['id'];
                        $_SESSION['user'] = $row[0]['id']; // Setup for api authentication
                        $_SESSION['usertypeid'] = $row[0]['userTypeID'];
                        $_SESSION['memberid'] = $row[0]['memberID'];
                        $_SESSION['entityid'] = $row[0]['entityID'];
                        $_SESSION['entitytype'] = $row[0]['entityTypeID'];
                        $_SESSION['usertypename'] = $row[0]['name'];
                        unset($_SESSION['invalidPassword']);
                        return $_SESSION['existinguserid'];
                  } else {
                    $_SESSION['invalidPassword'] = 'Account Has Not Been Activated!';
                    return false;
                  }
              } else {
                $_SESSION['invalidPassword'] = 'Username Not Found!';
                return false;
              }
        } catch (Exception $e) { // The authorization query failed verification
              //header('HTTP/1.1 404 Not Found');
              //header('Content-Type: text/plain; charset=utf8');
              //echo $e->getMessage();
              //exit();
              $_SESSION['invalidPassword'] = 'Username Not Found!';
              return false;
        }
    }

    public function proxylogout(&$db,$existinguserid) {

        try {

              $dbhandle = new $db('mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS);
              $result = $dbhandle->query("select users.id, users.username, users.status, users.userTypeID,
                                          members.id as memberID, members.entityID,
                                          entities.entityTypeID,
                                          user_types.name
                                          from users
                                          left join members on users.id = members.userID
                                          left join entities on entities.id = members.entityID
                                          left join user_types on user_types.id = users.userTypeID
                                          where users.id = '" . $existinguserid . "'
                                          and users.status = 'Active'");

              if (count($result) > 0) {
                    $row = $result->FetchAll();
                    $_SESSION['userid'] = $row[0]['id'];
                    $_SESSION['user'] = $row[0]['id']; // Setup for api authentication
                    $_SESSION['usertypeid'] = $row[0]['userTypeID'];
                    $_SESSION['memberid'] = $row[0]['memberID'];
                    $_SESSION['entityid'] = $row[0]['entityID'];
                    $_SESSION['entitytype'] = $row[0]['entityTypeID'];
                    $_SESSION['usertypename'] = $row[0]['name'];
                    unset($_SESSION['existinguserid']);
                    unset($_SESSION['invalidPassword']);
                    return true;
              } else {
                    $_SESSION['invalidPassword'] = 'Username Not Found!';
                    return false;
              }
        } catch (Exception $e) { // The authorization query failed verification
              //header('HTTP/1.1 404 Not Found');
              //header('Content-Type: text/plain; charset=utf8');
              //echo $e->getMessage();
              //exit();
              $_SESSION['invalidPassword'] = 'Username Not Found!';
              return false;
        }
    }

    public function getUserTypes($db) {
        try {

                $userTypes = array();

                // if there is no error below code run
                $userTypesResult = $db->prepare( "select * from user_types order by id" );
                $userTypesResult->execute();

                if (count($userTypesResult) > 0) {
                    while($row = $userTypesResult->fetch())
                    {
                            $userTypes[] = array("id" => $row['id'], "name" => $row['name'], "status" => $row['status']);
                    }
                }

                return json_encode(array("status" => "success", "userTypes" => $userTypes));

        } catch (PDOException $e) { // The authorization query failed verification
             return json_encode(array("status" => "failed",
                                                            "error" => "Catch Exception: " . $e->getMessage()
             ));
        }
    }

    public function getAll($db, $entityid) {
        try {

                $users = array();

                // if there is no error below code run
                $userResult = $db->prepare( "select users.id, users.userTypeID, members.entityID, members.firstName, members.lastName, members.email, users.status as usersStatus, user_types.name as userTypeName, entities.name as entityName
                                                                 from users
                                                                 left join user_types on user_types.id = users.userTypeID
                                                                 left join members on members.userId = users.id
                                                                 left join entities on entities.id = members.entityID
                                                                 where entities.id = $entityid
                                                                 order by members.entityID, members.lastName, members.firstName" );
                $userResult->execute();

                if (count($userResult) > 0) {
                    while($row = $userResult->fetch())
                    {
                            $users[] = array("id" => $row['id'], "entityID" => $row['entityID'],  "firstName" => $row['firstName'], "lastName" => $row['lastName'], "userTypeID" => $row['userTypeID'], "userTypeName" => $row['userTypeName'], "email" => $row['email'], "business" => $row['entityName'], "status" => $row['usersStatus']);
                    }
                }

                return json_encode(array("status" => "success", "users" => $users));

        } catch (PDOException $e) { // The authorization query failed verification
             return json_encode(array("status" => "failed",
                                                            "error" => "Catch Exception: " . $e->getMessage()
             ));
        }
    }

    public function post($db, $data) {
        try {

                $id = 0; // Set default lastInsertId to send back
                $password = crypt(random_bytes(8), SECRET_KEY);
                $passwordResetCode = crypt($data[0]['email'], SECRET_KEY);

                // if there is no error below code run
                $userResult = $db->prepare( "insert into users (username, password, passwordResetCode, status, userTypeID, createdAt, updatedAt)
                                                                            values ('" . $data[0]['email'] . "', '" . $password . "', '" . $passwordResetCode . "', 'Active', '" . $data[0]['userTypeID'] . "', '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "') ");
                $userResult->execute();
                $userRows = $userResult->rowCount();

                if ($userResult->rowCount() > 0) {
                        $id = $db->lastInsertId();
                }

                if ($userRows > 0) {
                        // if there is no error below code run
                        $userResult = $db->prepare("insert into members (userID, entityID, firstName, lastName,email,  status, createdAt, updatedAt)
                                                                        values ('" . $id . "',  '" . $data[0]['entityID'] . "', '" . $data[0]['firstName'] . "', '" . $data[0]['lastName'] . "', '" . $data[0]['email'] . "', 'Active', '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "') ");
                        $userResult->execute();
                        $userRows = $userResult->rowCount();
                }
                $passwordReset = $this->newuserpassword($db, $data[0]['email']);

                return json_encode(array("status" => "success", "id" => $id));

        } catch (PDOException $e) { // The authorization query failed verification
             return json_encode(array("status" => "failed",
                                                            "error" => "Catch Exception: " . $e->getMessage()
             ));
        }
    }

    public function put($db, $data) {
        try {

                // if there is no error below code run
                $memberResult = $db->prepare("update members set entityID = '" . $data[0]['entityID'] . "', firstName = '" . $data[0]['firstName'] . "', lastName = '" . $data[0]['lastName'] . "', email = '" . $data[0]['email'] . "', updatedAt = '" . $data[0]['updatedAt'] . "' where userID = '" . $data[0]['id'] . "'");
                $memberResult->execute();
                $memberRows = $memberResult->rowCount();

                 if ($memberRows > 0) {
                        // if there is no error below code run
                        $userResult = $db->prepare("update users set userTypeID = '" . $data[0]['userTypeID'] . "' where id = '" . $data[0]['id'] . "'");
                        $userResult->execute();
                        $userRows = $userResult->rowCount();
                 }

                 return json_encode(array("status" => "success", "id" => $data[0]['id']));

        } catch (PDOException $e) { // The authorization query failed verification
                 return json_encode(array("status" => "failed",
                                                                "error" => "Catch Exception: " . $e->getMessage()
                 ));
        }
    }

    public function updateProfile($db, $data) {
        try {
                //return json_encode(array("id" => $data[0]['id'], "userID" => $data[0]['userID']));

                // if there is no error below code run
                $memberResult = $db->prepare("update members set firstName = '" . $data[0]['firstName'] . "', lastName = '" . $data[0]['lastName'] . "', email = '" . $data[0]['email'] . "', updatedAt = '" . $data[0]['updatedAt'] . "' where id = '" . $data[0]['id'] . "'");
                $memberResult->execute();
                $memberRows = $memberResult->rowCount();

                 if ( !empty($data[0]['password']) ) {

                        $password = password_hash($data[0]['password'], PASSWORD_BCRYPT);
                        $passwordResetCode = crypt($data[0]['password'], SECRET_KEY);

                        $userResult = $db->prepare("update users set password = '" . $password . "', passwordResetCode = '" . $passwordResetCode . "' where id = '" . $data[0]['userID'] . "'");
                        $userResult->execute();
                        $userRows = $userResult->rowCount();
                 }

                 return json_encode(array("status" => "success", "id" => $data[0]['id']));

        } catch (PDOException $e) { // The authorization query failed verification
                 return json_encode(array("status" => "failed",
                                                                "error" => "Catch Exception: " . $e->getMessage()
                 ));
        }
    }

    public function setStatus($db, $id, $status) {
        try {

                // if there is no error below code run
                $userResult = $db->prepare("update users set status = '" . $status . "' where id = '" . $id . "'");
                $userResult->execute();
                $affectedRows = $userResult->rowCount();

                return json_encode(array("status" => "success", "id" => $id));

        } catch (PDOException $e) { // The authorization query failed verification
             return json_encode(array("status" => "failed",
                                                            "error" => "Catch Exception: " . $e->getMessage()
             ));
        }
    }

}

//$user = new User();
