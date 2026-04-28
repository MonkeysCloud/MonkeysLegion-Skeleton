<?php
declare(strict_types=1);

namespace App\Enum;

/**
 * Order lifecycle status.
 */
enum OrderStatus: string
{
    case Pending    = 'pending';
    case Confirmed  = 'confirmed';
    case Processing = 'processing';
    case Shipped    = 'shipped';
    case Delivered  = 'delivered';
    case Cancelled  = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending    => 'Pending Review',
            self::Confirmed  => 'Confirmed',
            self::Processing => 'Processing',
            self::Shipped    => 'In Transit',
            self::Delivered  => 'Delivered',
            self::Cancelled  => 'Cancelled',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Delivered, self::Cancelled], true);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending    => '#f59e0b',
            self::Confirmed  => '#3b82f6',
            self::Processing => '#8b5cf6',
            self::Shipped    => '#06b6d4',
            self::Delivered  => '#10b981',
            self::Cancelled  => '#ef4444',
        };
    }

    /**
     * @return list<self>
     */
    public static function active(): array
    {
        return array_filter(
            self::cases(),
            fn(self $s) => !$s->isFinal(),
        );
    }
}
