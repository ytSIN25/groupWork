# LUMIÈRE - The Art of Cinema

LUMIÈRE is a premium, web-based cinema management and patron booking system designed with a vintage aesthetic and modern functional architecture. It serves as a dual-portal application for both cinema administrators (Directors) and patrons (Audience).

## 🎞️ Key Features

### For Directors (Admin Portal)
- **Executive Summary Dashboard**: Real-time financial analytics including Total Gross Revenue, Total Admissions, and Active Catalog Size.
- **Dynamic Revenue Breakdown**: Interactive doughnut charts (powered by Chart.js) visualizing film performance.
- **Film Repertoire Management**: 
    - Add, edit, and archive movies with a "Smart Sync" system that preserves sales history.
    - Intelligent file management that purges local poster assets upon movie deletion.
    - Automated showtime generation based on release windows.
- **Security**: Role-based access control (RBAC) ensuring only authorized staff can access the projection booth (admin area).

### For Patrons (User Portal)
- **Cinematic Experience**: A high-fidelity, responsive UI featuring glassmorphism and smooth CSS animations.
- **Interactive Booking**: A dynamic seat selection map with real-time price calculation.
- **Personalized Dashboard**: Track your membership status, loyalty points, and viewing history.
- **Promotions**: Integrated voucher system for discounted screenings.

## 🛠️ Technology Stack

- **Backend**: PHP 8.x
- **Database**: MySQL (with InnoDB foreign key cascading for data integrity)
- **Frontend**: Vanilla JavaScript (ES6+), HTML5, CSS3
- **Libraries**:
    - **Chart.js**: For financial visualizations.
    - **SweetAlert2**: For stylized, non-blocking UI notifications and confirmations.
    - **Google Fonts**: Inter, Outfit, and custom calligraphy faces.

## 🚀 Setup Instructions

1.  **Database Configuration**:
    - Import `database.sql` into your MySQL server to create the schema.
    - (Optional) Import `dummyData.sql` to populate the system with sample movies, users, and orders.
    - Update `config.php` with your database credentials (host, username, password, dbname).

2.  **Server Requirements**:
    - Ensure your PHP environment has the `mysqli` extension enabled.
    - The `assets/uploads/` directory must be writeable by the server for movie poster uploads.

3.  **Default Credentials (if using dummy data)**:
    - **Admin**: `arthur@lumiere.com` / `admin`
    - **Patron**: `julian@patron.com` / `patron123`

## 📂 Project Structure

- `/js/main.js`: Core frontend logic and page transition engine.
- `/css/`: Modular CSS files (base, global, and page-specific).
- `/api_*.php`: Headless JSON endpoints for session handling and registration.
- `dashboard_admin.php`: The command center for cinema staff.
- `dashboard_user.php`: The personal hub for patrons.