<?php
class Character {
    public function addNew($db, $data) {
        $characterCheck = $db->prepare("select * from characters where rsn = {$data[0]['rsn']} and active = 'Y'");
        $characterCheck->execute();
        
        if ($characterCheck->rowCount() <= 0) {
            $osrsAPI ="http://services.runescape.com/m=hiscore_oldschool/index_lite.ws?player=";
            $hsLink = $osrsAPI . $datap[0]['rsn'];
            
            $characterAdd = $db->prepare("insert into characters(rsn, userID, characterType, highScoreLink, active) VALUES ('{$data[0]['rsn']}', {$data[0]['userID']}, '{$data[0]['accountType']}','{$hsLink}', 'Y'");
            if($characterAdd->execute()){     
                return json_encode(array("status" => "success"));
        } else {
            return json_encode(array("status" => "fail", "message" => "there was an error"));
        }
                
        } else {
               return json_encode(array("status" => "fail", "message" => "rsn already exist"));
        }
    }
}