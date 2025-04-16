# 🛒 **E-Commerce Cyna-IT**

Une application e-commerce scalable construite avec une architecture **microservices**, utilisant **Symfony (PHP)** pour les services backend et **Vue.js** pour le frontend.

## 🚀 **Stack Technique**

- **Frontend** : Vue.js
- **Backend** : Symfony
- **Base de données** : PostgreSQL
- **Authentification** : JWT

[//]: # (- **Conteneurisation** : Docker)

---

## 📦 **Structure du projet**(à verifier ou màj)

Voici un aperçu de la structure du projet backend (à adapter selon les services et microservices) :

```
mon_projet_api/
├── bin/                    # Scripts d'exécution
├── config/                 # Configuration Symfony (services, routes, etc.)
│   ├── packages/
│   ├── routes/
│   └── services.yaml
├── public/                 # Point d'entrée public (index.php)
│   └── index.php
├── src/                    # Code métier de l'application
│   ├── Controller/         # Contrôleurs d'API
│   ├── Entity/             # Entités Doctrine
│   ├── Repository/         # Repositories Doctrine
│   ├── Service/            # Services métier (facultatif)
│   └── ...
├── templates/              # Templates Twig (inutile si API pure)
├── tests/                  # Tests
├── translations/           # Traductions (souvent inutile pour une API)
├── var/                    # Fichiers temporaires
├── vendor/                 # Dépendances installées par Composer
└── .env                    # Variables d'environnement
```

---

## 🧑‍💻 **Démarrage rapide**

### 1. **Prérequis**

Avant de commencer, assure-toi d’avoir installé les outils suivants :

- **Node.js** (v18+ pour le frontend)
- **Composer** (gestionnaire de dépendances PHP)
- **Symfony CLI** (outil de développement Symfony)
- **Base de données** (PostgreSQL ou MySQL)

### 2. **Installation**

Cloner le projet depuis le repository GitHub et se rendre dans le dossier :

```bash
git clone https://github.com/TeddyPrivat/cyna_it.git
cd cyna_it
```

### 3. **Installer les dépendances PHP**

Exécute la commande suivante pour installer toutes les dépendances via **Composer** :

```bash
composer install
```

---

### 4. **Configurer les variables d’environnement**

Dans le dossier `mon_projet_api`, trouve et modifie le fichier `.env` pour configurer les paramètres de ta base de données et d'autres variables importantes :

```bash
DATABASE_URL="pgsql://user:password@127.0.0.1:5432/your_database"
CORS_ALLOW_ORIGIN=http://localhost:3000
APP_SECRET=your-app-secret
```

Si tu utilises PostgreSQL, assure-toi que la base de données est créée :

```bash
php bin/console doctrine:database:create
```

---

### 5. **Exécuter les migrations de base de données**

Pour appliquer les migrations (création des tables, etc.), utilise cette commande :

```bash
php bin/console doctrine:migrations:migrate
```

---

### 6. **Démarrer le serveur Symfony**

Une fois les dépendances installées et la base de données configurée, démarre le serveur local avec la commande suivante :

```bash
symfony server:start
```

Le backend démarrera sur **http://localhost:8000**. Tu peux maintenant tester les routes API définies dans ton projet.

---

## 🌐 **Services disponibles**

| Service              | Port local     | URL                       |
|----------------------|----------------|---------------------------|
| **Frontend (Vue.js)**    | `5173`         | http://localhost:5173     |
| **Auth Service**         | `8001`         | http://localhost:8001/api |
| **Catalog Service**      | `8002`         | http://localhost:8002/api |
| **Cart Service**         | `8003`         | http://localhost:8003/api |
| **Order Service**        | `8004`         | http://localhost:8004/api |
| **Payment Service**      | `8005`         | http://localhost:8005/api |
| **Notification Service** | `8006`         | http://localhost:8006/api |
| **RabbitMQ Dashboard**   | `15672`        | http://localhost:15672    |

> 🛡️ **L'API Gateway** gère la redirection vers les services appropriés.

---

## 📈 **À venir**

- [ ] Version mobile responsive
- [ ] PWA / Offline support
- [ ] Authentification OAuth2
- [ ] Déploiement sur Kubernetes

---

## 👤 **Auteurs**

**[ltrentesaux](https://github.com/ltrentesaux)** [LinkedIn](https://www.linkedin.com/in/l%C3%A9o-trentesaux-94a733229/)

**[TeddyPrivat](https://github.com/TeddyPrivat)** [LinkedIn](https://www.linkedin.com/in/teddy-privat/)

**[wakakiVongola](https://github.com/WakakiVongola)** [LinkedIn](https://www.linkedin.com/in/aaron-tiavinjanahary-sdvrennes/)

