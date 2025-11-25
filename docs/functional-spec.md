# Ticketeer Platform Functional Specification

- [Ticketeer Platform Functional Specification](#ticketeer-platform-functional-specification)
  - [User Roles \& Permissions](#user-roles--permissions)
    - [Venue Manager](#venue-manager)
      - [Company-Wide Permissions](#company-wide-permissions)
      - [Event-Specific Permissions](#event-specific-permissions)
    - [Venue Staff](#venue-staff)
      - [Company-Wide Permissions](#company-wide-permissions-1)
      - [Event-Specific Permissions](#event-specific-permissions-1)
    - [Buyer](#buyer)
  - [Event \& Ticket Management](#event--ticket-management)
  - [Payment \& Refunds](#payment--refunds)
  - [User Experience \& Access](#user-experience--access)


## User Roles & Permissions

### Venue Manager
- Invited/hired as a company organizer
- Permissions are divided into company-wide and event-specific

#### Company-Wide Permissions
- Access company dashboard and analytics
- Manage staff accounts and assign roles/permissions
- View and manage company profile and settings
- Create events

#### Event-Specific Permissions
- Edit event details
- Create and manage ticket types for events
- View event statistics and ticket sales
- View buyer contact information for event attendees
- Cancel and refund tickets for specific events

### Venue Staff
- Invited/hired by company organizer
- Permissions are configurable and may be limited by the organizer

#### Company-Wide Permissions
- Access company dashboard and analytics (if granted)
- View company profile and settings (if granted)

#### Event-Specific Permissions
- Edit event details
- Create and manage ticket types for events
- View event statistics and ticket sales
- View buyer contact information for event attendees
- Cancel and refund tickets for specific events
- Manage event attendees
- Access wallet and view collected funds for specific events (withdrawal permission optional)

### Buyer
- Browse events without authentication
- Add tickets to cart; login required only at checkout
- Cancel and refund tickets within 15 days of purchase, except when less than 10 days remain in the ticket sales period
- Pay for tickets via Stripe
- Manage personal profile information
- View and filter purchased tickets by date and status (valid, expired, canceled)
- See ticket expiration and time remaining
- View tickets in card format with QR code for scanning
- Download tickets as PDF with embedded QR code

## Event & Ticket Management
- Event creation includes specifying event name, date/time, and ticket sales window
- Multiple ticket types per event, each with price and quantity
- Ticket status: valid, expired, canceled
- Ticket expiration and refund eligibility rules

## Payment & Refunds
- Stripe integration for ticket purchases
- Refunds processed according to eligibility (within 15 days of purchase, not within last 10 days of sales period)
- Venue manager wallet for tracking and withdrawing funds

## User Experience & Access
- Separate login and registration pages for buyers, and venue managers/staff
- Buyers can browse events and add tickets to cart before authentication
- Profile and ticket management pages for buyers