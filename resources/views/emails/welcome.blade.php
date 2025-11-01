<x-mail::message>
    # Welcome to {{ config('app.name') }}!

    Hi {{ $user['name'] }},

    We're thrilled to have you join our community! Thank you for signing up for {{ config('app.name') }}.

    <x-mail::button :url="$user['url'] ?? url('/')">
        Get Started
    </x-mail::button>

    ## What's Next?

    Here are a few things you can do to get started:

    - **Complete your profile** - Add a profile picture and fill out your information
    - **Explore our products** - Check out our latest offerings
    - **Connect with others** - Join our community discussions

    ## Need Help?

    Our support team is always here to help. If you have any questions, feel free to reach out to us at
    [support@{{ config('app.name') }}.com](mailto:support@{{ config('app.name') }}.com).

    Thanks again for joining us. We can't wait to see what you'll accomplish with {{ config('app.name') }}!

    Best regards,<br>
    The {{ config('app.name') }} Team
</x-mail::message>
