"""
Génère l'onglet du jour dans "Rapport de la réunion Administrative"
à partir des réclamations non traitées, rappels/échéances, absences en
attente et postes à recruter du CRM.

Colonnes du modèle observé : B=Sujet / Point à discuter,
C=Détails / Problématique, D=Décision prise / Action à entreprendre (laissée
vide, à remplir pendant la réunion).
"""
import config
import db
from sheets_utils import ouvrir_client, nouvel_onglet_depuis_modele


def table_existe(cur, nom_table):
    cur.execute("SHOW TABLES LIKE %s", (nom_table,))
    return cur.fetchone() is not None


def collecter_points():
    """Retourne une liste de tuples (sujet, details)."""
    points = []
    conn = db.connecter(config.CHEMIN_CRM)

    with conn.cursor() as cur:
        # Réclamations non traitées (clients / employés / fournisseurs confondus)
        cur.execute("""
            SELECT r.department, r.sujet, r.message, r.date_add,
                   COALESCE(c.raison_social, CONCAT(c.prenom,' ',c.nom)) AS client
            FROM crm_reclamation r
            LEFT JOIN crm_client c ON c.id = r.id_client
            WHERE r.etat = 0 OR r.etat IS NULL
            ORDER BY r.date_add
        """)
        for row in cur.fetchall():
            sujet = f"Réclamation - {row['department'] or 'N/A'} - {row['sujet'] or ''}"
            details = f"{row['client'] or ''} ({row['date_add']}) : {row['message'] or ''}"
            points.append((sujet, details))

        # Rappels / échéances non archivés
        cur.execute("""
            SELECT rp.type, rp.domaine, rp.date_expir, rp.remarque,
                   COALESCE(c.raison_social, CONCAT(c.prenom,' ',c.nom)) AS client
            FROM crm_rappel rp
            LEFT JOIN crm_client c ON c.id = rp.id_client
            WHERE rp.archived IS NULL OR rp.archived = 0
            ORDER BY rp.date_expir
        """)
        for row in cur.fetchall():
            sujet = f"Rappel - {row['type'] or ''} / {row['domaine'] or ''}"
            details = f"{row['client'] or ''} - échéance {row['date_expir']} : {row['remarque'] or ''}"
            points.append((sujet, details))

        # Absences en attente de validation (si colonne status existe)
        if table_existe(cur, "crm_absence"):
            try:
                cur.execute("""
                    SELECT a.nature_of_absence, a.start_date, a.end_date, a.status,
                           rh.firstname, rh.lastname
                    FROM crm_absence a
                    LEFT JOIN crm_resourcehumaine rh ON rh.id = a.id_resourcehumaine
                    WHERE a.status IS NULL OR a.status = 0
                """)
                for row in cur.fetchall():
                    sujet = "Absence à valider"
                    details = f"{row['firstname']} {row['lastname']} - {row['nature_of_absence']} du {row['start_date']} au {row['end_date']}"
                    points.append((sujet, details))
            except Exception:
                pass

        # Postes à recruter (module joboffer, ajouté après le dump SQL initial)
        if table_existe(cur, "crm_joboffer"):
            try:
                cur.execute("SELECT * FROM crm_joboffer")
                lignes = cur.fetchall()
                for row in lignes:
                    statut = str(row.get("statut", row.get("status", ""))).lower()
                    if statut in ("", "ouvert", "en cours", "0", "none"):
                        sujet = "Recrutement / poste ouvert"
                        details = str({k: v for k, v in row.items() if v not in (None, "")})
                        points.append((sujet, details))
            except Exception as e:
                points.append(("Recrutement (crm_joboffer)", f"Impossible de lire la table automatiquement ({e}). Vérifie les colonnes."))

        # Fournisseurs non validés (documents/dossiers en attente)
        cur.execute("""
            SELECT COALESCE(raison_social, CONCAT(prenom,' ',nom)) AS fournisseur, ville
            FROM crm_fournisseur
            WHERE valide = 0 OR valide IS NULL
        """)
        for row in cur.fetchall():
            points.append(("Fournisseur à valider", f"{row['fournisseur']} ({row['ville'] or ''})"))

    conn.close()
    return points


def generer():
    points = collecter_points()

    gc = ouvrir_client(config.CHEMIN_SERVICE_ACCOUNT)
    sh = gc.open_by_key(config.SHEET_ID_ADMINISTRATIF)
    ws, _ = nouvel_onglet_depuis_modele(sh)

    maj = []
    ligne = 4  # première ligne de données observée dans le modèle
    for sujet, details in points:
        maj.append({"range": f"B{ligne}", "values": [[f"[Auto CRM] {sujet}"]]})
        maj.append({"range": f"C{ligne}", "values": [[details]]})
        ligne += 1

    if maj:
        ws.batch_update(maj)

    print(f"Onglet '{ws.title}' créé dans le rapport administratif avec {ligne - 4} point(s).")


if __name__ == "__main__":
    generer()
