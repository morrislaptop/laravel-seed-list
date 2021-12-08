<?php

namespace Morrislaptop\LaravelSeedList;

use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use function Termwind\{render,renderUsing};
use Illuminate\Database\Seeder;
use Symfony\Component\Finder\Finder;
use PhpParser\NodeVisitor\NameResolver;
use hanneskod\classtools\Iterator\ClassIterator;

class LaravelSeedLister extends Seeder
{
    public function run()
    {
        $seeders = collect($this->getSeeders())
            ->sort()
            ->values()
            ->toArray();

        renderUsing($this->command->getOutput());

        $html = view('seed-list::list', [
            'seeders' => $seeders
        ]);

        /**
         * Termwind seems to add spaces randomly, but we can't add those
         * spaces to our test file as trailing spaces are removed by
         * the editor. Luckily we're only printing class names
         * so let's just get rid of all WS from the html.
         */
        $html = preg_replace('/\s+/', '', $html);

        render($html);

        $chosen = $this->choose($seeders);

        foreach ($chosen as $seeder) {
            $this->call($seeder);
        }
    }

    protected function choose($seeders)
    {
        $chosen = $this->command->ask(
            question: 'Which seeder(s) would you like to run? Separate multiple choices with a comma',
        );

        $split = array_map('trim', explode(',', $chosen));

        $range = range(1, count($seeders));

        $notInRange = array_diff($split, $range);

        if (count($notInRange) > 0) {
            $this->command->error(
                'The following choices are not valid: '.implode(', ', $notInRange)
            );

            return $this->choose($seeders);
        }

        $classes = array_map(fn ($i) => $seeders[$i - 1]['name'], $split);

        return $classes;
    }

    /**
     * @return array{name: string, children: array}
     */
    protected function getSeeders()
    {
        $finder = new Finder;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $classIterator = new ClassIterator($finder->in(config('seed-list.seeders_path')));
        $classMap = collect($classIterator->not($classIterator->type(LaravelSeedLister::class))->getClassMap());

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

    /**
     * @return array{name: string, children: array}
     */
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
