<?php

namespace Morrislaptop\LaravelSeedList;

use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use function Termwind\{render};
use Illuminate\Database\Seeder;
use PhpParser\NodeVisitorAbstract;
use Symfony\Component\Finder\Finder;
use PBergman\Console\Helper\TreeHelper;
use PhpParser\NodeVisitor\NameResolver;
use hanneskod\classtools\Iterator\ClassIterator;

class LaravelSeedLister extends Seeder
{
    public function run()
    {
        $seeders = collect($this->getSeeders())
            // ->map(fn ($c) => $c->getShortName())
            // ->filter(fn ($c) => $c !== 'DatabaseSeeder')
            ->sort()
            ->values()
            ->toArray();

        render(
            view('seed-list::list', [
                'seeders' => $seeders
            ])
        );

        $chosen = $this->command->ask(
            question: 'Which seeder(s) would you like to run? Separate multiple choices with a space.',
        );

        foreach ($chosen as $seeder) {
            $this->call('Database\\Seeders\\'.$seeder);
        }
    }

    protected function getSeeders()
    {
        $finder = new Finder;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $classIterator = new ClassIterator($finder->in(database_path('seeders')));
        $classMap = collect($classIterator->getClassMap());

        $fqAsts = $classMap->map(function ($splFileInfo) use ($parser) {
            $ast = $parser->parse($splFileInfo->getContents());

            $nameTraverser = new NodeTraverser;
            $nameResolver = new NameResolver;
            $nameTraverser->addVisitor($nameResolver);

            return $nameTraverser->traverse($ast);
        });

        $classes = $classMap->map(function ($splFileInfo, $className) use ($fqAsts) {
            return [
                'name' => $className,
                'children' => $this->getChildren($className, $fqAsts),
            ];
        });

        return $classes;
    }

    protected function getChildren($className, $fqAsts)
    {
        $ast = $fqAsts[$className];

        $children = [];

        $nodeFinder = new NodeFinder;
        $calls = $nodeFinder->find($ast, function(Node $node) {
            return $node instanceof Node\Stmt\Expression
            && $node->expr instanceof Node\Expr\MethodCall
            && $node->expr->name instanceof Node\Identifier
            && $node->expr->name->toString() === 'call';
        });

        foreach ($calls as $node) {
            $class = $node->expr->args[0]->value;

            $seeders = $class instanceof Node\Expr\Array_
                ? array_map(fn ($item) => $item->value, $class->items)
                : [$class];

            foreach ($seeders as $seeder) {
                $seederName = $seeder->class->toString();

                $children[] = [
                    'name' => $seederName,
                    'children' => $this->getChildren($seederName, $fqAsts),
                ];
            }
        }

        return $children;
    }
}
