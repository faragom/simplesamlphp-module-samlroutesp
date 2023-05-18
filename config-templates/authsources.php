<?php

$config = array(


    // Example of authsource config over an example of a base saml authsource
    'router' => array(
        //'saml:SP',
        'samlroutesp:samlRouteSP',

        'entityID' => NULL,

        // If set, it will override the mappings as the only destination  // TODO: or should we do it the defailt value but make the mappings prevail?
        //  'idp' => 'https://example.domain/idp1/saml2/idp/metadata.php',

        // The URL to the discovery service.
        // Can be NULL/unset, in which case a builtin discovery service will be used.
        'discoURL' => NULL,



        // ---- this authsource specific config items ----

        // Map of remote SP entityIDs to remote IDP entityIDs
        'routes' => array(
            'https://example.domain/sp1/module.php/saml/sp/metadata.php/default-sp' => 'https://example.domain/idp1/saml2/idp/metadata.php',
            'https://example.domain/sp2/module.php/saml/sp/metadata.php/default-sp' => 'https://example.domain/idp2/saml2/idp/metadata.php',
        ),

    ),


);
