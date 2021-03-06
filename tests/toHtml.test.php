<?php

use BEM\BH;

class bhToHtmlContentTest extends PHPUnit_Framework_TestCase {

    /**
     * @before
     */
    function setupBhInstance () {
        $this->bh = new BH();
    }

    function test_it_should_return_empty_content () {
        $this->assertEquals(
            '<div></div>',
            $this->bh->apply([
                false,
                null,
                [],
                '',
                ['content' => false], // `div` is here
                ['tag' => false]
            ])
        );
    }

    function test_it_should_escape_string_when_option_enabled () {
        $this->bh->setOptions(['escapeContent' => true]);
        $this->assertEquals(
            '&lt;a&gt;&amp;nbsp;&lt;/a&gt;',
            $this->bh->apply('<a>&nbsp;</a>')
        );
    }

    function test_it_should_escape_content_when_option_enabled () {
        $this->bh->setOptions(['escapeContent' => true]);
        $this->assertEquals(
            '<div>&lt;&amp;&gt;&lt;&amp;&gt;<div>&lt;&amp;&gt;</div></div>',
            $this->bh->apply([
                'content' => [
                    '<&>',
                    ['content' => '<&>', 'tag' => false],
                    ['content' => '<&>']
                ]
            ])
        );
    }

    function test_it_should_prefer__html__field () {
        $this->assertEquals(
            '<div><br/>1</div>',
            $this->bh->apply([
                'content' => '<br/>',
                'html' => '<br/>1'
            ])
        );
    }

}

class bhToHtmlBemTest extends PHPUnit_Framework_TestCase {

    /**
     * @before
     */
    function setupBhInstance () {
        $this->bh = new BH();
    }

    function test_it_should_not_set_class_if_not_bem () {
        $this->assertEquals(
            '<div></div>',
            $this->bh->apply(['block' => 'button', 'bem' => false]));
    }

    function test_it_should_not_set_js_if_not_bem () {
        $this->assertEquals(
            '<div></div>',
            $this->bh->apply(['block' => 'button', 'js' => true, 'bem' => false]));
    }

    function test_it_should_not_set_mixed_class_if_not_bem () {
        $this->assertEquals(
            '<div class="button"></div>',
            $this->bh->apply([
                'block' => 'button',
                'mix' => ['block' => 'link', 'bem' => false]
            ])
        );
    }

    function test_it_should_not_set_mixed_js_if_not_bem () {
        $this->assertEquals(
            '<div class="button"></div>',
            $this->bh->apply([
                'block' => 'button',
                'mix' => ['block' => 'link', 'js' => true, 'bem' => false]
            ])
        );
    }

}

class bhToHtmlTagsTest extends PHPUnit_Framework_TestCase {

    /**
     * @before
     */
    function setupBhInstance () {
        $this->bh = new BH();
    }

    function test_it_should_return_html_tag__div__by_default () {
        $this->assertEquals(
            '<div></div>',
            $this->bh->apply([]));
    }

    function test_it_should_return_html_tag__span () {
        $this->assertEquals(
            '<span></span>',
            $this->bh->apply(['tag' => 'span']));
    }

    function test_it_should_return_content_when__tag__is_empty () {
        $this->assertEquals(
            'label',
            $this->bh->apply(['tag' => false, 'content' => 'label']));
    }
}

class bhToHtmlAttrsTest extends PHPUnit_Framework_TestCase {

    /**
     * @before
     */
    function setupBhInstance () {
        $this->bh = new BH();
    }

    function test_it_should_ignore_null_attrs () {
        $this->bh->match('button', function ($ctx) {
            $ctx->tag('a');
            $ctx->attr('href', '#');
        });
        $this->bh->match('button', function ($ctx) {
            $ctx->attr('href', null);
        });
        $this->assertEquals(
            '<a class="button"></a>',
            $this->bh->apply(['block' => 'button'])
        );
    }

    function test_it_should_not_ignore_empty_attrs () {
        $this->bh->match('button', function ($ctx) {
            $ctx->tag('a');
            $ctx->attr('href', '#');
        });
        $this->bh->match('button', function ($ctx) {
            $ctx->attr('href', '');
        });
        $this->assertEquals(
            '<a class="button" href=""></a>',
            $this->bh->apply(['block' => 'button'])
        );
    }

    function test_it_should_escape_attrs_ () {
        $this->assertEquals(
            '<a href="&lt;script type=&quot;javascript&quot;&gt;window &amp;&amp; ' .
            'alert(document.cookie)&lt;/script&gt;">link</a>',
            $this->bh->apply([
                'tag' => 'a',
                'attrs' => ['href' => '<script type="javascript">window && alert(document.cookie)</script>'],
                'content' => 'link'
            ])
        );
    }
}

class bhToHtmlModsTest extends PHPUnit_Framework_TestCase {

    /**
     * @before
     */
    function setupBhInstance () {
        $this->bh = new BH();
    }

    function test_it_should_ignore_null_mods () {
        $this->bh->match('button', function ($ctx) {
            $ctx->tag('a');
            $ctx->mod('type', 'active');
        });
        $this->bh->match('button', function ($ctx) {
            $ctx->mod('type', null);
        });
        $this->assertEquals(
            '<a class="button"></a>',
            $this->bh->apply(['block' => 'button'])
        );
    }

