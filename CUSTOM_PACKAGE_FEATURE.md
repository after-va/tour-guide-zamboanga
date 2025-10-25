# Custom Package Feature Documentation

## Overview
This feature allows **Tour Guides** to create their own package offerings and **Tourists** to request custom packages from specific guides. The system includes an approval workflow where guides can accept or reject requests, followed by payment integration and notifications.

---

## Database Schema Updates

### New Tables Created

#### 1. `Custom_Package_Request`
Stores custom package requests from tourists to guides.

**Key Fields:**
- `request_ID` - Primary key
- `tourist_ID` - Tourist making the request
- `guide_ID` - Guide receiving the request
- `tourPackage_ID` - Optional base package to customize
- `request_title` - Title of the custom request
- `request_description` - Detailed description
- `preferred_date` - Preferred tour date
- `preferred_duration` - Duration (e.g., "Full Day", "2 Days / 1 Night")
- `number_of_pax` - Number of people
- `budget_range` - Budget range (e.g., "₱5,000 - ₱10,000")
- `special_requirements` - Special needs or requests
- `request_status` - Status: pending, accepted, rejected, cancelled, completed
- `rejection_reason` - Reason if rejected

#### 2. `Custom_Package_Spots`
Stores tour spots requested by tourist for custom packages.

**Key Fields:**
- `custom_spot_ID` - Primary key
- `request_ID` - Links to custom package request
- `spots_ID` - Tour spot
- `priority` - Priority level (1=must visit, 2=would like, 3=optional)
- `notes` - Additional notes for this spot

#### 3. `Guide_Package_Offering`
Packages created and offered by guides.

**Key Fields:**
- `offering_ID` - Primary key
- `guide_ID` - Guide offering the package
- `tourPackage_ID` - Base tour package
- `offering_price` - Base price
- `price_per_person` - Additional per person cost
- `min_pax` / `max_pax` - Capacity range
- `is_customizable` - Whether tourists can request modifications
- `is_active` - Whether visible to tourists
- `availability_notes` - Notes about availability

#### 4. `Package_Request_Messages`
Communication between tourist and guide about a request.

**Key Fields:**
- `message_ID` - Primary key
- `request_ID` - Related request
- `sender_ID` - Person sending message
- `message_text` - Message content
- `is_read` - Read status

---

## PHP Classes

### `CustomPackage.php`
Located at: `php/CustomPackage.php`

**Tourist Functions:**
- `createCustomRequest()` - Create a new custom package request
- `getRequestsByTourist()` - Get all requests made by a tourist
- `cancelRequest()` - Cancel a pending request

**Guide Functions:**
- `createGuideOffering()` - Create a package offering
- `getOfferingsByGuide()` - Get all offerings by a guide
- `getRequestsByGuide()` - Get all requests for a guide
- `acceptRequest()` - Accept a custom request
- `rejectRequest()` - Reject a custom request with reason
- `updateGuideOffering()` - Update an existing offering

**Common Functions:**
- `getRequestById()` - Get detailed request information
- `getRequestSpots()` - Get spots for a request
- `getAllActiveOfferings()` - Browse all active guide offerings
- `addRequestMessage()` - Add message to request thread
- `getRequestMessages()` - Get all messages for a request

---

## Tour Guide Pages

### 1. `guide/my-packages.php`
**Purpose:** Manage package offerings

**Features:**
- View all package offerings
- Create new package offering from existing tour packages
- Set pricing (base price + per person)
- Set capacity (min/max PAX)
- Mark as customizable
- Activate/deactivate offerings
- Add availability notes

### 2. `guide/package-requests.php`
**Purpose:** Manage incoming custom package requests

**Features:**
- View requests in tabs: Pending, Accepted, All
- See tourist details and contact information
- View requested spots with priorities
- Accept or reject requests
- Provide rejection reason
- Send messages to tourists
- View request history

### 3. `guide/get-request-details.php`
**Purpose:** AJAX endpoint for request details modal

**Features:**
- Display full request details
- Show message thread
- Allow sending messages
- Show rejection reason if applicable

---

## Tourist Pages

### 1. `tourist/browse-guides.php`
**Purpose:** Browse available tour guides

**Features:**
- View all registered guides
- See guide ratings
- View contact information
- Navigate to guide's packages
- Request custom package from guide

### 2. `tourist/guide-packages.php`
**Purpose:** View packages offered by a specific guide

**Features:**
- See guide information and rating
- Browse all active packages from guide
- View pricing and capacity
- See customization availability
- Request to customize or book package

### 3. `tourist/request-custom-package.php`
**Purpose:** Create custom package request

**Features:**
- Fill out request details (title, description)
- Select preferred date and duration
- Specify number of PAX
- Set budget range
- Add special requirements
- Select tour spots with priorities:
  - Must Visit (high priority)
  - Would Like to Visit (medium priority)
  - Optional (low priority)
- Add notes for each spot
- Base request on existing package (optional)

### 4. `tourist/my-requests.php`
**Purpose:** View and manage custom package requests

