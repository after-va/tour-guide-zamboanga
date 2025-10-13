# Tourismo Zamboanga - Tour Guide Booking Platform

A comprehensive web platform connecting tourists with certified local tour guides in Zamboanga, Philippines.

## 🌟 Overview

Tourismo Zamboanga is a tourism website that facilitates connections between tourists and professional tour guides. The platform features 10 popular tourist destinations in Zamboanga and allows tourists to browse, book, and manage tours with certified local guides.

## 🎯 Features

### For Tourists
- **Browse Destinations**: Explore 10 top tourist spots in Zamboanga
- **Find Tour Guides**: Browse certified tour guides with ratings, reviews, and specializations
- **Book Tours**: Select and book tour packages with flexible pricing
- **Manage Bookings**: Track upcoming tours, view booking history
- **Leave Reviews**: Rate and review tour guides after completing tours
- **Secure Login**: Personal dashboard to manage all activities

### For Tour Guides
- **Professional Profile**: Showcase experience, specializations, and certifications
- **Manage Bookings**: Accept/decline tour requests
- **Set Pricing**: Create custom tour packages with flexible pricing
- **Track Earnings**: Monitor monthly earnings and performance
- **View Reviews**: See customer feedback and ratings
- **Schedule Management**: Organize upcoming tours and availability

## 📍 Featured Destinations (Top 10 Zamboanga Spots)

1. **Great Santa Cruz Island** - Famous pink sand beach with crystal-clear waters
2. **Fort Pilar** - Historic Spanish-era fortress and shrine
3. **Pasonanca Park** - Natural tree park with pools and picnic areas
4. **Paseo del Mar** - Waterfront promenade with sunset views
5. **Yakan Weaving Village** - Traditional weaving and cultural experience
6. **Merloquet Falls** - Seven-tiered waterfall for nature trekking
7. **Zamboanga City Hall** - Iconic Spanish-inspired architecture
8. **Vinta Cruise** - Traditional colorful boat sailing experience
9. **Barter Trade Center** - Unique shopping from neighboring countries
10. **Canelar Barter Trade** - Authentic Zamboanga cuisine and food tours

## 🗂️ File Structure

```
tour-guide-zamboanga/
├── index.html                  # Main homepage with destinations
├── tourist-login.html          # Tourist login page
├── tourist-register.html       # Tourist registration page
├── tourist-dashboard.html      # Tourist dashboard
├── guide-login.html           # Tour guide login page
├── guide-register.html        # Tour guide registration/application
├── guide-dashboard.html       # Tour guide dashboard
├── tour-guides.html           # Browse all tour guides
├── assets/                    # CSS, JS, images, and vendor files
│   ├── css/
│   ├── js/
│   ├── img/
│   └── vendor/
├── classes/                   # PHP classes (if backend implemented)
└── README.md                  # This file
```

## 🚀 Getting Started

### Prerequisites
- Web server (Apache/Nginx) or XAMPP/WAMP
- Modern web browser
- (Optional) PHP 7.4+ and MySQL for backend functionality

### Installation

1. **Clone or download** the project to your web server directory:
   ```
   c:\xampp\htdocs\tour-guide-zamboanga\
   ```

2. **Start your web server** (if using XAMPP, start Apache)

3. **Access the website**:
   ```
   http://localhost/tour-guide-zamboanga/
   ```

### Quick Navigation

- **Homepage**: `index.html`
- **Tourist Login**: `tourist-login.html`
- **Guide Login**: `guide-login.html`
- **Browse Guides**: `tour-guides.html`
- **Tourist Dashboard**: `tourist-dashboard.html` (after login)
- **Guide Dashboard**: `guide-dashboard.html` (after login)

## 👥 User Roles

### Tourist Account
- Register and create profile
- Browse destinations and tour guides
- Book tours with selected guides
- Manage bookings and itinerary
- Leave reviews and ratings
- Track tour history

### Tour Guide Account
- Apply with certification
- Create professional profile
- Set tour packages and pricing
- Accept/decline booking requests
- Manage schedule and availability
- Track earnings and performance
- View customer reviews

## 💰 Tour Package Pricing (Sample)

