<?php

namespace App\Mail;

use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DynamicMail extends Mailable
{
    use Queueable, SerializesModels;

    protected string $templateCode;
    protected array $data;
    protected string $templateLocale;
    protected MailTemplate $mailTemplate;

    /**
     * Create a new message instance.
     *      
     * @param string        $templateCode Template code (e.g., 'welcome-email')
     * @param array         $data Variables to pass to template
     * @param string|null   $templateLocale Locale to use (defaults to app locale)
     */
    public function __construct(string $templateCode, array $data = [], ?string $templateLocale = "en")
    {
        $this->templateCode = $templateCode;
        $this->data         = $data;
        $this->locale       = $templateLocale ?? "en";

        $this->mailTemplate = MailTemplate::where('code', $templateCode)->first();

        if (!$this->mailTemplate) {
            throw new \Exception("Mail template '{$templateCode}' not found or inactive.");
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject : $this->mailTemplate->getTranslation('subject', $this->locale) ?? config('app.name'),
            from    : $this->mailTemplate->from_email ? new Address($this->mailTemplate->from_email, $this->mailTemplate->from_name ?? config('mail.from.name')) : null,
            cc      : is_array($this->mailTemplate->cc) ? $this->mailTemplate->cc : [],
            bcc     : is_array($this->mailTemplate->bcc) ? $this->mailTemplate->bcc : [],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: "emails.{$this->locale}.{$this->mailTemplate->blade_file}",
            with: $this->data,

        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
