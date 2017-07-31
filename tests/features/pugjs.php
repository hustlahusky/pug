<?php

use Pug\Pug;

class PugJsTest extends PHPUnit_Framework_TestCase
{
    public function testPugJsOption()
    {
        if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            return;
        }

        $pug = new Pug(array(
            'pugjs' => true,
        ));

        $html = $pug->render('h1=name', array('name' => 'Yop'));

        $this->assertSame('<h1>Yop</h1>', $html);

        $html = $pug->render(__DIR__ . '/../templates/basic.jade');

        $this->assertSame('<html><body><h1>Title</h1></body></html>', $html);

        $pug->setOption('cache', sys_get_temp_dir());
        $name = 'basic-copy-' . mt_rand(0, 99999999);
        $source = sys_get_temp_dir() . '/' . $name . '.jade';
        $cache = sys_get_temp_dir() . '/' . $name . '.js';
        copy(__DIR__ . '/../templates/basic.jade', $source);

        if (file_exists($cache)) {
            unlink($cache);
        }

        $html = trim($pug->render($source));
        clearstatcache();

        $this->assertTrue(file_exists($cache));

        $this->assertSame('<html><body><h1>Title</h1></body></html>', $html);

        file_put_contents($source, 'p=greet');
        touch($source, time() - 10);
        touch($cache, time() + 10);
        clearstatcache();

        $html = trim($pug->render($source, array(
            'greet' => 'Hello'
        )));

        $this->assertSame('<html><body><h1>Title</h1></body></html>', $html);

        touch($cache, time() - 20);
        clearstatcache();

        $html = trim($pug->render($source, array(
            'greet' => 'Hello'
        )));

        $this->assertSame('<p>Hello</p>', $html);

        $html = trim($pug->render($source, array(
            'greet' => 'Bye'
        )));

        $this->assertSame('<p>Bye</p>', $html);

        file_put_contents($source, 'div: p');
        touch($cache, time() - 20);
        clearstatcache();

        $html = trim($pug->render($source));

        $this->assertSame('<div><p></p></div>', $html);

        $pug->setOption('prettyprint', true);

        touch($cache, time() - 20);
        clearstatcache();

        $html = trim($pug->render($source));

        $this->assertSame("<div>\n  <p></p>\n</div>", $html);

        unlink($source);
        unlink($cache);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage is not a valid class name
     */
    public function testPugJsOptionException()
    {
        if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            throw new \RuntimeException('emulate "is not a valid class name" exception');
        }

        $pug = new Pug(array(
            'pugjs' => true,
        ));

        $pug->render('./bar/basic.jade');
    }

    public function testIssue147()
    {
        if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            return;
        }

        $pug = new Pug(array(
            'pugjs' => true,
        ));

        $html = $pug->render(
            'link(rel="shortcut icon", href=site.favicon, type="image/png")',
            array('site' => array('favicon' => '/favicon.png'))
        );

        $this->assertSame('<link rel="shortcut icon" href="/favicon.png" type="image/png"/>', $html);
    }

    public function testLocalsJsonFile()
    {
        if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            return;
        }

        $pug = new Pug(array(
            'pugjs' => true,
            'localsJsonFile' => true
        ));

        $html = $pug->render(
            'link(rel="shortcut icon", href=site.favicon, type="image/png")',
            array('site' => array('favicon' => '/favicon.png'))
        );

        $this->assertSame('<link rel="shortcut icon" href="/favicon.png" type="image/png"/>', $html);
    }
}
