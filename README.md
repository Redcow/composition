# composition

![image](https://github.com/Redcow/composition/assets/6064884/b76b1f20-574a-4d3b-bd26-df0e66fd62b0)

## mise en place
Installer le projet via la commande "docker-compose up -d"

Executer "composer install" via le terminal du container php

rendez-vous sur https://localhost:6333/ pour voir l'expression

## lancement des tests
vous pouvez déclenchez les tests via le terminal du container php : /app/vendor/bin/phpunit tests

## Structure :
HumanTranslator est le service déclencheur.

Les objets Composition et Criterion sont chargés via le PartProvider.
Les objets Composition et Criterion portent une même interface pour faciliter le traitement de production de texte quelque soit l'objet concerné dans l'arbre des compositions.

Les exceptions sont déclenchées si le JSON fournis est malformé. Leur spécialisation en Objet est utile pour les assertions dans les tests.

## Retours sur la mise en place de l'algo
Je m'attendais à un schéma de données en arbre, c'est un grand classique :)
Lorsque l'on doit parcourir un ensemble de noeuds et produire un résultat cumulatif, j'ai maintenant tendance à me tourner vers le design pattern composite.

J'ai également été guidé par une vidéo que j'ai vu récement concerant la mise en place d'un conteneur de service 'maison', 
là où on chargerait une instance de classe et ses dépendances de constructeur via un namespace,
ici j'ai tenté de charger une composition et ses identifiers via ses uuids.
(lien : https://youtu.be/78Vpg97rQwE?si=Mdu4cgrb8qa7cV8x tiré d'une playlist très complète : https://www.youtube.com/playlist?list=PLr3d3QYzkw2xabQRUpcZ_IBk9W50M9pe-)

Les difficultés rencontrées étaient diverses, je suis un grand fan des fonctions tableaux en Javascript, je trouve que PHP stagne les concernant (un petit array.find ferait du bien)
Du coup on se retrouve vite à faire des boucles, j'ai toujours des sueurs froides à en imbriquer!
Je ne suis pas hyper satisfait de la recherche du noeud parent par exemple.

J'ai démarré en voulant absolument mapper les éléments fournis par le JSON en objet d'où les classes Composition et Criterion, malgré tout je me pose la question si je n'aurai pas dû
également mettre en objet les identifiers car au final mon objet PartProvider manipule encore énormément de tableaux.

Pour éviter les magic string j'ai exporté les indexs en constantes,
c'est mieux pour la maintenabilité mais plus fatigant pour la lisibilité.

Bref j'ai déjà passé plus d'1h alors je m'arrête là
