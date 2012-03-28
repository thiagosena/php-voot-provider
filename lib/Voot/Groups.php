<?php

class Groups {
    
    private $_pdo;

    public function __construct(PDO $pdo) {
        $this->_pdo = $pdo;
    }

    public function getGroups($resourceOwnerId, $startIndex = 0, $count = null) {
    	$x = $this->_pdo->exec("PRAGMA foreign_keys = ON");

        // count the total number of results        
        $stmt = $this->_pdo->prepare("SELECT COUNT(*) AS count FROM membership m, groups g, roles r WHERE m.id=:id AND m.groupid = g.id AND m.role = r.id");
        $stmt->bindValue(":id", $resourceOwnerId, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (FALSE === $result) {
            return FALSE;
        }
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalResults = $data['count'];

        if($count === null) {
            $count = $totalResults;
        }

        // FIXME: how to do LIMIT? 
        $stmt = $this->_pdo->prepare("SELECT m.id, g.id, g.title, g.description, r.voot_membership_role FROM membership m, groups g, roles r WHERE m.id=:id AND m.groupid = g.id AND m.role = r.id LIMIT 0,3");
        $stmt->bindValue(":id", $resourceOwnerId, PDO::PARAM_STR);
        //$stmt->bindValue(":startIndex", $startIndex, PDO::PARAM_INT);
        //$stmt->bindValue(":count", $count, PDO::PARAM_INT);
        $result = $stmt->execute();
        if (FALSE === $result) {
            return FALSE;
        }
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (FALSE === $data) {
            return FALSE;
        }
	$returnData = array ( 'startIndex' => 0, 'totalResults' => $totalResults, 'itemsPerPage' => $count, 'entry' => $data);
        return $returnData;
    }

}
?>