    function test_it_should_ignore_empty_mods () {
        $this->bh->match('button', function ($ctx) {
            $ctx->tag('a');
            $ctx->mod('type', 'active');
        });
        $this->bh->match('button', function ($ctx) {
            $ctx->mod('type', '');
        });
        $this->assertEquals(
            '<a class="button"></a>',
            $this->bh->apply(['block' => 'button'])
        );
    }

    function test_it_should_not_ignore_boolean_mods () {
        $this->bh->match('button', function ($ctx) {
            $ctx->tag('button');
            $ctx->mod('disabled', 'disabled');
        });
        $this->bh->match('button', function ($ctx) {
            $ctx->mod('visible', false);
            $ctx->mod('disabled', true);
        });
        $this->assertEquals(
            '<button class="button button_disabled"></button>',
            $this->bh->apply(['block' => 'button'])
        );
    }
}

class bhToHtmlMixTest extends PHPUnit_Framework_TestCase {

    /**
     * @before
     */
    function setupBhInstance () {
        $this->bh = new BH();
    }

    function test_it_should_not_set_null_mix () {
        $this->assertEquals(
            '<div class="button"></div>',
            $this->bh->apply([
                'block' => 'button',
                'mix' => [null, null]
            ])
        );
    }

    function test_should_not_set_elem_mix_on_empty_node () {
        $this->assertEquals('<div></div>', $this->bh->apply([ 'mix' => [ 'elem' => 'button' ] ]));
    }

    function test_it_should_set_elem_mix () {
        $this->bh->match('button', function ($ctx) {
            $ctx->mix(['elem' => 'mix']);
        });
        $this->assertEquals(
            '<div class="button button__mix"></div>',
            $this->bh->apply(['block' => 'button'])
        );
    }

    function test_it_should_set_mods_mix () {
        $this->bh->match('button', function ($ctx) {
            $ctx->mix(['mods' => ['disabled' => true, 'theme' => 'normal']]);
        });
        $this->assertEquals(
            '<div class="button button_disabled button_theme_normal"></div>',
            $this->bh->apply(['block' => 'button'])
        );
    }

    function test_it_should_set_elem_mods_mix () {
        $this->bh->match('button', function ($ctx) {
            $ctx->mix(['elem' => 'control', 'mods' => ['disabled' => true]]);
        });
        $this->assertEquals(
            '<div class="button button__control button__control_disabled"></div>',
            $this->bh->apply(['block' => 'button'])
        );
    }

    function test_it_should_set_elem_elemMods_mix () {
        $this->bh->match('button', function ($ctx) {
            $ctx->mix(['elem' => 'control', 'elemMods' => ['disabled' => true]]);
        });
        $this->assertEquals(
            '<div class="button button__control button__control_disabled"></div>',
            $this->bh->apply(['block' => 'button'])
        );
    }

    function test_it_should_set_mixed_js () {
        $this->assertEquals(
            '<div class="button link button__control i-bem" ' .
                'onclick="return {&quot;link&quot;:{},&quot;button__control&quot;:{&quot;foo&quot;:&quot;bar&quot;}}"' .
            '></div>',
            $this->bh->apply([
                'block' => 'button',
                'mix' => [['block' => 'link', 'js' => true], ['elem' => 'control', 'js' => ['foo' => 'bar']]]
            ])
        );
    }

    function test_it_should_set_several_mixes () {
        $this->bh->match('button', function ($ctx) {
            $ctx->mix([
                ['block' => 'link'],
                ['elem' => 'control'],
                ['mods' => ['disabled' => true]],
                ['block' => 'label', 'elem' => 'first', 'mods' => ['color' => 'red']]
            ]);
        });
        $this->assertEquals(
            '<div class="button link button__control button_disabled label__first label__first_color_red"></div>',
            $this->bh->apply(['block' => 'button'])
        );
    }
}

class bhToHtmlJsTest extends PHPUnit_Framework_TestCase {

    /**
     * @before
     */
    function setupBhInstance () {
        $this->bh = new BH();
    }

    function test_itShouldNotSet_iBem_classOnElement () {
        $this->assertEquals(
            '<div class="button__control" onclick="return {&quot;button__control&quot;:{}}">submit</div>',
            $this->bh->apply([ 'block' => 'button', 'elem' => 'control', 'js' => true, 'content' => 'submit' ])
        );
    }

    function test_itShouldNotSet_iBem_classOnMixedElement () {
        $this->assertEquals(
            '<div class="icon button__control" onclick="return {&quot;button__control&quot;:{}}">submit</div>',
            $this->bh->apply([ 'block' => 'icon', 'content' => 'submit', 'mix' => [ 'block' => 'button', 'elem' => 'control', 'js' => true ]])
        );
    }

}

class bhToHtmlClsTest extends PHPUnit_Framework_TestCase {

    /**
     * @before
     */
    function setupBhInstance () {
        $this->bh = new BH();
    }

    function test_should_set_cls () {
        $this->assertEquals('<div class="clearfix"></div>', $this->bh->apply([ 'cls' => 'clearfix' ]));
    }

}
