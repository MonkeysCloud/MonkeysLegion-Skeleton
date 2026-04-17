<?php
declare(strict_types=1);

namespace Tests\Unit\Enum;

use App\Enum\OrderStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(OrderStatus::class)]
final class OrderStatusTest extends TestCase
{
    #[Test]
    public function allCasesHaveUniqueValues(): void
    {
        $values = array_map(fn(OrderStatus $s) => $s->value, OrderStatus::cases());

        $this->assertCount(6, $values);
        $this->assertCount(6, array_unique($values));
    }

    #[Test]
    #[DataProvider('finalStatusProvider')]
    public function isFinalReturnsTrueForTerminalStatuses(OrderStatus $status, bool $expected): void
    {
        $this->assertSame($expected, $status->isFinal());
    }

    /**
     * @return array<string, array{OrderStatus, bool}>
     */
    public static function finalStatusProvider(): array
    {
        return [
            'pending'    => [OrderStatus::Pending, false],
            'confirmed'  => [OrderStatus::Confirmed, false],
            'processing' => [OrderStatus::Processing, false],
            'shipped'    => [OrderStatus::Shipped, false],
            'delivered'  => [OrderStatus::Delivered, true],
            'cancelled'  => [OrderStatus::Cancelled, true],
        ];
    }

    #[Test]
    public function labelReturnsHumanReadableString(): void
    {
        $this->assertSame('In Transit', OrderStatus::Shipped->label());
        $this->assertSame('Pending Review', OrderStatus::Pending->label());
    }

    #[Test]
    public function colorReturnsHexCode(): void
    {
        $color = OrderStatus::Delivered->color();

        $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/', $color);
    }

    #[Test]
    public function activeExcludesFinalStatuses(): void
    {
        $active = OrderStatus::active();

        foreach ($active as $status) {
            $this->assertFalse($status->isFinal());
        }

        $this->assertCount(4, $active);
    }
}
