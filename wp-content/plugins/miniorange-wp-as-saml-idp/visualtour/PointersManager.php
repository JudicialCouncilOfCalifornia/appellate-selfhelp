<?php

namespace IDP\VisualTour;

use IDP\Helper\Traits\Instance;

class PointersManager
{
    use Instance;

    
    private $version;
    
    private $prefix;
    
    private $pointers = array();

    public function __construct()
    {
        $this->version = str_replace( '.', '_', MSI_POINTER_VERSION);
        $this->prefix = MSI_POINTER_PREFIX;
    }


    
    public function parse()
    {
        
        $pointers = Pointers::instance();
        if ( empty($pointers->getPointerForPage()) ) return $this;
        
        foreach ( $pointers->getPointerForPage() as $i => $pointer ) {
            $pointer->id = "{$this->prefix}{$this->version}_{$i}";
            $this->pointers[$pointer->id] = $pointer;
        }
        return $this;
    }


    
    public function filter( $page )
    {
        if ( empty( $this->pointers ) ) return array();
        $uid = get_current_user_id();
        $no = explode( ',', (string) get_user_meta( $uid, 'dismissed_wp_pointers', TRUE ) );
        $active_ids = array_diff( array_keys( $this->pointers ), $no );
        $good = array();
        
        foreach( $this->pointers as $i => $pointer ) {
            if (
                in_array( $i, $active_ids, TRUE )                 && isset( $pointer->where )                 && in_array( $page, $pointer->where, TRUE )             ) {
                $good[] = $pointer;
            }
        }
        $count = count( $good );
        
        foreach( array_values( $good ) as $i => $pointer ) {
            $pointer->next = $i+1 < $count ? $good[$i+1]->id : '';
        }
        return $good;
    }

    public function clear()
    {
        $uid = get_current_user_id();
        $dPointers = explode( ',', (string) get_user_meta( $uid, 'dismissed_wp_pointers', TRUE ) );
        
        $pointers = Pointers::instance();
        $pointers->restartTour($dPointers,$uid);
    }
}