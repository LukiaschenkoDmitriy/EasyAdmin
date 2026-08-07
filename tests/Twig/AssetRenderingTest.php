<?php declare(strict_types=1);

namespace EAdmin\Core\Tests\Twig;

use EAdmin\Core\Tests\Fixtures\TestKernel;
use EAdmin\Core\Tests\Page\MainPage;
use EAdmin\Core\Utils\TidyUtil;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

class AssetRenderingTest extends KernelTestCase {
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testDashboardComponentRenders(): void
    {
        self::bootKernel();

        $environment = static::getContainer()->get(Environment::class);

        $page = new MainPage();

        $html = TidyUtil::cleanAndRepairHTML($environment->render($page->template(), $page->allContext()));

        dump($html);

        // self::assertStringContainsString('<div', $html);
    }
}