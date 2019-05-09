# RMQ gfdgdfgd

Ce service peut être installé sur Lumen.

Il fournit des fonctionnalitées avec RabbitMQ :

- Creation de queue
- Communication RPC
- Mise en place de "listen" (consommateur de queue)

Fonctionnement RabbitMQ : [ici](explication.md)

## Prérequis

- Installation de RMQ sur votre serveur
  
  Lien : [rabbitmq](https://www.rabbitmq.com/#getstarted)
  


## Installation

1. Ajouter dans composer le lien entre composer et ce service

```
    "repositories": [
        ...
        {
            "type": "git",
            "url":  "https://github.com/BloomPhilippe/RMQ.git"
        }
    ],
```

2. Ajouter ce service dans les packages requis

```
"BloomPhilippe/RMQ": "*"
```

3. Ajouter la config ci-dessous dans votre composer.json

```
    "config": {
        "optimize-autoloader": true,
        "discard-changes": true,
        "secure-http":false
    }
```

4. Effectuer un composer update (ou install)


## Configuration

1. Ajouter le provider dans boostrap/app.php

```
$app->register(\BloomPhilippe\RMQ\ServiceProvider::class);
```

2. Activer le provider de baase de Lumen dans boostrap/app.php en décommentant la ligne ci-dessous

```
$app->register(App\Providers\AppServiceProvider::class);
```

3. Ajouter un extend pour étendre la Class RMQService du service dans /app/Providers/AppServiceProvider.php, 
dans la fonction register 


```
use App\Services\RMQService;
```

```
$this->app->extend('rmqService', function() {
            return new RMQService();
});
```

4. Ajouter votre Class que va étendre dans app/Services/RMQ

Dans cette Classe, il faut juste créer la fonction listenCallback()

Exemple : 

```
<?php
namespace App\Services;

use BloomPhilippe\RMQ\Services\RMQService as BaseRMQService;


class RMQService extends BaseRMQService
{
    public function __construct()
    {
        parent::__construct();
    }


    public function listenCallback(AMQPMessage $response)
    {
        try {
            echo $response->body;
            $response->delivery_info['channel']->basic_ack($response->delivery_info['delivery_tag']);
        } catch (Exception $e) {
            $response->delivery_info['channel']->basic_nack($response->delivery_info['delivery_tag']);
        }
    }

}
```

## Utilisation



