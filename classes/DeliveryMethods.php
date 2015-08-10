<?php

include_once 'DB.php';

class DeliveryMethods {

    public $db;

    public function __construct() {
        $db = new DB();
        $this->db = $db->db;
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function getDeliveryMethods() {
        $stmt = $this->db->query("SELECT dm.*, r.from, r.to FROM delivery_method as dm LEFT JOIN delivery_range as r ON dm.id = r.delivery_method_id");

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $deliveryMethods = [];
        foreach ($result as $row) {
            if (!array_key_exists($row['id'], $deliveryMethods)) {
                $deliveryMethods[$row['id']] = [
                    'id' => (int) $row['id'],
                    'name' => $row['name'],
                    'url' => $row['url'],
                    'weight_from' => (float) $row['weight_from'],
                    'weight_to' => (float) $row['weight_to'],
                    'notes' => $row['notes'],
                    'ranges' => []
                ];
            }
            $deliveryMethods[$row['id']]['ranges'][] = ['from' => ($row['from'] != NULL) ? (float) $row['from'] : NULL, 'to' => ($row['to'] != NULL) ? (float) $row['to'] : NULL];
        }
        return json_encode(array_values($deliveryMethods));
    }

    public function save($data) {
        $separatedData = $this->separatedData($data);
        $this->deleteData($separatedData['exists']);
        $this->insertData($separatedData['insert']);
        $this->updateData($separatedData['update']);
    }

    public function insertData($data) {
        $insertDMQuery = "INSERT INTO delivery_method (name, url, notes, weight_from, weight_to) VALUES (:name, :url, :notes, :weight_from, :weight_to) ";
        $insertRangeQuery = "INSERT INTO delivery_range (`delivery_method_id`, `from`, `to`) VALUES (:delivery_method_id, :fromvalue, :tovalue) ";

        foreach ($data as $element) {
            $stmt = $this->db->prepare($insertDMQuery);

            $stmt->bindParam(":name", $element['name'], PDO::PARAM_STR);
            $stmt->bindParam(":url", $element['url'], PDO::PARAM_STR);
            $stmt->bindParam(":notes", $element['notes'], PDO::PARAM_STR);
            $stmt->bindParam(":weight_from", $element['weight_from'], PDO::PARAM_STR);
            $stmt->bindParam(":weight_to", $element['weight_to'], PDO::PARAM_STR);

            $stmt->execute();
            $lastId = $this->db->lastInsertId();
            foreach ($element['ranges'] as $range) {
                $stmtRange = $this->db->prepare($insertRangeQuery);
                $stmtRange->bindParam(":delivery_method_id", $lastId, PDO::PARAM_INT);
                $stmtRange->bindParam(":fromvalue", $range['from'], PDO::PARAM_STR);
                $stmtRange->bindParam(":tovalue", $range['to'], PDO::PARAM_STR);
                $stmtRange->execute();
            }
        }
    }

    public function updateData($data) {
        $updateDMQuery = "UPDATE delivery_method SET name = :name, url = :url, notes = :notes, weight_from = :weight_from, weight_to = :weight_to WHERE id = :id";
        $deleteRangeQuery = "DELETE FROM  delivery_range WHERE delivery_method_id = :delivery_method_id";
        $insertRangeQuery = "INSERT INTO delivery_range (`delivery_method_id`, `from`, `to`) VALUES (:delivery_method_id, :fromvalue, :tovalue) ";
        
        $stmt = $this->db->prepare($updateDMQuery);
        foreach ($data as $element) {


            $stmt->bindParam(":id", $element['id'], PDO::PARAM_INT);
            $stmt->bindParam(":name", $element['name'], PDO::PARAM_STR);
            $stmt->bindParam(":url", $element['url'], PDO::PARAM_STR);
            $stmt->bindParam(":notes", $element['notes'], PDO::PARAM_STR);
            $stmt->bindParam(":weight_from", $element['weight_from'], PDO::PARAM_STR);
            $stmt->bindParam(":weight_to", $element['weight_to'], PDO::PARAM_STR);

            $stmt->execute();

            $delete = $this->db->prepare($deleteRangeQuery);
            $delete->bindValue(':delivery_method_id', $element['id'], PDO::PARAM_INT);
            $delete->execute();

            foreach ($element['ranges'] as $range) {
                $stmtRange = $this->db->prepare($insertRangeQuery);
                $stmtRange->bindParam(":delivery_method_id", $element['id'], PDO::PARAM_INT);
                $stmtRange->bindParam(":fromvalue", $range['from'], PDO::PARAM_STR);
                $stmtRange->bindParam(":tovalue", $range['to'], PDO::PARAM_STR);
                $stmtRange->execute();
                $stmtRange = NULL;
            }
        }
    }

    public function separatedData($data) {
        $return = ['insert' => [], 'update' => [], 'exists' => []];
        foreach ($data as $element) {
            if ($element['id']) {
                $return['exists'][] = $element['id'];
                $return['update'][] = $element;
            } else {
                $return['insert'][] = $element;
            }
        }

        return $return;
    }

    private function deleteData($exists) {
        $deleteQuery = "DELETE dm.*, r.* FROM delivery_method as dm LEFT JOIN delivery_range as r on dm.id = r.delivery_method_id WHERE id NOT IN (" . implode(", ", $exists) . ")";
        $delete = $this->db->prepare($deleteQuery);
        $delete->execute();

    }

    public function validate($data) {
        // in this case, we have no mandatory fiels, but have number fields and we need to check if lower range field is <= higher
        $validate = TRUE;
        foreach ($data as $dm) {
            if (isset($dm['weight_from'])) {
                if ($dm['weight_from'] != '' && !is_numeric($dm['weight_from'])) {
                    $validate = false;
                    break;
                }
                if ($dm['weight_to'] != '' && !is_numeric($dm['weight_to'])) {
                    $validate = false;
                    break;
                }

                if ($dm['weight_to'] < $dm['weight_from']) {
                    $validate = false;
                    break;
                }

                if (isset($dm['ranges']) && isset($dm['ranges'])) {
                    foreach ($dm['ranges'] as $range) {
                        if ($range['from'] != '' && !is_numeric($range['from'])) {
                            $validate = false;
                            break;
                        }
                        if ($range['to'] != '' && !is_numeric($range['to'])) {
                            $validate = false;
                            break;
                        }

                        if ($range['to'] < $range['from']) {
                            $validate = false;
                            break;
                        }
                    }
                }
            }
        }

        return $validate;
    }

}
