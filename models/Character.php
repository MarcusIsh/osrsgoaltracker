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
            return array("status" => "fail", "message" => "there was an error", "sql" => $sql);
        }
                
        } else {
               return json_encode(array("status" => "fail", "message" => "rsn already exist"));
        }
    }
    public function getAll($db, $id) {
        try{
            $getAll = $db->prepare("select * from characters where id = {$id}");
            $getAll->execute();


            if($getAll->rowCount() > 0){
               while($row = $getAll->fetch())
               {
                   $character[] = array("rsn" => $row['rsn'], "link" => $row['highScoreLink'], "characterType" => $row['characterType']);
               }
            }
            return json_encode(array("status" => "success", "character" => $character), true);
        } catch (PDOException $e) { // The authorization query failed verification
             return json_encode(array("status" => "failed",
                                                            "error" => "Catch Exception: " . $e->getMessage()
             ));
        }
    }
    function getCharName($db, $id) {
		// Create an array of skills
//		
//                $charInfo = $db->prepare("select rsn, characterType from characters where id = {$id}");
//                $charInfo->execute();
//                
//                $row = $charInfo->fetch();
                
                
		$url = "https://www.tip.it/runescape/json/hiscore_user?rsn=&old_stats=1";

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $result = curl_exec($ch);
                $decode = json_decode($result);
		
                $allSkills = $decode->stats;
                $skills = array();
                $out = Array();
                
                foreach($allSkills as $skill => $value){
                    $skills[$skill][] = $value['value'];
                    
                }

//		if (! $hs){
//			return null;
//                }
//		if (strpos($hs, '404 - Page not found')){
//			return null;
//                }
//
//		$stats = explode("\n", $hs);
//
//		// Loop through the skills
//		for($i = 0; $i<count($skills);$i++) {
//			// Explode each skill into 3 values - rank, level, exp
//			$stat = explode(',', $stats[$i]);
//			$out[$skills[$i]] = Array();
//			$out[$skills[$i]]['rank'] = $stat[0];
//			$out[$skills[$i]]['level'] = $stat[1];
//			$out[$skills[$i]]['xp'] = $stat[2];
//		}
	return $skills;
	}
}