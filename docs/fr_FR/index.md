# ![icon](../images/swassist_icon.png) Plugin "Switch Assistant" pour Jeedom
Le plugin **swassit** permet d'envoyer un ordre d'enclenchement ou de déclenchemenet à un équipement. L'ordre sera ensuite automatiquement répété jusqu'à ce que l'on a un retour confirmant que l'ordre a bien été exécuté.

# 1. Fonctionnement du plugin

## 1.1. Les commandes d'assistance

Trois commandes sont nécessaires au fonctionnement d'un équipement swassist. Une de type *info* et deux de type *action*
+ **L'info**<br>
  Cette commande représente l'état de l'équipement assisté. Cette commande doit avoir le sous-type *Binaire* et être liée à la commande de l'équipement assisté qui indique si cet équipement est enclenché et déclenché.
+ **Les deux actions**<br>
  Ces deux commandes sont les commandes qui vont enclencher ou déclencher l'équipement assisté. Ces commandes doivent être liées aux commandes d'enclenchement et déclenchement de l'équipement assisté.
  
  Trois paramètres en définissent le fonctionnment:
  + **Commande**<br>
    Ce paramètre indique si la commande est la commande d'enclenchement ou de déclenchement de l'équipement assisté. Une commande de type action n'ayant ni la valeur "ON" ni la valeur "OFF" ne sera pas assistée.
  + **Répétitions**<br>
    Le nombre maximun de répétions de l'ordre.
  + **Délai**<br>
    Le temps d'attente, en secondes, entre deus tentatives.
    
  En outre, il faudra indiquer, pour chacune de ces deux commandes, quelle est l'info qui indique l'état de l'équipement assisté. 

## 1.2. Info pour le comptage de répétitions

Une commande *info* peut être créée en cliquant sur le bouton `+ Nb tentatives` dans le panneau *Commandes* ou en sélectionnant `Créer Compteur de tentatives` lors de l'importation d'un équipement à assister.

Cette info indique le nombre de tentatives qui ont été effectuées lors du dernier enclenchement/déclenchement de l'équipement. Cette valeur sera négative si l'enclenchement ou le déclenchement a échoué. L'info contiendra donc la valeur
+ 1 : Si La commande à été effecutée du premier coup et qu'aucune répétition n'a été nécessaires.
+ 3 : S'il y a eu des problèmes lors de l'éxécution de la commande et qu'il a fallut relancer la commande à deux reprises.
+ -6 : S'il l'équipement n'as pas pu être enclenché ou déclenché malgré 6 répétitions.

Par défaut, cette info est visible, historisée sans lissage et le graphique de cet historique est en mode barre

## 1.3. Info sur le statut

Une commande "info" peut être créée en cliquent sur le bouton `+ Statut` dans le panneau *Commandes* ou en sélectionnant `Créer statut` lors de l'importation d'un équipement à assister.

Cette info peut prendre trois valeurs:
+ 0 : Une commande d'enclenchement ou de déclenchement est en cour d'exécution.
+ 1 : La dernière commande d'enclenchement ou de déclenchement a été exécutée correctement (après 0 ou plusieurs répétitions)
+ 2 : La dernière commande d'enclenchement ou de déclenchement a échoué après avoir effectué le nombre maximum de répétitions.

Par défaut, cette info n'est pas visible, historisée sans lissage et le graphique de cet historique est en mode barre

# 2. Configuration du plugin
Le plugin ne nécessite aucune configuration, il faut juste l’activer.

![Page de configuration du plugin](../images/config_plugin.png)

# 3. Création d'un équipement
Il est possible de créer et configurer un équipement soi-même mais le plus simple est de créer un équipement "vide" puis de le lier au switch qui sera assisté.

## 3.1 Création automatique
Dans cet exemple, nous allons assister le fonctionnement d'un équipement nommé \[bureau\]\[ZW-lampe\] qui a les actions et infos suivantes:

+ **ON**: Action pour allumer la lampe.
+ **OFF**: Action pour eteindre la lampe.
+ **Allumé**: Info binaire qui indique si la lampe est allumée.
+ **Puissance**: Puissance de la lampe.
+ **Consommation**: Energie consommée par la lampe.

![Equipement ZW-lampe](../images/ZW-lampe.png)

### 3.1.1 Création de l'équipement *swassist*
Ouvrir la page de gestion des équipements du plugin *swassist* et cliquer sur le bouton `ajouter`

![Gestion des équipements](../images/avant_creation.png)

+ Saisir le nom de l'équipement
+ Sélectionner l'objet parent
+ Rendre l'équipement visible
+ Activer l'équipement
+ Sauvegarder

![equipement créé](../images/equipement_lampe.png)

### 3.1.2 import de l'équipement à assister

+ Sélectionner le panneau **Commandes**
![panneau commandes avant import](../images/commandes_avant_import.png)

