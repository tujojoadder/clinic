<?php
namespace Tests\Unit;
use App\Services\AdmissionService;
use PHPUnit\Framework\TestCase;
class BedRentalTest extends TestCase
{
    public function test_minimum_and_partial_days(): void
    {
        $this->assertSame(1,AdmissionService::days('2026-10-10 10:00:00','2026-10-11 10:00:00'));
        $this->assertSame(2,AdmissionService::days('2026-10-10 10:00:00','2026-10-11 10:00:01'));
        $this->assertSame(1,AdmissionService::days('2026-10-10 10:00:00','2026-10-10 10:00:00'));
    }
    public function test_negative_interval_is_rejected(): void
    {
        $this->expectException(\DomainException::class);
        AdmissionService::days('2026-10-11 10:00:00','2026-10-10 10:00:00');
    }
}