Tour guides can set their own pricing. Typical packages include:

- **Half Day Tour** (4-6 hours): ₱1,200 - ₱2,000
- **Full Day Tour** (8+ hours): ₱2,000 - ₱3,000
- **Custom Packages**: Negotiable based on itinerary

Prices vary by:
- Tour duration
- Group size
- Destinations included
- Guide experience level
- Special requirements

## 🔐 Authentication System

Currently implemented with frontend validation. For production:

### Recommended Backend Implementation:
- User authentication with JWT tokens
- Password hashing (bcrypt)
- Email verification
- Session management
- Role-based access control
- Secure payment gateway integration

## 🎨 Design Features

- **Responsive Design**: Works on desktop, tablet, and mobile
- **Modern UI**: Clean, professional interface with gradient designs
- **Smooth Animations**: AOS (Animate On Scroll) effects
- **Bootstrap 5**: Mobile-first responsive framework
- **Custom Styling**: Unique color schemes for tourist (purple) and guide (pink) sections

## 📱 Pages Overview

### Public Pages
1. **index.html** - Homepage with hero section, about, 10 destinations, CTA
2. **tour-guides.html** - Browse all available tour guides with filters
3. **tourist-login.html** - Tourist authentication
4. **guide-login.html** - Guide authentication
5. **tourist-register.html** - New tourist registration
6. **guide-register.html** - Guide application form

### Protected Pages (Require Login)
1. **tourist-dashboard.html** - Tourist control panel
2. **guide-dashboard.html** - Guide management panel

## 🔧 Technologies Used

- **HTML5** - Structure and content
- **CSS3** - Styling and animations
- **Bootstrap 5** - Responsive framework
- **JavaScript** - Interactivity and form validation
- **Bootstrap Icons** - Icon library
- **AOS Library** - Scroll animations
- **Swiper.js** - Carousel/slider functionality

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 📝 Future Enhancements

### Recommended Features:
1. **Backend Integration**
   - PHP/Node.js backend
   - MySQL/PostgreSQL database
   - RESTful API

2. **Payment System**
   - PayPal integration
   - GCash/PayMaya support
   - Booking deposits

3. **Real-time Features**
   - Live chat between tourists and guides
   - Real-time booking notifications
   - GPS tracking during tours

4. **Advanced Features**
   - Multi-language support
   - Weather integration
   - Photo gallery uploads
   - Social media sharing
   - Mobile app (iOS/Android)

5. **Admin Panel**
   - Manage users and guides
   - Verify guide certifications
   - Monitor bookings
   - Generate reports
   - Handle disputes

## 🔒 Security Considerations

For production deployment:
- Implement HTTPS/SSL
- Sanitize all user inputs
- Use prepared statements for database queries
- Implement CSRF protection
- Add rate limiting
- Regular security audits
- Backup system

## 📞 Support & Contact

For issues or questions about the platform:
- Email: info@tourismozamboanga.com
- Phone: +63 917 123 4567

## 📄 License

This project is created for educational and demonstration purposes.

## 👨‍💻 Development Notes

### Current Status: Frontend Complete
- All HTML pages created
- Responsive design implemented
- Form validation (client-side)
- Navigation working
- Temporary login system (redirects only)

### Next Steps for Production:
1. Set up database schema
2. Implement backend authentication
3. Create API endpoints
4. Add payment processing
5. Deploy to production server
6. Set up email notifications
7. Implement booking system
8. Add review/rating system

## 🎉 Credits

- **Template**: Bootstrap-based custom design
- **Icons**: Bootstrap Icons
- **Fonts**: Google Fonts (Roboto, Ubuntu, Nunito)
- **Images**: Placeholder images (replace with actual Zamboanga photos)

---

**Version**: 1.0.0  
**Last Updated**: October 2024  
**Status**: Frontend Complete - Ready for Backend Integration

---

## Quick Start Guide

1. Open `index.html` in your browser
2. Explore the 10 destinations
3. Click "Browse Tour Guides" to see available guides
4. Try logging in as a tourist or guide
5. Explore the dashboards

**Note**: Current login is temporary and will redirect without actual authentication. Implement backend for full functionality.
