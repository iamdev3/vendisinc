<!DOCTYPE html>
<html>
<head>
    <title>Translation Test</title>
</head>
<body>
    <h1>Translation Test</h1>
    
    <h2>Common Translations:</h2>
    <p>Name: {{ __('common.fields.name') }}</p>
    <p>Slug: {{ __('common.fields.slug') }}</p>
    <p>Description: {{ __('common.fields.description') }}</p>
    <p>Is Active: {{ __('common.fields.is_active') }}</p>
    
    <h2>Product Translations:</h2>
    <p>Navigation Label: {{ __('products.navigation.label') }}</p>
    <p>Brand: {{ __('products.form.fields.brand_id') }}</p>
    <p>Category: {{ __('products.form.fields.category_id') }}</p>
    
    <h2>Raw JSON Test:</h2>
    <p>Direct JSON: {{ json_decode(file_get_contents(lang_path('common.json')), true)['fields']['name'] ?? 'Not found' }}</p>
    
    <h2>App Info:</h2>
    <p>Locale: {{ app()->getLocale() }}</p>
    <p>Fallback Locale: {{ config('app.fallback_locale') }}</p>
    {{ trans('fields.name')}}
    <h1>{{ __('Welcome') }}</h1>
<p>{{ __('Hello User') }}</p>

</body>
</html>
