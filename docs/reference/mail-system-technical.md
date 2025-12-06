# Technical Documentation: Mail System

## System Architecture

The mail system consists of several interconnected components that work together to provide a flexible and robust email solution.

### Core Components

#### 1. MailTemplate Model

Located at `app/Models/MailTemplate.php`

-   Uses the `HasTranslations` trait for multi-language support
-   Uses `MailTemplateObserver` for lifecycle events
-   Stores template data in the `mail_templates` database table
-   Fields include: name, code, subject, sender info, CC/BCC, content, etc.

#### 2. MailLog Model

Located at `app/Models/MailLog.php`

-   Tracks all email sending activities
-   Stores recipient, status, timestamps, and error information
-   Related to MailTemplate via foreign key

#### 3. DynamicMail Mailable

Located at `app/Mail/DynamicMail.php`

-   Extends Laravel's base Mailable class
-   Dynamically loads templates based on code identifier
-   Handles multi-language content rendering
-   Uses Blade templates for email content

#### 4. Mail Templates Admin Interface

Located in `app/Filament/Resources/MailTemplates/`

-   Provides CRUD operations for mail templates
-   Uses Unlayer visual editor for content design
-   Implements translatable fields for multi-language support

#### 5. Mail Settings System

Located in `app/Models/Setting.php` and related files

-   Stores SMTP configuration in database
-   Automatically updates `.env` file when settings change
-   Integrates with Laravel's mail configuration system

## Database Structure

### mail_templates Table

```php
Schema::create('mail_templates', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('name', 255);           // Template name
    $table->string('code')->unique()->index(); // Unique identifier
    $table->string('subject', 255);        // Email subject
    $table->string('from_email');          // Sender email
    $table->string('from_name')->nullable(); // Sender name
    $table->string('reply_to')->nullable();  // Reply-to address
    $table->json('cc')->nullable();        // CC recipients
    $table->json('bcc')->nullable();       // BCC recipients
    $table->text('content');               // Email content (HTML)
    $table->string('blade_file');          // Blade template reference
    $table->json('variables')->nullable(); // Available variables
    $table->string('category')->nullable(); // Template category
    $table->text('description')->nullable(); // Description
    $table->boolean('is_active')->default(true)->index(); // Active status
    $table->timestamps();                  // Created/updated timestamps
});
```

### mail_logs Table

```php
Schema::create('mail_logs', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->unsignedBigInteger('mail_template_id')->nullable()->index();
    $table->string('template_code')->nullable();
    $table->string('recipient')->index();
    $table->string('locale')->default('en');
    $table->string('status')->index();
    $table->string('subject')->nullable();
    $table->timestamp('sent_at')->nullable()->index();
    $table->timestamp('failed_at')->nullable();
    $table->text('error_message')->nullable();
    $table->json('metadata')->nullable();
    $table->timestamp('created_at');
    $table->timestamp('updated_at');

    $table->foreign('mail_template_id')->references('id')->on('mail_templates');
});
```

## Code Flow

### 1. Creating a Mail Template

1. User accesses Mail Templates resource in Filament admin
2. `MailTemplateForm` defines the form structure
3. Data is saved to `mail_templates` table via Filament
4. `MailTemplateObserver` handles model events if needed

### 2. Sending an Email

1. Application code instantiates `DynamicMail` with template code and data
2. `DynamicMail` constructor loads the template from database
3. `envelope()` method sets sender, subject, CC/BCC from template
4. `content()` method specifies the Blade view and data
5. Laravel's mail system sends the email using configured mailer
6. Result is logged in `mail_logs` table

### 3. Mail Settings Management

1. User updates mail settings in the Settings panel
2. Settings are saved to `settings` table
3. `Setting::generateConfig()` is called
4. `updateEnvironmentVariables()` updates `.env` file
5. Config cache is cleared
6. Laravel uses new settings for subsequent mail operations

## Key Classes and Methods

### DynamicMail

-   `__construct($templateCode, $data, $locale)`: Loads template and prepares data
-   `envelope()`: Defines email headers (from, subject, cc, bcc)
-   `content()`: Specifies view and data for email body

### Setting Model

-   `generateConfig()`: Creates config file and updates environment
-   `updateEnvironmentVariables()`: Updates .env file with mail settings
-   `escapeEnvValue()`: Safely escapes values for .env file

### MailTemplate Model

-   Uses `HasTranslations` trait for multi-language fields
-   Uses `MailTemplateObserver` for model events
-   Defines relationship with `MailLog` model

## Integration Points

### Laravel Mail Configuration

The system integrates with Laravel's native mail configuration through:

-   Environment variables (MAIL_MAILER, MAIL_HOST, etc.)
-   `config/mail.php` configuration file
-   Laravel's mail manager and transport system

### Filament Admin Panel

-   Uses Filament resources for CRUD operations
-   Implements translatable fields for multi-language support
-   Uses Unlayer component for visual email design

### Database Relationships

-   MailTemplate hasMany MailLog
-   Foreign key constraint ensures data integrity
-   Indexes optimize query performance

## Configuration Flow

1. User updates mail settings in admin panel
2. Settings saved to database
3. `generateConfig()` called to update config file
4. `.env` file updated with new values
5. Config cache cleared
6. Laravel's mail system picks up new configuration
7. Subsequent emails use updated settings

## Error Handling

-   Template not found exceptions in `DynamicMail`
-   Database constraint violations for unique codes
-   Mail sending failures logged in `mail_logs`
-   Environment update failures handled gracefully
