<?php
function main()
{
    $root = simplexml_load_file('php://stdin');
    $all = parse($root);

    $cwd = realpath(getcwd());
    list($cwd, $all) = normalize($cwd, $all);

    $content = generate($cwd, $all);
    echo $content;
}

function parse(SimpleXMLElement $root)
{
    $all = [];
    foreach ($root as $node) {
        if ($node->getName() === 'file') {
            $ret = parse_file($node);
        } else {
            $ret = parse($node);
        }
        $all = array_replace_recursive($all, $ret);
    }
    return $all;
}

function parse_file(SimpleXMLElement $file)
{
    $filename = (string)$file['name'];
    if (strlen($filename) === 0) {
        return [];
    }

    $ret = [];
    $classname = '';
    $namespace = '';

    foreach ($file as $node) {
        if ($node->getName() === 'class') {
            $classname = (string)$node['name'];
            $namespace = (string)$node['namespace'];
        } elseif ($node->getName() === 'line') {
            $num = (string)$node['num'];
            $type = (string)$node['type'];
            $count = (string)$node['count'];

            if (!ctype_digit($num)) {
                continue;
            }

            if ($type !== 'stmt') {
                continue;
            }

            if (!ctype_digit($count)) {
                continue;
            }

            $ret[$namespace][$classname][$filename][$num] = $count;
        }
    }

    return $ret;
}

function normalize(string $cwd, array $all)
{
    $ret = [];
    $pre = $cwd . DIRECTORY_SEPARATOR;

    foreach ($all as $namespace => $classes) {
        foreach ($classes as $class => $files) {
            foreach ($files as $filename => $lines) {
                if (substr($filename, 0, strlen($pre)) === $pre) {
                    $filename = substr($filename, strlen($pre));
                    $ret[$namespace][$class][$filename] = $lines;
                } else {
                    return ['/', $all];
                }
            }
        }
    }

    return [$cwd, $ret];
}

function generate(string $cwd, array $all)
{
    ob_start();
    render($cwd, $all);
    $content = ob_get_clean();

    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($content);
    return $dom->saveXML();
}

function render(string $cwd, array $all)
{
    echo'<?xml version="1.0" encoding="UTF-8"?>' ?>
        <coverage>
            <sources>
                <source><?= htmlspecialchars($cwd) ?></source>
            </sources>
            <packages>
                <?php foreach ($all as $package => $classes): ?>
                    <package name="<?= htmlspecialchars($package) ?>">
                        <classes>
                            <?php foreach ($classes as $class => $files): ?>
                                <?php foreach ($files as $filename => $lines): ?>
                                    <class name="<?= htmlspecialchars($class) ?>" filename="<?= htmlspecialchars($filename) ?>">
                                        <lines>
                                            <?php foreach ($lines as $line => $hits): ?>
                                                <line number="<?= htmlspecialchars($line) ?>" hits="<?= htmlspecialchars($hits) ?>"/>
                                            <?php endforeach; ?>
                                        </lines>
                                    </class>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </classes>
                    </package>
                <?php endforeach; ?>
            </packages>
        </coverage>
    <?php
}

main();
