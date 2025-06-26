# Indyanimal

Indyanimal is a privacy-focused, invite-only platform for discovering and organizing DIY and independent events in Indiana. It was built to reject the surveillance practices baked into most mainstream event platforms and to prove that privacy and usability can coexist.

---

## Project Purpose

Most event platforms ask for far more data than they need. Indyanimal is a counterexample. It’s designed to share information, not collect it. Every decision—from how users sign up to how events are listed—follows a data minimization mindset.

This was developed as my capstone project for the Informatics B.S. at Indiana University–Indianapolis. It pulls from both my technical training and coursework in legal informatics and data privacy policy.

---

## Key Principles

- No raw email storage (hashed with salt before saving)
- No password recovery or persistent identifiers
- Access is invite-only, with single-use codes
- All uploads are moderated by an admin before going public
- Session data is stored in the database and expires after inactivity
- Admin controls are tightly limited and monitored
- Payment logic works without linking identities to payments

---

## Target Audience

- People who care about privacy and community-based event organizing
- DIY collectives and music scenes that don’t want to rely on corporate platforms
- Promoters who need lightweight tools without invasive tracking

---

## Tech Stack

- PHP with Slim Framework (for routing and backend logic)
- MySQL for structured data
- JavaScript for dynamic frontend behavior
- Python for secure password hashing and verification
- Stripe for optional ticket sales or donations
- Local file-based media storage in `/uploads`


---

## Core Features

### Invite System
Accounts can’t be created without a valid invite code. Each code is one-time use and linked to the user who created it.

### User Roles
Regular users can view events and upload media. Admins have access to approve or reject submissions, create new events, and generate additional invites.

### Media Upload and Approval
Users can upload photos, videos, and lost/found posts. Nothing goes public until an admin reviews and approves it. If something is flagged by an optional AI check, admins will see that during moderation.

### Stripe Integration
Stripe Checkout is fully wired up. Events can be linked to a payment flow, and all completed orders are logged in the database. No identifying payment info is stored.

### AI Detection
A small Python script runs AI-based checks on submitted media. It flags anything unusual so admins can review it before approval. This feature is optional and doesn’t block uploads.

---

## Folder Structure

# Indyanimal

Indyanimal is a privacy-focused, invite-only platform for discovering and organizing DIY and independent events in Indiana. It was built to reject the surveillance practices baked into most mainstream event platforms and to prove that privacy and usability can coexist.

---

## Project Purpose

Most event platforms ask for far more data than they need. Indyanimal is a counterexample. It’s designed to share information, not collect it. Every decision—from how users sign up to how events are listed—follows a data minimization mindset.

This was developed as my capstone project for the Informatics B.S. at Indiana University–Indianapolis. It pulls from both my technical training and coursework in legal informatics and data privacy policy.

---

## Key Principles

- No raw email storage (hashed with salt before saving)
- No password recovery or persistent identifiers
- Access is invite-only, with single-use codes
- All uploads are moderated by an admin before going public
- Session data is stored in the database and expires after inactivity
- Admin controls are tightly limited and monitored
- Payment logic works without linking identities to payments

---

## Target Audience

- People in Indiana who care about privacy and community-based event organizing
- DIY collectives and music scenes that don’t want to rely on corporate platforms
- Promoters who need lightweight tools without invasive tracking

---

## Tech Stack

- PHP with Slim Framework (for routing and backend logic)
- MySQL for structured data
- JavaScript for dynamic frontend behavior
- Python for secure password hashing and verification
- Stripe for optional ticket sales or donations
- Local file-based media storage in `/uploads`

IPFS was part of the original plan but wasn’t implemented in this version.

---

## Core Features

### Invite System
Accounts can’t be created without a valid invite code. Each code is one-time use and linked to the user who created it.

### User Roles
Regular users can view events and upload media. Admins have access to approve or reject submissions, create new events, and generate additional invites.

### Media Upload and Approval
Users can upload photos, videos, and lost/found posts. Nothing goes public until an admin reviews and approves it. If something is flagged by an optional AI check, admins will see that during moderation.

### Stripe Integration
Stripe Checkout is fully wired up. Events can be linked to a payment flow, and all completed orders are logged in the database. No identifying payment info is stored.

### AI Detection (Optional)
A small Python script runs AI-based checks on submitted media. It flags anything unusual so admins can review it before approval. This feature is optional and doesn’t block uploads.

---

## Folder Structure

## Author

Built by Taniya Tucker as a senior capstone project in Informatics at IU Indianapolis.



