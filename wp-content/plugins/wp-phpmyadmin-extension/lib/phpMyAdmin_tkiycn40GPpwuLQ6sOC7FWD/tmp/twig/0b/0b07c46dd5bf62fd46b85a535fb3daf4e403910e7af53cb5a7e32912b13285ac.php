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

/* config/form_display/form_top.twig */
class __TwigTemplate_b49b39099d647a8ff16803a96c6a368866a042edb4bbbd8ccb58f259bcc2265e extends \Twig\Template
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
        echo "<form method=\"";
        echo twig_escape_filter($this->env, ($context["method"] ?? null), "html", null, true);
        echo "\" action=\"";
        echo ($context["action"] ?? null);
        echo "\" class=\"config-form disableAjax\">
  <input type=\"hidden\" name=\"tab_hash\" value=\"\">
  ";
        // line 3
        if (($context["has_check_page_refresh"] ?? null)) {
            // line 4
            echo "    <input type=\"hidden\" name=\"check_page_refresh\" id=\"check_page_refresh\" value=\"\">
  ";
        }
        // line 6
        echo "  ";
        echo PhpMyAdmin\Url::getHiddenInputs("", "", 0, "server");
        echo "
  ";
        // line 7
        echo PhpMyAdmin\Url::getHiddenFields(($context["hidden_fields"] ?? null), "", true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "config/form_display/form_top.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  56 => 7,  51 => 6,  47 => 4,  45 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "config/form_display/form_top.twig", "/home/humajvue/groups.cficast.com/wp-content/plugins/wp-phpmyadmin-extension/lib/phpMyAdmin_tkiycn40GPpwuLQ6sOC7FWD/templates/config/form_display/form_top.twig");
    }
}
