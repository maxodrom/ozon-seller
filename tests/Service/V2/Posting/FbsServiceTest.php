<?php declare(strict_types=1);

namespace Gam6itko\OzonSeller\Tests\Service\V2\Posting;

use Gam6itko\OzonSeller\Enum\SortDirection;
use Gam6itko\OzonSeller\Enum\Status;
use Gam6itko\OzonSeller\Service\V2\Posting\FbsService;
use Gam6itko\OzonSeller\Tests\Service\AbstractTestCase;

class FbsServiceTest extends AbstractTestCase
{
    protected function getClass(): string
    {
        return FbsService::class;
    }

    public function testList(): void
    {
        $this->quickTest(
            'list',
            [
                SortDirection::ASC,
                0,
                10,
                [
                    'since'  => new \DateTime('2018-11-18T11:27:45.154Z'),
                    'to'     => new \DateTime('2019-11-18T11:27:45.154Z'),
                    'status' => Status::AWAITING_APPROVE,
                ],
            ],
            [
                'POST',
                '/v2/posting/fbs/list',
                ['body' => '{"filter":{"since":"2018-11-18T11:27:45+00:00","to":"2019-11-18T11:27:45+00:00","status":"awaiting_approve"},"dir":"asc","offset":0,"limit":10}'],
            ]
        );
    }

    public function testGet(): void
    {
        $this->quickTest(
            'get',
            ['39268230-0002-3'],
            [
                'POST',
                '/v2/posting/fbs/get',
                ['body' => '{"posting_number":"39268230-0002-3"}'],
            ]
        );
    }

    public function testUnfulfilledList(): void
    {
        $this->quickTest(
            'unfulfilledList',
            [
                Status::AWAITING_APPROVE,
                SortDirection::ASC,
                0,
                10,
                ['barcodes'],
            ],
            [
                'POST',
                '/v2/posting/fbs/unfulfilled/list',
                ['body' => '{"status":["awaiting_approve"],"dir":"asc","offset":0,"limit":10,"with":["barcodes"]}'],
            ]
        );
    }

    public function testShip(): void
    {
        $packages = [
            ['items' => [['quantity' => 3, 'sku' => 123065]]],
        ];
        $this->quickTest(
            'ship',
            [$packages, '13076543-0001-1'],
            [
                'POST',
                '/v2/posting/fbs/ship',
                ['body' => '{"packages":[{"items":[{"quantity":3,"sku":123065}]}],"posting_number":"13076543-0001-1"}'],
            ]
        );
    }

    public function testActCreate(): void
    {
        $this->quickTest(
            'actCreate',
            [],
            [
                'POST',
                '/v2/posting/fbs/act/create',
                ['body' => '{}'],
            ],
            '{"result": { "id": 15684442104000 }}',
            static function ($result) {
                self::assertSame(15684442104000, $result);
            }
        );
    }

    public function testActCheckStatus(): void
    {
        $this->quickTest(
            'actCheckStatus',
            [15684442104000],
            [
                'POST',
                '/v2/posting/fbs/act/check-status',
                ['body' => '{"id":15684442104000}'],
            ]
        );
    }

    public function testActGetPdf(): void
    {
        $this->quickTest(
            'actGetPdf',
            [15684442104000],
            [
                'POST',
                '/v2/posting/fbs/act/get-pdf',
                ['body' => '{"id":15684442104000}'],
            ],
            'pdf_content',
            static function ($string) {
                self::assertSame('pdf_content', $string);
            }
        );
    }

    public function testPackageLabel(): void
    {
        $this->quickTest(
            'packageLabel',
            ['13076543-0001-1'],
            [
                'POST',
                '/v2/posting/fbs/package-label',
                ['body' => '{"posting_number":["13076543-0001-1"]}'],
            ],
            'pdf_content',
            static function ($string) {
                self::assertSame('pdf_content', $string);
            }
        );
    }

    public function testArbitration(): void
    {
        $this->quickTest(
            'arbitration',
            ['13076543-0001-1'],
            [
                'POST',
                '/v2/posting/fbs/arbitration',
                ['body' => '{"posting_number":["13076543-0001-1"]}'],
            ],
            '{"result":"true"}',
            static function ($result) {
                self::assertTrue($result);
            }
        );
    }

    public function testAwaitingDelivery(): void
    {
        $this->quickTest(
            'awaitingDelivery',
            [
                '13076543-0001-1',
            ],
            [
                'POST',
                '/v2/posting/fbs/awaiting-delivery',
                ['body' => '{"posting_number":["13076543-0001-1"]}'],
            ]
        );

        $this->quickTest(
            'awaitingDelivery',
            [
                ['13076543-0001-1', '02898753-0009-2'],
            ],
            [
                'POST',
                '/v2/posting/fbs/awaiting-delivery',
                ['body' => '{"posting_number":["13076543-0001-1","02898753-0009-2"]}'],
            ]
        );
    }

    public function testCancel(): void
    {
        $this->quickTest(
            'cancel',
            [
                '39268230-0002-3',
                361,
                'Cancel it!',
            ],
            [
                'POST',
                '/v2/posting/fbs/cancel',
                ['body' => '{"posting_number":"39268230-0002-3","cancel_reason_id":361,"cancel_reason_message":"Cancel it!"}'],
            ],
            '{"result": "true"}',
            static function ($result): void {
                self::assertTrue($result);
            }
        );
    }

    public function testCancelReasons(): void
    {
        $this->quickTest(
            'cancelReasons',
            [],
            [
                'POST',
                '/v2/posting/fbs/cancel-reason/list',
                ['body' => '{}'],
            ]
        );
    }
}