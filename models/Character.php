<?php
class Character {
    public function addNew($db, $data) {
        $RSN = str_replace(' ','_',$data[0]['rsn']);
        
        $characterCheck = $db->prepare("select * from characters where rsn = '{$RSN}' and active = 'Y'");
        $characterCheck->execute();
        
        if ($characterCheck->rowCount() <= 0) {
            $osrsAPI ="http://services.runescape.com/m=hiscore_oldschool/index_lite.ws?player=";
            $hsLink = $osrsAPI . $RSN;
            
            $characterAdd = $db->prepare("insert into characters(rsn, userID, characterType, highScoreLink, active) VALUES ('{$RSN}', {$data[0]['userID']}, '{$data[0]['accountType']}','{$hsLink}', 'Y'");
            echo "insert into characters(rsn, userID, characterType, highScoreLink, active) VALUES ('{$RSN}', {$data[0]['userID']}, '{$data[0]['accountType']}','{$hsLink}', 'Y'";
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