**Features:**
- View all requests with status badges
- See guide information
- Track request status:
  - ⏳ Pending - Waiting for guide response
  - ✓ Accepted - Ready for payment
  - ✗ Rejected - With reason
  - Cancelled - User cancelled
  - Completed - Paid and confirmed
- Cancel pending requests
- View details and messages
- Proceed to payment for accepted requests

### 5. `tourist/get-request-details-tourist.php`
**Purpose:** AJAX endpoint for tourist request details

**Features:**
- Show full request information
- Display message thread
- Send messages to guide
- Show payment button if accepted

### 6. `tourist/payment-custom-package.php`
**Purpose:** Payment processing for accepted custom packages

**Features:**
- Confirm booking details
- Select confirmed date and time
- Set meeting spot
- Enter payment amount (agreed with guide)
- Choose payment method
- Select payment gateway
- Process payment and create booking
- Generate booking reference
- Send confirmation notification to guide

---

## Workflow

### For Tour Guides:

1. **Create Package Offerings**
   - Go to "My Packages"
   - Click "Create New Package Offering"
   - Select base tour package
   - Set pricing and capacity
   - Mark as customizable (optional)
   - Add availability notes
   - Submit

2. **Manage Incoming Requests**
   - Go to "Package Requests"
   - View pending requests
   - Review tourist details and requirements
   - Check requested spots and priorities
   - Send messages for clarification
   - Accept or reject request
   - If rejecting, provide reason

3. **After Acceptance**
   - Tourist receives notification
   - Tourist proceeds to payment
   - Guide receives payment confirmation
   - Booking is created automatically
   - View in "My Bookings"

### For Tourists:

1. **Browse and Request**
   - Go to "Browse Guides"
   - Select a guide
   - View their package offerings OR
   - Click "Request Custom Package"

2. **Create Custom Request**
   - Fill out request form
   - Add title and description
   - Select preferred date and duration
   - Specify PAX and budget
   - Add special requirements
   - Select tour spots with priorities
   - Submit request

3. **Track Request**
   - Go to "My Requests"
   - Monitor status
   - Send messages to guide
   - Wait for acceptance/rejection

4. **Payment (After Acceptance)**
   - Click "Proceed to Payment"
   - Confirm booking details
   - Enter agreed payment amount
   - Select payment method
   - Process payment
   - Receive booking confirmation

---

## Notification System Integration

The system automatically sends notifications for:

- **New Request** - Guide notified when tourist submits request
- **Request Accepted** - Tourist notified to proceed to payment
- **Request Rejected** - Tourist notified with reason
- **Request Cancelled** - Guide notified of cancellation
- **New Message** - Both parties notified of new messages
- **Payment Received** - Guide notified when payment is completed

---

## Database Migration

To implement this feature, run the SQL updates in:
```
tourguidesystem.sql (lines 462-535)
```

This will create:
- 4 new tables
- 5 new indexes
- Foreign key relationships

---

## File Structure

```
tour-guide-zamboanga/
├── php/
│   └── CustomPackage.php (New)
├── guide/
│   ├── my-packages.php (New)
│   ├── package-requests.php (New)
│   └── get-request-details.php (New)
├── tourist/
│   ├── browse-guides.php (New)
│   ├── guide-packages.php (New)
│   ├── request-custom-package.php (New)
│   ├── my-requests.php (New)
│   ├── get-request-details-tourist.php (New)
│   └── payment-custom-package.php (New)
└── tourguidesystem.sql (Updated)
```

---

## Key Features Summary

✅ **Tour guides can create package offerings**
✅ **Tourists can request custom packages from specific guides**
✅ **Approval workflow with accept/reject functionality**
✅ **Messaging system between tourist and guide**
✅ **Payment integration after approval**
✅ **Automatic booking creation**
✅ **Notification system for all actions**
✅ **Priority-based tour spot selection**
✅ **Budget range specification**
✅ **Special requirements handling**
✅ **Request status tracking**

---

## Testing Checklist

- [ ] Guide can create package offering
- [ ] Tourist can browse guides
- [ ] Tourist can view guide packages
- [ ] Tourist can request custom package
- [ ] Guide receives notification of new request
- [ ] Guide can view request details
- [ ] Guide can send messages
- [ ] Guide can accept request
- [ ] Tourist receives acceptance notification
- [ ] Tourist can proceed to payment
- [ ] Payment creates booking and schedule
- [ ] Guide receives payment notification
- [ ] Guide can reject request with reason
- [ ] Tourist receives rejection notification
- [ ] Tourist can cancel pending request
- [ ] All notifications are sent correctly

---

## Future Enhancements

- Payment gateway integration (PayPal, Stripe, GCash API)
- Email notifications
- SMS notifications
- Package templates for guides
- Review system for custom packages
- Automated pricing calculator
- Calendar integration for availability
- Multi-currency support
- Package comparison feature
- Favorite guides feature

---

## Support

For issues or questions, contact the development team or refer to the main system documentation.
