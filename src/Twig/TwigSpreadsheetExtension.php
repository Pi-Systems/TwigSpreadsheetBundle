<?php

namespace MewesK\TwigSpreadsheetBundle\Twig;

use MewesK\TwigSpreadsheetBundle\Helper\Arrays;
use MewesK\TwigSpreadsheetBundle\Twig\NodeVisitor\MacroContextNodeVisitor;
use MewesK\TwigSpreadsheetBundle\Twig\NodeVisitor\SyntaxCheckNodeVisitor;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\AlignmentTokenParser;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\CellTokenParser;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\DocumentTokenParser;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\DrawingTokenParser;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\HeaderFooterTokenParser;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\RowTokenParser;
use MewesK\TwigSpreadsheetBundle\Twig\TokenParser\SheetTokenParser;
use MewesK\TwigSpreadsheetBundle\Wrapper\HeaderFooterWrapper;
use MewesK\TwigSpreadsheetBundle\Wrapper\PhpSpreadsheetWrapper;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TwigSpreadsheetExtension.
 */
class TwigSpreadsheetExtension extends AbstractExtension
{
    /**
     * @var array
     */
    private array $attributes;

    /**
     * TwigSpreadsheetExtension constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    : array
    {
        return [
            new TwigFunction('xlsmergestyles', [$this, 'mergeStyles']),
            new TwigFunction('xlscellindex', [$this, 'getCurrentColumn'], ['needs_context' => true]),
            new TwigFunction('xlsrowindex', [$this, 'getCurrentRow'], ['needs_context' => true]),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function getTokenParsers()
    : array
    {
        return [
            new AlignmentTokenParser([], HeaderFooterWrapper::ALIGNMENT_CENTER),
            new AlignmentTokenParser([], HeaderFooterWrapper::ALIGNMENT_LEFT),
            new AlignmentTokenParser([], HeaderFooterWrapper::ALIGNMENT_RIGHT),
            new CellTokenParser(),
            new DocumentTokenParser($this->attributes),
            new DrawingTokenParser(),
            new HeaderFooterTokenParser([], HeaderFooterWrapper::BASETYPE_FOOTER),
            new HeaderFooterTokenParser([], HeaderFooterWrapper::BASETYPE_HEADER),
            new RowTokenParser(),
            new SheetTokenParser(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    : array
    {
        return [
            new MacroContextNodeVisitor(),
            new SyntaxCheckNodeVisitor(),
        ];
    }

    /**
     * @param array $style1
     * @param array $style2
     *
     * @throws RuntimeError
     *
     * @return array
     */
    public function mergeStyles(array $style1, array $style2): array
    {
        if (!\is_array($style1) || !\is_array($style2)) {
            throw new RuntimeError('The xlsmergestyles function only works with arrays.');
        }
        return Arrays::mergeRecursive($style1, $style2);
    }

    /**
     * @param array $context
     *
     * @throws RuntimeError
     *
     * @return int|null
     */
    public function getCurrentColumn(array $context)
    : ?int
    {
        if (!isset($context[PhpSpreadsheetWrapper::INSTANCE_KEY])) {
            throw new RuntimeError('The PhpSpreadsheetWrapper instance is missing.');
        }
        return $context[PhpSpreadsheetWrapper::INSTANCE_KEY]->getCurrentColumn();
    }

    /**
     * @param array $context
     *
     * @throws RuntimeError
     *
     * @return int|null
     */
    public function getCurrentRow(array $context)
    : ?int
    {
        if (!isset($context[PhpSpreadsheetWrapper::INSTANCE_KEY])) {
            throw new RuntimeError('The PhpSpreadsheetWrapper instance is missing.');
        }
        return $context[PhpSpreadsheetWrapper::INSTANCE_KEY]->getCurrentRow();
    }
}
