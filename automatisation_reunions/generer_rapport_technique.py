"""
Génère l'onglet du jour dans "rapport des reunion" (technique)
à partir des devis actifs (acceptés / contrat en attente / payés) du CRM.

Colonnes du modèle observé : B=Client, C=Prestations, D=Etat du projet,
E=Date début de Projet, puis des blocs par personne (Stagiaires, Community
Manager, ...) avec "Tache a effectuer"/"Deadline" -- non automatisables
(pas de table de tâches par client dans le CRM), donc laissés vides.

LIMITE CONNUE : le modèle actuel groupe manuellement les clients par entité
("HELLO WORLD MAROC", etc.) avec des couleurs de fond selon le statut. Ce
script écrit une liste plate (sans les sous-groupes ni les couleurs) sous le
modèle dupliqué -- à toi de réorganiser/mettre en couleur pendant la réunion,
ou de me dire si tu veux que j'aille plus loin dans la reproduction exacte.
"""
import config
import db
from sheets_utils import ouvrir_client, nouvel_onglet_depuis_modele


def nom_client(row):
    return row["raison_social"] or f'{row["prenom"] or ""} {row["nom"] or ""}'.strip() or f'Client #{row["id"]}'


def recuperer_projets_actifs():
    conn = db.connecter(config.CHEMIN_CRM)
    projets = {}

    with conn.cursor() as cur:
        cur.execute("""
            SELECT d.id AS id_devis, d.date_devis, d.statu,
                   c.id, c.raison_social, c.prenom, c.nom
            FROM crm_devis d
            JOIN crm_client c ON c.id = d.id_client
            WHERE d.statu IN (2, 3, 4)
            ORDER BY d.date_devis DESC
        """)
        devis_rows = cur.fetchall()

        for row in devis_rows:
            client = nom_client(row)
            if client in projets:
                continue  # garde le devis le plus récent par client (déjà trié)

            cur.execute("""
                SELECT titre FROM crm_item_devis WHERE id_devis = %s ORDER BY ordre
            """, (row["id_devis"],))
            prestations = ", ".join(i["titre"] for i in cur.fetchall() if i["titre"])

            projets[client] = {
                "prestations": prestations,
                "etat": config.STATUT_DEVIS.get(row["statu"], row["statu"]),
                "date_debut": row["date_devis"],
            }

    conn.close()
    return projets


def generer():
    projets = recuperer_projets_actifs()

    gc = ouvrir_client(config.CHEMIN_SERVICE_ACCOUNT)
    sh = gc.open_by_key(config.SHEET_ID_TECHNIQUE)
    ws, _ = nouvel_onglet_depuis_modele(sh)

    maj = []
    ligne = 6  # sous le modèle dupliqué (row 5 = en-tête de groupe dans le modèle observé)
    for client, info in sorted(projets.items()):
        maj.append({"range": f"B{ligne}", "values": [[client]]})
        maj.append({"range": f"C{ligne}", "values": [[info["prestations"]]]})
        maj.append({"range": f"D{ligne}", "values": [[f"[Auto CRM] {info['etat']}"]]})
        maj.append({"range": f"E{ligne}", "values": [[str(info["date_debut"]) if info["date_debut"] else ""]]})
        ligne += 1

    if maj:
        ws.batch_update(maj)

    print(f"Onglet '{ws.title}' créé dans le rapport technique avec {ligne - 6} projet(s) actif(s).")
    print("Pense à répartir manuellement les tâches par personne (colonnes de droite) et les regroupements par entité.")


if __name__ == "__main__":
    generer()
