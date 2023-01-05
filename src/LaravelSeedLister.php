<?php

namespace Morrislaptop\LaravelSeedList;

use Illuminate\Database\Seeder;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;
use Uzbek\ClassTools\Iterator\ClassIterator;

class LaravelSeedLister extends Seeder
{
    public function run()
    {
        $seeders = $this->getSeeders();

        $chosen = $this->choose($seeders);

        foreach ($chosen as $seeder) {
            $this->call(config('seed-list.seeders_namespace') . $seeder);
        }
    }

    /**
     * Sorts and formats the list of seeders and returns an array
     * of seeders to run
     *
     * @return string[]
     */
    protected function choose($seeders)
    {
        $choices = $seeders->sortBy('name')->mapWithKeys(function ($seeder) {
            $willRun = $seeder['children']->prepend($seeder['name']);

            return [
                $seeder['name'] => $willRun->implode(', '),
            ];
        });

        $chosen = $this->command->choice(
            'Which seeder would you like to run?',
            $choices->toArray(),
            multiple: true,
        );

        return $chosen;
    }

    /**
     * @return array{name: string, children: array}
     */
    protected function getSeeders()
    {
        $finder = new Finder();
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $classIterator = new ClassIterator($finder->in(config('seed-list.seeders_path')));
        $classMap = collect($classIterator->not($classIterator->type(LaravelSeedLister::class))->getClassMap());

        $fqAsts = $classMap->map(function ($splFileInfo) use ($parser) {
            $ast = $parser->parse($splFileInfo->getContents());

            $nameTraverser = new NodeTraverser();
            $nameResolver = new NameResolver();
            $nameTraverser->addVisitor($nameResolver);

            return $nameTraverser->traverse($ast);
        });

        $classes = $classMap->map(function ($splFileInfo, $className) use ($fqAsts) {
            return [
                'name' => class_basename($className),
                'children' => $this->getChildren($className, $fqAsts, collect()),
            ];
        });

        return $classes;
    }

    /**
     * @return array{name: string, children: array}
     */
    protected function getChildren($className, $fqAsts, $children)
    {
        $ast = $fqAsts[$className];

        $nodeFinder = new NodeFinder();
        $calls = $nodeFinder->find($ast, function (Node $node) {
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

                $children->add(class_basename($seederName));

                $this->getChildren($seederName, $fqAsts, $children);
            }
        }

        return $children;
    }
}
