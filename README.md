Installation
============

### Get the bundle using composer

Add GlavwebCaptchaBundle by running this command from the terminal at the root of
your Symfony project:

```bash
php composer.phar require glavweb/captcha-bundle
```

### Enable the bundle

To start using the bundle, register the bundle in your application's kernel class:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Glavweb\CaptchaBundle\GlavwebCaptchaBundle(),
        // ...
    );
}
```
### Configure the bundle

Add config:

```yaml
#  app/config/config.yml
glavweb_captcha: ~
```

Add routing:

```yaml
#  app/config/routing.yml

glavweb_captcha_routing:
    resource: "@GlavwebCaptchaBundle/Resources/config/routing.yml"
```