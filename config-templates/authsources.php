<?php

$config = array(


    // Example of authsource config over an example of a base saml authsource
    'router' => array(
        //'saml:SP',
        'samlroutesp:samlRouteSP',

        'entityID' => NULL,

        // If set, it will be considered the 'default mapping'. That is,
        // any SP appearing on the routes dictionary, will be routed to that IDP,
        // and if the SP does not appear, it will be routed to 'idp' as per the
        // usual behaviour of saml:SP authsource (if not set, saml:SP default
        // behaviour applies)
        'idp' => 'https://example.domain/idp0/saml2/idp/metadata.php',

        // The URL to the discovery service.
        // Can be NULL/unset, in which case a builtin discovery service will be used.
        'discoURL' => NULL,



        // ---- this authsource specific config items ----

        // Map of remote SP entityIDs to remote IDP entityIDs. Any requesting SP
        // appearing here will be routed to the specified IDP
        'routes' => array(
            'https://example.domain/sp1/module.php/saml/sp/metadata.php/default-sp' => 'https://example.domain/idp1/saml2/idp/metadata.php',
            'https://example.domain/sp2/module.php/saml/sp/metadata.php/default-sp' => 'https://example.domain/idp2/saml2/idp/metadata.php',
        ),

    ),


);
