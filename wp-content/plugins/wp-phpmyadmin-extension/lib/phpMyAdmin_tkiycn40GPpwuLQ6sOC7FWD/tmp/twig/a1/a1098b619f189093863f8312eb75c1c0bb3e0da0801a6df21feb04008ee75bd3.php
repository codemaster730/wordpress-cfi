<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* javascript/display.twig */
class __TwigTemplate_8cba84e189922159ac32568096f47993c45bfac80f040f7298f10a660b25a4f9 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<script type=\"text/javascript\">
    if (typeof configInlineParams === 'undefined' || !Array.isArray(configInlineParams)) {
        configInlineParams = [];
    }
    configInlineParams.push(function () {
        ";
        // line 6
        echo twig_join_filter(($context["js_array"] ?? null), ";
");
        echo ";
    });
    if (typeof configScriptLoaded !== 'undefined' && configInlineParams) {
        loadInlineConfig();
    }
</script>
";
    }

    public function getTemplateName()
    {
        return "javascript/display.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  44 => 6,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "javascript/display.twig", "/home/humajvue/groups.cficast.com/wp-content/plugins/wp-phpmyadmin-extension/lib/phpMyAdmin_tkiycn40GPpwuLQ6sOC7FWD/templates/javascript/display.twig");
    }
}
