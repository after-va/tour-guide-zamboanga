<?php
require_once __DIR__ . "/../config/database.php";
require_once "trait/person/trait-name-info.php";
require_once "trait/person/trait-address.php";
require_once "trait/person/trait-phone.php";
require_once "trait/person/trait-emergency.php";
require_once "trait/person/trait-contact-info.php";
require_once "trait/person/trait-person.php";
require_once "trait/person/trait-user.php";


class Tourist extends Database {
    use PersonTrait, UserTrait, NameInfoTrait, AddressTrait, PhoneTrait, EmergencyTrait, ContactInfoTrait;


    public function getTouristBirthdateByTouristID($tourist_ID) {
        $sql  = "SELECT p.person_DateOfBirth
                 FROM account_info ai
                 JOIN user_login ul   ON ul.user_ID   = ai.user_ID
                 JOIN person p        ON p.person_ID  = ul.person_ID
                 WHERE ai.account_ID = :tourist_ID";
        $db   = $this->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':tourist_ID', $tourist_ID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();             
    }

    public function getTouristPWDStatusByTouristID($tourist_ID) {
        $db   = $this->connect();
        $sql  = "SELECT p.person_isPWD
                 FROM account_info ai
                 JOIN user_login ul   ON ul.user_ID   = ai.user_ID
                 JOIN person p        ON p.person_ID  = ul.person_ID
                 WHERE ai.account_ID = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $tourist_ID, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();          // 1 or 0
    }

    public function getTouristCategory($tourist_ID) {
        $birthDate = $this->getTouristBirthdateByTouristID($tourist_ID);
        $isPWD     = $this->getTouristPWDStatusByTouristID($tourist_ID);

       
        if (!$birthDate) {
            return 'Unknown';
        }

        $birth = new DateTime($birthDate);
        $today = new DateTime();
        $age   = $today->diff($birth)->y;

        if ($isPWD) {
            return 'PWD';
        }

        if ($age < 2)          return 'Infant';        // 0-1
        if ($age <= 12)        return 'Child';         // 2-12
        if ($age <= 17)        return 'Young Adult';   // 13-17
        if ($age <= 59)        return 'Adult';         // 18-59
        return 'Senior';                               // 60+
    }

    public function getPricingOfTourist($tourist_category, $booking_ID){
        $db = $this->connect(); // make sure your class has connect() method
        $sql = "SELECT p.pricing_foradult, p.pricing_foryoungadult, p.pricing_forsenior, p.pricing_forpwd 
                FROM booking b 
                JOIN tour_package tp ON b.tourpackage_ID = tp.tourpackage_ID
                JOIN schedule s ON tp.schedule_ID = s.schedule_ID
                JOIN number_of_people nop ON s.numberofpeople_ID = nop.numberofpeople_ID
                JOIN pricing p ON p.pricing_ID = nop.pricing_ID 
                WHERE b.booking_ID = :booking_ID";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':booking_ID', $booking_ID, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return 0; // no data found
        }

        switch ($tourist_category) {
            case 'Young Adult':
                return (float)$result['pricing_foryoungadult']; 
            case 'Adult':
                return (float)$result['pricing_foradult'];
            case 'Senior':
                return (float)$result['pricing_forsenior'];
            case 'PWD':
                return (float)$result['pricing_forpwd'];
            default:
                return 0;
        }
    }


}