+ Cliquer sur `Importer un équipement`
+ Sélectionner
    + l'équipement à importer
    + La commande qui indique l'état de switch
    + La commande d'enclenchement
    + La commande de déclenchment 
    + Sélectioner `Créer compteur de tentatives` si l'on désire avoir une info qui indique combien de fois la commande à du être envoyée pour le dernier enclenchement ou déclenchement.
    + Sélectionner `Créer statut` si l'on désire avoir une info qui indiqe le statut de l'exécution de la dernière commande.

![selection de l'équipement à importer](../images/selection_commandes.png)

+ Cliquer sur `valider`
+ Resélectionner le panneau *Commandes* pour voir les commandes importées

![Les commandes importées](../images/commandes_apres_import.png)

On voit que:
* Une commande liée a été créée pour chaque commande de l'équipement assisté.
* La commande 1159 est la commande d'enclechement.
* La commande 1160 est la commande de déclenchement.
* Etat est l'info de retour pour les commande d'enclenchement et de déclenchement.
* Les comandes d'enclencement et de déclenchement seront répétée au maximum 5 fois toutes les 3 secondes.
* La valeur des options des commandes ont été reprises des comandes liées.
* La commande 1164 qui contiendra le d'envois de la commande lors du dernier enclanchement/déclenchement a été créée

### 3.1.3. L'équipement créé dans le dashboard

![dashboard](../images/dashboard.png)

On voit dans le dashboard que les définition des widgets ont été reprises de l'équipement assisté et qu'une info *Nb tentatives* a été ajoutée.

Un click sur l'icône de l'ampoule de l'équipement swassist provoquera l'extinction ou l'allumage de l'équipement assisté. On peut donc rendre l'équipement assisté invisible et l'on utilisera uniquement l'équipement swassist et ses commandes pourront également être utilisées dans les alertes et scénarios à la place des commandes de l'équipement assisté.

## 3.2. Création manuelle

A titre d'exemple, nous allons créer manuellement un équipement swassist identique à celui qui a été créé automatiquemen ci-dessus.

### 3.2.1. Création de l'équipement *swassist*
Ouvrir la page de gestion des équipements du plugin *swassist* et cliquer sur le bouton **ajouter**

![Gestion des équipements](../images/avant_creation.png)

+ Saisir le nom de l'équipement.
+ Sélectionner l'objet parent.
+ Rendre l'équipement visible.
+ Activer l'équipement.
+ Sauvegarder.

![equipement créé](../images/equipement_lampe.png)

### 3.2.2. Ajout de la commande info pour le retour de l'état de la lampe

+ Afficher le panneau **Commandes**.
+ Cliquer sur le bouton `Ajouter une info`.
+ Saisir le nom de la commande (*Etat* dans notre exemple).
+ Sélectionner le sous-type *binaire*.
+ Saisir ou sélectionner (en cliquant sur l'icône à droite de champ de saisie) la commande liée.
+ Cliquer sur sauvegarder.

### 3.2.3. Ajout de la commande d'allumage

+ Cliquer sur le bouton `Ajouter une commande`.
+ Saisir le nom de la commande (*ON* dans notre exemple).
+ Sélectionner le nom de la commande de retour d'état (celle que nous avons créé ci-dessus).
+ Saisir ou sélectionner la commande liée (la commande de type action qui allume l'équipement assisté).
+ Sélectionner *ON* dans l'option **Commande** pour indiquer qu'il s'agit d'une commande d'enclenchement.
+ Saisir le nomde de répétitions maximum et l'intervale entre ces répétitions. 
+ Cliquer sur sauvegarder.

### 3.2.4. Ajout de la commande d'extinction

+ Cliquer sur le bouton `Ajouter une commande`.
+ Saisir le nom de la commande (*OFF* dans notre exemple).
+ Sélectionner le nom de la commande de retour d'état (celle que nous avons créé ci-dessus).
+ Saisir ou sélectionner la commande liée (la commande de type action qui éteint l'équipement assisté).
+ Sélectionner *OFF* dans l'option **Commande** pour indiquer qu'il s'agit d'une commande de déclenchement.
+ Saisir le nomde de répétitions maximum et l'intervale entre ces répétitions.
+ Cliquer sur sauvegarder.

### 3.2.5. Ajout des commandes de puissance et consommation

Ces commandes sont optionnelles.

+ Cliquer sur le bouton `Ajouter une info`.
+ Saisir le nom de la commande (*puissance* ou *consommation* dans notre exemple).
+ Sélectionner le sous-type qui doit être le même celui de la commande liéée.
+ Saisir les options de la commande (on reprend généralement les option de la commande liée).
+ Cliquer sur sauvegarder.

### 3.2.6. Ajout de l'info du nombre de tentatives

Cette commande est optionnelle.

+ Cliquer sur le bouton `+ Nb tentatives`

### 3.2.6. Ajout de l'info du statut

Cette commande est optionnelle.

+ Cliquer sur le bouton `+ Statut`
