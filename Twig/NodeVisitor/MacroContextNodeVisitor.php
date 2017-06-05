<?php

namespace MewesK\TwigSpreadsheetBundle\Twig\NodeVisitor;

use MewesK\TwigSpreadsheetBundle\Wrapper\PhpSpreadsheetWrapper;

/**
 * Class MacroContextNodeVisitor.
 */
class MacroContextNodeVisitor extends \Twig_BaseNodeVisitor
{
    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(\Twig_Node $node, \Twig_Environment $env)
    {
        // Add main spreadsheet wrapper as argument on method/macro calls
        if ($node instanceof \Twig_Node_Expression_MethodCall) {
            /**
             * @var \Twig_Node_Expression_Array $argumentsNode
             */
            $argumentsNode = $node->getNode('arguments');
            $argumentsNode->addElement(
                new \Twig_Node_Expression_Name(PhpSpreadsheetWrapper::INSTANCE_KEY, $node->getTemplateLine()),
                new \Twig_Node_Expression_Constant(PhpSpreadsheetWrapper::INSTANCE_KEY, $node->getTemplateLine())
            );
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(\Twig_Node $node, \Twig_Environment $env)
    {
        return $node;
    }
}
