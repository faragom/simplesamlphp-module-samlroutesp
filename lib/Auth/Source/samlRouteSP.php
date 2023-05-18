<?php

declare(strict_types=1);

namespace SimpleSAML\Module\samlroutesp\Auth\Source;



use Exception;
use SimpleSAML\Configuration;
use SimpleSAML\Logger;
use SimpleSAML\Metadata\MetaDataStorageHandler;
use SimpleSAML\Module\saml\Error\NoAvailableIDP;
use SimpleSAML\Module\saml\Error\NoSupportedIDP;
use SAML2\Constants;



class samlRouteSP extends \SimpleSAML\Module\saml\Auth\Source\SP
{

    /**
     * A map of remote SP entityIDs to remote IDP entityIDs pairings to route
     *
     * @var string[]
     */
    private $routes = [];


    /**
     * The IdP where to direct all SPs that don't have a speecific mapping.
     *
     * @var string|null  Had to add this parameter due to a weird
     *                   implementation in SSP that prevents me from using
     *                   'idp' parameter, which must be NULL when using this source.
     */
    private $default_idp;


    /**
     * Constructor for SAML SP authentication source.
     *
     * @param array $info Information about this authentication source.
     * @param array $config Configuration.
     * @throws Exception
     */
    public function __construct($info, $config)
    {
        assert(is_array($info));
        assert(is_array($config));

        // Call the parent constructor first, as required by the interface
        parent::__construct($info, $config);

        $metadata = Configuration::loadFromArray(
            $config,
            'authsources[' . var_export($this->authId, true) . ']'
        );

        // We load the map of routes from the config, being routes[SPentityid] => [IDPentityID]
        $this->routes = $metadata->getArray('routes',[]);


        $this->default_idp = $metadata->getString('default_idp',NULL);
    }



    /**
     * Start login.
     *
     * This function checks the remote SP and establishes remote IDP to follow. Then, the
     * original implementation it overloads saves the information about the login,
     * and redirects to the IdP.
     *
     * @param array &$state Information about the current authentication.
     * @return void
     * @throws NoSupportedIDP|NoAvailableIDP
     */
    public function authenticate(&$state)
    {
        assert(is_array($state));


        // The remote SP entityID
        $sp = $state['SPMetadata']['entityid'];

        $idp = '';
        // If the SP is routed to a specific IdP
        if(array_key_exists($sp, $this->routes)){
            $idp = $this->routes[$sp];
            Logger::debug("[samlRouteSP] Found route from SP $sp to IDP $idp");
        }

        // There was no route but we have a default
        if($idp == NULL || $idp == "") {
            $idp = $this->default_idp;
            Logger::debug("[samlRouteSP] Using default route $idp for SP $sp");
        }

        // The 'route' seems like a valid string at least
        if($idp !== NULL && $idp != "") {

            // Check if the destination IDP is known to this proxy
            $mdh = MetaDataStorageHandler::getMetadataHandler();
            $matches = $mdh->getMetaDataForEntities([$idp], 'saml20-idp-remote');

            // IDP in route unknown
            if (empty($matches)) {
                throw new NoSupportedIDP(
                    Constants::STATUS_REQUESTER,
                    "Routed IdP ($idp) for SP ($sp) is not supported by this proxy."
                );
            }

            // Now we have an IDP to route for, we set it up as the destination IdP
            // This value in the state overrides the value in the authsource config
            // If it was called by an integrating app, it also overrides
            // the 'saml:idp' value set on the parameters of the instance
            $state['saml:idp'] = $idp;

            Logger::debug("[samlRouteSP] SP $sp routed to IDP $idp");
        }

        // If there was no route, the common saml:SP  behaviour applies:
        //  1. If instantiating app had $state['saml:idp'] set, that is used
        //  2. If authsource config had 'idp' set, that is used
        //  3. If AuthnReq had scoping (IDPList), intersection with saml20-idp-remote is calculated
        //  4. If at this point a single idp entityID is left, go there
        //  5. Else, discovery is called

        parent::authenticate($state);
    }


}
