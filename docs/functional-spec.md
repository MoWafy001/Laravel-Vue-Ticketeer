# Ticketeer Platform Functional Specification

- [Ticketeer Platform Functional Specification](#ticketeer-platform-functional-specification)
  - [User Roles \& Permissions](#user-roles--permissions)
    - [Venue Manager](#venue-manager)
    - [Venue Staff](#venue-staff)
    - [Buyer](#buyer)
  - [Event \& Ticket Management](#event--ticket-management)
  - [Payment \& Refunds](#payment--refunds)
  - [User Experience \& Access](#user-experience--access)


## User Roles & Permissions

### Venue Manager
- Create and manage events
- Define ticket types, set prices, and specify ticket quantities
- View detailed ticket sales statistics (sales timeline, buyer info, contact details)
- Cancel and refund tickets as needed
- Access a wallet to view collected funds and withdraw eligible amounts (withdrawals only for non-refundable tickets after event completion)
- Specify event details: name, date/time, and ticket sales period (start/end dates)

### Venue Staff
- Invited/hired by venue manager
- Granted permissions to access or create venues and perform venue management tasks
- Permissions are configurable and may be limited by the venue manager

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