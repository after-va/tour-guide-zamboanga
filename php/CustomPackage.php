<?php

require_once "Database.php";

class CustomPackage extends Database {
    
    // ==================== TOURIST FUNCTIONS ====================
    
    /**
     * Create a custom package request from tourist to guide
     */
    public function createCustomRequest($tourist_ID, $guide_ID, $data) {
        $db = $this->connect();
        $db->beginTransaction();
        
        try {
            // Insert custom package request
            $sql = "INSERT INTO Custom_Package_Request 
                    (tourist_ID, guide_ID, tourPackage_ID, request_title, request_description, 
                     preferred_date, preferred_duration, number_of_pax, budget_range, special_requirements) 
                    VALUES (:tourist_ID, :guide_ID, :tourPackage_ID, :request_title, :request_description, 
                            :preferred_date, :preferred_duration, :number_of_pax, :budget_range, :special_requirements)";
            
            $query = $db->prepare($sql);
            $query->bindParam(":tourist_ID", $tourist_ID);
            $query->bindParam(":guide_ID", $guide_ID);
            $query->bindParam(":tourPackage_ID", $data['tourPackage_ID']);
            $query->bindParam(":request_title", $data['request_title']);
            $query->bindParam(":request_description", $data['request_description']);
            $query->bindParam(":preferred_date", $data['preferred_date']);
            $query->bindParam(":preferred_duration", $data['preferred_duration']);
            $query->bindParam(":number_of_pax", $data['number_of_pax']);
            $query->bindParam(":budget_range", $data['budget_range']);
            $query->bindParam(":special_requirements", $data['special_requirements']);
            
            if (!$query->execute()) {
                $db->rollBack();
                return false;
            }
            
            $request_ID = $db->lastInsertId();
            
            // Add requested spots if provided
            if (!empty($data['spots']) && is_array($data['spots'])) {
                $spotSql = "INSERT INTO Custom_Package_Spots (request_ID, spots_ID, priority, notes) 
                            VALUES (:request_ID, :spots_ID, :priority, :notes)";
                $spotQuery = $db->prepare($spotSql);
                
                foreach ($data['spots'] as $spot) {
                    $spotQuery->bindParam(":request_ID", $request_ID);
                    $spotQuery->bindParam(":spots_ID", $spot['spots_ID']);
                    $spotQuery->bindParam(":priority", $spot['priority']);
                    $spotQuery->bindParam(":notes", $spot['notes']);
                    $spotQuery->execute();
                }
            }
            
            $db->commit();
            return $request_ID;
            
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Create Custom Request Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all requests made by a tourist
     */
    public function getRequestsByTourist($tourist_ID) {
        $sql = "SELECT cpr.*, 
                       CONCAT(gn.name_first, ' ', gn.name_last) as guide_name,
                       ar.role_rating_score as guide_rating,
                       tp.tourPackage_Name as base_package_name,
                       COUNT(DISTINCT cps.custom_spot_ID) as total_spots
                FROM Custom_Package_Request cpr
                INNER JOIN Person g ON cpr.guide_ID = g.person_ID
                INNER JOIN Name_Info gn ON g.name_ID = gn.name_ID
                LEFT JOIN User_Login ul ON g.person_ID = ul.person_ID
                LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID AND ar.role_ID = 2
                LEFT JOIN Tour_Package tp ON cpr.tourPackage_ID = tp.tourPackage_ID
                LEFT JOIN Custom_Package_Spots cps ON cpr.request_ID = cps.request_ID
                WHERE cpr.tourist_ID = :tourist_ID
                GROUP BY cpr.request_ID
                ORDER BY cpr.created_at DESC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":tourist_ID", $tourist_ID);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    /**
     * Cancel a custom request
     */
    public function cancelRequest($request_ID, $tourist_ID) {
        $sql = "UPDATE Custom_Package_Request 
                SET request_status = 'cancelled', updated_at = NOW()
                WHERE request_ID = :request_ID AND tourist_ID = :tourist_ID AND request_status = 'pending'";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":request_ID", $request_ID);
        $query->bindParam(":tourist_ID", $tourist_ID);
        
        return $query->execute();
    }
    
    // ==================== GUIDE FUNCTIONS ====================
    
    /**
     * Create a package offering by guide
     */
    public function createGuideOffering($guide_ID, $tourPackage_ID, $data) {
        $sql = "INSERT INTO Guide_Package_Offering 
                (guide_ID, tourPackage_ID, offering_price, price_per_person, min_pax, max_pax, 
                 is_customizable, availability_notes) 
                VALUES (:guide_ID, :tourPackage_ID, :offering_price, :price_per_person, :min_pax, 
                        :max_pax, :is_customizable, :availability_notes)";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":guide_ID", $guide_ID);
        $query->bindParam(":tourPackage_ID", $tourPackage_ID);
        $query->bindParam(":offering_price", $data['offering_price']);
        $query->bindParam(":price_per_person", $data['price_per_person']);
        $query->bindParam(":min_pax", $data['min_pax']);
        $query->bindParam(":max_pax", $data['max_pax']);
        $query->bindParam(":is_customizable", $data['is_customizable']);
        $query->bindParam(":availability_notes", $data['availability_notes']);
        
        return $query->execute();
    }
    
    /**
     * Get all package offerings by a guide
     */
    public function getOfferingsByGuide($guide_ID) {
        $sql = "SELECT gpo.*, 
                       tp.tourPackage_Name,
                       tp.tourPackage_Description,
                       tp.tourPackage_Duration,
                       tp.tourPackage_Capacity,
                       COUNT(DISTINCT ps.spots_ID) as total_spots
                FROM Guide_Package_Offering gpo
                INNER JOIN Tour_Package tp ON gpo.tourPackage_ID = tp.tourPackage_ID
                LEFT JOIN Package_Spots ps ON tp.tourPackage_ID = ps.tourPackage_ID
                WHERE gpo.guide_ID = :guide_ID
                GROUP BY gpo.offering_ID
                ORDER BY gpo.created_at DESC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":guide_ID", $guide_ID);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    /**
     * Get all custom requests for a guide
     */
    public function getRequestsByGuide($guide_ID, $status = null) {
        $sql = "SELECT cpr.*, 
                       CONCAT(tn.name_first, ' ', tn.name_last) as tourist_name,
                       tci.contactinfo_email as tourist_email,
                       tph.phone_number as tourist_phone,
                       tp.tourPackage_Name as base_package_name,
                       COUNT(DISTINCT cps.custom_spot_ID) as total_spots
                FROM Custom_Package_Request cpr
                INNER JOIN Person t ON cpr.tourist_ID = t.person_ID
                INNER JOIN Name_Info tn ON t.name_ID = tn.name_ID
                LEFT JOIN Contact_Info tci ON t.contactinfo_ID = tci.contactinfo_ID
                LEFT JOIN Phone_Number tph ON tci.phone_ID = tph.phone_ID
                LEFT JOIN Tour_Package tp ON cpr.tourPackage_ID = tp.tourPackage_ID
                LEFT JOIN Custom_Package_Spots cps ON cpr.request_ID = cps.request_ID
                WHERE cpr.guide_ID = :guide_ID";
        
        if ($status) {
            $sql .= " AND cpr.request_status = :status";
        }
        
        $sql .= " GROUP BY cpr.request_ID ORDER BY cpr.created_at DESC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":guide_ID", $guide_ID);
        if ($status) {
            $query->bindParam(":status", $status);
        }
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    /**
     * Accept a custom package request
     */
    public function acceptRequest($request_ID, $guide_ID) {
        $sql = "UPDATE Custom_Package_Request 
                SET request_status = 'accepted', updated_at = NOW()
                WHERE request_ID = :request_ID AND guide_ID = :guide_ID AND request_status = 'pending'";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":request_ID", $request_ID);
        $query->bindParam(":guide_ID", $guide_ID);
        
        return $query->execute();
    }
    
    /**
     * Reject a custom package request
     */
    public function rejectRequest($request_ID, $guide_ID, $rejection_reason) {
        $sql = "UPDATE Custom_Package_Request 
                SET request_status = 'rejected', rejection_reason = :rejection_reason, updated_at = NOW()
                WHERE request_ID = :request_ID AND guide_ID = :guide_ID AND request_status = 'pending'";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":request_ID", $request_ID);
        $query->bindParam(":guide_ID", $guide_ID);
        $query->bindParam(":rejection_reason", $rejection_reason);
        
        return $query->execute();
    }
    
    /**
     * Update guide offering
     */
    public function updateGuideOffering($offering_ID, $guide_ID, $data) {
        $sql = "UPDATE Guide_Package_Offering 
                SET offering_price = :offering_price,
                    price_per_person = :price_per_person,
                    min_pax = :min_pax,
                    max_pax = :max_pax,
                    is_customizable = :is_customizable,
                    is_active = :is_active,
                    availability_notes = :availability_notes,
                    updated_at = NOW()
                WHERE offering_ID = :offering_ID AND guide_ID = :guide_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":offering_ID", $offering_ID);
        $query->bindParam(":guide_ID", $guide_ID);
        $query->bindParam(":offering_price", $data['offering_price']);
        $query->bindParam(":price_per_person", $data['price_per_person']);
        $query->bindParam(":min_pax", $data['min_pax']);
        $query->bindParam(":max_pax", $data['max_pax']);
        $query->bindParam(":is_customizable", $data['is_customizable']);
        $query->bindParam(":is_active", $data['is_active']);
        $query->bindParam(":availability_notes", $data['availability_notes']);
        
        return $query->execute();
    }
    
    // ==================== COMMON FUNCTIONS ====================
    
    /**
     * Get request details by ID
     */
    public function getRequestById($request_ID) {
        $sql = "SELECT cpr.*, 
                       CONCAT(tn.name_first, ' ', tn.name_last) as tourist_name,
                       tci.contactinfo_email as tourist_email,
                       tph.phone_number as tourist_phone,
                       CONCAT(gn.name_first, ' ', gn.name_last) as guide_name,
                       gci.contactinfo_email as guide_email,
                       gph.phone_number as guide_phone,
                       tp.tourPackage_Name as base_package_name,
                       tp.tourPackage_Description as base_package_description
                FROM Custom_Package_Request cpr
                INNER JOIN Person t ON cpr.tourist_ID = t.person_ID
                INNER JOIN Name_Info tn ON t.name_ID = tn.name_ID
                LEFT JOIN Contact_Info tci ON t.contactinfo_ID = tci.contactinfo_ID
                LEFT JOIN Phone_Number tph ON tci.phone_ID = tph.phone_ID
                INNER JOIN Person g ON cpr.guide_ID = g.person_ID
                INNER JOIN Name_Info gn ON g.name_ID = gn.name_ID
                LEFT JOIN Contact_Info gci ON g.contactinfo_ID = gci.contactinfo_ID
                LEFT JOIN Phone_Number gph ON gci.phone_ID = gph.phone_ID
                LEFT JOIN Tour_Package tp ON cpr.tourPackage_ID = tp.tourPackage_ID
                WHERE cpr.request_ID = :request_ID";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":request_ID", $request_ID);
        
        if ($query->execute()) {
            $request = $query->fetch();
            if ($request) {
                // Get requested spots
                $request['spots'] = $this->getRequestSpots($request_ID);
            }
            return $request;
        }
        return null;
    }
    
    /**
     * Get spots for a custom request
     */
    public function getRequestSpots($request_ID) {
        $sql = "SELECT cps.*, ts.spots_Name, ts.spots_Description, ts.spots_category, ts.spots_Address
                FROM Custom_Package_Spots cps
                INNER JOIN Tour_Spots ts ON cps.spots_ID = ts.spots_ID
                WHERE cps.request_ID = :request_ID
                ORDER BY cps.priority ASC, ts.spots_Name ASC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":request_ID", $request_ID);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    /**
     * Get all active guide offerings (for tourists to browse)
     */
    public function getAllActiveOfferings($guide_ID = null) {
        $sql = "SELECT gpo.*, 
                       tp.tourPackage_Name,
                       tp.tourPackage_Description,
                       tp.tourPackage_Duration,
                       tp.tourPackage_Capacity,
                       CONCAT(n.name_first, ' ', n.name_last) as guide_name,
                       ar.role_rating_score as guide_rating,
                       COUNT(DISTINCT ps.spots_ID) as total_spots
                FROM Guide_Package_Offering gpo
                INNER JOIN Tour_Package tp ON gpo.tourPackage_ID = tp.tourPackage_ID
                INNER JOIN Person p ON gpo.guide_ID = p.person_ID
                INNER JOIN Name_Info n ON p.name_ID = n.name_ID
                LEFT JOIN User_Login ul ON p.person_ID = ul.person_ID
                LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID AND ar.role_ID = 2
                LEFT JOIN Package_Spots ps ON tp.tourPackage_ID = ps.tourPackage_ID
                WHERE gpo.is_active = 1";
        
        if ($guide_ID) {
            $sql .= " AND gpo.guide_ID = :guide_ID";
        }
        
        $sql .= " GROUP BY gpo.offering_ID ORDER BY gpo.created_at DESC";
        
        $query = $this->connect()->prepare($sql);
        if ($guide_ID) {
            $query->bindParam(":guide_ID", $guide_ID);
        }
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
    
    /**
     * Add message to package request
     */
    public function addRequestMessage($request_ID, $sender_ID, $message_text) {
        $sql = "INSERT INTO Package_Request_Messages (request_ID, sender_ID, message_text) 
                VALUES (:request_ID, :sender_ID, :message_text)";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":request_ID", $request_ID);
        $query->bindParam(":sender_ID", $sender_ID);
        $query->bindParam(":message_text", $message_text);
        
        return $query->execute();
    }
    
    /**
     * Get messages for a request
     */
    public function getRequestMessages($request_ID) {
        $sql = "SELECT prm.*, 
                       CONCAT(n.name_first, ' ', n.name_last) as sender_name,
                       ar.role_ID
                FROM Package_Request_Messages prm
                INNER JOIN Person p ON prm.sender_ID = p.person_ID
                INNER JOIN Name_Info n ON p.name_ID = n.name_ID
                LEFT JOIN User_Login ul ON p.person_ID = ul.person_ID
                LEFT JOIN Account_Role ar ON ul.login_ID = ar.login_ID
                WHERE prm.request_ID = :request_ID
                ORDER BY prm.created_at ASC";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":request_ID", $request_ID);
        
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return [];
    }
}
