<?php

namespace Io\Tests\Unit\View;

use PHPUnit\Framework\TestCase;
use Io\View\Check;

/** A project of its own in the temp directory, with Io's vendor directory. */
final class CheckTest extends TestCase
{
    private static string $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = sys_get_temp_dir() . '/io-check-test-' . bin2hex(random_bytes(4));
        mkdir(self::$root . '/pages/Shop', 0777, true);
        symlink(dirname(__DIR__, 3) . '/vendor', self::$root . '/vendor');
        file_put_contents(self::$root . '/composer.json', '{"extra": {"io": {"views": "pages"}}}');
        file_put_contents(self::$root . '/pages/_Layout.php', "<title>@yield('title')</title>\n{{ \$content }}\n");
        file_put_contents(self::$root . '/pages/Shop/Good.php', "@use (Io\\Html)\n@model (Html)\n@section('title'){{ \$model }}@endsection\n<p>{{ \$model->__toString() }} {{ \$this->t('A plugin helper') }}</p>\n");
        file_put_contents(self::$root . '/pages/Shop/Sloppy.php', "@model (Io\\Html)\n<p>\n{{ \$model->nmae }}\n{{ \$this->scopeAtribute() }}\n</p>\n");
        file_put_contents(self::$root . '/pages/Shop/Broken.php', "<p>\n@if (\$model)\n");
        // A use statement of its own: PHPStan reads the view with it, on its line
        file_put_contents(self::$root . '/pages/Shop/Plain.php', "<?php\nuse Io\\Html;\n?>\n<p>{{ (new Html('a'))->nope }}</p>\n");
    }

    public static function tearDownAfterClass(): void
    {
        array_map('unlink', [...glob(self::$root . '/pages/Shop/*.php') ?: [], self::$root . '/pages/_Layout.php', self::$root . '/composer.json', self::$root . '/vendor']);
        rmdir(self::$root . '/pages/Shop');
        rmdir(self::$root . '/pages');
        rmdir(self::$root);
    }

    public function testTheViewsAreBelowTheDirectoriesComposerJsonNames(): void
    {
        $this->assertSame(['Shop/Broken.php', 'Shop/Good.php', 'Shop/Plain.php', 'Shop/Sloppy.php', '_Layout.php'], array_map(static fn (string $file) => substr($file, strlen(self::$root) + 7), Check::views(self::$root)));
    }

    public function testProblemsAreTheCompilersAtTheirLines(): void
    {
        $this->assertSame(['Shop/Broken.php:2: @if has no @endif'], array_map(
            static fn (array $p) => substr($p['file'], strlen(self::$root) + 7) . ":{$p['line']}: {$p['message']}",
            Check::problems(self::$root),
        ));
        $this->assertSame([['line' => 2, 'message' => "Unmatched ')'", 'severity' => 'error']], Check::inView("<p>\n{{ f()) }}"));
    }

    public function testALineCommentInAnEchoIsAnErrorThatNamesTheFile(): void
    {
        $php = "<p>\n<?= \$a // note ?>";

        $this->assertSame([[
            'line' => 2,
            'message' => 'A line comment inside <?= ?> also comments out what the view adds before ?>: use /* */',
            'severity' => 'error',
        ]], Check::comments($php));
        $this->assertStringContainsString('what the template adds', Check::comments($php, 'template')[0]['message']);
        $this->assertSame([], Check::comments('<?= $a /* note */ ?>'));
    }

    public function testPhpstanReadsTheViewsWithThisAndTheModelAtTheirLines(): void
    {
        $this->assertSame([
            'Shop/Plain.php:4: Access to an undefined property Io\Html::$nope.',
            'Shop/Sloppy.php:3: Access to an undefined property Io\Html::$nmae.',
            'Shop/Sloppy.php:4: Call to an undefined method Io\Http\View::scopeAtribute(). Did you mean scopeAttribute()?',
        ], array_map(
            static fn (array $p) => substr($p['file'], strlen(self::$root) + 7) . ":{$p['line']}: {$p['message']}",
            Check::phpstan(self::$root),
        ));
    }

    public function testHelperNamesAreReadFromThePluginsWithoutRunningThem(): void
    {
        $root = sys_get_temp_dir() . '/io-helpers-' . bin2hex(random_bytes(4));
        mkdir("$root/src", 0777, true);
        file_put_contents("$root/composer.json", json_encode(['autoload' => ['psr-4' => ['App\\' => 'src/']]]));
        file_put_contents("$root/src/Words.php", "<?php\nfinal class Words implements Io\\Plugin\\ViewHelper {\n    public function helpers(): array\n    {\n        return ['shout' => fn (\$t) => strtoupper(\$t), 'whisper' => strtolower(...)];\n    }\n}\n");
        try {
            $this->assertSame(['shout', 'whisper'], Check::helperNames($root));
            $this->assertSame('View helper tt() is offered by no installed plugin. Did you mean t()?', Check::unknownHelper('tt', ['t']));
        } finally {
            unlink("$root/src/Words.php");
            unlink("$root/composer.json");
            rmdir("$root/src");
            rmdir($root);
        }
    }

    public function testAnIncludeOfAViewThatIsNotThereIsAProblem(): void
    {
        $views = dirname(__DIR__, 2) . '/Fixtures/Views';
        $source = "<p>\n@include ('Partials/Badge', ['label' => 'a', 'hot' => false])\n@include ('Partials/Nope')\n@include (\$name)\n</p>\n";

        $this->assertSame(
            [['line' => 3, 'message' => "@include ('Partials/Nope'): there is no view Partials/Nope.php", 'severity' => 'error']],
            Check::includes($source, [$views]),
        );
    }
}
