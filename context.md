# ULFA BLOG — PROJECT CONTEXT

## Project Overview

**Project Name:** Ulfa Blog  
**Client:** Ulfa  

Ulfa Blog is a clean, maintainable blog platform built for:
- Public content consumption (SEO-friendly)
- Easy content management for non-technical users
- Future monetization (ads, affiliate, etc.)
- Reusability as a base template for future projects

---

## Tech Stack

### Backend
- CodeIgniter 4

### Frontend
- Bulma CSS
- Alpine.js (NO jQuery)

### Authentication
- CodeIgniter Shield (default, extended if needed)

### Database
- MySQL

---

## UI Reference

Design reference is located at:

/reference/logbook-bulma-main

### Rules for Using Reference

- Use as DESIGN REFERENCE ONLY
- DO NOT use:
  - jQuery
  - Gulp
- Extract and adapt HTML into CI4 Views
- Convert jQuery interactions → Alpine.js
- Maintain visual consistency

---

## STRICT UI RULES

- Bulma is the PRIMARY design system
- DO NOT create custom color classes for:
  - buttons
  - text colors
  - backgrounds

- ALL colors must come from:
  - Bulma variables OR
  - global CSS variables (:root)

- is-primary MUST reflect application primary color

- DO NOT hardcode:
  - hex colors
  - rgb values

- Custom CSS ONLY allowed for:
  - layout adjustments
  - non-Bulma components

- If a Bulma class exists, ALWAYS use it

---

## Core Principles

- Keep it simple and clean
- Avoid overengineering
- Maintain reusability
- Readability over cleverness
- Make it reusable for future projects

---

## Architecture Rules

### Controllers
- Must be thin
- Handle request/response only
- No business logic

### Models
- Handle all database operations

### Helpers
- Store reusable logic here
- Examples:
  - formatting
  - UI helpers
  - common transformations

### Service Layer
- DO NOT USE

---

## View Architecture (Component-Based)

Structure:

/views
    /layouts
        main.php
        admin.php

    /components
        navbar.php
        footer.php
        sidebar.php
        post-card.php

    /pages
        /blog
        /admin

### Rules

- Layouts must be reusable
- Components must be isolated
- Avoid duplication

---

## Theming System

Client can control:

### Colors
- Primary
- Secondary

### Theme
- Light mode
- Dark mode

### Implementation

- Store in `web_settings`
- Use CSS variables

Example:

:root {
  --primary: #xxxxxx;
  --secondary: #xxxxxx;
}

---

## Features

### Public Pages (SEO)

ONLY:

- Blog List (index-full-right.html)
- Post Detail
- Author Page
- About Page
- Contact Page

Include:
- Navbar
- Footer

---

### Admin Dashboard (NON-SEO)

- Use client-side fetching (AJAX / fetch)
- Use Alpine.js

Features:
- Dashboard overview
- CRUD posts
- Categories
- Tags
- Media
- Settings (theme, colors)

---

## Author Profile System

Since author data is public, users must be able to customize their profile.

### Extend Shield User

Add profile-related fields (via separate table or extension):

Suggested fields:
- display_name
- username (optional, if different from email login)
- bio
- avatar (media_id or file path)
- social_links (json or separate fields)
  - twitter
  - instagram
  - linkedin
  - website

### Features

- Profile settings page (user can edit their identity)
- Author public page:
  - display_name
  - avatar
  - bio
  - list of posts
  - social links

### Rules

- Do NOT modify Shield core directly
- Extend via:
  - additional table (recommended: `user_profiles`)
  - or safe model extension

---

## Data Fetching Strategy

### Public
- Server-rendered (SEO)

### Admin
- Client-side fetch (AJAX)

---

## Caching Strategy

- No Redis for now
- Use CI4 caching if needed
- Keep simple first

---

## Database Structure

Tables:

- blog_posts
- blog_categories
- blog_post_categories
- blog_tags
- blog_post_tags
- media
- blog_post_media
- web_settings
- (new) user_profiles (but first see does the table form shield already has enough to work with)

Notes:
- Use joins when needed
- Do not overcomplicate

---

## Blog Rules

### Status
- draft
- published

### Slug
- unique

### SEO
- seo_title
- seo_description

Fallback:
- title → seo_title
- excerpt → seo_description

---

## Media Handling

- Use media table
- Types:
  - cover
  - inline
  - gallery

- Avoid duplicates

---

## Coding Conventions

- RESTful routes
- Consistent naming
- No magic logic
- Prefer clarity

---

## Frontend Rules

- Use Bulma
- Minimal custom CSS
- Use Alpine.js for:
  - toggles
  - dropdowns
  - modals

---

## What to Avoid

- No jQuery
- No Gulp
- No service layer
- No overengineering
- No fat controllers
- No logic in views

---

## Instructions for AI (Codex)

- Follow this context strictly
- Use reference folder as UI guide only
- Convert UI into CI4 views
- Replace JS with Alpine.js
- Keep controllers thin
- Use helpers for reusable logic
- Work incrementally
- Do not introduce new frameworks

---

## Development Strategy

### Phase 1
- Blog model
- Blog list
- Post detail

### Phase 2
- Layout system
- Navbar & footer

### Phase 3
- Author profile system

### Phase 4
- Admin dashboard (client fetch)

### Phase 5
- Theming system

### Phase 6
- Optimization

---

## Final Philosophy

This project is:
- A real product
- A reusable template

Keep it simple, clean, and reusable.