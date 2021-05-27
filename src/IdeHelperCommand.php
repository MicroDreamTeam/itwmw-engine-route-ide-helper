<?php

namespace Itwmw\Engine\Route\Ide\Helper;

use Composer\Autoload\ClassLoader;
use Ergebnis\Classy\Constructs;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class IdeHelperCommand extends Command
{
    protected function configure()
    {
        $this->setName('make:route-ide')->setDescription('生成路由URI提示')
            ->addArgument('name', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, '控制器完整命名空间', [])
            ->addOption('dir', 'd', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, '搜索目录', [])
            ->addOption('vendor', 'iv', InputOption::VALUE_OPTIONAL, '是否忽略Vendor目录，默认为true', true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $names = $input->getArgument('name');
        if (!empty($names)) {
            $_names = $names;
            $names  = [];
            foreach ($_names as $name) {
                if (file_exists($name)) {
                    $constructs = Constructs::fromSource(file_get_contents($name));
                    foreach ($constructs as $construct) {
                        $names[] = $construct->name();
                    }
                } elseif (class_exists($name)) {
                    $names[] = $name;
                } else {
                    throw new \RuntimeException('class：' . $name . ' does not exist');
                }
            }
        }
        $dirs         = $input->getOption('dir');
        $ignoreVendor = (bool)$input->getOption('vendor');
        $reflection   = new ReflectionClass(ClassLoader::class);
        $appDir       = dirname($reflection->getFileName(), 3);
        $dirs         = array_map(function ($dir) use ($appDir) {
            return $appDir . DIRECTORY_SEPARATOR . $dir;
        }, $dirs);

        if (!empty($dirs)) {
            $finder = new Finder();
            $finder->in($dirs);
            if ($ignoreVendor) {
                $finder->exclude('vendor');
            }
            $finder->name('*.php');
            foreach ($finder as $file) {
                $constructs = Constructs::fromSource($file->getContents());
                foreach ($constructs as $construct) {
                    $names[] = $construct->name();
                }
            }
        }

        if (empty($names)) {
            $output->writeln("\033[0;32m\nSuccess\033[0m");
            return 1;
        }
        $progress = new ProgressBar($output, count($names));
        $progress->setFormat('%current%/%max% [%bar%] %message%');
        $progress->setMessage('Being processed:' . $names[0]);
        $progress->start();

        foreach ($names as $name) {
            (new RouteUriGenerate($name))->generateCommon();
            $progress->setMessage($name . 'Processing completed');
            $progress->advance();
        }

        $progress->setMessage('All processed');
        $progress->finish();
        $output->writeln("\033[0;32m\nSuccess\033[0m");
        return 1;
    }
}
