<?php

namespace MewesK\TwigSpreadsheetBundle\Tests\Twig;

use MewesK\TwigSpreadsheetBundle\Helper\Filesystem;
use MewesK\TwigSpreadsheetBundle\Twig\TwigSpreadsheetExtension;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/**
 * Class BaseTwigTest.
 */
abstract class BaseTwigTest extends TestCase
{
    public const CACHE_PATH = './../../var/cache';
    public const RESULT_PATH = './../../var/result';
    public const RESOURCE_PATH = './Fixtures/views';
    public const TEMPLATE_PATH = './Fixtures/templates';

    /**
     * @var Environment|null
     */
    protected static ?Environment $environment = null;

    /**
     * {@inheritdoc}
     *
     * @throws LoaderError
     */
    public static function setUpBeforeClass() : void
    {
        $cachePath = sprintf('%s/%s/%s', __DIR__, static::CACHE_PATH, str_replace('\\', DIRECTORY_SEPARATOR, static::class));

        // remove temp files
        Filesystem::remove($cachePath);
        Filesystem::remove(sprintf('%s/%s/%s', __DIR__, static::RESULT_PATH, str_replace('\\', DIRECTORY_SEPARATOR, static::class)));

        // set up Twig environment
        $twigFileSystem = new FilesystemLoader([sprintf('%s/%s', __DIR__, static::RESOURCE_PATH)]);
        $twigFileSystem->addPath(sprintf('%s/%s', __DIR__, static::TEMPLATE_PATH), 'templates');

        static::$environment ??= new Environment($twigFileSystem, ['debug' => true, 'strict_variables' => true]);
        static::$environment->addExtension(new TwigSpreadsheetExtension([
            'pre_calculate_formulas' => true,
            'cache' => [
                'bitmap' => $cachePath.'/spreadsheet/bitmap',
                'xml' => false
            ],
            'csv_writer' => [
                'delimiter' => ',',
                'enclosure' => '"',
                'excel_compatibility' => false,
                'include_separator_line' => false,
                'line_ending' => PHP_EOL,
                'sheet_index' => 0,
                'use_bom' => true
            ]
        ]));
        static::$environment->setCache($cachePath.'/twig');
    }

    /**
     * @param string $templateName
     * @param string $format
     *
     * @throws SyntaxError
     * @throws LoaderError
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     *
     * @throws RuntimeError
     * @return Spreadsheet|string
     */
    protected function getDocument($templateName, $format)
    {
        $format = strtolower($format);

        // prepare global variables
        $request = new Request();
        $request->setRequestFormat($format);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $appVariable = new AppVariable();
        $appVariable->setRequestStack($requestStack);

        // generate source from template
        $source = static::$environment->load($templateName.'.twig')->render(['app' => $appVariable]);

        // create path
        $resultPath = sprintf('%s/%s/%s/%s.%s', __DIR__, static::RESULT_PATH, str_replace('\\', DIRECTORY_SEPARATOR, static::class), $templateName, $format);

        // save source
        Filesystem::dumpFile($resultPath, $source);

        return $resultPath;
    }
}
