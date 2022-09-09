<?php

/* template.twig */
class __TwigTemplate_84bc50da5b610c19eef6d6afe9bf8a56121da45ee3132049ee8b9bf19456a3ee extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["css_classes_flag"] = twig_trim_filter(("wpml-ls-flag " . $this->getAttribute(($context["backward_compatibility"] ?? null), "css_classes_flag", array())));
        // line 2
        $context["css_classes_native"] = twig_trim_filter(("wpml-ls-native " . $this->getAttribute(($context["backward_compatibility"] ?? null), "css_classes_native", array())));
        // line 3
        $context["css_classes_display"] = twig_trim_filter(("wpml-ls-display " . $this->getAttribute(($context["backward_compatibility"] ?? null), "css_classes_display", array())));
        // line 4
        $context["css_classes_bracket"] = twig_trim_filter(("wpml-ls-bracket " . $this->getAttribute(($context["backward_compatibility"] ?? null), "css_classes_bracket", array())));
        // line 6
        if (($context["flag_url"] ?? null)) {
            // line 7
            echo "<img class=\"";
            echo twig_escape_filter($this->env, ($context["css_classes_flag"] ?? null), "html", null, true);
            echo "\" src=\"";
            echo twig_escape_filter($this->env, ($context["flag_url"] ?? null), "html", null, true);
            echo "\" alt=\"";
            echo twig_escape_filter($this->env, ($context["code"] ?? null), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, ($context["flag_title"] ?? null), "html", null, true);
            echo "\">";
        }
        // line 10
        if (($context["native_name"] ?? null)) {
            // line 11
            echo "<span class=\"";
            echo twig_escape_filter($this->env, ($context["css_classes_native"] ?? null), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, ($context["native_name"] ?? null), "html", null, true);
            echo "</span>";
        }
        // line 14
        if (($context["display_name"] ?? null)) {
            // line 15
            echo "<span class=\"";
            echo twig_escape_filter($this->env, ($context["css_classes_display"] ?? null), "html", null, true);
            echo "\">";
            // line 16
            if (($context["native_name"] ?? null)) {
                echo "<span class=\"";
                echo twig_escape_filter($this->env, ($context["css_classes_bracket"] ?? null), "html", null, true);
                echo "\"> (</span>";
            }
            // line 17
            echo twig_escape_filter($this->env, ($context["display_name"] ?? null), "html", null, true);
            // line 18
            if (($context["native_name"] ?? null)) {
                echo "<span class=\"";
                echo twig_escape_filter($this->env, ($context["css_classes_bracket"] ?? null), "html", null, true);
                echo "\">)</span>";
            }
            // line 19
            echo "</span>";
        }
    }

    public function getTemplateName()
    {
        return "template.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  69 => 19,  63 => 18,  61 => 17,  55 => 16,  51 => 15,  49 => 14,  42 => 11,  40 => 10,  29 => 7,  27 => 6,  25 => 4,  23 => 3,  21 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "template.twig", "D:\\home\\site\\wwwroot\\wp-content\\plugins\\sitepress-multilingual-cms\\templates\\language-switchers\\menu-item\\template.twig");
    }
}
