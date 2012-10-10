# Composer Installer for Sledgehammer Framework modules

Using this installer, all [Composer][http://getcomposer.org/] packages of `"type" : "sledgehammer-module"`
will be installed in `sledgehammer/` folder.

## Package requirements
Add the "sledgehammer/core" as dependancy to your package.

```
    "require": {
        "sledgehammer/core": "*"
    }
 ```

You can also add "sledgehammer/composer-installer" directly as dependancy, but then sledgehammer/core won't be installed as depencancy 
(which the package probably relies on, being a "sledgehammer-module" and all)


## Installing Composer

```
curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin
```

##  Instaling the core module via composer

```
cd  /your/project/
composer.phar install sledgehammer/core
```