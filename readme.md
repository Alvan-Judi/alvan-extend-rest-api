# Extend WP REST API - Apprendre à développer un Plugin pour WordPress

## Introduction

Cette idée de plugin pour WordPress est un bon exemple pour apprendre à en créer un. Vous allez découvrir une méthode de **structuration**, comment ajouter **une page de réglages**, ajouter **des styles** dans l'administration (même si je vous conseille d'utiliser au maximum les styles de base de WordPress pour que l'utilisateur ait l'impression que votre fonctionnalité est native) etc. dans le but de permettre d'ajouter des données à l'API de WordPress.

Vous trouverez également dans ce *readme* quelques informations et éléments importants pour vous guider.

Donc avant d'écumer le code du plugin, il va falloir essayer de le **créer vous même** !

### Le but du plugin

Le but du plugin est de permettre à l'utilisateur final, d'ajouter quelques données supplémentaires aux retours des types de publication de l'API.
On devra vérifier pour chaque post type si  `public` et `show_in_rest` sont à `true`.

Les données additionnelles sont :

- l'url de l'image à la une (si supporté par le type de post)
- le nom de l'auteur
- l'url de l'avatar de l'auteur

 L'utilisateur devra pouvoir activer ces options via une page d'aministration dédiée et lors de la suppression du plugin toutes les options stockées devront être supprimées.

*PS: Le plugin devra être coder en objet.*

### Nos besoins

Avant de commencer à coder quoi que ce soit, il faut se poser quelques instants et réfléchir à quels sont nos besoins.

Nous avons besoin de :

- Créer une page d'administration
- Ajouter des options à cette page
- Ajouter des styles supplémentaires (optionnel)
- Ajouter des données à l'API de WordPress
- Supprimer les données lors de la désinstallation du plugin

Il est important de prendre ce temps la à définir nos besoins pour avoir une vision globale de notre projet avant même de le commencer.

