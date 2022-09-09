<?php
namespace IDP\VisualTour;

use IDP\Helper\Traits\Instance;
use IDP\Helper\Utilities\PluginPageDetails;
use IDP\Helper\Utilities\TabDetails;
use IDP\Helper\Utilities\Tabs;

class Pointers
{
    use Instance;

    
    private $ssoPagePointers;
    
    private $wsfedPagePointers;
    
    private $metadataPagePointers;
    
    private $attrPagePointers;
    
    private $pricingPagePointers;

    public function __construct()
    {
        
        $tabs = TabDetails::instance();
        $this->ssoPagePointers = $this->loadSSOPagePointers($tabs->_tabDetails);
        $this->wsfedPagePointers = $this->loadWsfedPagePointers($tabs->_tabDetails);
        $this->metadataPagePointers = $this->loadMetadataPagePointers($tabs->_tabDetails);
        $this->attrPagePointers = $this->loadAttrPagePointers($tabs->_tabDetails);
        $this->pricingPagePointers = $this->loadPricingPagePointers($tabs->_tabDetails);
    }

    
    private function loadAttrPagePointers($tabDetails)
    {
        return [
            'moidp-nameid-attr' => new PointerData(
                'NameID',
                'This attribute value is sent in the SAML Response. Users in your Service Provider 
                will be searched (existing users) or created (new users) based on this attribute.',
                '#nameIdTable',
                'top',
                'right',
                $tabDetails[Tabs::ATTR_SETTINGS]->_menuSlug
            )
        ];
    }

    
    private function loadPricingPagePointers($tabDetails)
    {
        return [
            'moidp-pricing-attr' => new PointerData(
                'Upgrade',
                'You will need to Register or Login with miniOrange in order to be able to upgrade. 
                 Click on the register button below to get started.',
                '#freeUpgrade',
                'top',
                'left',
                $tabDetails[Tabs::LICENSE]->_menuSlug
            )
        ];
    }

    
    private function loadMetadataPagePointers($tabDetails)
    {
        return [
            'moidp-idp-metadata' => new PointerData(
                'IDP Information',
                'Provide the information given here to your SP to configure wordpress as IDP',
                '#idpInfoTable',
                'right',
                'left',
                $tabDetails[Tabs::METADATA]->_menuSlug
            ),
            'moidp-idp-xml' => new PointerData(
                'IDP Metadata XML',
                'You can even provide this metadata file / URL to your SP for easy setup. Make sure your SP supports this',
                '#metadataXML',
                'right',
                'left',
                $tabDetails[Tabs::METADATA]->_menuSlug
            )
        ];
    }

    
    private function loadWsfedPagePointers($tabDetails)
    {
        return [
            'moidp-wsfed-metadata' => new PointerData(
                'WS-FED Configuration',
                'Get following information from your SP and save it to configure your IDP',
                '#wsFedTable',
                'right',
                'left',
                $tabDetails[Tabs::IDP_CONFIG]->_menuSlug
            )
        ];
    }

    
    private function loadSSOPagePointers($tabDetails)
    {
        return [
            'moidp-quicklink-pointer' => new PointerData(
                'Quick Links',
                'Check our FAQ, Upgrade Plans from here or contact us if you need any help.',
                '#idp-quicklinks',
                'top',
                'left',
                $tabDetails[Tabs::IDP_CONFIG]->_menuSlug
            ),
            'moidp-protocol-pointer' => new PointerData(
                'Select your Protocol',
                'Select your protocol for which you need to configure SP.',
                '#protocolDiv',
                'right',
                'right',
                $tabDetails[Tabs::IDP_CONFIG]->_menuSlug
            ),
            'moidp-select-your-sp' => new PointerData(
                'Service Provider Name',
                'Enter your Service Provider name and do further configuration.',
                '#idpName',
                'top',
                'right',
                $tabDetails[Tabs::IDP_CONFIG]->_menuSlug
            ),
            'moidp-save-metadata' => new PointerData(
                'Save your Configuration',
                'Once all details have been entered click on save to save your configuration.',
                '#Save',
                'bottom',
                'left',
                $tabDetails[Tabs::IDP_CONFIG]->_menuSlug
            ),
            'moidp-test-configuration' => new PointerData(
                'Test your Configuration',
                'Test Configuration to check if both SP and IDP are successfully configured and are in sync.',
                '#testConfig',
                'bottom',
                'left',
                $tabDetails[Tabs::IDP_CONFIG]->_menuSlug
            )
        ];
    }


    
    public function getPointerForPage()
    {
        
        $tabs = TabDetails::instance();
        $tab = isset($_GET['page']) ?  $_GET['page'] : $tabs->_tabDetails[Tabs::IDP_CONFIG]->_menuSlug;
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        if($tab==$tabs->_tabDetails[Tabs::IDP_CONFIG]->_menuSlug && $action=="add_wsfed_app") {
            return $this->wsfedPagePointers;
        }else if($tab==$tabs->_tabDetails[Tabs::METADATA]->_menuSlug) {
            return $this->metadataPagePointers;
        }else if($tab==$tabs->_tabDetails[Tabs::ATTR_SETTINGS]->_menuSlug) {
            return $this->attrPagePointers;
        }else if($tab==$tabs->_tabDetails[Tabs::IDP_CONFIG]->_menuSlug) {
            return $this->ssoPagePointers;
        }else if($tab==$tabs->_tabDetails[Tabs::LICENSE]->_menuSlug) {
            return $this->pricingPagePointers;
        }
        return [];
    }

    
    public function restartTour($dPointers,$userId)
    {
        $pointers = $this->getPointerForPage();
        $dPointers = array_diff($dPointers,$this->getPointerId($pointers));
        update_user_meta($userId,'dismissed_wp_pointers',implode(",",$dPointers));
    }


    
    private function getPointerId($pointers) {
        return array_map(
            function($item){
                $prefix = MSI_POINTER_PREFIX . str_replace( '.', '_', MSI_POINTER_VERSION);
                return $prefix."_".$item;
            },
            array_keys($pointers)
        );
    }
}