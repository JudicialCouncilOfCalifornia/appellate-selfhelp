<?php

namespace IDP\VisualTour;

class PointerData
{
    
    public $id;
    
    public $title;

    
    public $content;

    
    public $anchor_id;

    
    public $edge;

    
    public $align;

    
    public $where;

    
    public function __construct($title, $content, $anchor_id, $edge, $align, $where)
    {
        $this->title = $this->generateTitle($title);
        $this->content = $this->generateContent($content);
        $this->anchor_id = $anchor_id;
        $this->edge = $edge;
        $this->align = $align;
        $this->where = $this->generateWhere($where);
    }

    
    private function generateTitle($title)
    {
        return sprintf( '<h3>%s</h3>', esc_html__($title ) );
    }

    
    private function generateContent($content)
    {
        return sprintf( '<p>%s</p>', esc_html__( $content));
    }

    
    private function generateWhere($where)
    {
        return [ 'wordpress-idp_page_' . $where];
    }
}