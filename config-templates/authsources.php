<?php

$config = array(


    // Example of authsource config over an example of a base saml authsource
    'router' => array(
        //'saml:SP',
        'samlroutesp:samlRouteSP',

        // All saml:SP parameters apply here. Just take into account
        // what is explained below



        // ON THIS WRAPPER AUTHSOURCE, MUST BE STRICTLY SET TO NULL OR UNSET
        // Otherwise, request will fail because the saml:SP implementation
        // does a check on getIdPMetadata($entityId) for that entityID to match
        // this, else 'Cannot retrieve metadata for IdP ...' is thrown. But this
        // collides with the behaviour of allowing  $state['saml:idp'] to override.
        // If both are set and are different, the same exception will pop.
        'idp' => NULL,


        // ---- this authsource specific config items ----

        // Map of remote SP entityIDs to remote IDP entityIDs. Any requesting SP
        // appearing here will be routed to the specified IDP
        'routes' => array(
            'https://example.domain/sp1/module.php/saml/sp/metadata.php/default-sp' => 'https://example.domain/idp1/saml2/idp/metadata.php',
            'https://example.domain/sp2/module.php/saml/sp/metadata.php/default-sp' => 'https://example.domain/idp2/saml2/idp/metadata.php',
        ),

        // If set, it will be considered the 'default mapping'. That is,
        // any SP appearing on the routes dictionary, will be routed to that IDP,
        // and if the SP does not appear, it will be routed to 'idp' as per the
        // usual behaviour of saml:SP authsource (if not set, saml:SP default
        // behaviour applies)
        // Added to circumvent implementation thing in SSP, does the
        // thing we would expect by setting 'idp'
        'default_idp' => 'https://example.domain/idp0/saml2/idp/metadata.php',

    ),


);
