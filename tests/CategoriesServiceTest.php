<?php

use Gam6itko\OzonSeller\Service\CategoriesService;

/**
 * @covers CategoriesService
 */
class CategoriesServiceTest extends \PHPUnit\Framework\TestCase
{
    /** @var CategoriesService */
    private static $svc;

    public static function setUpBeforeClass()
    {
        self::$svc = new CategoriesService($_SERVER['CLIENT_ID'], $_SERVER['API_KEY'], 'http://cb-api.test.ozon.ru/');
    }

    /**
     * @expectedException \Gam6itko\OzonSeller\Exception\NotFoundException
     */
    public function testTreeException()
    {
        $res = self::$svc->tree(1);
        self::assertNotEmpty($res);
    }

    public function testTreeRoot()
    {
        $res = self::$svc->tree();
        self::assertNotEmpty($res);
        self::assertCount(19, $res);
    }

    /**
     * @dataProvider dataTree
     * @param int $id
     * @param string $title
     */
    public function testTree(int $id, string $title)
    {
        $res = self::$svc->tree($id);
        self::assertNotEmpty($res);
        self::assertCount(1, $res);
        $cat = $res[0];
        self::assertEquals($title, $cat['title']);
    }

    public function dataTree()
    {
        return [
            [41777480, 'Куртка'],
            [17036379, 'Коврик туристический'],
        ];
    }

    /**
     * @dataProvider dataAttributes
     * @param int $id
     */
    public function testAttributes(int $id)
    {
        $res = self::$svc->attributes($id);
        self::assertNotEmpty($res);
    }

    public function dataAttributes()
    {
        return [
            [17029835],
        ];
    }
}