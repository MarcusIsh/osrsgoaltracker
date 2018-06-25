<?php
class Character {
    public function addNew($db, $data) {
        $RSN = str_replace(' ','_',$data[0]['rsn']);
        
        $characterCheck = $db->prepare("select * from characters where rsn = '{$RSN}' and active = 'Y'");
        $characterCheck->execute();
        
        if ($characterCheck->rowCount() <= 0) {
            $osrsAPI ="/m=hiscore_oldschool/index_lite.ws?player=";
            $hsLink = $osrsAPI . $RSN;
//            $sql= "insert into characters(rsn, userID, characterType, highScoreLink, active) VALUES ('{$RSN}', {$data[0]['userID']}, '{$data[0]['accountType']}','{$hsLink}', 'Y')";
            $characterAdd = $db->prepare("insert into characters(rsn, userID, characterType, highScoreLink, active) VALUES ('{$RSN}', {$data[0]['userID']}, '{$data[0]['accountType']}','{$hsLink}', 'Y')");
            
            if($characterAdd->execute()){     
                return json_encode(array("status" => "success"));
        } else {
            return json_encode(array("status" => "fail", "message" => "there was an error", "sql" => $sql));
        }
                
        } else {
               return json_encode(array("status" => "fail", "message" => "rsn already exist"));
        }
    }
    public function getAll($db, $id, $userID) {
        try{
            $getAll = $db->prepare("select * from characters where id = {$id}");
            $getAll->execute();


            if($getAll->rowCount() > 0){
               while($row = $getAll->fetch())
               {
                   $character[] = array("rsn" => $row['rsn'], "link" => $row['highScoreLink'], "characterType" => $row['characterType']);
               }
            }
            return json_encode(array("status" => "success", "character" => $character));
        } catch (PDOException $e) { // The authorization query failed verification
             return json_encode(array("status" => "failed",
                                                            "error" => "Catch Exception: " . $e->getMessage()
             ));
        }
    }
}