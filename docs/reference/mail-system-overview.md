# Mail System Overview

## Introduction

The Vendisync application has a comprehensive mail system that allows administrators to create, manage, and send email templates through a user-friendly interface. This document provides an overview of how the mail system works for general users and developers.

## Components

### 1. Mail Templates

Mail templates are reusable email designs that can be customized for different purposes. Each template includes:

-   Template name and code identifier
-   Email subject
-   Sender information (name and email)
-   CC and BCC recipients
-   Visual email content editor (using Unlayer)
-   Multi-language support

### 2. Mail Logs

All sent emails are tracked in a logging system that records:

-   Recipient information
-   Send status (success/failure)
-   Timestamps
-   Error messages (for failed emails)
-   Associated template information

### 3. Mail Settings

The application uses configurable SMTP settings that can be managed through the admin panel:

-   Mail server (host and port)
-   Authentication credentials (username and password)
-   Default sender information

## How It Works

### Creating a Mail Template

1. Navigate to Settings > Mail Templates in the admin panel
2. Click "Create Mail Template"
3. Fill in the template details:
    - Name: Descriptive name for the template
    - Code: Unique identifier (used programmatically)
    - Blade file: Reference to the email view file
    - Subject: Email subject line
    - Sender information: From name and email
    - CC/BCC: Additional recipients
4. Design the email content using the visual editor
5. Save the template

### Sending Emails

Emails are sent programmatically using the `DynamicMail` class:

```php
$mail = new DynamicMail('template-code', $data, $locale);
Mail::to($recipient)->send($mail);
```

### Tracking Emails

All sent emails are automatically logged in the mail logs table, which can be viewed in the admin panel.

## Configuration

Mail settings are configured in the Settings section and automatically update the application's environment variables for immediate effect.

## Benefits

-   Visual email template designer
-   Multi-language support
-   Comprehensive logging and tracking
-   Easy configuration through admin panel
-   Flexible template system
