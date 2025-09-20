# Translation Structure

This directory contains JSON-based translation files for the application with a **common + specific** approach for better maintainability.

## Structure

```
lang/
├── en/
│   └── json/
│       ├── common.json      # Common translations (fields, actions, messages)
│       └── products.json    # Product-specific translations
├── gu/
│   └── json/
│       ├── common.json      # Common translations (fields, actions, messages)
│       └── products.json    # Product-specific translations
└── README.md
```

## Usage

### In Filament Resources

```php
// Common fields (reusable across all resources)
->label(__('common.fields.name'))
->label(__('common.fields.is_active'))
->tooltip(__('common.actions.view'))

// Resource-specific fields
->label(__('products.form.fields.brand_id'))
->placeholder(__('products.form.placeholders.name'))
->label(__('products.table.columns.price'))
```

### In Blade Templates

```blade
{{ __('products.pages.list.title') }}
{{ __('common.messages.created') }}
```

### In Controllers

```php
return redirect()->back()->with('success', __('common.messages.created'));
```

## Translation Key Structure

### Common Translations (`common.json`)

-   `fields.*` - Common field labels (name, slug, description, is_active, etc.)
-   `actions.*` - Common actions (view, edit, delete, create, save, etc.)
-   `placeholders.*` - Common placeholders
-   `filters.*` - Common filter options
-   `messages.*` - Common success/error messages
-   `sections.*` - Common section titles

### Resource-Specific Translations (`products.json`)

-   `navigation.*` - Navigation labels and groups
-   `form.sections.*` - Form section titles and descriptions
-   `form.fields.*` - Resource-specific field labels
-   `form.placeholders.*` - Resource-specific placeholders
-   `form.helpers.*` - Resource-specific helper text
-   `table.columns.*` - Resource-specific table columns
-   `table.filters.*` - Resource-specific filters
-   `table.bulk_actions.*` - Resource-specific bulk actions
-   `pages.*` - Page titles and descriptions

## Benefits of This Structure

1. **DRY Principle**: Common elements are defined once and reused
2. **Maintainability**: Easy to update common translations across all resources
3. **Consistency**: Ensures consistent terminology across the application
4. **Scalability**: Easy to add new resources following the same pattern
5. **Shorter Keys**: Less verbose translation keys

## Adding New Resources

1. Create `{resource}.json` in both language directories
2. Use common translations for standard fields/actions
3. Add resource-specific translations only for unique elements
4. Follow the established structure

## Language Switching

To switch languages in your application, you can use Laravel's built-in localization features or implement a custom language switcher in your Filament admin panel.
