<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Users {
    public function register($db, $username, $password, $email, $firstname, $lastname) {
        try {
            // check if username exists
            $emailCheck = $db->prepare("select * from users where email = '" . $email . "'");
            $emailCheck->execute();
            if ($emailCheck->rowCount() <= 0) {
               $userCheck = $db->prepare("select * from users where username = '". $username ."'");
               $userCheck->execute();
               if ($userCheck->rowCount() <= 0) {

                   $cryptPass = password_hash($password, PASSWORD_DEFAULT);
                   $passwordResetCode = crypt($password, "G0dASh0ftH3W0r1d");

                    // if there is no error below code run
                    $userResult = $db->prepare("insert into users (username, password, passwordResetCode, email, create_datetime, edit_datetime, active) values ('{$username}', '{$cryptPass}', '{$passwordResetCode}', '{$email}', NOW(), NOW(), 'Y')");
                    $userResult->execute();



                    return json_encode(array("status" => "success"));
               } else {
                   return json_encode(array("status" => "fail", "message" => "username already exist"));
               }
            } else {
                    return json_encode(array("status" => "fail", "message" => "user with that email address already exist"));
            } 

        } catch (PDOException $e) { // The authorization query failed verification

            return json_encode(array("status" => "failed",
                                        "error" => "Catch Exception: " . $e->getMessage(),

            ));
        }
    }
    public function login($db,$username,$password) {
        try {

                // if there is no error below code run
                $userCount = $db->prepare( "select id, password from users where username = '" . $username . "' and active = 'Y'" );
                $userCount->execute();
                $row = $userCount->fetchAll();

                if($userCount->rowCount() > 0 && password_verify($password,$row[0]['password']))
                {
                          // JWT could go here
                          return json_encode(array("status" => "success", "userid" => $row[0]['id']));
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
}
