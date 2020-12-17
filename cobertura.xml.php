<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
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
