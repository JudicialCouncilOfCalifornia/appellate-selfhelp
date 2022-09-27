<?php

namespace IDP\Helper\Utilities;


use IDP\Helper\Traits\Instance;

final class TabDetails
{
    use Instance;

    /**
     * Array of PluginPageDetails Object detailing
     * all the page menu options.
     *
     * @var array[PluginPageDetails] $_tabDetails
     */
    public $_tabDetails;

    /**
     * The parent menu slug
     * @var string $_parentSlug
     */
    public $_parentSlug;

    /** Private constructor to avoid direct object creation */
    private function __construct()
    {
        $registered = MoIDPUtility::micr();
        $this->_parentSlug = 'idp_settings';
        $this->_tabDetails = [
            Tabs::IDP_CONFIG => new PluginPageDetails(
                'SAML IDP - Configure IDP',
                'idp_configure_idp',
                'Service Providers',
                'Service Providers',
                "This Tab is the section where you Configure your Service Provider's details needed for SSO."
            ),
            Tabs::METADATA => new PluginPageDetails(
                'SAML IDP - Metadata',
                'idp_metadata',
                'IDP Metadata',
                'IDP Metadata',
                "This Tab is where you will find information to put in your Service Provider's configuration page."
            ),
            Tabs::DEMO_REQUEST => new PluginPageDetails(
                'SAML IDP - Request a Demo',
                'idp_request_demo',
                'Request a Demo',
                'Request a Demo',
                "This Tab is where you can request a demo of the WordPress SAML IDP Premium plugin."
            ),
            Tabs::LICENSE => new PluginPageDetails(
                'SAML IDP - License',
                'idp_upgrade_settings',
                'License',
                'Upgrade Plans',
                "This Tab details all the plugin plans and their details along with their upgrade links."
            ),
            Tabs::ATTR_SETTINGS => new PluginPageDetails(
                'SAML IDP - Attribute Settings',
                'idp_attr_settings',
                'Attribute / Role Mapping',
                'Attribute / Role Mapping',
                "This Tab is where you configure the User Attributes and Role that you want to send out to your Service Provider."
            ),
            Tabs::SIGN_IN_SETTINGS => new PluginPageDetails(
                'SAML IDP - SignIn Settings',
                'idp_signin_settings',
                'SSO Options',
                'SSO Options',
                "This Tab is where you will find ShortCode and IdP Initiated Links for SSO."
            ),    
            Tabs::ADDONS    => new PluginPageDetails(
                'SAML IDP - ADDONS',
                'idp_addons' ,
                'Add-Ons',
                'Add-Ons',
                "This page provides you with the SAML IDP compatible addons"
            ),
            Tabs::PROFILE => new PluginPageDetails(
                "SAML IDP - Account",
                "idp_profile",
                !$registered ? 'Account Setup' : 'User Profile',
                !$registered ? "Account Setup" : "Profile",
                "This Tab contains your Profile information. If you haven't registered then you can do so from here."
            ),  
            Tabs::SUPPORT   => new PluginPageDetails(
                'SAML IDP - Support',
                'idp_support',
                'Support',
                'Support',
                "You can use the form here to get in touch with us for any kind of support."
            ),
            Tabs::DASHBOARD   => new PluginPageDetails(
                'SAML IDP - DashBoard',
                 $this->_parentSlug,
                'Dashboard',
                'Dashboard',
                "You can use the form here to get in touch with us for any kind of support."
            ),     
        ];
    }
}