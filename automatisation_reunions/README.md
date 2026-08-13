# Automatisation des 3 rapports de réunion (CRM → Google Sheets)

Ce dossier génère, en une commande, un nouvel onglet daté du jour dans
chacun de tes 3 fichiers Google Sheets, pré-rempli avec les données
fraîches de ton CRM (hw_crm) :

- **Rapport de la réunion commerciale** : factures impayées/partielles,
  devis en attente, dernière relance par client → réparti par responsable
  (Hibatallah / Hamid / Zakaria)
- **Rapport de la réunion Administrative** : réclamations non traitées,
  rappels/échéances, absences à valider, postes à recruter, fournisseurs à
  valider
- **rapport des reunion (technique)** : projets actifs (devis acceptés /
  contrat en attente / payés), prestations, statut, date de début

Les colonnes "Décision" / "Tâche à effectuer" / "Deadline" restent
**volontairement vides** : c'est le contenu de la réunion elle-même, pas
une donnée du CRM.

## Ce que ce script ne fait PAS (limites connues)

- **HubSpot n'est pas connecté au CRM** aujourd'hui (c'est juste une valeur
  de menu "source client"). Si tu veux inclure les devis créés directement
  sur HubSpot, donne-moi un token d'API privée HubSpot (scope
  `crm.objects.deals.read`) et j'ajoute la connexion.
- Le rapport **technique** a un modèle très personnalisé (regroupement par
  entité, couleurs manuelles). Le script écrit une liste plate en dessous
  du modèle dupliqué ; à toi de réorganiser/colorer pendant la réunion.
- **L'assignation client → commercial** (Hibatallah/Hamid/Zakaria) n'existe
  pas dans le CRM : complète le dictionnaire `RESPONSABLE_PAR_CLIENT` dans
  `config.py`. Les clients non mappés apparaissent sous "NON ASSIGNÉ".
- Le module recrutement (`crm_joboffer`) n'était pas dans l'export SQL que
  j'ai analysé (ajouté après) : le script le lit dynamiquement mais vérifie
  bien le résultat au premier essai, les noms de colonnes peuvent différer.

## Installation (à faire une seule fois)

### 1. Dépendances Python

Sur ta machine (celle qui fait tourner XAMPP), dans ce dossier :

```
pip install -r requirements.txt
```

### 2. Compte de service Google (pour écrire dans les Sheets)

1. Va sur https://console.cloud.google.com/ (connecte-toi avec ton compte
   Google, celui propriétaire des 3 fichiers).
2. Crée un projet (ou utilise un projet existant).
3. Menu "API et services" → "Bibliothèque" → active **Google Sheets API**
   et **Google Drive API**.
4. "API et services" → "Identifiants" → "Créer des identifiants" →
   **Compte de service**. Donne-lui un nom (ex : `hw-crm-reports`), pas
   besoin de rôle particulier au niveau projet.
5. Ouvre le compte de service créé → onglet "Clés" → "Ajouter une clé" →
   JSON. Un fichier `.json` se télécharge : renomme-le
   `service_account.json` et mets-le dans ce dossier.
6. Le compte de service a une adresse email du type
   `hw-crm-reports@xxxx.iam.gserviceaccount.com` (visible dans la page ou
   dans le fichier JSON, champ `client_email`).
7. **Partage les 3 Google Sheets avec cette adresse email**, en droit
   "Éditeur" (bouton "Partager" en haut à droite de chaque fichier).

### 3. Configuration

Ouvre `config.py` et vérifie/adapte :

- `CHEMIN_CRM` : chemin vers le dossier `hw_crm` (déjà pré-rempli)
- `EQUIPE_COMMERCIALE` et `RESPONSABLE_PAR_CLIENT` : qui gère quel client
- `HUBSPOT_TOKEN` : optionnel, voir plus haut

## Utilisation

Avant chaque réunion, lance :

```
python generer_tout.py
```

Ou un seul rapport :

```
python generer_rapport_commercial.py
python generer_rapport_administratif.py
python generer_rapport_technique.py
```

Chaque script crée un nouvel onglet (nommé avec la date du jour) tout à
droite du fichier concerné, en dupliquant la mise en forme du dernier
onglet existant.

## Automatiser complètement (optionnel)

Une fois que tu as testé et validé les résultats manuellement plusieurs
fois, on peut planifier `generer_tout.py` pour qu'il tourne tout seul
(ex : tous les lundis matin) via le Planificateur de tâches Windows ou
`cron` sur Mac/Linux — dis-le-moi et je prépare la tâche planifiée.

## Premier test recommandé

1. Fais d'abord tourner un seul script (ex : `generer_rapport_administratif.py`,
   le plus simple) et vérifie le résultat dans Google Sheets.
2. Ajuste `config.py` si des libellés/statuts ne correspondent pas à ce que
   tu attends.
3. Une fois satisfait, lance `generer_tout.py` pour les 3 rapports.
