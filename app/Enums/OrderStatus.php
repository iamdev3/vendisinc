<?php

namespace App\Enums;

enum OrderStatus: string
{
    // case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    // case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case ON_HOLD = 'on_hold';

    public function label(): string
    {
        return match ($this) {
            // // self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            // // self::PROCESSING => 'Processing',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
            self::ON_HOLD => 'On Hold',
        };
    }

    public function color(): string
    {
        return match ($this) {
            // self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            // self::PROCESSING => 'primary',
            self::SHIPPED => 'success',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
            self::REFUNDED => 'gray',
            self::ON_HOLD => 'warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            // self::PENDING => 'heroicon-o-clock',
            self::CONFIRMED => 'heroicon-o-check-circle',
            // self::PROCESSING => 'heroicon-o-cog',
            self::SHIPPED => 'heroicon-o-truck',
            self::DELIVERED => 'heroicon-o-check-badge',
            self::CANCELLED => 'heroicon-o-x-circle',
            self::REFUNDED => 'heroicon-o-arrow-uturn-left',
            self::ON_HOLD => 'heroicon-o-pause-circle',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
