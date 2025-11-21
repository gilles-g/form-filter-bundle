<?php

/*
 * This file is part of the composer-write-changelogs project.
 *
 * (c) Dev Spiriit <dev@spiriit.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\FormFilterBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'spiriit:form-filter:generate',
    description: 'Generate a form filter type from a Doctrine entity'
)]
class GenerateFormFilterCommand extends Command
{
    private const TYPE_MAPPING = [
        'string' => 'TextFilterType',
        'text' => 'TextFilterType',
        'integer' => 'NumberFilterType',
        'int' => 'NumberFilterType',
        'smallint' => 'NumberFilterType',
        'bigint' => 'NumberFilterType',
        'decimal' => 'NumberFilterType',
        'float' => 'NumberFilterType',
        'boolean' => 'BooleanFilterType',
        'bool' => 'BooleanFilterType',
        'date' => 'DateFilterType',
        'datetime' => 'DateTimeFilterType',
        'datetimetz' => 'DateTimeFilterType',
        'time' => 'DateTimeFilterType',
    ];

    private Filesystem $filesystem;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('entity', InputArgument::REQUIRED, 'The fully qualified entity class name (e.g., App\\Entity\\User)')
            ->addOption('output-dir', 'o', InputOption::VALUE_OPTIONAL, 'Output directory for the generated filter type')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command generates a form filter type from a Doctrine entity.

Usage:
  <info>php %command.full_name% App\Entity\User</info>

This will generate a filter type class in the appropriate namespace.

You can specify a custom output directory:
  <info>php %command.full_name% App\Entity\User --output-dir=src/Form/Filter</info>
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $entityClass = $input->getArgument('entity');

        // Validate entity class
        if (!class_exists($entityClass)) {
            $io->error(sprintf('Entity class "%s" does not exist.', $entityClass));
            return Command::FAILURE;
        }

        try {
            $metadata = $this->entityManager->getClassMetadata($entityClass);
        } catch (\Exception $e) {
            $io->error(sprintf('Could not load metadata for entity "%s": %s', $entityClass, $e->getMessage()));
            return Command::FAILURE;
        }

        $filterTypeCode = $this->generateFilterType($metadata, $entityClass);
        
        // Determine output path
        $outputDir = $input->getOption('output-dir');
        $filterTypeClass = $this->getFilterTypeClassName($entityClass);
        $outputPath = $this->getOutputPath($entityClass, $outputDir);

        // Create directory if it doesn't exist
        $directory = dirname($outputPath);
        if (!$this->filesystem->exists($directory)) {
            $this->filesystem->mkdir($directory);
        }

        // Write the file
        if ($this->filesystem->exists($outputPath)) {
            $io->warning(sprintf('File "%s" already exists.', $outputPath));
            if (!$io->confirm('Do you want to overwrite it?', false)) {
                $io->note('Generation cancelled.');
                return Command::SUCCESS;
            }
        }

        $this->filesystem->dumpFile($outputPath, $filterTypeCode);

        $io->success([
            sprintf('Filter type generated successfully at: %s', $outputPath),
            sprintf('Filter type class: %s', $filterTypeClass)
        ]);

        return Command::SUCCESS;
    }

    private function generateFilterType(ClassMetadata $metadata, string $entityClass): string
    {
        $entityShortName = $this->getShortClassName($entityClass);
        $filterTypeClass = $entityShortName . 'FilterType';
        $filterTypeNamespace = $this->getFilterTypeNamespace($entityClass);

        $fields = [];
        $importsMap = [
            'Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType' => true,
            'Symfony\Component\Form\AbstractType' => true,
            'Symfony\Component\Form\FormBuilderInterface' => true,
        ];

        // Pre-compute identifier fields for faster lookup
        $identifierFields = array_flip($metadata->getIdentifierFieldNames());

        // Process fields
        foreach ($metadata->fieldMappings as $fieldName => $fieldMapping) {
            // Skip id fields
            if (isset($identifierFields[$fieldName])) {
                continue;
            }

            $type = $fieldMapping['type'];
            $filterType = $this->getFilterTypeForDoctrineType($type);
            
            if ($filterType !== 'TextFilterType') {
                $importsMap['Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\\' . $filterType] = true;
            }

            $fields[$fieldName] = $filterType;
        }

        // Process associations
        foreach ($metadata->associationMappings as $fieldName => $associationMapping) {
            if ($associationMapping['type'] === ClassMetadata::MANY_TO_ONE || 
                $associationMapping['type'] === ClassMetadata::ONE_TO_ONE) {
                $entityFilterType = 'Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\EntityFilterType';
                $targetEntity = $associationMapping['targetEntity'];
                
                $importsMap[$entityFilterType] = true;
                $importsMap[$targetEntity] = true;
                
                $fields[$fieldName] = [
                    'type' => 'EntityFilterType',
                    'targetEntity' => $targetEntity
                ];
            }
        }

        $imports = array_keys($importsMap);
        sort($imports);

        return $this->renderTemplate($filterTypeNamespace, $filterTypeClass, $fields, $imports);
    }

    private function renderTemplate(string $namespace, string $className, array $fields, array $imports): string
    {
        $templatePath = __DIR__ . '/../Resources/skeleton/FormFilterType.tpl.php';
        
        if (!file_exists($templatePath)) {
            throw new \RuntimeException(sprintf('Template file not found: %s', $templatePath));
        }
        
        // Prepare fields with target entity short names for template
        $preparedFields = [];
        foreach ($fields as $fieldName => $fieldInfo) {
            if (is_array($fieldInfo)) {
                $preparedFields[$fieldName] = [
                    'type' => $fieldInfo['type'],
                    'target_entity_short' => $this->getShortClassName($fieldInfo['targetEntity'])
                ];
            } else {
                $preparedFields[$fieldName] = $fieldInfo;
            }
        }
        
        // Extract variables for template
        $class_name = $className;
        $fields = $preparedFields;
        
        ob_start();
        require $templatePath;
        return ob_get_clean();
    }

    private function getFilterTypeForDoctrineType(string $doctrineType): string
    {
        return self::TYPE_MAPPING[$doctrineType] ?? 'TextFilterType';
    }

    private function getShortClassName(string $fqcn): string
    {
        $parts = explode('\\', $fqcn);
        return end($parts);
    }

    private function getFilterTypeClassName(string $entityClass): string
    {
        $shortName = $this->getShortClassName($entityClass);
        return $this->getFilterTypeNamespace($entityClass) . '\\' . $shortName . 'FilterType';
    }

    private function getFilterTypeNamespace(string $entityClass): string
    {
        // Replace \Entity\ with \Form\Filter\
        $namespace = substr($entityClass, 0, strrpos($entityClass, '\\'));
        $namespace = str_replace('\\Entity', '\\Form\\Filter', $namespace);
        
        return $namespace;
    }

    private function getOutputPath(string $entityClass, ?string $outputDir): string
    {
        $shortName = $this->getShortClassName($entityClass);
        $filterTypeClass = $shortName . 'FilterType';

        if ($outputDir) {
            return rtrim($outputDir, '/') . '/' . $filterTypeClass . '.php';
        }

        // Try to determine the path from the entity class
        $reflectionClass = new ReflectionClass($entityClass);
        $entityPath = $reflectionClass->getFileName();

        if ($entityPath) {
            // Replace Entity directory with Form/Filter
            $path = str_replace('/Entity/', '/Form/Filter/', $entityPath);
            $path = str_replace($shortName . '.php', $filterTypeClass . '.php', $path);
            return $path;
        }

        // Fallback to current directory
        return getcwd() . '/' . $filterTypeClass . '.php';
    }
}
