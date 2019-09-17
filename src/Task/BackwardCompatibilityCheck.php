<?php

namespace GrumPHP\Task;

use GrumPHP\Process\ProcessBuilder;
use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BackwardCompatibilityCheck extends AbstractExternalTask
{
    public function getName(): string
    {
        return 'BackwardCompatibilityCheck';
    }

    public function getConfigurableOptions(): OptionsResolver
    {
        $options = new OptionsResolver();
        $options->setDefault('report_file', 'report.md');
        $options->setDefault('format', 'markdown');

        $options->setAllowedTypes('report_file', 'string');
        $options->setAllowedTypes('format', 'string');

        $options->setAllowedValues('format', ['markdown', 'text']);

        return $options;
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return $context instanceof GitPreCommitContext || $context instanceof RunContext;
    }

    public function run(ContextInterface $context): TaskResultInterface
    {

        $files = $context->getFiles()->name('*.php');
        if (0 === \count($files)) {
            return TaskResult::createSkipped($this, $context);
        }

        $config = $this->getConfiguration();

        $arguments = $this->processBuilder->createArgumentsForCommand('roave-backward-compatibility-check');
        $arguments->addOptionalArgument('--format=%s', $config['format']);
        $arguments->addOptionalArgument('> %s', $config['report_file']);

        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        if (!$process->isSuccessful()) {
            return TaskResult::createFailed($this, $context, $this->formatter->format($process));
        }

        return TaskResult::createPassed($this, $context);
    }
}
