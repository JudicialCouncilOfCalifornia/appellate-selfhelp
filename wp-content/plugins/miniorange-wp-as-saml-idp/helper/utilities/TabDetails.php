<?php

namespace IDP\Helper\Utilities;


use IDP\Helper\Traits\Instance;

final class TabDetails
{
    use Instance;

    
    public $_tabDetails;

    
    public $_parentSlug;

    
    private function __construct()
    {
        $registered = MoIDPUtility::micr();
        $this->_parentSlug = 'idp_settings';
        $this->_tabDetails = [
            Tabs::PROFILE => new PluginPageDetails(
                "SAML IDP - Account",
                "idp_profile",
                !$registered ? 'Account Setup' : 'User Profile',
                !$registered ? "Account Setup" : "Profile",
                "This Tab contains your Profile information. If you haven't registered then you can do so from here."
            ),
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
            Tabs::SIGN_IN_SETTINGS => new PluginPageDetails(
                'SAML IDP - SignIn Settings',
                'idp_signin_settings',
                'SSO Options',
                'SSO Options',
                "This Tab is where you will find ShortCode and IdP Initiated Links for SSO."
            ),
            Tabs::ATTR_SETTINGS => new PluginPageDetails(
                'SAML IDP - Attribute Settings',
                'idp_attr_settings',
                'Attribute/Role Mapping',
                'Attribute/Role Mapping',
                "This Tab is where you configure the User Attributes and Role that you want to send out to your Service Provider."
            ),
            Tabs::LICENSE => new PluginPageDetails(
                'SAML IDP - License',
                'idp_upgrade_settings',
                'License',
                'Upgrade Plans',
                "This Tab details all the plugin plans and their details along with their upgrade links."
            ),
            Tabs::SUPPORT   => new PluginPageDetails(
                'SAML IDP - Support',
                'idp_support',
                'Support',
                'Support',
                "You can use the form here to get in touch with us for any kind of support."
            ),
        ];
    }
}