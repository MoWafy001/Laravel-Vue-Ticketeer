# API Design

## Table of Contents
- [API Design](#api-design)
  - [Table of Contents](#table-of-contents)
  - [Standard Response Schema](#standard-response-schema)
    - [Success Response](#success-response)
    - [Error Response](#error-response)
    - [Paginated Response](#paginated-response)
  - [Authentication](#authentication)
    - [POST /api/auth/organizer/register](#post-apiauthorganizerregister)
    - [POST /api/auth/organizer/login](#post-apiauthorganizerlogin)
    - [POST /api/auth/buyer/register](#post-apiauthbuyerregister)
    - [POST /api/auth/buyer/login](#post-apiauthbuyerlogin)
    - [POST /api/auth/logout](#post-apiauthlogout)
  - [Organizers API](#organizers-api)
    - [GET /api/organizer/profile](#get-apiorganizerprofile)
    - [PUT /api/organizer/profile](#put-apiorganizerprofile)
  - [Companies API](#companies-api)
    - [POST /api/companies](#post-apicompanies)
    - [GET /api/companies](#get-apicompanies)
    - [GET /api/companies/{id}](#get-apicompaniesid)
    - [PUT /api/companies/{id}](#put-apicompaniesid)
    - [DELETE /api/companies/{id}](#delete-apicompaniesid)
    - [GET /api/companies/{id}/analytics](#get-apicompaniesidanalytics)
  - [Company Members API](#company-members-api)
    - [POST /api/companies/{company\_id}/members](#post-apicompaniescompany_idmembers)
    - [GET /api/companies/{company\_id}/members](#get-apicompaniescompany_idmembers)
    - [GET /api/companies/{company\_id}/members/{member\_id}](#get-apicompaniescompany_idmembersmember_id)
    - [PUT /api/companies/{company\_id}/members/{member\_id}](#put-apicompaniescompany_idmembersmember_id)
    - [DELETE /api/companies/{company\_id}/members/{member\_id}](#delete-apicompaniescompany_idmembersmember_id)
  - [Events API](#events-api)
    - [POST /api/events](#post-apievents)
    - [GET /api/events](#get-apievents)
    - [GET /api/events/{id}](#get-apieventsid)
    - [PUT /api/events/{id}](#put-apieventsid)
    - [DELETE /api/events/{id}](#delete-apieventsid)
    - [GET /api/events/{id}/analytics](#get-apieventsidanalytics)
    - [GET /api/events/{id}/attendees](#get-apieventsidattendees)
  - [Event Members API](#event-members-api)
    - [POST /api/events/{event\_id}/members](#post-apieventsevent_idmembers)
    - [GET /api/events/{event\_id}/members](#get-apieventsevent_idmembers)
    - [PUT /api/events/{event\_id}/members/{member\_id}](#put-apieventsevent_idmembersmember_id)
    - [DELETE /api/events/{event\_id}/members/{member\_id}](#delete-apieventsevent_idmembersmember_id)
  - [Tickets API](#tickets-api)
    - [POST /api/events/{event\_id}/tickets](#post-apieventsevent_idtickets)
    - [GET /api/events/{event\_id}/tickets](#get-apieventsevent_idtickets)
    - [GET /api/tickets/{id}](#get-apiticketsid)
    - [PUT /api/tickets/{id}](#put-apiticketsid)
    - [DELETE /api/tickets/{id}](#delete-apiticketsid)
  - [Buyers API](#buyers-api)
    - [GET /api/buyer/profile](#get-apibuyerprofile)
    - [PUT /api/buyer/profile](#put-apibuyerprofile)
    - [GET /api/buyer/tickets](#get-apibuyertickets)
    - [GET /api/buyer/tickets/{id}](#get-apibuyerticketsid)
    - [GET /api/buyer/tickets/{id}/pdf](#get-apibuyerticketsidpdf)
    - [POST /api/buyer/tickets/{id}/cancel](#post-apibuyerticketsidcancel)
    - [POST /api/tickets/scan](#post-apiticketsscan)
  - [Orders API](#orders-api)
    - [POST /api/orders](#post-apiorders)
    - [GET /api/orders](#get-apiorders)
    - [GET /api/orders/{id}](#get-apiordersid)
  - [Payments API](#payments-api)
    - [POST /api/payments/checkout](#post-apipaymentscheckout)
    - [POST /api/payments/webhook](#post-apipaymentswebhook)
    - [GET /api/payments/{id}](#get-apipaymentsid)
  - [Bought Tickets API](#bought-tickets-api)
    - [POST /api/organizer/tickets/{bought\_ticket\_id}/cancel](#post-apiorganizerticketsbought_ticket_idcancel)
  - [Error Codes](#error-codes)
  - [Rate Limiting](#rate-limiting)


## Standard Response Schema

All API responses follow this standard structure:

### Success Response
```json
{
  "status": "success",
  "message": "Operation completed successfully",
  "data": {},
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### Error Response
```json
{
  "status": "error",
  "message": "Error description",
  "error": {
    "code": "ERROR_CODE",
    "details": "Detailed error message",
    "field": "field_name"
  },
  "data": null,
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### Paginated Response
```json
{
  "status": "success",
  "message": "Data retrieved successfully",
  "data": [],
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0",
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 100,
      "total_pages": 5,
      "has_more": true
    }
  }
}
```

---

## Authentication

### POST /api/auth/organizer/register
Register a new organizer account.

**Request Body:**
```json
{
  "name": "string (required, max: 255)",
  "email": "string (required, email, unique)",
  "password": "string (required, min: 8)"
}
```

**Response (201):**
```json
{
  "status": "success",
  "message": "Organizer registered successfully",
  "data": {
    "organizer": {
      "id": "uuid",
      "name": "string",
      "email": "string",
      "created_at": "datetime",
      "updated_at": "datetime"
    },
    "token": "string",
    "token_type": "Bearer"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### POST /api/auth/organizer/login
Login for organizers and venue staff.

**Request Body:**
```json
{
  "email": "string (required, email)",
  "password": "string (required)"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": {
      "id": "uuid",
      "name": "string",
      "email": "string",
      "type": "organizer|company_member"
    },
    "token": "string",
    "token_type": "Bearer"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### POST /api/auth/buyer/register
Register a new buyer account.

**Request Body:**
```json
{
  "name": "string (required, max: 255)",
  "email": "string (required, email, unique)",
  "password": "string (required, min: 8)"
}
```

**Response (201):**
```json
{
  "status": "success",
  "message": "Buyer registered successfully",
  "data": {
    "buyer": {
      "id": "uuid",
      "name": "string",
      "email": "string",
      "created_at": "datetime",
      "updated_at": "datetime"
    },
    "token": "string",
    "token_type": "Bearer"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### POST /api/auth/buyer/login
Login for buyers.

**Request Body:**
```json
{
  "email": "string (required, email)",
  "password": "string (required)"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "buyer": {
      "id": "uuid",
      "name": "string",
      "email": "string"
    },
    "token": "string",
    "token_type": "Bearer"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### POST /api/auth/logout
Logout the authenticated user.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Logged out successfully",
  "data": null,
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

---

## Organizers API

### GET /api/organizer/profile
Get authenticated organizer profile.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Profile retrieved successfully",
  "data": {
    "id": "uuid",
    "name": "string",
    "email": "string",
    "created_at": "datetime",
    "updated_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### PUT /api/organizer/profile
Update organizer profile.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "string (optional, max: 255)",
  "email": "string (optional, email, unique)",
  "password": "string (optional, min: 8)",
  "current_password": "string (required if changing email or password)"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Profile updated successfully",
  "data": {
    "id": "uuid",
    "name": "string",
    "email": "string",
    "updated_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

---

## Companies API

### POST /api/companies
Create a new company.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "string (required, max: 255)"
}
```

**Response (201):**
```json
{
  "status": "success",
  "message": "Company created successfully",
  "data": {
    "id": "uuid",
    "name": "string",
    "owner_id": "uuid",
    "created_at": "datetime",
    "updated_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### GET /api/companies
Get all companies owned by the authenticated organizer.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
```
page: integer (optional, default: 1)
per_page: integer (optional, default: 20, max: 100)
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Companies retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "name": "string",
      "owner_id": "uuid",
      "created_at": "datetime",
      "updated_at": "datetime"
    }
  ],
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0",
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 5,
      "total_pages": 1,
      "has_more": false
    }
  }
}
```

### GET /api/companies/{id}
Get a specific company by ID.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Company retrieved successfully",
  "data": {
    "id": "uuid",
    "name": "string",
    "owner_id": "uuid",
    "created_at": "datetime",
    "updated_at": "datetime",
    "members_count": 5,
    "events_count": 10
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### PUT /api/companies/{id}
Update company details.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "string (required, max: 255)"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Company updated successfully",
  "data": {
    "id": "uuid",
    "name": "string",
    "owner_id": "uuid",
    "updated_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### DELETE /api/companies/{id}
Delete a company.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Company deleted successfully",
  "data": null,
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### GET /api/companies/{id}/analytics
Get company analytics and statistics.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
```
start_date: date (optional, format: YYYY-MM-DD)
end_date: date (optional, format: YYYY-MM-DD)
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Analytics retrieved successfully",
  "data": {
    "total_events": 25,
    "total_tickets_sold": 1500,
    "total_revenue": 75000.00,
    "active_events": 5,
    "upcoming_events": 3,
    "past_events": 17,
    "revenue_by_event": [
      {
        "event_id": "uuid",
        "event_name": "string",
        "tickets_sold": 150,
        "revenue": 7500.00
      }
    ],
    "sales_by_date": [
      {
        "date": "2025-01-15",
        "tickets_sold": 50,
        "revenue": 2500.00
      }
    ]
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

---

## Company Members API

### POST /api/companies/{company_id}/members
Invite a new member to the company.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "organizer_id": "uuid (required)",
  "can_view_analytics": "boolean (optional, default: false)",
  "can_manage_members": "boolean (optional, default: false)",
  "can_manage_settings": "boolean (optional, default: false)",
  "can_create_events": "boolean (optional, default: false)",
  "can_manage_all_events": "boolean (optional, default: false)",
  "can_manage_wallet": "boolean (optional, default: false)"
}
```

**Response (201):**
```json
{
  "status": "success",
  "message": "Member added successfully",
  "data": {
    "id": "uuid",
    "company_id": "uuid",
    "organizer_id": "uuid",
    "can_view_analytics": true,
    "can_manage_members": false,
    "can_manage_settings": false,
    "can_create_events": true,
    "can_manage_all_events": false,
    "can_manage_wallet": false,
    "created_at": "datetime",
    "updated_at": "datetime",
    "organizer": {
      "id": "uuid",
      "name": "string",
      "email": "string"
    }
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### GET /api/companies/{company_id}/members
Get all members of a company.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
```
page: integer (optional, default: 1)
per_page: integer (optional, default: 20, max: 100)
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Members retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "company_id": "uuid",
      "organizer_id": "uuid",
      "can_view_analytics": true,
      "can_manage_members": false,
      "can_manage_settings": false,
      "can_create_events": true,
      "can_manage_all_events": false,
      "can_manage_wallet": false,
      "created_at": "datetime",
      "updated_at": "datetime",
      "organizer": {
        "id": "uuid",
        "name": "string",
        "email": "string"
      }
    }
  ],
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0",
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 8,
      "total_pages": 1,
      "has_more": false
    }
  }
}
```

### GET /api/companies/{company_id}/members/{member_id}
Get a specific company member.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Member retrieved successfully",
  "data": {
    "id": "uuid",
    "company_id": "uuid",
    "organizer_id": "uuid",
    "can_view_analytics": true,
    "can_manage_members": false,
    "can_manage_settings": false,
    "can_create_events": true,
    "can_manage_all_events": false,
    "can_manage_wallet": false,
    "created_at": "datetime",
    "updated_at": "datetime",
    "organizer": {
      "id": "uuid",
      "name": "string",
      "email": "string"
    }
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### PUT /api/companies/{company_id}/members/{member_id}
Update member permissions.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "can_view_analytics": "boolean (optional)",
  "can_manage_members": "boolean (optional)",
  "can_manage_settings": "boolean (optional)",
  "can_create_events": "boolean (optional)",
  "can_manage_all_events": "boolean (optional)",
  "can_manage_wallet": "boolean (optional)"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Member permissions updated successfully",
  "data": {
    "id": "uuid",
    "company_id": "uuid",
    "organizer_id": "uuid",
    "can_view_analytics": true,
    "can_manage_members": true,
    "can_manage_settings": false,
    "can_create_events": true,
    "can_manage_all_events": false,
    "can_manage_wallet": false,
    "updated_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### DELETE /api/companies/{company_id}/members/{member_id}
Remove a member from the company.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Member removed successfully",
  "data": null,
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

---

## Events API

### POST /api/events
Create a new event.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "company_id": "uuid (required)",
  "name": "string (required, max: 255)",
  "description": "text (optional)",
  "start_time": "datetime (required, format: YYYY-MM-DD HH:MM:SS)",
  "end_time": "datetime (required, format: YYYY-MM-DD HH:MM:SS, after start_time)",
  "sale_start_time": "datetime (required, format: YYYY-MM-DD HH:MM:SS)",
  "sale_end_time": "datetime (required, format: YYYY-MM-DD HH:MM:SS, after sale_start_time)"
}
```

**Response (201):**
```json
{
  "status": "success",
  "message": "Event created successfully",
  "data": {
    "id": "uuid",
    "company_id": "uuid",
    "created_by": "uuid",
    "name": "string",
    "description": "text",
    "start_time": "datetime",
    "end_time": "datetime",
    "sale_start_time": "datetime",
    "sale_end_time": "datetime",
    "created_at": "datetime",
    "updated_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### GET /api/events
Get all events (public - no authentication required).

**Query Parameters:**
```
page: integer (optional, default: 1)
per_page: integer (optional, default: 20, max: 100)
search: string (optional, search in name and description)
company_id: uuid (optional, filter by company)
start_date: date (optional, filter events starting after this date)
end_date: date (optional, filter events starting before this date)
status: string (optional, values: upcoming|ongoing|past|on_sale)
sort_by: string (optional, values: start_time|name|created_at, default: start_time)
sort_order: string (optional, values: asc|desc, default: asc)
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Events retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "company_id": "uuid",
      "name": "string",
      "description": "text",
      "start_time": "datetime",
      "end_time": "datetime",
      "sale_start_time": "datetime",
      "sale_end_time": "datetime",
      "created_at": "datetime",
      "company": {
        "id": "uuid",
        "name": "string"
      },
      "tickets_available": 150,
      "min_price": 50.00,
      "max_price": 200.00
    }
  ],
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0",
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 45,
      "total_pages": 3,
      "has_more": true
    }
  }
}
```

### GET /api/events/{id}
Get a specific event by ID (public - no authentication required).

**Response (200):**
```json
{
  "status": "success",
  "message": "Event retrieved successfully",
  "data": {
    "id": "uuid",
    "company_id": "uuid",
    "name": "string",
    "description": "text",
    "start_time": "datetime",
    "end_time": "datetime",
    "sale_start_time": "datetime",
    "sale_end_time": "datetime",
    "created_at": "datetime",
    "updated_at": "datetime",
    "company": {
      "id": "uuid",
      "name": "string"
    },
    "ticket_types": [
      {
        "id": "uuid",
        "code": "string",
        "type": "string",
        "price": 100.00,
        "quantity": 200,
        "available": 150
      }
    ]
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### PUT /api/events/{id}
Update event details.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "string (optional, max: 255)",
  "description": "text (optional)",
  "start_time": "datetime (optional, format: YYYY-MM-DD HH:MM:SS)",
  "end_time": "datetime (optional, format: YYYY-MM-DD HH:MM:SS)",
  "sale_start_time": "datetime (optional, format: YYYY-MM-DD HH:MM:SS)",
  "sale_end_time": "datetime (optional, format: YYYY-MM-DD HH:MM:SS)"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Event updated successfully",
  "data": {
    "id": "uuid",
    "company_id": "uuid",
    "name": "string",
    "description": "text",
    "start_time": "datetime",
    "end_time": "datetime",
    "sale_start_time": "datetime",
    "sale_end_time": "datetime",
    "updated_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### DELETE /api/events/{id}
Delete an event.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Event deleted successfully",
  "data": null,
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### GET /api/events/{id}/analytics
Get event analytics and statistics.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Event analytics retrieved successfully",
  "data": {
    "event_id": "uuid",
    "event_name": "string",
    "total_tickets": 500,
    "tickets_sold": 350,
    "tickets_available": 150,
    "total_revenue": 35000.00,
    "sales_by_ticket_type": [
      {
        "ticket_type": "VIP",
        "total": 100,
        "sold": 80,
        "available": 20,
        "revenue": 16000.00
      }
    ],
    "sales_by_date": [
      {
        "date": "2025-01-15",
        "tickets_sold": 25,
        "revenue": 2500.00
      }
    ],
    "buyers_count": 300,
    "refunds_count": 5,
    "refunds_amount": 500.00
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### GET /api/events/{id}/attendees
Get list of event attendees with contact information.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
```
page: integer (optional, default: 1)
per_page: integer (optional, default: 20, max: 100)
search: string (optional, search in buyer name and email)
ticket_type: uuid (optional, filter by ticket type)
status: string (optional, values: valid|used|cancelled)
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Attendees retrieved successfully",
  "data": [
    {
      "ticket_id": "uuid",
      "buyer_id": "uuid",
      "buyer_name": "string",
      "buyer_email": "string",
      "ticket_type": "string",
      "ticket_code": "string",
      "purchase_date": "datetime",
      "status": "valid",
      "used_at": "datetime|null"
    }
  ],
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0",
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 350,
      "total_pages": 18,
      "has_more": true
    }
  }
}
```

---

## Event Members API

### POST /api/events/{event_id}/members
Assign a member to an event with specific permissions.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "member_id": "uuid (required, company member ID)",
  "can_edit_details": "boolean (optional, default: false)",
  "can_manage_tickets": "boolean (optional, default: false)",
  "can_view_analytics": "boolean (optional, default: false)",
  "can_view_buyer_contacts": "boolean (optional, default: false)",
  "can_cancel_tickets": "boolean (optional, default: false)",
  "can_scan_tickets": "boolean (optional, default: false)"
}
```

**Response (201):**
```json
{
  "status": "success",
  "message": "Member assigned to event successfully",
  "data": {
    "id": "uuid",
    "event_id": "uuid",
    "member_id": "uuid",
    "can_edit_details": true,
    "can_manage_tickets": true,
    "can_view_analytics": true,
    "can_view_buyer_contacts": false,
    "can_cancel_tickets": true,
    "can_scan_tickets": true,
    "created_at": "datetime",
    "updated_at": "datetime",
    "member": {
      "id": "uuid",
      "organizer": {
        "id": "uuid",
        "name": "string",
        "email": "string"
      }
    }
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### GET /api/events/{event_id}/members
Get all members assigned to an event.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Event members retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "event_id": "uuid",
      "member_id": "uuid",
      "can_edit_details": true,
      "can_manage_tickets": true,
      "can_view_analytics": true,
      "can_view_buyer_contacts": false,
      "can_cancel_tickets": true,
      "can_scan_tickets": true,
      "created_at": "datetime",
      "member": {
        "id": "uuid",
        "organizer": {
          "id": "uuid",
          "name": "string",
          "email": "string"
        }
      }
    }
  ],
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### PUT /api/events/{event_id}/members/{member_id}
Update event member permissions.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "can_edit_details": "boolean (optional)",
  "can_manage_tickets": "boolean (optional)",
  "can_view_analytics": "boolean (optional)",
  "can_view_buyer_contacts": "boolean (optional)",
  "can_cancel_tickets": "boolean (optional)",
  "can_scan_tickets": "boolean (optional)"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Event member permissions updated successfully",
  "data": {
    "id": "uuid",
    "event_id": "uuid",
    "member_id": "uuid",
    "can_edit_details": true,
    "can_manage_tickets": false,
    "can_view_analytics": true,
    "can_view_buyer_contacts": true,
    "can_cancel_tickets": false,
    "can_scan_tickets": true,
    "updated_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### DELETE /api/events/{event_id}/members/{member_id}
Remove a member from an event.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Member removed from event successfully",
  "data": null,
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

---

## Tickets API

### POST /api/events/{event_id}/tickets
Create a new ticket type for an event.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "code": "string (required, max: 50, unique per event)",
  "type": "string (required, max: 100)",
  "price": "decimal (required, min: 0, max: 999999.99)",
  "quantity": "integer (required, min: 1)"
}
```

**Response (201):**
```json
{
  "status": "success",
  "message": "Ticket type created successfully",
  "data": {
    "id": "uuid",
    "event_id": "uuid",
    "code": "string",
    "type": "string",
    "price": 100.00,
    "quantity": 200,
    "created_at": "datetime",
    "updated_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### GET /api/events/{event_id}/tickets
Get all ticket types for an event.

**Response (200):**
```json
{
  "status": "success",
  "message": "Ticket types retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "event_id": "uuid",
      "code": "string",
      "type": "string",
      "price": 100.00,
      "quantity": 200,
      "available": 150,
      "sold": 50,
      "created_at": "datetime"
    }
  ],
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### GET /api/tickets/{id}
Get a specific ticket type by ID.

**Response (200):**
```json
{
  "status": "success",
  "message": "Ticket type retrieved successfully",
  "data": {
    "id": "uuid",
    "event_id": "uuid",
    "code": "string",
    "type": "string",
    "price": 100.00,
    "quantity": 200,
    "available": 150,
    "sold": 50,
    "created_at": "datetime",
    "updated_at": "datetime",
    "event": {
      "id": "uuid",
      "name": "string",
      "start_time": "datetime",
      "end_time": "datetime"
    }
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### PUT /api/tickets/{id}
Update ticket type details.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "code": "string (optional, max: 50)",
  "type": "string (optional, max: 100)",
  "price": "decimal (optional, min: 0, max: 999999.99)",
  "quantity": "integer (optional, min: current sold count)"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Ticket type updated successfully",
  "data": {
    "id": "uuid",
    "event_id": "uuid",
    "code": "string",
    "type": "string",
    "price": 120.00,
    "quantity": 250,
    "updated_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### DELETE /api/tickets/{id}
Delete a ticket type (only if no tickets sold).

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Ticket type deleted successfully",
  "data": null,
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

---

## Buyers API

### GET /api/buyer/profile
Get authenticated buyer profile.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Profile retrieved successfully",
  "data": {
    "id": "uuid",
    "name": "string",
    "email": "string",
    "created_at": "datetime",
    "updated_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### PUT /api/buyer/profile
Update buyer profile.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "string (optional, max: 255)",
  "email": "string (optional, email, unique)",
  "password": "string (optional, min: 8)",
  "current_password": "string (required if changing email or password)"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Profile updated successfully",
  "data": {
    "id": "uuid",
    "name": "string",
    "email": "string",
    "updated_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### GET /api/buyer/tickets
Get all tickets purchased by the authenticated buyer.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
```
page: integer (optional, default: 1)
per_page: integer (optional, default: 20, max: 100)
status: string (optional, values: valid|expired|cancelled)
date_from: date (optional, filter by event start date)
date_to: date (optional, filter by event start date)
sort_by: string (optional, values: purchase_date|event_date, default: event_date)
sort_order: string (optional, values: asc|desc, default: asc)
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Tickets retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "ticket_id": "uuid",
      "order_id": "uuid",
      "buyer_id": "uuid",
      "valid_until": "datetime",
      "used_at": "datetime|null",
      "status": "valid",
      "qr_code": "string",
      "purchased_at": "datetime",
      "can_refund": true,
      "refund_deadline": "datetime",
      "ticket": {
        "id": "uuid",
        "code": "string",
        "type": "string",
        "price": 100.00,
        "event": {
          "id": "uuid",
          "name": "string",
          "start_time": "datetime",
          "end_time": "datetime",
          "company": {
            "id": "uuid",
            "name": "string"
          }
        }
      }
    }
  ],
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0",
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 15,
      "total_pages": 1,
      "has_more": false
    }
  }
}
```

### GET /api/buyer/tickets/{id}
Get a specific purchased ticket by ID.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Ticket retrieved successfully",
  "data": {
    "id": "uuid",
    "ticket_id": "uuid",
    "order_id": "uuid",
    "buyer_id": "uuid",
    "valid_until": "datetime",
    "used_at": "datetime|null",
    "status": "valid",
    "qr_code": "string",
    "purchased_at": "datetime",
    "can_refund": true,
    "refund_deadline": "datetime",
    "time_remaining": "5 days 3 hours",
    "ticket": {
      "id": "uuid",
      "code": "string",
      "type": "string",
      "price": 100.00,
      "event": {
        "id": "uuid",
        "name": "string",
        "description": "text",
        "start_time": "datetime",
        "end_time": "datetime",
        "company": {
          "id": "uuid",
          "name": "string"
        }
      }
    }
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### GET /api/buyer/tickets/{id}/pdf
Download ticket as PDF with QR code.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```
Content-Type: application/pdf
Content-Disposition: attachment; filename="ticket-{id}.pdf"

[PDF Binary Data]
```

### POST /api/buyer/tickets/{id}/cancel
Cancel and refund a purchased ticket.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Ticket cancelled and refund initiated",
  "data": {
    "ticket_id": "uuid",
    "status": "cancelled",
    "refund_amount": 100.00,
    "refund_status": "pending",
    "cancelled_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### POST /api/tickets/scan
Scan and validate a ticket (for venue staff).

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "qr_code": "string (required)"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Ticket validated successfully",
  "data": {
    "ticket_id": "uuid",
    "valid": true,
    "status": "valid",
    "used_at": "datetime",
    "buyer": {
      "name": "string",
      "email": "string"
    },
    "ticket_type": "string",
    "event": {
      "id": "uuid",
      "name": "string"
    }
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

---

## Orders API

### POST /api/orders
Create a new order (add items to cart).

**Headers:**
```
Authorization: Bearer {token} (required at checkout)
```

**Request Body:**
```json
{
  "items": [
    {
      "ticket_id": "uuid (required)",
      "quantity": "integer (required, min: 1)"
    }
  ]
}
```

**Response (201):**
```json
{
  "status": "success",
  "message": "Order created successfully",
  "data": {
    "id": "uuid",
    "buyer_id": "uuid",
    "status": "pending",
    "amount": 250.00,
    "items": [
      {
        "ticket_id": "uuid",
        "ticket_type": "string",
        "price": 100.00,
        "quantity": 2,
        "subtotal": 200.00
      },
      {
        "ticket_id": "uuid",
        "ticket_type": "string",
        "price": 50.00,
        "quantity": 1,
        "subtotal": 50.00
      }
    ],
    "created_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### GET /api/orders
Get all orders for the authenticated buyer.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
```
page: integer (optional, default: 1)
per_page: integer (optional, default: 20, max: 100)
status: string (optional, values: pending|completed|failed|refunded)
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Orders retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "buyer_id": "uuid",
      "payment_id": "uuid",
      "status": "completed",
      "amount": 250.00,
      "created_at": "datetime",
      "updated_at": "datetime",
      "items_count": 3,
      "event": {
        "id": "uuid",
        "name": "string",
        "start_time": "datetime"
      }
    }
  ],
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0",
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 8,
      "total_pages": 1,
      "has_more": false
    }
  }
}
```

### GET /api/orders/{id}
Get a specific order by ID.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Order retrieved successfully",
  "data": {
    "id": "uuid",
    "buyer_id": "uuid",
    "payment_id": "uuid",
    "status": "completed",
    "amount": 250.00,
    "created_at": "datetime",
    "updated_at": "datetime",
    "items": [
      {
        "id": "uuid",
        "ticket_id": "uuid",
        "ticket_type": "string",
        "price": 100.00,
        "event": {
          "id": "uuid",
          "name": "string",
          "start_time": "datetime"
        }
      }
    ],
    "payment": {
      "id": "uuid",
      "provider": "stripe",
      "status": "completed",
      "created_at": "datetime"
    }
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

---

## Payments API

### POST /api/payments/checkout
Initiate payment for an order.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "order_id": "uuid (required)",
  "payment_method": "string (optional, default: card)",
  "return_url": "string (optional, URL to redirect after payment)"
}
```

**Response (201):**
```json
{
  "status": "success",
  "message": "Payment session created",
  "data": {
    "payment_id": "uuid",
    "order_id": "uuid",
    "provider": "stripe",
    "status": "pending",
    "amount": 250.00,
    "checkout_url": "https://checkout.stripe.com/...",
    "session_id": "string",
    "expires_at": "datetime"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### POST /api/payments/webhook
Stripe webhook endpoint for payment status updates.

**Request Body:**
```json
{
  "type": "string",
  "data": {}
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Webhook processed",
  "data": null,
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### GET /api/payments/{id}
Get payment details.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Payment retrieved successfully",
  "data": {
    "id": "uuid",
    "buyer_id": "uuid",
    "provider": "stripe",
    "status": "completed",
    "created_at": "datetime",
    "updated_at": "datetime",
    "order": {
      "id": "uuid",
      "status": "completed",
      "amount": 250.00
    }
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

---

## Bought Tickets API

### POST /api/organizer/tickets/{bought_ticket_id}/cancel
Cancel a buyer's ticket (organizer action).

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "reason": "string (optional, max: 500)"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Ticket cancelled successfully",
  "data": {
    "ticket_id": "uuid",
    "status": "cancelled",
    "refund_amount": 100.00,
    "cancelled_at": "datetime",
    "cancelled_by": "organizer"
  },
  "meta": {
    "timestamp": "2025-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

---

## Error Codes

| Code | Description |
|------|-------------|
| `UNAUTHORIZED` | Authentication required or invalid token |
| `FORBIDDEN` | Insufficient permissions |
| `NOT_FOUND` | Resource not found |
| `VALIDATION_ERROR` | Invalid input data |
| `DUPLICATE_ENTRY` | Resource already exists |
| `PAYMENT_FAILED` | Payment processing failed |
| `REFUND_NOT_ALLOWED` | Ticket not eligible for refund |
| `INSUFFICIENT_TICKETS` | Not enough tickets available |
| `TICKET_ALREADY_USED` | Ticket has already been scanned |
| `EVENT_SALES_CLOSED` | Ticket sales period has ended |
| `INTERNAL_ERROR` | Server error |

---

## Rate Limiting

All API endpoints are rate-limited to prevent abuse:

- **Public endpoints**: 100 requests per minute
- **Authenticated endpoints**: 200 requests per minute
- **Payment endpoints**: 20 requests per minute

Rate limit headers are included in all responses:
```
X-RateLimit-Limit: 200
X-RateLimit-Remaining: 195
X-RateLimit-Reset: 1642248000
```

