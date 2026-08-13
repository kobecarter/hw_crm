"""
Configuration du générateur de rapports de réunion.
Modifie les valeurs ci-dessous selon ton organisation.
"""

# Chemin vers le dossier racine du CRM (contient config.php)
CHEMIN_CRM = r"/Applications/XAMPP/xamppfiles/htdocs/hw_crm"

# Chemin vers le fichier JSON du compte de service Google (voir README.md)
CHEMIN_SERVICE_ACCOUNT = r"./service_account.json"

# IDs des 3 Google Sheets (récupérés depuis leurs URLs)
SHEET_ID_COMMERCIAL = "1ORamihKzsaU-Uj89oPPis5YASis4p8lu438mtjfRdwA"
SHEET_ID_ADMINISTRATIF = "13tpVW3d1kI3ODGRYGg-s-Fk5Aq9mrewznEbJjfD2xRA"
SHEET_ID_TECHNIQUE = "1h8TfaUaZwVjAbSKnGPQbG_OYXZNBAyP5xG8wlKoMVdY"

# Équipe commerciale : noms tels qu'affichés dans le fichier "Rapport de la
# réunion commerciale" (en-têtes de blocs de colonnes : Taches faites / Taches à effectuer)
EQUIPE_COMMERCIALE = ["HIBATALLAH", "HAMID", "ZAKARIA"]

# Mapping client -> responsable commercial (nom EXACT présent dans EQUIPE_COMMERCIALE).
# Complète cette liste au fur et à mesure. Un client absent de ce dictionnaire
# sera placé automatiquement sous "NON ASSIGNÉ" en bas du rapport, à répartir en réunion.
RESPONSABLE_PAR_CLIENT = {
    # "SUD PREPA IBN GHAZI": "ZAKARIA",
    # "SABWARE": "HAMID",
    # "ALFAHD GALLERY": "ZAKARIA",
}

# Statuts devis (com_devis/views/devis/list.php)
STATUT_DEVIS = {
    None: "Brouillon",
    0: "Brouillon",
    1: "Envoyé",
    2: "Accepté",
    3: "Contrat en attente de signature",
    4: "Paiement effectué",
    5: "Devis Refusé",
}

# Statuts facture (com_facture/classes/facture.php -> checkPayment())
STATUT_FACTURE = {
    0: "Impayée",
    1: "Payée",
    2: "Partiellement payée",
}

# Optionnel : token d'API privée HubSpot (Réglages > Intégrations > Apps privées,
# scope crm.objects.deals.read) pour inclure les devis créés directement sur HubSpot
# dans le rapport commercial. Laisser vide pour désactiver.
HUBSPOT_TOKEN = ""
