# USJ CITS Desk

### University Systems Support & Ticketing Management Portal

USJ CITS Desk is a centralized, web-based ticketing and technical support management system designed for the Centre for IT Services (CITS) at the University of Sri Jayewardenepura, Sri Lanka.

The system aims to streamline the reporting, tracking, assignment, and resolution of technical issues and support requests related to university information systems developed and maintained by CITS.

Built with Laravel and React, the platform focuses on security, scalability, maintainability, dynamic access control, and a responsive user experience.

## Project Objectives

- Centralize technical support requests across university information systems.
- Simplify issue reporting and ticket tracking.
- Improve communication between system users and technical support teams.
- Provide secure, dynamic role-based access control.
- Improve accountability and transparency in issue resolution.
- Reduce manual support management processes.

## Features

- University information system management
- System categories and modules
- User-to-system assignments
- Ticket creation and management
- Personal support requests
- System change requests
- Ticket priorities and statuses
- Ticket assignment to technical support teams
- Image and document attachments
- Ticket communication and activity history
- Notifications
- Dashboard analytics
- Reports and audit logs
- Permission-aware administration interfaces

## Technology Stack

| Layer | Technology |
|---|---|
| Frontend | React, JSX |
| Build Tool | Vite |
| Styling | Tailwind CSS |
| Icons | Lucide React |
| Routing | React Router |
| HTTP Client | Axios |
| State Management | React Context API |
| Backend | Laravel, PHP |
| Database | MySQL |
| Authentication | Laravel Sanctum |
| Authorization | Spatie Laravel Permission |
| Testing | Laravel Feature Tests |
| Version Control | Git and GitHub |

## System Architecture

The application follows a decoupled frontend and backend architecture.

**Frontend — React**

Responsible for the user interface, navigation, authentication state, form handling, and API communication.

**Backend — Laravel**

Responsible for business logic, authentication, authorization, request validation, database operations, and REST API services.

**Database — MySQL**

Responsible for persistent storage of users, roles, permissions, sessions, and future ticketing information.

## Project Structure

- `ticketing-api/` — Laravel backend application
- `ticketing-frontend/` — React frontend application

### Backend Structure

- `app/Models/` — Eloquent models
- `app/Http/Controllers/Api/` — API controllers
- `app/Http/Requests/` — Request validation
- `app/Support/` — Authorization and security helpers
- `app/Providers/` — Application configuration
- `database/migrations/` — Database schema
- `database/seeders/` — Initial system data
- `routes/` — Application routes
- `tests/Feature/` — Automated feature tests

### Frontend Structure

- `src/api/` — API communication
- `src/components/` — Reusable components
- `src/context/` — Global application state
- `src/features/` — Feature-based modules
- `src/layouts/` — Application layouts
- `src/utils/` — Utility functions

## Local Development

### Prerequisites

- PHP 8.3 or later
- Composer
- Node.js compatible with the installed Vite version
- npm
- MySQL
- Git

### Backend Setup

1. Navigate to the `ticketing-api` directory.
2. Install dependencies using `composer install`.
3. Create a local `.env` file from `.env.example`.
4. Generate the application key using `php artisan key:generate`.
5. Configure the MySQL database and environment settings.
6. Run database migrations using `php artisan migrate`.
7. Initialize roles and permissions using `php artisan db:seed --class=AccessControlSeeder`.
8. Securely create the initial Super Admin using `php artisan app:bootstrap-super-admin`.
9. Start the backend using `php artisan serve --host=localhost --port=8000`.

### Frontend Setup

1. Navigate to the `ticketing-frontend` directory.
2. Install dependencies using `npm install`.
3. Create a `.env` file and configure `VITE_API_URL=http://localhost:8000`.
4. Start the development server using `npm run dev`.

### Local Application URLs

| Application | URL |
|---|---|
| Frontend | http://localhost:5173 |
| Backend | http://localhost:8000 |

## Security Practices

The project follows a security-focused development approach, including:

- HTTP-only session cookies
- CSRF protection
- Server-side authentication and authorization
- Secure password hashing
- Request validation
- Restricted administrative operations
- Protection against unauthorized privilege escalation
- Database-level uniqueness constraints
- Environment-based configuration
- Isolation of automated testing databases

Sensitive credentials, passwords, application keys, and environment files must not be committed to version control.

## Testing

The backend includes automated Laravel Feature Tests covering authentication, user management, role management, permission assignment, and access-control restrictions.

Run the test suite using `php artisan test` with an isolated testing database.

## Project Status

**Under Active Development**

The authentication, user management, and dynamic role-based access control foundation has been developed. The frontend administration interfaces and core ticketing workflows are currently being developed.

## Acknowledgements

- University of Sri Jayewardenepura (USJ)
- Centre for IT Services (CITS)
- Laravel
- React
- Tailwind CSS
- Spatie Laravel Permission
- Lucide

---

**USJ CITS Desk**

University Systems Support & Ticketing Management Portal

Designed for secure, efficient, and centralized university technical support management.
