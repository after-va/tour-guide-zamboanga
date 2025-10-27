# Tourismo Zamboanga - Tour Guide System

A comprehensive tour guide management system built with PHP, MySQL, and pure HTML/JavaScript (no CSS frameworks).

## Features

### For Tourists
- Browse tour packages and tourist spots
- Book tours with schedules
- Manage bookings (view, cancel)
- Add companions to bookings
- Rate and review tours, guides, and spots
- View booking history and payment details

### For Tour Guides
- Manage tour schedules
- Create package offerings with custom pricing
- Set availability calendar
- View bookings and statistics
- Track ratings and reviews

### For Administrators
- Manage tour packages and tourist spots
- Manage bookings and users
- View system statistics
- Full CRUD operations on all entities

## Database Structure

The system uses a comprehensive database with 40+ tables including:
- **User Management**: User_Login, Account_Role, Person, Name_Info
- **Location**: Country, Region, Province, City, Barangay, Address_Info
- **Tour Management**: Tour_Package, Tour_Spots, Package_Spots, Schedule
- **Booking**: Booking, Booking_Bundle, Companion_Info
- **Payment**: Payment_Info, Payment_Transaction, Payment_Method
- **Rating**: Rating, Rating_Category, Review_Images
- **Guide Features**: Guide_Availability, Guide_Package_Offering
- **Communication**: Messages, Notifications

## Installation

1. **Setup Database**
   ```bash
   # Import the SQL files in order:
   1. sql/tourguidesystem.sql
   2. sql/tourguidesystem-data.sql
   ```

2. **Configure Database Connection**
   - Edit `classes/database.php`
   - Update host, username, password, and database name

3. **Setup Web Server**
   - Place files in `htdocs/tour-guide-zamboanga/`
   - Access via `http://localhost/tour-guide-zamboanga/`

## Architecture

### Trait-Based Class System

The system uses PHP traits for modular, reusable code:

**Core Traits:**
- `PersonTrait` - Person management
- `UserTrait` - User authentication
- `NameInfoTrait` - Name handling
- `AddressTrait` - Address management
- `PhoneTrait` - Phone number handling
- `EmergencyTrait` - Emergency contact info
- `ContactInfoTrait` - Contact information
- `BookingTrait` - Booking operations
- `TourPackageTrait` - Package management
- `TourSpotsTrait` - Tourist spot operations
- `ScheduleTrait` - Schedule management
- `PaymentTrait` - Payment processing
- `RatingTrait` - Rating and review system

**Main Classes:**
- `Database` - Database connection
- `Auth` - Authentication and authorization
- `Tourist` - Tourist-specific operations
- `Guide` - Guide-specific operations
- `BookingManager` - Booking and companion management
- `TourManager` - Tour packages, spots, and schedules
- `GuideManager` - Guide profiles and offerings

## File Structure

```
tour-guide-zamboanga/
├── classes/
│   ├── trait/
│   │   ├── person/          # Person-related traits
│   │   ├── trait-booking.php
│   │   ├── trait-tour-package.php
│   │   ├── trait-tour-spots.php
│   │   ├── trait-schedule.php
│   │   ├── trait-payment.php
│   │   └── trait-rating.php
│   ├── consuming-class/     # Helper classes
│   ├── database.php
│   ├── auth.php
│   ├── tourist.php
│   ├── guide.php
│   ├── booking-manager.php
│   ├── tour-manager.php
│   └── guide-manager.php
├── pages/
│   ├── public/              # Public pages
│   │   ├── browse-packages.php
│   │   ├── browse-spots.php
│   │   ├── package-details.php
│   │   └── spot-details.php
│   ├── tourist/             # Tourist dashboard
│   │   ├── dashboard.php
│   │   ├── my-bookings.php
│   │   ├── book-tour.php
│   │   ├── booking-details.php
│   │   └── cancel-booking.php
│   ├── guide/               # Guide dashboard
│   │   └── dashboard.php
│   └── admin/               # Admin panel
│       ├── dashboard.php
│       ├── manage-packages.php
│       ├── manage-spots.php
│       ├── add-package.php
│       └── add-spot.php
├── registration/
│   ├── tourist-registration.php
│   └── guide-registration.php
├── sql/
│   ├── tourguidesystem.sql
│   ├── tourguidesystem-data.sql
│   ├── table-draw.sql
│   ├── ph-location.sql
│   └── global-location-data.sql
├── index.php                # Login page
├── logout.php
├── config.php
└── README.md
```

## Default Roles

The system has three user roles:
1. **Tourist** (role_ID: 1) - Can browse and book tours
2. **Guide** (role_ID: 2) - Can manage tours and schedules
3. **Admin** (role_ID: 3) - Full system access

## Key Features Implementation

### Booking System
- Multi-step booking process
- Companion management
- Payment integration
- Status tracking (pending, confirmed, completed, cancelled)
- Booking history

### Rating System
- Rate guides, packages, and spots
- 5-star rating with reviews
- Average rating calculation
- Review images support

### Schedule Management
- Guide availability tracking
- Capacity management
- Real-time slot availability
- Meeting spot information

### Payment Processing
- Multiple payment methods
- Transaction tracking
- Payment status management
- Refund support

## Security Features

- Password hashing (PHP password_hash)
- SQL injection prevention (PDO prepared statements)
- Session management
- Role-based access control
- Input validation and sanitization

## Browser Compatibility

Works on all modern browsers:
- Chrome
- Firefox
- Safari
- Edge

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: Pure HTML5 + JavaScript (ES6)
- **Server**: Apache (XAMPP)

## Future Enhancements

- Email notifications
- SMS integration
- Online payment gateway
- Image upload for spots and reviews
- Advanced search and filtering
- Mobile responsive design
- Calendar view for schedules
- Report generation

## License

Proprietary - Tourismo Zamboanga Tour Guide System

## Support

For support and questions, contact the system administrator.
