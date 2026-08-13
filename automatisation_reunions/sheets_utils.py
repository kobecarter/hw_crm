"""
Utilitaires communs pour dupliquer l'onglet "modèle" (dernier onglet du fichier)
et écrire les nouvelles données dedans, en conservant la mise en forme existante.
"""
import datetime
import gspread


def ouvrir_client(chemin_service_account):
    return gspread.service_account(filename=chemin_service_account)


def nouvel_onglet_depuis_modele(spreadsheet, nom_nouvel_onglet=None):
    """
    Duplique le dernier onglet du classeur (utilisé comme modèle de mise en
    forme) et le place tout à droite, avec la date du jour comme nom.
    Retourne le nouvel onglet (gspread.Worksheet).
    """
    onglets = spreadsheet.worksheets()
    modele = onglets[-1]

    if nom_nouvel_onglet is None:
        nom_nouvel_onglet = datetime.date.today().strftime("%d/%m/%Y")

    # Évite les doublons si le script est relancé le même jour
    noms_existants = [o.title for o in onglets]
    nom_final = nom_nouvel_onglet
    suffixe = 2
    while nom_final in noms_existants:
        nom_final = f"{nom_nouvel_onglet} ({suffixe})"
        suffixe += 1

    nouvel_onglet = spreadsheet.duplicate_sheet(
        source_sheet_id=modele.id,
        insert_sheet_index=len(onglets),
        new_sheet_name=nom_final,
    )
    return spreadsheet.worksheet(nom_final), nouvel_onglet


def vider_zone_donnees(worksheet, premiere_ligne, derniere_colonne_lettre, nb_lignes=200):
    """Efface le contenu (pas la mise en forme) d'une zone pour repartir propre."""
    plage = f"A{premiere_ligne}:{derniere_colonne_lettre}{premiere_ligne + nb_lignes}"
    worksheet.batch_clear([plage])
