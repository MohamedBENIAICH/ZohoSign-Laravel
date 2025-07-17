# ZohoSign-Laravel

## Description

Ce projet est une application Laravel qui intègre l'API Zoho Sign pour la gestion des documents et des signatures électroniques. Il permet aux utilisateurs d'envoyer des documents pour signature, de suivre leur statut et par la suite recevront un email si tout est bon.

## Fonctionnalités

*   **Intégration Zoho Sign**: Intègre l'API Zoho Sign pour les opérations de signature électronique.
*   **Gestion des documents**: Permet d'envoyer des documents pour signature.
*   **Suivi du statut**: Suivi en temps réel du statut des documents envoyés pour signature.
*   **Gestion des signataires**: Ajout et gestion des signataires pour chaque document.
## Installation

Suivez ces étapes pour configurer le projet localement :

1.  **Cloner le dépôt :**

    ```bash
    git clone https://github.com/MohamedBENIAICH/ZohoSign-Laravel.git
    cd ZohoSign-Laravel
    ```

2.  **Installer les dépendances Composer :**

    ```bash
    composer install
    ```

3.  **Copier le fichier d'environnement :**

    ```bash
    cp .env.example .env
    ```

4.  **Générer la clé d'application :**

    ```bash
    php artisan key:generate
    ```

5.  **Configurer la base de données :** (Pas obligatoire dans ce projet)

    Ouvrez le fichier `.env` et configurez les informations de votre base de données :

    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password
    ```

6.  **Exécuter les migrations de la base de données :**

    ```bash
    php artisan migrate
    ```

7.  **Configurer les informations d'identification Zoho Sign :**

    Vous devrez obtenir vos informations d'identification d'API Zoho Sign (Client ID, Client Secret, Refresh Token, etc. ). Ajoutez-les à votre fichier `.env` :

    ```
    ZOHO_SIGN_CLIENT_ID=your_client_id
    ZOHO_SIGN_CLIENT_SECRET=your_client_secret
    ZOHO_SIGN_REFRESH_TOKEN=your_refresh_token
    ZOHO_SIGN_ORG_ID=your_organization_id
    ```

8.  **Lancer le serveur de développement :**

    ```bash
    php artisan serve
    ```

    L'application sera accessible à l'adresse `http://127.0.0.1:8000`.

## Utilisation

Une fois l'installation terminée, vous pouvez accéder à l'API via votre navigateur ou l'outil Postman.Assurez-vous que vos informations d'identification Zoho Sign sont correctement configurées pour que l'intégration fonctionne.

## Contribution

Les contributions sont les bienvenues ! Si vous souhaitez améliorer ce projet, veuillez suivre ces étapes :

1.  Fork le dépôt.
2.  Créez une nouvelle branche (`git checkout -b feature/nouvelle-fonctionnalite` ).
3.  Effectuez vos modifications et commitez-les (`git commit -am 'Ajouter une nouvelle fonctionnalité'`).
4.  Poussez votre branche (`git push origin feature/nouvelle-fonctionnalite`).
5.  Créez une Pull Request.

