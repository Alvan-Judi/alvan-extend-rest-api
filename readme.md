# Extend REST API - Comment développer un Plugin pour WordPress

## Introduction

Ce plugin pour WordPress est un bon exemple pour apprendre à en créer un. Nous verrons comment structurer, ajouter une page de réglages, ajouter des styles dans l'administration (même si je vous conseil d'utiliser au maximum les styles de base de WordPress pour que l'utilisateur ait l'impression que votre fonctionnalité est native), créer un fichier de traduction etc. dans le but de permettre d'ajouter des données à l'API de WordPress.

### Le but du plugin

Le but du plugin est de permettre à l'utilisateur final, d'ajouter quelques données supplémentaires aux retours des post types de l'API. Ces données sont :

- l'url de l'image à la une (si supporté par le type de post)
- le nom de l'auteur
- l'url de l'avatar de l'auteur

 L'utilisateur devra pouvoir le faire via une page d'aministration dédiée et lors de la suppression du plugin tous les réglages stockées du plugin devront être supprimé.

### Nos besoins

Avant de commencer à coder quoi que ce soit, il faut se poser quelques instants et réfléchir à quelle sont nos besoins.

Nous avons besoin de :

- Créer une page d'aministration
- Ajouter des options à cette page
- Ajouter des styles supplémentaires
- Ajouter des données à l'API de WordPress
- Supprimer les données lors de la désinstallation du plugin

Il est important de prendre ce temps la a définir nos besoins pour avoir une vision globale de notre projet avant même de le commencer.

Deuxième chose importante, si vous ne savez pas comment coder les besoins que vous avez listé, il peut être judicieux d'aller à la pêche à l'information dans [la documentation de WordPress](https://codex.wordpress.org/).

Je vais conclure par une comparaison : Imaginez commencer la fabrication d'un meuble sans savoir quels outils utilisés ni avoir de plans. Au bout de la troisème planche vissé laborieusement, vous allez vous rendre compte qu'il fallait commencer par poncer les planches avant de toute monter, puis qu'il aurait peut être fallu avoir une équerre parce que c'est pas vraiment droit, et que la scie circulaire aurait été nettement plus efficace que la scie sauteuse (on sent le vécu ou pas ?). Après moult démontage, remontage et allez-retours au magasin de bricolage vous allez vous démotivez et abandonner.

C'est exactement pareil ici, si vous foncez tête baissée, vous avez de grande chance de devoir tout recommencer très vite.

**C'est bon pour vous ? C'est parti !**

---

## La structure

Maintenant qu'on a nos besoins d'écrit et qu'on a écumé la documentation de WordPress on commence à créer notre structure de base. Notre plugin sera en codé en objet et dans le respect des [codings standard de WordPress](https://make.wordpress.org/core/handbook/best-practices/coding-standards/).

### La base

Un plugin WordPress doit être un fichier php ou un dossier placé dans le dossier *plugins* de WordPress. 