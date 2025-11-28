<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class UnlayerEditor extends Field
{
    protected string $view = 'filament.forms.components.unlayer-editor';

    protected ?string $projectId    = null;
    protected ?string $apiKey       = null; // NEW
    protected string $minHeight     = '70svh';
    protected array $options        = [];
    protected array $tools          = [];
    protected string $displayMode   = 'email';
    protected ?string $locale       = null;
    protected bool $stockImages     = false; // NEW
    protected bool $userUploads     = true; // NEW

    public function projectId(?string $projectId): static
    {
        $this->projectId = $projectId;
        return $this;
    }

    public function getProjectId(): ?string
    {
        return $this->projectId ?? config('services.unlayer.project_id');
    }

    // NEW: API Key
    public function apiKey(?string $apiKey): static
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey ?? config('services.unlayer.api_key');
    }

    public function minHeight(string $height): static
    {
        $this->minHeight = $height;
        return $this;
    }

    public function getMinHeight(): string
    {
        return $this->minHeight;
    }

    // NEW: Enable stock images (requires project ID)
    public function stockImages(bool $enabled = true): static
    {
        $this->stockImages = $enabled;
        return $this;
    }

    public function getStockImages(): bool
    {
        return $this->stockImages;
    }

    // NEW: Enable user uploads
    public function userUploads(bool $enabled = true): static
    {
        $this->userUploads = $enabled;
        return $this;
    }

    public function getUserUploads(): bool
    {
        return $this->userUploads;
    }

    public function displayMode(string $mode): static
    {
        $this->displayMode = $mode;
        return $this;
    }

    public function getDisplayMode(): string
    {
        return $this->displayMode;
    }

    public function options(array $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions(): array
    {
        // Merge feature flags into options
        $defaultOptions = [
            'features' => [
                'stockImages' => $this->stockImages && $this->getProjectId(), // Only if project ID exists
                'userUploads' => $this->userUploads,
            ]
        ];

        return array_merge_recursive($defaultOptions, $this->options);
    }

    public function tools(array $tools): static
    {
        $this->tools = $tools;
        return $this;
    }

    public function getTools(): array
    {
        return $this->tools;
    }

    // NEW: Custom merge tags helper
    public function mergeTags(array $tags): static
    {
        $formattedTags = [];
        foreach ($tags as $key => $label) {
            $formattedTags[] = [
                'name' => $label,
                'value' => "{{" . $key . "}}",
                'sample' => $label
            ];
        }
        
        $this->options['mergeTags'] = $formattedTags;
        return $this;
    }
}