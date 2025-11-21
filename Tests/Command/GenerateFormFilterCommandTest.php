<?php

/*
 * This file is part of the composer-write-changelogs project.
 *
 * (c) Dev Spiriit <dev@spiriit.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\FormFilterBundle\Tests\Command;

use Spiriit\Bundle\FormFilterBundle\Command\GenerateFormFilterCommand;
use Spiriit\Bundle\FormFilterBundle\Tests\Fixtures\Entity\Item;
use Spiriit\Bundle\FormFilterBundle\Tests\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateFormFilterCommandTest extends TestCase
{
    private $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/spiriit_form_filter_test_' . uniqid();
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $this->removeDirectory($this->tempDir);
        }
    }

    public function testGenerateFormFilterFromEntity()
    {
        $entityManager = $this->getSqliteEntityManager();
        $command = new GenerateFormFilterCommand($entityManager);
        
        $application = new Application();
        $application->add($command);
        
        $command = $application->find('spiriit:form-filter:generate');
        $commandTester = new CommandTester($command);
        
        $commandTester->execute([
            'entity' => Item::class,
            '--output-dir' => $this->tempDir,
        ]);
        
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Filter type generated successfully', $output);
        
        $generatedFile = $this->tempDir . '/ItemFilterType.php';
        $this->assertFileExists($generatedFile);
        
        $content = file_get_contents($generatedFile);
        $this->assertStringContainsString('class ItemFilterType extends AbstractType', $content);
        $this->assertStringContainsString('use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType', $content);
        $this->assertStringContainsString('use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\NumberFilterType', $content);
        $this->assertStringContainsString('use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\BooleanFilterType', $content);
        $this->assertStringContainsString('use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type\DateTimeFilterType', $content);
        $this->assertStringContainsString('$builder->add(\'name\', TextFilterType::class)', $content);
        $this->assertStringContainsString('$builder->add(\'position\', NumberFilterType::class)', $content);
        $this->assertStringContainsString('$builder->add(\'enabled\', BooleanFilterType::class)', $content);
    }

    public function testGenerateFormFilterWithNonExistentEntity()
    {
        $entityManager = $this->getSqliteEntityManager();
        $command = new GenerateFormFilterCommand($entityManager);
        
        $application = new Application();
        $application->add($command);
        
        $command = $application->find('spiriit:form-filter:generate');
        $commandTester = new CommandTester($command);
        
        $commandTester->execute([
            'entity' => 'NonExistent\\Entity\\Class',
            '--output-dir' => $this->tempDir,
        ]);
        
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('does not exist', $output);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    private function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
