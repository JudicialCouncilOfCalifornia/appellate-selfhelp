<?php

/* media-translation.twig */
class __TwigTemplate_a08a64cb0375de3a6be5b4d1b468cbd6eb66a1800744a51b7162687886e49b2f extends Twig_Template
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
        echo "<div class=\"wrap\">

    <h2>";
        // line 3
        echo twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "heading", array()), "html", null, true);
        echo "</h2>

    ";
        // line 5
        $this->loadTemplate("batch-translation.twig", "media-translation.twig", 5)->display(array_merge($context, ($context["batch_translation"] ?? null)));
        // line 6
        echo "
    <div class=\"tablenav top wpml-media-tablenav\">
        ";
        // line 8
        $this->loadTemplate("media-translation-filters.twig", "media-translation.twig", 8)->display($context);
        // line 9
        echo "    </div>

    <table class=\"widefat striped wpml-media-table\">
        <thead>
        <tr>
            <th class=\"wpml-col-media-title\">";
        // line 14
        echo twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "original_language", array()), "html", null, true);
        echo "</th>
            <th class=\"wpml-col-media-translations\">
                ";
        // line 16
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["languages"] ?? null));
        foreach ($context['_seq'] as $context["code"] => $context["language"]) {
            // line 17
            echo "                    ";
            if ((twig_test_empty(($context["target_language"] ?? null)) || (($context["target_language"] ?? null) == $context["code"]))) {
                // line 18
                echo "                        <span title=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "name", array()), "html", null, true);
                echo "\"><img src=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "flag", array()), "html", null, true);
                echo "\" width=\"16\" height=\"12\"
                                                               alt=\"";
                // line 19
                echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "code", array()), "html", null, true);
                echo "\"></span>
                    ";
            }
            // line 21
            echo "                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['code'], $context['language'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 22
        echo "            </th>
        </tr>
        </thead>
        <tbody>
        ";
        // line 26
        if (($context["attachments"] ?? null)) {
            // line 27
            echo "            ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["attachments"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["attachment"]) {
                // line 28
                echo "                ";
                $this->loadTemplate("media-translation-table-row.twig", "media-translation.twig", 28)->display(array("attachment" => $context["attachment"], "languages" => ($context["languages"] ?? null), "strings" => ($context["strings"] ?? null), "target_language" => ($context["target_language"] ?? null)));
                // line 29
                echo "            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['attachment'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 30
            echo "        ";
        } else {
            // line 31
            echo "            <tr>
                <td colspan=\"2\">";
            // line 32
            echo twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "no_attachments", array()), "html", null, true);
            echo "</td>
            </tr>
        ";
        }
        // line 35
        echo "        </tbody>

    </table>

    <div class=\"tablenav bottom\">
        ";
        // line 40
        $this->loadTemplate("pagination.twig", "media-translation.twig", 40)->display(array("pagination_model" => ($context["pagination"] ?? null)));
        // line 41
        echo "
        ";
        // line 42
        $this->loadTemplate("media-translation-popup.twig", "media-translation.twig", 42)->display($context);
        // line 43
        echo "
    </div>

</div>";
    }

    public function getTemplateName()
    {
        return "media-translation.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  121 => 43,  119 => 42,  116 => 41,  114 => 40,  107 => 35,  101 => 32,  98 => 31,  95 => 30,  89 => 29,  86 => 28,  81 => 27,  79 => 26,  73 => 22,  67 => 21,  62 => 19,  55 => 18,  52 => 17,  48 => 16,  43 => 14,  36 => 9,  34 => 8,  30 => 6,  28 => 5,  23 => 3,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "media-translation.twig", "D:\\home\\site\\wwwroot\\wp-content\\plugins\\wpml-media-translation\\templates\\menus\\media-translation.twig");
    }
}
