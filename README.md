# Cold Breeze backend bundle

Sylius integration bundle for Cold Breeze app

# Installation

Add dependency:

    $ composer require im0rtality:coldbreeze:dev-master

Register bundle in `app/AppKernel.php`:

    $bundles = array(
        ...
        new \Im0rtality\ColdBreezeBundle\Im0rtalityColdBreezeBundle(),
    );

Register route in `app/condig/routing.yml`:

    coldbreeze:
        resource: "@Im0rtalityColdBreezeBundle/Resources/config/routing.yml"
        prefix: /coldbreeze
