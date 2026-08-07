<?php declare(strict_types=1);

namespace EAdmin\Core\Utils;

class TidyUtil {
    private static array $config = [
        'indent'         => true,
        'indent-spaces'  => 4,
        'wrap'           => 0,
        'output-xhtml'   => false,
        'show-body-only' => false,
    ];

    public static function cleanAndRepairHTML(string $html): string {
        $tidy = new \tidy;

        $tidy->parseString($html, TidyUtil::$config, "utf8");
        $tidy->cleanRepair();

        return (string) $tidy;
    }
}