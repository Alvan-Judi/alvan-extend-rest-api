# Extend REST API - Apprendre à développer un Plugin pour WordPress

## Introduction

Cette idée de plugin pour WordPress est un bon exemple pour apprendre à en créer un. Vous allez découvrir une méthode de **structuration**, comment ajouter **une page de réglages**, ajouter **des styles** dans l'administration (même si je vous conseil d'utiliser au maximum les styles de base de WordPress pour que l'utilisateur ait l'impression que votre fonctionnalité est native) etc. dans le but de permettre d'ajouter des données à l'API de WordPress.

Vous trouverz également dans le ce *readme* quelques informations et éléments important pour vous guider.

Donc avant d'écumer le code du plugin. Il va falloir essayer de le **créer vous même** !

### Le but du plugin

Le but du plugin est de permettre à l'utilisateur final, d'ajouter quelques données supplémentaires aux retours des post types de l'API.
On devra vérifier pour chaque post type si  `public` et `show_in_rest` sont à `true` 

Les données additonels sont :

- l'url de l'image à la une (si supporté par le type de post)
- le nom de l'auteur
- l'url de l'avatar de l'auteur

 L'utilisateur devra pouvoir activer ces options via une page d'aministration dédiée et lors de la suppression du plugin toutes les options stockées devront être supprimé.

*PS: Le plugin devra être coder en objet.*

### Nos besoins

Avant de commencer à coder quoi que ce soit, il faut se poser quelques instants et réfléchir à quelle sont nos besoins.

Nous avons besoin de :

- Créer une page d'aministration
- Ajouter des options à cette page
- Ajouter des styles supplémentaires (optionnel)
- Ajouter des données à l'API de WordPress
- Supprimer les données lors de la désinstallation du plugin

Il est important de prendre ce temps la a définir nos besoins pour avoir une vision globale de notre projet avant même de le commencer.

Deuxième chose importante, si vous ne savez pas comment coder les besoins que vous avez listé, il peut être judicieux d'aller à la pêche à l'information dans [la documentation de WordPress](https://codex.wordpress.org/).

Je vais conclure par une comparaison : Imaginez commencer la fabrication d'un meuble sans savoir quels outils utilisés ni avoir de plans. Au bout de la troisème planche vissé laborieusement, vous allez vous rendre compte qu'il fallait commencer par poncer les planches avant de toute monter, puis qu'il aurait peut être fallu avoir une équerre parce que c'est pas vraiment droit, et que la scie circulaire aurait été nettement plus efficace que la scie sauteuse (on sent le vécu ou pas ?). Après moult démontage, remontage et allez-retours au magasin de bricolage vous allez vous démotivez et abandonner.

C'est exactement pareil ici, si vous foncez tête baissée, vous avez de grande chance de devoir tout recommencer très vite.

**C'est bon pour vous ? Lancez-vous !**

---

Vous bloquez ? Voici quelques éléments pour vous aider.

---

## La structure

Il existe pléthore de possibilités pour structurer votre plugin. Il suffit de regarder le code de différent plugin pour vous rendre compte qu'aucun n'est structuré pareil. Vous devez simplement réfléchir à ce qui vous va le mieu.

Dans mon cas la structure est :

- assets
  - dist
    - css
  - src
    - css
- includes
- languages

Le dossier **assets** contient tous les fichiers *css*, *scss*, *js* et des images par exemple.

Le dossier **includes** contient tous les fichiers *php* dont nous avons besoins.

Le dossier **languages** dit tout avec son nom.

Si le plugin devait également modifier des choses en front end, on aurait ajouté un dossier admin dans assets et un autre dans includes pour pouvoir bien différencier les deux parties.

## Les classes

Si vous avez bien compris nos besoins vous aurez deviné comment organiser nos fichiers pour que tout soit clair, facilement utilisable et maintenable. (Le choix des noms est toujours matière à discussion, vous l'avez certainement compris si vous avez fouillé dans quelques plugins)

- **Plugin** nous permettra d'instancier toutes les autres classes
- **Settings** nous permettra de fournir des informations sur le plugin aux autres classes
- **Admin** gérera l'affichage dans l'administration
- **Enqueue** qui gérera l'inclusion de fichier css ou js
- **REST_API** pour notre fonctionnalité principale

## L'autoloading

Comme on essai de respecter les [Coding Standards de WordPress](https://make.wordpress.org/core/handbook/best-practices/coding-standards/), on a besoin de faire appelle à un autoloading personnalisé grâce à la fonction `spl_autoload_register`.

On passe en paramètre une fonction qu'on aura crée, qui permet d'automatiquement faire un `require_once` de nos classes php lorsqu'on en a besoin.

## Singleton

Pour éviter que le plugin ne soit lancé plusieurs fois, on utilise un *Singleton*

```php
class Plugin {

    /**
     * Plugin instance
     */
    protected static single_instance = null;

    /**
     * Creates or returns an instance of this class.
     */
    public static function get_instance() {
        if ( null === self::$single_instance ) {
            self::$single_instance = new self();
        }

        return self::$single_instance;
    }
}
```

On lance ensuite le plugin et les différents hook d'activation ou de désactivation.

```php
function aera_plugin() {
    return Plugin::get_instance();
}

// Launch
add_action( 'plugins_loaded', array( aera_plugin(), 'hooks' ) );

// Activate hook
register_activation_hook( __FILE__, array( aera_plugin() , 'plugin_activate' ) );
```

## Liste de hooks à utiliser

- **init** pour instancier toutes nos classes
- **plugin_action_links_{plugin_name}** pour ajouter un lien vers les réglages du plugin depuis la page de liste des plugins installés.
- **admin_menu** pour ajouter notre page de réglages à l'administration
- **admin_init** pour déclarer les options de notre plugin
- **admin_enqueue_scripts** pour ajouter nos styles ou scripts
- **rest_api_init** pour modifier les terminaisons de types de publication de l'API

