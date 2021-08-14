# ![icon](../images/swassist_icon.png) Plugin "Switch Assistant" pour Jeedom
Le plugin **swassit** permet d'envoyer un ordre d'enclenchement ou de déclenchemenet à un équipement. L'ordre sera ensuite automatiquement répété jusqu'à ce que l'on a un retour confirmant que l'ordre a bien été exécuté.

# 1. Configuration du plugin
Le plugin ne nécessite aucune configuration, il faut juste l’activer.

![Page de configuration du plugin](../images/config_plugin.png)

# 2. Création d'un équipement
Il est possible de créer et configurer un équipement soi-même mais le plus simple est de créer un équipement "vide" puis de le lier au switch qui sera assisté.

## 2.1 Création automatique
Dans cet exemple, nous allons assister le fonctionnement d'un équipement nommé \[bureau\]\[ZW-lampe\] qui a les actions et infos suivantes:

+ **ON**: Action pour allumer la lampe.
+ **OFF**: Action pour eteindre la lampe.
+ **Allumé**: Info binaire qui indique si la lampe est allumée.
+ **Puissance**: Puissance de la lampe.
+ **Consommation**: Energie consommée par la lampe.

![Equipement ZW-lampe](../images/ZW-lampe.png)

### 2.1.1 Création de l'équipement *swassist*
Ouvrir la page de gestion des équipements du plugin *swassist* et cliquer sur le bouton **ajouter**

![Gestion des équipements](../images/avant_creation.png)

+ Saisir le nom de l'équipement
+ Sélectionner l'objet parent
+ Rendre l'équipement visible
+ Activer l'équipement
+ Sauvegarder

![equipement créé](../images/equipement_lampe.png)

### 2.1.2 import de l'équipement à assister

+ Sélectionner le panneau **Commandes**
![panneau commandes avant import](../images/commandes_avant_import.png)

+ Cliquer sur **Importer un équipement**
+ Sélectionner
    + l'équipement à importer
    + La commande qui indique l'état de switch
    + La commande d'enclenchement
    + La commande de déclenchment 

![selection de l'équipement à importer](../images/selection_commandes.png)

+ Cliquer sur *valider*
+ Resélectionner le panneau *Comandes* pour voir les commandes importées

![Les commandes importées](../images/commandes_apres_import.png)

On voit que:
* Une commande liée a été créée pour chaque commande de l'équipement assisté.
* La commande 1103 est la commande d'enclechement.
* La commande 1104 est la commande de déclenchement.
* Etat est l'info de retour pour les commande d'enclenchement et de déclenchement.
* Les comandes d'enclencement et de déclenchement seront répétée au maximum 5 fois toutes les 3 secondes.
* La valeur des options des commandes ont été reprises des comandes liées.

### 2.1.3. L'équipement créé dans le dashboard

![dashboard](../images/dashboard.png)

On voit dans le dashboard que les définition des widgets ont été reprises de l'équipement assisté.

Un click sur l'icône de l'ampoule de l'équipement swassist provoquera l'extinction ou l'allumage de l'équipement assisté. On peut donc rendre l'équipement assisté invisible et l'on utilisera uniquement l'équipement swassist et ses commandes pourront également être utilisées dans les alertes et scénarios à la place des commandes de l'équipement assisté.

## 2.2. Création manuelle

A titre d'exemple, nous allons créer manuellement un équipement swassist identique à celui qui a été créé automatiquemen ci-dessus.

### 2.2.1. Création de l'équipement *swassist*
Ouvrir la page de gestion des équipements du plugin *swassist* et cliquer sur le bouton **ajouter**

![Gestion des équipements](../images/avant_creation.png)

+ Saisir le nom de l'équipement.
+ Sélectionner l'objet parent.
+ Rendre l'équipement visible.
+ Activer l'équipement.
+ Sauvegarder.

![equipement créé](../images/equipement_lampe.png)

### 2.2.2. Ajout de la commande info pour le retour de l'état de la lampe

+ Afficher le panneau **Commandes**.
+ Cliquer sur le bouton **Ajouter une info**.
+ Saisir le nom de la commande (*Etat* dans notre exemple).
+ Sélectionner le sous-type "binaire".
+ Saisir ou sélectionner (en cliquant sur l'icône à droite de champ de saisie) la commande liée.
+ Cliquer sur sauvegarder.

### 2.2.3. Ajout de la commande d'allumage

+ Cliquer sur le bouton **Ajouter une commande**.
+ Saisir le nom de la commande (*ON* dans notre exemple).
+ Sélectionner le nom de la commande de retour d'état (celle que nous avons créé ci-dessus).
+ Saisir ou sélectionner la commande liée (la commande de type action qui allume l'équipement assisté).
+ Sélectionner *ON* dans l'option **Commande** pour indiquer qu'il s'agit d'une commande d'enclenchement.
+ Saisir le nomde de répétitions maximum et l'intervale entre ces répétitions. 
+ Cliquer sur sauvegarder.

### 2.2.4. Ajout de la commande d'extinction

+ Cliquer sur le bouton **Ajouter une commande**.
+ Saisir le nom de la commande (*OFF* dans notre exemple).
+ Sélectionner le nom de la commande de retour d'état (celle que nous avons créé ci-dessus).
+ Saisir ou sélectionner la commande liée (la commande de type action qui éteint l'équipement assisté).
+ Sélectionner *OFF* dans l'option **Commande** pour indiquer qu'il s'agit d'une commande de déclenchement.
+ Saisir le nomde de répétitions maximum et l'intervale entre ces répétitions.
+ Cliquer sur sauvegarder.

### 2.2.5. Ajout des commandes de puissance et consommation

Ces commandes sont optionnelles

+ Cliquer sur le bouton **Ajouter une info**.
+ Saisir le nom de la commande (*puissance* ou *consommation* dans notre exemple).
+ Sélectionner le sous-type qui doit être le même celui de la commande liéée.
+ Saisir les options de la commande (on reprend généralement les option de la commande liée).
+ Cliquer sur sauvegarder.