Deuxième chose importante, si vous ne savez pas comment coder les besoins que vous avez listés, il peut être judicieux d'aller à la pêche à l'information dans [la documentation de WordPress](https://codex.wordpress.org/).

Je vais conclure par une comparaison : Imaginez commencer la fabrication d'un meuble sans savoir quels outils utilisés ni avoir de plans. Au bout de la troisième planche vissée laborieusement, vous allez vous rendre compte qu'il fallait commencer par poncer les planches avant de tout monter, puis qu'il aurait peut-être fallu avoir une équerre parce que c'est pas vraiment droit, et que la scie circulaire aurait été nettement plus efficace que la scie sauteuse (on sent le vécu ou pas ?). Après moult démontages, remontages et allers-retours au magasin de bricolage vous allez vous démotiver et abandonner.

C'est exactement pareil ici, si vous foncez tête baissée, vous avez de grande chance de devoir tout recommencer très vite.

**C'est bon pour vous ? Lancez-vous !**

---

Vous bloquez ? Voici quelques éléments pour vous aider.

---

## La structure

Il existe pléthore de possibilités pour structurer votre plugin. Il suffit de regarder le code de différents plugins pour vous rendre compte qu'aucun n'est structuré pareil. Vous devrez simplement réfléchir à ce qui vous va le mieux.

Dans mon cas la structure est :

- assets
  - dist
    - css
  - src
    - css
- includes
- languages

Le dossier **assets** contient tous les fichiers *css*, *scss*, *js* et des images par exemple.

Le dossier **includes** contient tous les fichiers *php* dont nous avons besoin.

Le dossier **languages** porte bien son nom.

Si le plugin devait également modifier des choses en front end, on aurait ajouté des dossiers *admin* et *front* dans assets et includes pour pouvoir bien différencier les deux parties.

## Les classes

Si vous avez bien compris nos besoins vous aurez deviné comment organiser nos fichiers pour que tout soit clair, facilement utilisable et maintenable. (Le choix des noms est toujours matière à discussion, vous l'avez certainement compris si vous avez fouillé dans quelques plugins)

- **Plugin** nous permettra d'instancier toutes les autres classes
- **Settings** nous permettra de fournir des informations sur le plugin aux autres classes
- **Admin** gérera l'affichage dans l'administration
- **Enqueue** qui gérera l'inclusion de fichier css ou js
- **REST_API** pour notre fonctionnalité principale

## L'autoloading

Comme on essaie de respecter les [Coding Standards de WordPress](https://make.wordpress.org/core/handbook/best-practices/coding-standards/), on a besoin de faire appel à un autoloading personnalisé grâce à la fonction `spl_autoload_register`.

On passe en paramètre une fonction qu'on aura créée, qui permet d'automatiquement faire un `require_once` de nos classes php lorsqu'on en a besoin.

## Singleton

Pour éviter que le plugin ne soit lancé plusieurs fois, on utilise un *Singleton*

```php
class Plugin {

    /**
     * Plugin instance
     */
    private static single_instance = null;

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

On lance ensuite le plugin et les différents hooks d'activation ou de désactivation.

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

- **[init](https://developer.wordpress.org/reference/hooks/init/)** pour instancier toutes nos classes
- **[plugin_action_links_{plugin_file}](https://developer.wordpress.org/reference/hooks/plugin_action_links_plugin_file/)** pour ajouter un lien vers les réglages du plugin depuis la page de liste des plugins installés.
- **[admin_menu](https://developer.wordpress.org/reference/hooks/admin_menu/)** pour ajouter notre page de réglages à l'administration
- **[admin_init](https://developer.wordpress.org/reference/hooks/admin_init/)** pour déclarer les options de notre plugin
- **[admin_enqueue_scripts](https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/)** pour ajouter nos styles ou scripts
- **[rest_api_init](https://developer.wordpress.org/reference/hooks/rest_api_init/)** pour modifier les terminaisons de types de publication de l'API

## Liste de fonctions à utiliser

- **[add_menu_page()](https://developer.wordpress.org/reference/functions/add_menu_page/)** pour ajouter notre page de réglages
- **[register_setting()](https://developer.wordpress.org/reference/functions/register_setting/)** pour enregistrer notre nom d'option
  - **[add_settings_section()](https://developer.wordpress.org/reference/functions/add_settings_section/)** pour définir une section de réglages liée à notre option
  - **[add_settings_field()](https://developer.wordpress.org/reference/functions/add_settings_field/)** pour enregistrer nos options par type de post
- **[get_rest_url()](https://developer.wordpress.org/reference/functions/get_rest_url/)** nous permet de générer l'url de nos terminaisons de post types
- **[settings_fields()](https://developer.wordpress.org/reference/functions/settings_fields/)** génère pour nous des champs cachés pour faire lien avec notre option et également un champ *nonce*  avec `wp_nonce_field()`;
- **[do_settings_section()](https://developer.wordpress.org/reference/functions/do_settings_sections/)** qui va appeler chacun des champs qu'on a enregistré pour notre section
- **[submit_button()](https://developer.wordpress.org/reference/functions/submit_button/)** 🤷
- **[wp_enqueue_style()](https://developer.wordpress.org/reference/functions/wp_enqueue_style/)** pour inclure nos styles
- **[load_plugin_textdomain()](https://developer.wordpress.org/reference/functions/load_plugin_textdomain/)** pour charger nos chaînes de caractères traduisibles
- **[register_rest_field()](https://developer.wordpress.org/reference/functions/register_rest_field/)** pour ajouter nos champs supplémentaires à l'API

[...] et d'autres encore, mais qui sont plus *obligatoire* à utiliser, comme pour récupérer l'url de l'image à la une ou l'url de l'avatar de l'auteur.

Vous pourriez très bien par exemple ne pas utiliser *register_setting()* et toutes les fonctions qui en découlent mais vous devrez vous-même développer tout le formulaire avec la sauvegarde des options et la vérification du *nonce*. Ce serait contre-productif et vous ferait écrire un code beaucoup plus lourd.

## Internationalisation ou i18n

Déjà, pourquoi **i18n** ?

18 représente le nombre de lettre comprise entre le *i* et le *n* d'internationalization. (oui la version anglaise, ce qui ne change rien avec nous qui avons un *s*, c'est le même nombre de caractères).

Ne vous angoissez donc pas si vous tombez sur cette appellation ici et là (notamment *date_i18n()*), c'est juste un raccourci et pas un calcul mathématique compliqué :).

---

**Note**
Si vous n'avez pas utilisé les fonctions d'internationalisation de WordPress et que vous avez mis vos chaînes de caractères directement dans le HTML ça ne marchera pas.
voir : [__()](https://developer.wordpress.org/reference/functions/__/), [_e()](https://developer.wordpress.org/reference/functions/_e/), [_x()](https://developer.wordpress.org/reference/functions/_x/)

`_x()` permet de spécifier un contexte. Si par exemple vous avez dans votre plugin une fois le mot *avocat* pour le fruit et une autre fois pour le métier vous pouvez donner une indication de traduction, le contexte.

ex:

```php
_x( 'Avocat', 'fruit', 'alvan-extend-wp-rest-api' );
```

---

Pour intertan... pour internatilis.. pour inartertionalis... pour rendre traduisible votre plugin vous pouvez utiliser [wp-cli](https://wp-cli.org/fr/). Si vous ne l'avez déjà pas installé, suivez simplement les instructions.

Pour créer un fichier *.pot* avec les chaines de caractères traduisibles de votre plugin il suffit d'exécuter la commande suivante depuis votre plugin.

```bash
wp i18n make-pot . languages/alvan-extend-wp-rest-api.pot
```

Le fichier généré pourra être utilisé par [poedit](https://poedit.net) ou des plugins WordPress comme [Loco Translate](https://fr.wordpress.org/plugins/loco-translate/) pour créer les traductions de langues de votre choix.

## PHP_CodeSniffer, WordPress Coding Standards et compagnie

Pour s'assurer que notre code soit uniforme et qu'il respecte les standards de WordPress et PHP on peut installer via *Composer* différents composants. J'ai ici utilisé :

- [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) permet de détecter les violations aux standards de code PHP
- [WordPress Coding Standards](https://github.com/WordPress/WordPress-Coding-Standards) le même principe mais pour WordPress
- [PHP_CodeSniffer VariableAnalysis](https://github.com/sirbrillig/phpcs-variable-analysis) permet de vérifier que nos variables sont bien utilisés et crées
- [PHP Compatibility Coding Standards for PHP CodeSniffer](https://github.com/PHPCompatibility/PHPCompatibility) permet de tester la compatibilité des versions PHP avec notre code
- [PHP_CodeSniffer Standards Composer Installer Plugin](https://github.com/Dealerdirect/phpcodesniffer-composer-installer) permet de charger tout ce petit monde automatiquement pour nous

Vous pouvez réutiliser le **composer.json** de ce plugin dans votre projet et via votre terminal exécuter la commande :

`composer install`

Pendant l'installation, récupérez également le fichier phpcs.xml et modifiez les valeurs pour correspondre à votre projet.
Notamment:

```xml
<ruleset name="WordPress Coding Standards for Alvan Extend REST API plugin">
[...]
<config name="text_domain" value="alvan-extend-wp-rest-api,default"/>
[...]
<file>./includes</file>
<file>./autoload.php</file>
<file>./alvan-extend-wp-rest-api.php</file>
```

Vous pourrez ensuite lancer la commande suivante :

```bash
# Chemin vers l'exécutable phpcs | chemin vers votre fichier de configuration
./vendor/bin/phpcs --standard=phpcs.xml
```

Vous aurez ensuite un retour d'erreurs (ou pas, mais j'en doute) pour chacun de vos fichiers. Quelque chose comme ça :

```bash
FILE: /XXX/alvan-extend-wp-rest-api/includes/class-rest-api.php
------------------------------------------------------------------------------------------------------------------------------------
FOUND 0 ERRORS AND 3 WARNINGS AFFECTING 3 LINES
------------------------------------------------------------------------------------------------------------------------------------
  91 | WARNING | [x] Please spell "WordPress" correctly. Found 1 misspelling(s): Wordpress
     |         |     (WordPress.WP.CapitalPDangit.Misspelled)
 102 | WARNING | [x] Please spell "WordPress" correctly. Found 1 misspelling(s): Wordpress
     |         |     (WordPress.WP.CapitalPDangit.Misspelled)
 113 | WARNING | [x] Please spell "WordPress" correctly. Found 1 misspelling(s): Wordpress
     |         |     (WordPress.WP.CapitalPDangit.Misspelled)
------------------------------------------------------------------------------------------------------------------------------------
PHPCBF CAN FIX THE 3 MARKED SNIFF VIOLATIONS AUTOMATICALLY
------------------------------------------------------------------------------------------------------------------------------------
```

On vous indique que PHPCBF peut corriger des erreurs automatiquement, il vous suffit donc d'utiliser la commande suivante:

`./vendor/bin/phpcbf --standard=phpcs.xml`

qui fera la même chose que la commande précédente mais en corrigeant les erreurs en même temps. Plutôt bien non ? :)

Il en restera évidemment certaines à corriger à la main, et peut être certaines à laisser. Dans mon cas j'ai volontairement décider de ne pas prendre en compte ces remarques pour les fichiers index.php qui sont simplement là pour une question de sécurité : éviter que quelqu'un puisse lister le contenu de vos dossiers si la configuration de votre serveur ne l'empêche pas.

Voilà ! Je crois que vous êtes maintenant prêt à développer vos propres plugin toujours avec le même procédé:

- Définition des besoins
- Estimation de vos connaissances PHP ou WordPress concernant ces besoins
- Recherche dans la documentation et test de vos découvertes
- Structuration de votre plugin
- Développement
- Vérification de votre code avec des outils comme PHPCS
- Internationalisation de votre plugin (oui je vous l'ai mis avant PHPCS dans ce document, mais PHPCS peut vous faire des retours sur des mauvais *text domain* si vous avez fait des erreurs de frappe, donc faites le avant)
