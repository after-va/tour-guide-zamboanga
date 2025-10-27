
Table Country {
    country_ID INT 
    country_name VARCHAR() 
    country_codename VARCHAR(10)
    country_codenumber VARCHAR(10)
}

Table Phone_Number {
    phone_ID INT 
    country_ID INT
    phone_number VARCHAR(15)
}

Table Region {
    region_ID INT 
    region_name VARCHAR()
    country_ID INT
}

Table Province {
    province_ID INT 
    province_name VARCHAR()
    region_ID INT
}

Table City {
    city_ID INT 
    city_name VARCHAR()
    province_ID INT
}

Table Barangay {
    barangay_ID INT 
    barangay_name VARCHAR()
    city_ID INT
}

Table Address_Info {
    address_ID INT 
    address_houseno VARCHAR(50)
    address_street VARCHAR(50)
    barangay_ID INT
}

Table Emergency_Info {
    emergency_ID INT 
    emergency_Name VARCHAR(225)
    emergency_Relationship VARCHAR(225)
    phone_ID INT
}

Table Contact_Info {
    contactinfo_ID INT 
    address_ID INT
    phone_ID INT
    contactinfo_email VARCHAR()
    emergency_ID INT
}

Table Name_Info {
    name_ID INT 
    name_first VARCHAR()
    name_second VARCHAR(225)
    name_middle VARCHAR(225)
    name_last VARCHAR(225)
    name_suffix VARCHAR(225)
}

Table Rating_Category {
    ratingcategory_ID INT 
    ratingcategory_name VARCHAR()
    ratingcategory_from INT
    ratingcategory_to INT
}

Table Role_Info {
    role_ID INT 
    role_name VARCHAR(225)
}

Table Person {
    person_ID INT 
    name_ID INT
    person_Nationality VARCHAR(225)
    person_Gender VARCHAR(225)
    person_DateOfBirth DATE
    contactinfo_ID INT
}

Table Tour_Spots {
    spots_ID INT 
    spots_Name VARCHAR(225)
    spots_Description VARCHAR(225)
    spots_category VARCHAR(225)
    spots_Address VARCHAR(225)
    spots_GoogleLink VARCHAR()
}

Table Tour_Package {
    tourPackage_ID INT 
    tourPackage_Name VARCHAR(225)
    tourPackage_Description VARCHAR(225)
    tourPackage_Capacity VARCHAR(50)
    tourPackage_Duration VARCHAR(50)
    spots_ID INT
}

Table Companion_Category {
    companioncategory_ID INT 
    companioncategory_name VARCHAR()
}

Table Companion_Info {
    companion_ID INT 
    companion_name VARCHAR(225)
    companioncategory_ID INT
}

Table Schedule {
    schedule_ID INT 
    tourPackage_ID INT
    guide_ID INT
    schedule_StartDateTime DATETIME
    schedule_EndDateTime DATETIME
    schedule_Capacity INT
    schedule_MeetingSpot VARCHAR(255)
}

Table Booking {
    booking_ID INT 
    customer_ID INT
    schedule_ID INT
    tourPackage_ID INT
    booking_Status VARCHAR(225)
    booking_PAX INT
}

Table Booking_Bundle {
    bookingbundle_ID INT 
    companion_ID INT
    booking_ID INT
}

Table Payment_Info {
    paymentinfo_ID INT 
    booking_ID INT
    paymentinfo_Amount DECIMAL(10,2)
    paymentinfo_Date DATE
}

Table User_Login {
    login_ID INT 
    person_ID INT
    username VARCHAR() UNIQUE
    password_hash VARCHAR(255)
    last_login DATETIME
    is_active TINYINT(1) 
    created_at TIMESTAMP 
    updated_at TIMESTAMP  
}

Table Account_Role {
    account_role_ID INT 
    login_ID INT
    role_ID INT
    role_rating_score DECIMAL(3,2) 
    is_active TINYINT(1) 
    created_at TIMESTAMP 
    updated_at TIMESTAMP  
}

Table Rating {
    rating_ID INT 
    rater_account_role_ID INT
    rated_account_role_ID INT
    rating_value DECIMAL(2,1)
    rating_description VARCHAR(255)
    rating_date DATETIME 
}

Table Password_Reset {
    reset_ID INT 
    person_ID INT
    reset_token VARCHAR() UNIQUE
    expires_at DATETIME
    used TINYINT(1) 
    created_at TIMESTAMP 
}

