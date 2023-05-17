<?php

declare(strict_types=1);

namespace SimpleSAML\Module\samlroutesp\Auth\Source;



use Exception;
use SimpleSAML\Configuration;
use SimpleSAML\Logger;



class samlRouteSP extends \SimpleSAML\Module\saml\Auth\Source\SP
{

    /**
     * A map of remote SP entityIDs to remote IDP entityIDs pairings to route
     *
     * @var string[]
     */
    private $routes = [];


    /**
     * The IdP the user is allowed to log into from any SP, as defined in the authsource.
     *
     * @var string|null  The IdP the user can log into, or null if we check the mappings.
     */
    private $staticidp;


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

        //We check the authsource config, to see if there is a fixed IDP. If there is, we avoid mapping  // TODO: is this the behaviour we want? or mapping does override all?
        $this->staticidp = $metadata->getString('idp', null);

    }



    /**
     * Start login.
     *
     * This function checks the remote SP and establishes remote IDP to follow. Then, the
     * original implementation it overloads saves the information about the login,
     * and redirects to the IdP.
     *
     * @param array &$state  Information about the current authentication.
     * @return void
     */
    public function authenticate(&$state)
    {
        assert(is_array($state));


        // The remote SP entityID
        $sp = $state['SPMetadata']['entityid'];
        $idp = '';

        // If idp config item is set, it overrides the behaviour of this
        if( isset($this->staticidp))
            Logger::debug('[samlRouteSP] idp parameter at the authsource is set '.$this->staticidp.': overriding any mapping');
        else {

            // If the SP is routed to a specific IdP
            if(array_key_exists($sp, $this->routes))
                $idp = $this->routes[$sp];

            // TODO: check that the idp exists or throw a specific routing
            //       exception (maybe not needed, the parent function does
            //       it already, it's just that the message is not as specific for this source)

            // If now we have an IDP to route for, we set it up as the destination IdP
            if($idp !== NULL && $idp != "")
                // This value in the state overrides the value in the authsource config
                $state['saml:idp'] = $idp;
        }

        parent::authenticate($state);
    }


}
