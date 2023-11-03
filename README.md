# SAML route SP

Simple SAML PHP module that, when acting as a proxy, enables routing specific remote SPs to specific remote IDPs


# Forward the response authnContext in SSP<2.1
Just add this as a PHP filter for the hosted idp
$state['saml:AuthnContextClassRef'] = $state['saml:sp:AuthnContext'];