Table Activity_Log {
    log_ID INT 
    user_ID INT
    action VARCHAR()
    description TEXT
    ip_address VARCHAR(45)
    user_agent VARCHAR(255)
    created_at TIMESTAMP 
}

Table Guide_Availability {
    availability_ID INT 
    guide_ID INT
    available_date DATE
    start_time TIME
    end_time TIME
    is_available TINYINT(1) 
    notes TEXT
    created_at TIMESTAMP 
}

Table Package_Pricing {
    pricing_ID INT 
    tourPackage_ID INT
    guide_ID INT
    base_price DECIMAL(10,2)
    price_per_person DECIMAL(10,2)
    max_persons INT
    min_persons INT 
    currency VARCHAR(10)
    is_active TINYINT(1) 
    created_at TIMESTAMP 
    updated_at TIMESTAMP  
}

Table Payment_Method {
    method_ID INT 
    method_name VARCHAR(50)
    method_type ENUM('card', 'ewallet', 'bank', 'cash')
    is_active TINYINT(1) 
    processing_fee DECIMAL(5,2) 
    created_at TIMESTAMP 
}

Table Payment_Transaction {
    transaction_ID INT 
    paymentinfo_ID INT
    method_ID INT
    transaction_reference VARCHAR() UNIQUE
    transaction_status ENUM('pending', 'processing', 'completed', 'failed', 'refunded') 
    payment_gateway VARCHAR(50)
    gateway_response TEXT
    paid_at DATETIME
    refunded_at DATETIME
    refund_amount DECIMAL(10,2)
    refund_reason TEXT
    created_at TIMESTAMP 
    updated_at TIMESTAMP  
}

Table Notifications {
    notification_ID INT 
    user_ID INT
    notification_type VARCHAR(50)
    title VARCHAR(255)
    message TEXT
    is_read TINYINT(1) 
    link_url VARCHAR(255)
    created_at TIMESTAMP 
    read_at DATETIME
}

Table Review_Images {
    image_ID INT 
    rating_ID INT
    image_path VARCHAR(255)
    created_at TIMESTAMP 
}

Table Booking_Status_History {
    history_ID INT 
    booking_ID INT
    old_status VARCHAR(50)
    new_status VARCHAR(50)
    changed_by INT
    change_reason TEXT
    created_at TIMESTAMP 
}

Table User_Favorites {
    favorite_ID INT 
    user_ID INT
    guide_ID INT
    tourPackage_ID INT
    spots_ID INT
    created_at TIMESTAMP 
}

Table Messages {
    message_ID INT 
    sender_ID INT
    receiver_ID INT
    booking_ID INT
    message_text TEXT
    is_read TINYINT(1) 
    read_at DATETIME
    created_at TIMESTAMP 
}

Table System_Settings {
    setting_ID INT 
    setting_key VARCHAR() UNIQUE
    setting_value TEXT
    setting_type VARCHAR(50)
    description TEXT
    updated_by INT
    updated_at TIMESTAMP  
}

Table Package_Spots {
    package_spot_ID INT 
    tourPackage_ID INT
    spots_ID INT
    spot_order INT 
    created_at TIMESTAMP 
}

Table Custom_Package_Request {
    request_ID INT 
    tourist_ID INT
    guide_ID INT
    tourPackage_ID INT
    request_title VARCHAR(255)
    request_description TEXT
    preferred_date DATE
    preferred_duration VARCHAR(50)
    number_of_pax INT
    budget_range VARCHAR()
    special_requirements TEXT
    request_status ENUM('pending', 'accepted', 'rejected', 'cancelled', 'completed') 
    rejection_reason TEXT
    created_at TIMESTAMP 
    updated_at TIMESTAMP  
}

Table Custom_Package_Spots {
    custom_spot_ID INT 
    request_ID INT
    spots_ID INT
    priority INT 
    notes TEXT
    created_at TIMESTAMP 
}

Table Guide_Package_Offering {
    offering_ID INT 
    guide_ID INT
    tourPackage_ID INT
    offering_price DECIMAL(10,2)
    price_per_person DECIMAL(10,2)
    min_pax INT 
    max_pax INT
    is_customizable TINYINT(1) 
    is_active TINYINT(1) 
    availability_notes TEXT
    created_at TIMESTAMP 
    updated_at TIMESTAMP  
}

Table Package_Request_Messages {
    message_ID INT 
    request_ID INT
    sender_ID INT
    message_text TEXT
    is_read TINYINT(1) 
    created_at TIMESTAMP 
}
