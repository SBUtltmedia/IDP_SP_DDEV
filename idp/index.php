<?php
require 'vendor/autoload.php';

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Credential\X509Credential;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\Attribute;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Assertion\AuthnContext;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\Protocol\Status;
use LightSaml\Model\Protocol\StatusCode;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\SamlConstants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

// Hardcoded user data
$users = [
    'user1' => [
        'password' => 'password1',
        'attributes' => [
            'email' => 'user1@example.com',
            'role' => 'admin',
        ],
    ],
];

// Load credentials (replace with your certificate/key paths)
$certificate = X509Certificate::fromFile(__DIR__ . '/idp.crt');
$privateKey = KeyHelper::createPrivateKey(__DIR__ . '/idp.key', '', true);
$credential = new X509Credential($certificate, $privateKey);

// Handle requests
$request = Request::createFromGlobals();
$path = $request->getPathInfo();

switch ($path) {
    case '/sso':
        handleSsoEndpoint($request, $users, $credential);
        break;
    case '/metadata':
        serveMetadata();
        break;
    default:
        echo "Mock SAML IdP is running. Use endpoints: /sso, /metadata";
        break;
}

// Handle SAML AuthnRequest
function handleSsoEndpoint($request, $users, $credential) {
    // Parse SAML AuthnRequest
    $samlRequest = $request->get('SAMLRequest');
    if (!$samlRequest) {
        die('Missing SAMLRequest parameter');
    }

    $decoded = base64_decode($samlRequest);
    $authnRequest = new AuthnRequest();
    $deserializationContext = new \LightSaml\Model\Context\DeserializationContext();
    $deserializationContext->getDocument()->loadXML($decoded);
    $authnRequest->deserialize($deserializationContext->getDocument()->firstChild, $deserializationContext);

    // Authenticate user (simplified for demo)
    $username = $request->get('username', 'user1'); // Replace with form input
    $password = $request->get('password', 'password1');
    $user = $users[$username] ?? null;

    if (!$user || $user['password'] !== $password) {
        die('Authentication failed');
    }

    // Build SAML Response
    $response = new Response();
    $response->setID(\LightSaml\Helper::generateID());
    $response->setIssueInstant(new \DateTime());
    $response->setIssuer(new Issuer('https://mock-idp.example.com'));
    $response->setStatus(new Status(new StatusCode(SamlConstants::STATUS_SUCCESS)));

    // Build Assertion
    $assertion = new Assertion();
    $assertion->setID(\LightSaml\Helper::generateID());
    $assertion->setIssueInstant(new \DateTime());
    $assertion->setIssuer(new Issuer('https://mock-idp.example.com'));

    // Subject
    $nameID = new NameID($user['attributes']['email'], SamlConstants::NAME_ID_FORMAT_EMAIL);
    $assertion->setSubject((new Subject())->setNameID($nameID));

    // Conditions
    $assertion->setConditions((new Conditions())
        ->setNotBefore(new \DateTime())
        ->setNotOnOrAfter(new \DateTime('+5 minutes'))
    );

    // AuthnStatement
    $assertion->addItem((new AuthnStatement())
        ->setAuthnInstant(new \DateTime())
        ->setAuthnContext((new AuthnContext())
            ->setAuthnContextClassRef(SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT))
    );

    // Attributes
    $attributeStatement = new AttributeStatement();
    foreach ($user['attributes'] as $name => $value) {
        $attributeStatement->addAttribute(new Attribute($name, $value));
    }
    $assertion->addItem($attributeStatement);

    // Sign Assertion
    $assertion->setSignature(new SignatureWriter($credential->getCertificate(), $credential->getPrivateKey()));

    // Add Assertion to Response
    $response->addAssertion($assertion);

    // Serialize and return Response
    $serializationContext = new \LightSaml\Model\Context\SerializationContext();
    $response->serialize($serializationContext->getDocument(), $serializationContext);
    $xml = $serializationContext->getDocument()->saveXML();

    $httpResponse = new HttpResponse($xml);
    $httpResponse->headers->set('Content-Type', 'application/xml');
    $httpResponse->send();
}

// Serve IdP metadata
function serveMetadata() {
    $metadata = file_get_contents(__DIR__ . '/idp_metadata.xml');
    $httpResponse = new HttpResponse($metadata);
    $httpResponse->headers->set('Content-Type', 'application/xml');
    $httpResponse->send();
}