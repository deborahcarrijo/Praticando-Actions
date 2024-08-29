<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer\Graph;

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Guides\Graphs\Renderer\PlantumlRenderer;
use phpDocumentor\Guides\RenderContext;
use Psr\Log\LoggerInterface;

use function addslashes;
use function file_put_contents;
use function implode;

use const PHP_EOL;

final class PlantumlClassDiagram implements Generator
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly PlantumlRenderer $plantumlRenderer,
    ) {
    }

    public function create(DocumentationSetDescriptor $documentationSet, string $filename): void
    {
        if ($documentationSet instanceof ApiSetDescriptor === false) {
            return;
        }

        $output = $this->plantumlRenderer->render(
            new class extends RenderContext{
                public function __construct()
                {
                }

                public function getLoggerInformation(): array
                {
                    return [];
                }
            },
            <<<PUML
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\\\

{$this->renderNamespace($documentationSet->getNamespace())}
PUML,
        );

        if (! $output) {
            $this->logger->error('Generating the class diagram failed');

            return;
        }

        file_put_contents($filename, $output);
    }

    private function renderNamespace(NamespaceInterface $namespace): string
    {
        $output = '';
        /** @var ClassDescriptor $class */
        foreach ($namespace->getClasses() as $class) {
            $abstract = $class->isAbstract() ? 'abstract ' : '';
            $className = addslashes((string) $class->getFullyQualifiedStructuralElementName());

            $extends = '';
            if ($class->getParent() !== null) {
                $parentFqsen = $class->getParent() instanceof ClassDescriptor
                    ? (string) $class->getParent()->getFullyQualifiedStructuralElementName()
                    : (string) $class->getParent();

                $extends = ' extends ' . addslashes($parentFqsen);
            }

            $implementsList = [];
            foreach ($class->getInterfaces() as $parent) {
                $parentFqsen = $parent instanceof InterfaceDescriptor
                    ? (string) $parent->getFullyQualifiedStructuralElementName()
                    : (string) $parent;

                $implementsList[] = addslashes($parentFqsen);
            }

            if ($implementsList !== []) {
                $implements = ' implements ' . implode(',', $implementsList);
            } else {
                $implements = '';
            }

            foreach ($class->getUsedTraits() as $parent) {
                $parentFqsen = $parent instanceof TraitDescriptor
                    ? (string) $parent->getFullyQualifiedStructuralElementName()
                    : (string) $parent;

                $output .= addslashes($parentFqsen) . ' <-- ' . $className . ' : uses' . PHP_EOL;
            }

            $output .= <<<PUML

{$abstract}class {$className}{$extends}{$implements} {
}

PUML;
        }

        /** @var InterfaceDescriptor $interface */
        foreach ($namespace->getInterfaces() as $interface) {
            $interfaceName = addslashes((string) $interface->getFullyQualifiedStructuralElementName());

            $implementsList = [];
            foreach ($interface->getParent() as $parent) {
                $parentFqsen = $parent instanceof InterfaceDescriptor
                    ? (string) $parent->getFullyQualifiedStructuralElementName()
                    : (string) $parent;

                $implementsList[] = addslashes($parentFqsen);
            }

            if ($implementsList !== []) {
                $implements = ' extends ' . implode(',', $implementsList);
            } else {
                $implements = '';
            }

            $output .= <<<PUML

interface {$interfaceName}{$implements} {
}

PUML;
        }

        /** @var TraitDescriptor $class */
        foreach ($namespace->getTraits() as $class) {
            $className = addslashes((string) $class->getFullyQualifiedStructuralElementName());

            foreach ($class->getUsedTraits() as $parent) {
                $parentFqsen = $parent instanceof TraitDescriptor
                    ? (string) $parent->getFullyQualifiedStructuralElementName()
                    : (string) $parent;

                $output .= addslashes($parentFqsen) . ' <-- ' . $className . ' : uses' . PHP_EOL;
            }

            $output .= <<<PUML

class {$className} << (T,#FF7700) Trait >> {
}

PUML;
        }

        foreach ($namespace->getChildren() as $child) {
            $output .= $this->renderNamespace($child);
        }

        return $output;
    }
}
